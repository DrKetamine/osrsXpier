<?php

namespace App\Http\Controllers;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class PathController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $paths = Path::where("user_id", Auth::id())
            ->withCount("steps")
            ->get();
        $actions = Action::all();

        $sort = "";
        $direction = "asc";
        $showAll = false;

        return view(
            "actions.index",
            compact("paths", "actions", "sort", "direction", "showAll")
        );
    }

    public function create()
    {
        $actions = Action::all();
        return view("paths.create", compact("actions"));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "steps" => "required|array|min:1",
            "steps.*.action_id" => ["required", Rule::exists("actions", "id")],
            "steps.*.level_from" => "required|integer|min:1",
            "steps.*.level_to" => "required|integer|min:1",
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input("steps", []) as $i => $step) {
                if (isset($step["level_from"], $step["level_to"])) {
                    if ($step["level_to"] < $step["level_from"]) {
                        $validator
                            ->errors()
                            ->add(
                                "steps.$i.level_to",
                                "Level To must be greater than or equal to Level From."
                            );
                    }

                    $action = Action::find($step["action_id"]);
                    if ($action && $step["level_from"] < $action->level) {
                        $validator
                            ->errors()
                            ->add(
                                "steps.$i.level_from",
                                "Level From cannot be below the action's unlock level ({$action->level})."
                            );
                    }
                }
            }
        });

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(
                    ["errors" => $validator->errors()],
                    422
                );
            }
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        if (!$user || !$user->is_verified) {
            abort(403, "You must be a verified user to create a path.");
        }

        $validated = $validator->validated();

        $path = Path::create([
            "name" => $validated["name"],
            "user_id" => $user->id,
        ]);

        foreach ($validated["steps"] as $index => $step) {
            $step["step_order"] = $index + 1;
            $path->steps()->create($step);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Path created',
                'redirect_url' => route('paths.show', $path->id),
            ]);
        }

        return redirect()
            ->route('paths.show', $path->id)
            ->with('success', 'Path updated');
    }

    public function edit(Path $path)
    {
        $this->authorize("update", $path);
        $actions = Action::all();
        $path->load("steps");
        return view("paths.edit", compact("path", "actions"));
    }

    public function update(Request $request, Path $path)
    {
        $this->authorize("update", $path);

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "steps" => "required|array|min:1",
            "steps.*.action_id" => ["required", Rule::exists("actions", "id")],
            "steps.*.level_from" => "required|integer|min:1",
            "steps.*.level_to" =>
                "required|integer|min:1|gte:steps.*.level_from",
        ]);

        $path->update(["name" => $validated["name"]]);

        $path->steps()->delete(); // Remove old
        foreach ($validated["steps"] as $index => $step) {
            $step["step_order"] = $index + 1;
            $path->steps()->create($step);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Path updated',
                'redirect_url' => route('paths.show', $path->id),
            ]);
        }

        return redirect()
            ->route('paths.show', $path->id)
            ->with('success', 'Path updated');
    }

    public function show(Request $request, Path $path)
    {
        $path->load("steps.action.ingredients");

        // Input parsing
        $sort = $request->input("sort") ?? "";
        $direction = $request->input("direction") ?? "asc";
        $showAll = false;

        $currentLevel = $request->filled("current_level")
            ? (int) $request->input("current_level")
            : null;
        $goalLevel = $request->filled("goal_level")
            ? (int) $request->input("goal_level")
            : null;
        $currentXp = $request->filled("current_xp")
            ? (int) $request->input("current_xp")
            : null;
        $goalXp = $request->filled("goal_xp")
            ? (int) $request->input("goal_xp")
            : null;

        $xpNeeded = null;
        if ($currentLevel !== null && $goalLevel !== null) {
            $currentXp =
                \App\Models\Level::where("level", $currentLevel)->value("xp") ??
                0;
            $goalXp =
                \App\Models\Level::where("level", $goalLevel)->value("xp") ?? 0;
            $xpNeeded = max(0, $goalXp - $currentXp);
        } elseif ($currentXp !== null && $goalXp !== null) {
            $xpNeeded = max(0, $goalXp - $currentXp);
        }

        // Augment each action in the path
        $actions = $path->steps->map(function ($step) use ($xpNeeded) {
            $action = $step->action;
            $action->margin = $action->sell - $action->buy;
            $action->margin_percent =
                $action->buy > 0
                    ? (($action->sell - $action->buy) / $action->buy) * 100
                    : 0;
            $action->quantity_needed =
                $xpNeeded && $action->xp > 0
                    ? ceil($xpNeeded / $action->xp)
                    : null;
            $action->first_ingredient =
                optional($action->ingredients->first())->name ?? "";
            $action->step = $step;
            return $action;
        });

        // Sorting
        $validDbColumns = [
            "name",
            "level",
            "xp",
            "buy",
            "sell",
            "members_only",
        ];
        if ($sort === "quantity_needed") {
            $actions = $actions
                ->sortBy(
                    function ($a) {
                        return $a->quantity_needed ?? PHP_INT_MAX;
                    },
                    SORT_REGULAR,
                    $direction === "desc"
                )
                ->values();
        } elseif ($sort === "first_ingredient") {
            $actions = $actions
                ->sortBy(
                    function ($a) {
                        return $a->first_ingredient;
                    },
                    SORT_REGULAR,
                    $direction === "desc"
                )
                ->values();
        } elseif (in_array($sort, $validDbColumns)) {
            $actions =
                $direction === "asc"
                    ? $actions->sortBy($sort)->values()
                    : $actions->sortByDesc($sort)->values();
        } else {
            $actions = $actions->values();
        }

        return view(
            "paths.show",
            compact(
                "path",
                "actions",
                "sort",
                "direction",
                "xpNeeded",
                "showAll"
            )
        )->with([
            "showImage" => true,
            "showName" => true,
            "showLevel" => true,
            "showXp" => false,
            "showIngredients" => true,
            "showQuantity" => false,
            "showBuy" => false,
            "showSell" => false,
            "showMargin" => false,
            "showMarginPercent" => false,
            "showMembersOnly" => false,
        ]);
    }

    public function destroy(Path $path)
    {
        $this->authorize("delete", $path);
        $path->delete();
        return redirect()
            ->route("paths.index")
            ->with("success", "Path deleted");
    }
}