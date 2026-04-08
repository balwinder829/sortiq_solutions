<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\HelpdeskArticle;
use App\Models\Helpdesk\HelpdeskTechnology;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Helpdesk\HelpdeskAttachment;
use Illuminate\Support\Facades\Storage;


class HelpdeskArticleController extends Controller
{
    public function index(Request $request)
    {

        if ($request->filled('category')) {

            $technology = HelpdeskTechnology::findOrFail($request->category);

            abort_unless(
                auth()->user()->can($technology->slug.'.view'),
                403
            );
        }
       $query = HelpdeskArticle::with('technology');

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('technology', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->latest()->paginate(15);

        // categories for dropdown
        $categories = HelpdeskTechnology::pluck('name','id');

        return view(
            'helpdesk.admin.articles.index',
            compact('items','categories')
        );
        // return view('helpdesk.admin.articles.index', compact('items'));
    }

    public function create()
    {
        $technologies = HelpdeskTechnology::pluck('name','id');
        return view('helpdesk.admin.articles.create',compact('technologies'));
    }

    public function store(Request $request)
{   


    $technology = HelpdeskTechnology::findOrFail($request->technology_id);

        abort_unless(
            auth()->user()->can($technology->slug.'.create'),
            403
        );

    $request->validate([
        'technology_id' => 'required|exists:helpdesk_technologies,id',
        'title' => 'required|max:255',
        'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
        'expires_at' => 'nullable|date'
    ]);

    $article = HelpdeskArticle::create([
        'technology_id' => $request->technology_id,
        'title' => $request->title,
        'slug' => Str::slug($request->title),
        'description' => $request->description,
        'status' => $request->status ?? 'draft',
        'is_active' => $request->is_active ?? '1',
        'expires_at' => $request->filled('expires_at')
            ? $request->expires_at
            : null,
    ]);

    /* 🔥 SAVE ATTACHMENTS */
    if ($request->hasFile('files')) {

        foreach ($request->file('files') as $file) {

            $path = $file->store('helpdesk');

            HelpdeskAttachment::create([
                'article_id' => $article->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType()
            ]);
        }
    }

    return redirect()
        ->route('admin.helpdesk.articles.index')
        ->with('success', 'Article created successfully');
}



    public function show(HelpdeskArticle $article)
    {
        $article->load('attachments','technology');
        return view('helpdesk.admin.articles.show',compact('article'));
    }

    public function edit(HelpdeskArticle $article)
    {
        $technologies = HelpdeskTechnology::pluck('name','id');
        abort_unless(
            auth()->user()->can($article->technology->slug.'.edit'),
            403
        );
        return view('helpdesk.admin.articles.edit',compact('article','technologies'));
    }

    public function update(Request $request, HelpdeskArticle $article)
{

    abort_unless(
        auth()->user()->can($article->technology->slug.'.edit'),
        403
    );
    $request->validate([
        'title' => 'required|max:255',
        'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        'expires_at' => 'nullable|date'
    ]);

    $article->update([
        'technology_id' => $request->technology_id,
        'title' => $request->title,
        'slug' => Str::slug($request->title),
        'description' => $request->description,
        'status' => $request->status,
        'is_active' => $request->is_active,
        'expires_at' => $request->filled('expires_at')
                    ? $request->expires_at
                    : null
    ]);

    /* 🔥 ADD NEW FILES */
    if ($request->hasFile('files')) {

        foreach ($request->file('files') as $file) {

            $path = $file->store('helpdesk');

            HelpdeskAttachment::create([
                'article_id' => $article->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType()
            ]);
        }
    }

    return redirect()
        ->route('admin.helpdesk.articles.index')
        ->with('success', 'Article updated successfully');
}



    public function destroy(HelpdeskArticle $article)
    {
        abort_unless(
            auth()->user()->can($article->technology->slug.'.delete'),
            403
        );
        $article->delete();

        return back()->with('success','Deleted');
    }
}
