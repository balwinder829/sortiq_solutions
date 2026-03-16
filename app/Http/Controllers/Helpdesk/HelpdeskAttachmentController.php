<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\HelpdeskAttachment;
use App\Models\Helpdesk\HelpdeskArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HelpdeskAttachmentController extends Controller
{
    public function index()
    {
        $items = HelpdeskAttachment::latest()->paginate(20);
        return view('helpdesk.admin.attachments.index',compact('items'));
    }

    public function create()
    {
        $articles = HelpdeskArticle::pluck('title','id');
        return view('helpdesk.admin.attachments.create',compact('articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id'=>'required|exists:helpdesk_articles,id',
            'file'=>'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480'
        ]);

        $file = $request->file('file');

        $path = $file->store('helpdesk');

        HelpdeskAttachment::create([
            'article_id'=>$request->article_id,
            'file_name'=>$file->getClientOriginalName(),
            'file_path'=>$path,
            'file_type'=>$file->getClientMimeType(),
            'expires_at'=>$request->expires_at
        ]);

        return redirect()->back()->with('success','Uploaded');
    }

    public function destroy(HelpdeskAttachment $attachment)
    {
        Storage::delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success','Deleted');
    }
}
