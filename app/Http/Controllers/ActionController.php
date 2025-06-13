<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Level;
use Illuminate\Http\Request;

class ActionController extends Controller {
    public function index(Request $request){
        $xpNeeded = null;
        $query = Action::with('ingredients');

        // LEVEL-based input
        if ($request->filled('current_level') && $request->filled('goal_level')) {
            $currentLevel = (int) $request->input('current_level');
            $goalLevel = (int) $request->input('goal_level');

            $currentXp = Level::where('level', $currentLevel)->value('xp') ?? 0;
            $goalXp = Level::where('level', $goalLevel)->value('xp') ?? 0;

            $xpNeeded = max(0, $goalXp - $currentXp);
        }
        // XP-based input
        elseif ($request->filled('current_xp') && $request->filled('goal_xp')) {
            $currentXp = (int) $request->input('current_xp');
            $goalXp = (int) $request->input('goal_xp');

            $xpNeeded = max(0, $goalXp - $currentXp);
        }

        $actions = Action::with('ingredients')->get()->map(function ($action) use ($xpNeeded) {
            if ($xpNeeded !== null && $action->xp > 0) {
                $action->times_needed = ceil($xpNeeded / $action->xp);
            } else {
                $action->times_needed = null;
            }
            return $action;
        });

        // Sorting
        $allowedSorts = ['name', 'level', 'xp', 'buy', 'sell']; // whatever columns you want sortable
        $sort = $request->input('sort');
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        if (in_array($sort, $allowedSorts)) {
            $query = $query->orderBy($sort, $direction);
        }

        $actions = $query->get();

        return view('actions.index', compact('actions', 'xpNeeded', 'sort', 'direction'));
    }
}