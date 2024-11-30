<?php

namespace App\Http\Controllers;

use App\Enums\JobStatusEnum;
use App\Http\Requests\BackgroundJobRequest;
use App\Models\BackgroundJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BackgroundJobsController extends Controller
{
    public function index(): View
    {
        return view('background_jobs.index', [
            'jobs' => BackgroundJob::orderBy('id', 'desc')->simplePaginate(15),
        ]);
    }

    public function create(): View
    {
        return view('background_jobs.create');
    }

    public function store(BackgroundJobRequest $request)
    {
        $validated = $request->validated();

        BackgroundJob::create($validated)->dispatch();

        session()->flash('success', 'Successfully created job.');

        return redirect()->route('background-jobs.index');
    }

    public function show(BackgroundJob $job): View
    {
        return view('background_jobs.show', compact('job'));
    }

    public function retry(BackgroundJob $job): RedirectResponse
    {
        if ($job->failed) {
            $job->update([
                'status' => JobStatusEnum::Pending->value,
                'retry_count' => $job->retry_count + 1
            ]);
        }

        return redirect()->back();
    }
}
