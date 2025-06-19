
<x-header />
<head>
    <meta charset="UTF-8">
    <link href="{{ asset('css/tablePages.css') }}" rel="stylesheet">
</head>
<div class="staticWrap pathEdit">
    <form id="pathFormBig" method="POST" action="{{ route('paths.update', $path) }}" class="pathForm" data-path-id="{{ $path->id }}">
        @csrf
        @method('PUT')

        <h2>Path Editor</h2>
        <div class="inputsWrap" style="margin: 0;">
            <div id="form-messages"></div>

            <div class="singleWrap marg">
                <label>Path Name</label>
                <input class="pathName" type="text" name="name" value="{{ old('name', $path->name) }}" required>
            </div>

            <div id="path-steps">
                @foreach ($path->steps as $i => $step)
                    <div class="step">
                        <label>What We Cooking?</label>
                        <select class="sel1" name="steps[{{ $i }}][action_id]" required>
                            @foreach ($actions as $action)
                                <option value="{{ $action->id }}" @if ($step->action_id == $action->id) selected @endif>
                                    {{ $action->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="levelWrap">
                            <label>Cooking Levels</label>
                            <div class="pathLevels marg">
                                <input class="inpt1 marg" type="number" name="steps[{{ $i }}][level_from]" min="1" required value="{{ $step->level_from }}">
                                <input class="inpt1 marg" type="number" name="steps[{{ $i }}][level_to]" min="1" required value="{{ $step->level_to }}">
                            </div>
                        </div>
                        <button type="button" class="remove-step iconBtn" aria-label="Remove step"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="impButtonsFP">
            <button type="button" class="btn1" id="add-step">Add Step</button>
            <button type="submit" class="btn1">Update Path</button>
        </div>
    </form>
    <script src="{{ asset('js/pathFuncs.js') }}" defer></script>
    <script>
        window.initialStepIndex = {{ $path->steps->count() }};
    </script>
</div>