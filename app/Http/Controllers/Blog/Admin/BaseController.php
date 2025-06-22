<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;

class BaseController extends Controller
{
    use DispatchesJobs;

    public function __construct()
    {
        //
    }
}
