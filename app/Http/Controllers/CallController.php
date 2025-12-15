<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadCallRequest;
use App\Jobs\ProcessCallRecording;
use App\Models\Call;
use App\Services\CallAnalysisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CallController extends Controller
{
    public function index(): View
    {
        $calls = Call::latest()
            ->paginate(15);

        return view('calls.index', compact('calls'));
    }

    public function create(): View
    {
        return view('calls.upload');
    }

    public function store(UploadCallRequest $request): RedirectResponse
    {
        $file = $request->file('audio');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('calls', $filename);

        $call = Call::create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        ProcessCallRecording::dispatch($call);

        return redirect()
            ->route('calls.show', $call)
            ->with('success', 'Call uploaded successfully. Processing will begin shortly.');
    }

    public function show(Call $call): View
    {
        return view('calls.show', compact('call'));
    }

    public function reanalyze(Call $call, CallAnalysisService $analysisService): RedirectResponse
    {
        if (!$call->transcript) {
            return back()->with('error', 'Cannot reanalyze a call without a transcript.');
        }

        $analysisService->reanalyze($call);

        return back()->with('success', 'Call has been reanalyzed successfully.');
    }

    public function destroy(Call $call): RedirectResponse
    {
        $call->delete();

        return redirect()
            ->route('calls.index')
            ->with('success', 'Call record deleted successfully.');
    }
}

