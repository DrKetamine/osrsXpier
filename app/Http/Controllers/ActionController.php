<?php

namespace App\Http\Controllers;

use App\Models\Action;

class ActionController extends Controller
{
    public function index()
    {
        $actions = Action::with('ingredients')->get();
        return view('actions.index', compact('actions'));
    }
}
