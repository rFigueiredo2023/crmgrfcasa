<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Custormers extends Controller
{
  public function index()
  {
    return view('content.pages.customers.pages-customers');
  }
}
