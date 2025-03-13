<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PowerStructureController extends Controller
{
    public function people()
    {
        return view('pages.power.people', ['navbarTheme' => 'navbar-light']);
    }

    public function president()
    {
        return view('pages.power.president', ['navbarTheme' => 'navbar-light']);
    }

    public function assembly()
    {
        return view('pages.power.assembly', ['navbarTheme' => 'navbar-light']);
    }

    public function executive()
    {
        return view('pages.power.executive', ['navbarTheme' => 'navbar-light']);
    }

    public function legislative()
    {
        return view('pages.power.legislative', ['navbarTheme' => 'navbar-light']);
    }

    public function judicial()
    {
        return view('pages.power.judicial', ['navbarTheme' => 'navbar-light']);
    }

    public function representatives()
    {
        return view('pages.power.representatives', ['navbarTheme' => 'navbar-light']);
    }

    public function council()
    {
        return view('pages.power.council', ['navbarTheme' => 'navbar-light']);
    }

    public function constitutional()
    {
        return view('pages.power.constitutional', ['navbarTheme' => 'navbar-light']);
    }

    public function supreme()
    {
        return view('pages.power.supreme', ['navbarTheme' => 'navbar-light']);
    }
}
