<?php


namespace App\Http\Controllers;


class FrontController
{
    public function index() {
        return view('front.index');
    }

    public function contacts()
    {
        return view('front.contacts');
    }

    public function about()
    {
        return view('front.about');
    }
}
