<?php

namespace App\Http\Controllers;

use App\Enums\PhpExecStatusEnum;
use App\Http\Requests\BackgroundJobRequest;
use App\Models\PhpExecCommandModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BackgroundJobsController extends Controller
{
    public function index(): View
    {
        return view('background_jobs.index', [
            'jobs' => PhpExecCommandModel::orderBy('id', 'desc')->simplePaginate(15),
        ]);
    }

    public function create(): View
    {
        return view('background_jobs.create');
    }

    public function store(BackgroundJobRequest $request)
    {
        $validated = $request->validated();

        PhpExecCommandModel::create($validated);

        session()->flash('success', 'Successfully created job.');

        return redirect()->route('background-jobs.index');
    }

    public function show(PhpExecCommandModel $job): View
    {
        return view('background_jobs.show', compact('job'));
    }

    public function retry(PhpExecCommandModel $job): RedirectResponse
    {
        if ($job->failed) {
            $job->update([
                'status' => PhpExecStatusEnum::Pending->value,
                'retry_count' => $job->retry_count + 1
            ]);
        }

        return redirect()->back();
    }
}
