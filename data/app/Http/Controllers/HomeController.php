<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    protected bool $auth = false;

    public function index()
    {
        return $this->View('home');
    }
}
