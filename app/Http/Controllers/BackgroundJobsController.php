<?php

namespace App\Http\Controllers;

use App\Http\Requests\BackgroundJobRequest;
use App\Models\PhpExecCommandModel;
use Illuminate\View\View;

class BackgroundJobsController extends Controller
{
    public function index(): View
    {
        return view('background_jobs.index', [
            'jobs' => PhpExecCommandModel::orderBy('id', 'desc')->simplePaginate(5),
        ]);
    }

    public function create(): View
    {
        return view('background_jobs.create');
    }

    public function store(BackgroundJobRequest $request)
    {
        // todo: Convert args to array
        $validated = $request->validated();

        PhpExecCommandModel::create($validated);

        session()->flash('success', 'Successfully created job.');

        return redirect()->route('background-jobs.index');
    }

    public function show(PhpExecCommandModel $job): View
    {
        return view('background_jobs.show', compact('job'));
    }
}
