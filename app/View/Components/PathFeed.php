<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use App\Models\Path;
use Illuminate\View\Component;

class PathFeed extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.path-feed', [
            'paths' => Path::withCount('steps')->latest()->get()
        ]);
    }
}
