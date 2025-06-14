<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Level;
use Illuminate\Http\Request;

class ActionController extends Controller {
    public function index(Request $request){
        $xpNeeded = null;
        $showAll = $request->has('show_all');
        $sort = $request->input('sort') ?? '';
        $direction = $request->input('direction') ?? 'asc';
        $query = Action::with('ingredients');

        if ($showAll) {
            $actions = $query->get()->map(function ($action) {
                $action->margin = $action->sell - $action->buy;
                $action->margin_percent = $action->buy > 0 ? (($action->sell - $action->buy) / $action->buy) * 100 : 0;
                $action->quantity_needed = null;

                $action->first_ingredient = optional($action->ingredients->first())->name ?? '';
                return $action;
            });

            // Pass $sort, $direction, $xpNeeded for consistency
            return view('actions.index', compact('actions', 'showAll', 'sort', 'direction'))->with('xpNeeded', null);
        }

        // Level-based input
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

        // Sorting
        $validDbColumns = ['name', 'level', 'xp', 'buy', 'sell', 'members_only'];
        $sort = $request->input('sort');
        $direction = $request->input('direction');

        if ($sort && in_array($sort, $validDbColumns)) {
            $query->orderBy($sort, $direction);
        }

        if ($request->has('f2p_only')) {
            $query->where('members_only', false);
        }

        if ($request->has('only_profitable')) {
            $query->whereRaw('sell - buy > 0');
        }

        $actions = $query->with('ingredients')->get()->map(function ($action) use ($xpNeeded) {
            $action->margin = $action->sell - $action->buy;
            $action->margin_percent = $action->buy > 0 ? (($action->sell - $action->buy) / $action->buy) * 100 : 0;
            $action->quantity_needed = ($xpNeeded && $action->xp > 0) ? ceil($xpNeeded / $action->xp) : null;

            $action->first_ingredient = optional($action->ingredients->first())->name ?? '';

            return $action;
        });

        if ($sort === 'first_ingredient') {
            $actions = $actions->sortBy(function ($a) {
                return $a->first_ingredient;
            }, SORT_REGULAR, $direction === 'desc');
        } else {
            $actions = $actions->values();
        }

        if ($sort === 'quantity_needed') {
            $actions = $actions->sortBy(function ($a) {
                return $a->quantity_needed ?? 0;
            }, SORT_REGULAR, $direction === 'desc')->values();
        } elseif ($sort === 'first_ingredient') {
            $actions = $actions->sortBy(function ($a) {
                return $a->first_ingredient;
            }, SORT_REGULAR, $direction === 'desc')->values();
        } else {
            $actions = $actions->values();
        }

        // Now apply collection sort if needed
        if (!in_array($sort, $validDbColumns) && in_array($direction, ['asc', 'desc'])) {
            $actions = $direction === 'asc'
                ? $actions->sortBy($sort)->values()
                : $actions->sortByDesc($sort)->values();
        }

        return view('actions.index', compact('actions', 'xpNeeded', 'sort', 'direction'))->with('showAll', false);
    }
}