<?php

namespace App\Http\Controllers;

use App\Models\PhpExecCommandModel;
use Illuminate\View\View;

class BackgroundJobsController extends Controller
{
    public function index(): View
    {
        return view('background_jobs.index', [
            'jobs' => PhpExecCommandModel::paginate(50),
        ]);
    }
}
