<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class FrontendPageController extends Controller
{
    //
    public function show($slug = null)
    {


        return view('frontend.page');
    }

}
