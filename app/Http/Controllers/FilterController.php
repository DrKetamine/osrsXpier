<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'params' => 'required|string',
        ]);

        // Save filter for current user
        /** @var User $user */
        $user = Auth::user();
        $user->savedFilters()->create([
            'name' => $request->name,
            'params' => $request->params,
            'is_default' => false,
        ]);

        return response()->json(['success' => true]);
    }

    public function load(UserFilter $filter)
    {

        // Redirect to main page with filter params in URL
        return redirect(route('actions.index') . '?' . $filter->params);
    }
}
