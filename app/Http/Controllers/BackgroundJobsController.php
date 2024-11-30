<?php

namespace App\Http\Controllers;

use App\Http\Requests\BackgroundJobRequest;
use App\Models\BackgroundJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * BackgroundJobsController handles HTTP requests related to background jobs.
 *
 * This controller provides endpoints for listing, creating, viewing, and retrying background jobs.
 */
class BackgroundJobsController extends Controller
{
    /**
     * Display a list of all background jobs.
     *
     * @return View The view containing the list of background jobs with pagination.
     */
    public function index(): View
    {
        return view('background_jobs.index', [
            'jobs' => BackgroundJob::orderBy('id', 'desc')->simplePaginate(15),
        ]);
    }

    /**
     * Display the form for creating a new background job.
     *
     * @return View The view containing the create form with an initial instance of a new background job.
     */
    public function create(): View
    {
        return view('background_jobs.create', ['job' => BackgroundJob::make()]);
    }

    /**
     * Store a newly created background job in the database.
     *
     * @param BackgroundJobRequest $request The validation rules for creating a new background job.
     *
     * @return RedirectResponse A redirect response to the list view after successful creation.
     */
    public function store(BackgroundJobRequest $request)
    {
        // Validate the request data
        $validated = $request->validated();

        // Create and dispatch a new background job with the validated data
        BackgroundJob::create($validated)->dispatch();

        // Flash a success message to the session
        session()->flash('success', 'Successfully created job.');

        // Redirect back to the list view
        return redirect()->route('background-jobs.index');
    }

    /**
     * Display the details of a background job.
     *
     * @param BackgroundJob $job The background job instance being displayed.
     *
     * @return View The view containing the details of the background job.
     */
    public function show(BackgroundJob $job): View
    {
        return view('background_jobs.show', compact('job'));
    }

    /**
     * Retry a failed background job.
     *
     * @param BackgroundJob $job The background job instance being retried.
     *
     * @return RedirectResponse A redirect response to the list view after retrying the job.
     */
    public function retry(BackgroundJob $job): RedirectResponse
    {
        // Retry the background job
        $job->attemptRetryIfFailed();

        // Return a redirect response back to the original request
        return redirect()->back();
    }
}
