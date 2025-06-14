<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Level;
use Illuminate\Http\Request;

class ActionController extends Controller {
    public function index(Request $request) {
        // Input setup
        $showAll = $request->has('show_all');
        $sort = $request->input('sort') ?? '';
        $direction = $request->input('direction') ?? 'asc';

        // Filters
        $filterMode = $request->input('filter_mode');
        $f2pOnly = $request->has('f2p_only');
        $onlyProfitable = $request->has('only_profitable');

        // Level & XP inputs
        $currentLevel = $request->filled('current_level') ? (int) $request->input('current_level') : null;
        $goalLevel = $request->filled('goal_level') ? (int) $request->input('goal_level') : null;
        $currentXp = $request->filled('current_xp') ? (int) $request->input('current_xp') : null;
        $goalXp = $request->filled('goal_xp') ? (int) $request->input('goal_xp') : null;

        $xpNeeded = null;

        // === Handle "Show All" mode ===
        if ($showAll) {
            $actions = Action::with('ingredients')->get()->map(function ($action) {
                $action->margin = $action->sell - $action->buy;
                $action->margin_percent = $action->buy > 0 ? (($action->sell - $action->buy) / $action->buy) * 100 : 0;
                $action->quantity_needed = null;
                $action->first_ingredient = optional($action->ingredients->first())->name ?? '';
                return $action;
            });

            return view('actions.index', compact('actions', 'showAll', 'sort', 'direction'))->with('xpNeeded', null);
        }

        // === Build Query ===
        $query = Action::with('ingredients');

        // Apply level-based filters (only if level input exists)
        if ($filterMode === 'can_do_now') {
            $query->where('level', '<=', $currentLevel ?? 0);
        } elseif ($filterMode === 'highlight_future') {
            $query->where('level', '<=', $goalLevel ?? 0);
        }

        // F2P filter
        if ($f2pOnly) {
            $query->where('members_only', false);
        }

        // Profitability filter
        if ($onlyProfitable) {
            $query->whereRaw('sell - buy > 0');
        }

        // === XP Needed Calculation ===
        if ($currentLevel !== null && $goalLevel !== null) {
            $currentXp = Level::where('level', $currentLevel)->value('xp') ?? 0;
            $goalXp = Level::where('level', $goalLevel)->value('xp') ?? 0;
            $xpNeeded = max(0, $goalXp - $currentXp);
        } elseif ($currentXp !== null && $goalXp !== null) {
            $xpNeeded = max(0, $goalXp - $currentXp);
        }

        // === DB-Level Sorting ===
        $validDbColumns = ['name', 'level', 'xp', 'buy', 'sell', 'members_only'];
        if (in_array($sort, $validDbColumns)) {
            $query->orderBy($sort, $direction);
        }

        // === Fetch & Transform Actions ===
        $actions = $query->get()->map(function ($action) use ($xpNeeded) {
            $action->margin = $action->sell - $action->buy;
            $action->margin_percent = $action->buy > 0 ? (($action->sell - $action->buy) / $action->buy) * 100 : 0;
            $action->quantity_needed = ($xpNeeded && $action->xp > 0) ? ceil($xpNeeded / $action->xp) : null;
            $action->first_ingredient = optional($action->ingredients->first())->name ?? '';
            return $action;
        });

        // === Collection-Level Sorting (custom keys only) ===
        if ($sort === 'quantity_needed') {
            $actions = $actions->sortBy(function ($a) {
                return $a->quantity_needed ?? PHP_INT_MAX; // Push nulls to bottom
            }, SORT_REGULAR, $direction === 'desc')->values();
        } elseif ($sort === 'first_ingredient') {
            $actions = $actions->sortBy(function ($a) {
                return $a->first_ingredient;
            }, SORT_REGULAR, $direction === 'desc')->values();
        } elseif (!in_array($sort, $validDbColumns) && in_array($direction, ['asc', 'desc'])) {
            // Final fallback sort by any safe attribute
            $actions = $direction === 'asc'
                ? $actions->sortBy($sort)->values()
                : $actions->sortByDesc($sort)->values();
        } else {
            // Reset collection keys regardless
            $actions = $actions->values();
        }

        // === Return View ===
        return view('actions.index', compact('actions', 'xpNeeded', 'sort', 'direction'))->with('showAll', false);
    }
}
