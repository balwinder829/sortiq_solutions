<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\HelpdeskTechnology;
use App\Models\Helpdesk\HelpdeskArticle;
use App\Models\Helpdesk\HelpdeskAttachment;
use Illuminate\Support\Facades\Storage;

class HelpdeskFrontController extends Controller
{
    public function index()
    {
        abort(404);
        $technologies = HelpdeskTechnology::latest()->get();
        return view('frontend.helpdesk.article',compact('technologies'));
    }

    public function technology_old($slug)
    {
        $technology = HelpdeskTechnology::where('slug',$slug)->firstOrFail();

        $articles = $technology->articles()
            ->where('status','published')
            ->latest()
            ->get();

        return view('helpdesk.front.technology',compact('technology','articles'));
    }

    public function technology($slug)
    {
        abort(404);
        $technology = HelpdeskTechnology::where('slug',$slug)->firstOrFail();

        $articles = $technology->articles()
            ->where('status','published')
            ->where('is_active',1)
            ->where(function($q){
                $q->whereNull('expires_at')
                  ->orWhere('expires_at','>',now());
            })
            ->latest()
            ->get();

        return view('frontend.helpdesk.technology',compact('technology','articles'));
    }

    public function article($techSlug, $articleSlug)
    {
        $article = HelpdeskArticle::where('slug',$articleSlug)
            ->whereHas('technology', function($q) use ($techSlug){
                $q->where('slug',$techSlug);
            })
            ->where('status','published')
            ->where('is_active',1)
            // ->where(function($q){
            //     $q->whereNull('expires_at')
            //       ->orWhere('expires_at','>',now());
            // })
            ->with('attachments','technology')
            ->firstOrFail();

        return view('frontend.helpdesk.article',compact('article'));
    }

    public function article_old($techSlug,$articleSlug)
    {
        $article = HelpdeskArticle::where('slug',$articleSlug)
            ->where('status','published')
            ->with('attachments','technology')
            ->firstOrFail();

        return view('helpdesk.front.article',compact('article'));
    }

    public function download(HelpdeskAttachment $attachment)
    {
        abort_if(
            $attachment->expires_at && now()->gt($attachment->expires_at),
            403,
            'File expired'
        );

        $attachment->increment('downloads');

        return Storage::download(
            $attachment->file_path,
            $attachment->file_name
        );
    }
}
