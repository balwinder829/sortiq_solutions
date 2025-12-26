<?php

// app/Http/Controllers/TutorialController.php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index()
    {
        $tutorials = Tutorial::latest()->paginate(100);
        return view('tutorials.index', compact('tutorials'));
    }

    public function create()
    {
        return view('tutorials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url', // User provides the full URL
            'technology' => 'required',
            'level' => 'required',
            'description' => 'nullable|string',
        ]);
        
        $youtubeId = $this->extractYoutubeId($validated['youtube_url']);

        if (!$youtubeId) {
            return back()->withErrors(['youtube_url' => 'Could not extract a valid YouTube video ID from the URL.'])->withInput();
        }

        Tutorial::create([
            // 'user_id' => auth()->id(),
            'title' => $validated['title'],
            'youtube_id' => $youtubeId,
            'description' => $validated['description'],
            'technology' => $validated['technology'],
            'level' => $validated['level'],
        ]);

        return redirect()->route('tutorials.index')->with('success', 'Tutorial added successfully!');
    }

    public function show(Tutorial $tutorial)
    {
        return view('tutorials.show', compact('tutorial'));
    }

    public function edit(Tutorial $tutorial)
    {
        return view('tutorials.edit', compact('tutorial'));
    }

    public function update(Request $request, Tutorial $tutorial)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'required|url', // User provides the full URL
            'technology' => 'required',
            'level' => 'required',
            'description' => 'nullable|string',
        ]);
        $youtubeId = $this->extractYoutubeId($validated['youtube_url']);

        if (!$youtubeId) {
            return back()->withErrors(['youtube_url' => 'Could not extract a valid YouTube video ID from the URL.'])->withInput();
        }

        $tutorial->update([
            'title' => $validated['title'],
            'youtube_id' => $youtubeId,
            'description' => $validated['description'],
            'technology' => $validated['technology'],
            'level' => $validated['level'],
        ]);

        return redirect()->route('tutorials.index')->with('success', 'Tutorial updated successfully!');
    }

    public function destroy(Tutorial $tutorial)
    {
        $tutorial->delete();
        return redirect()->route('tutorials.index')->with('success', 'Tutorial removed (soft deleted).');
    }
    
    /**
     * Helper to extract the YouTube video ID from various URL formats.
     */
    protected function extractYoutubeId($url)
    {
        $patterns = [
            '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }
}