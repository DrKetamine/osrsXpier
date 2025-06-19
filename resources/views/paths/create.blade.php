<head>
    <meta charset="UTF-8">
</head>
<div id="pathForm" class="formWrapper pathFormWrapper">
    <form method="POST" action="{{ route('paths.store') }}">
            @csrf
            <input type="hidden" name="return_to" id="returnToInput" value="">
            <button class="iconBtn closeFormBtn" type="button"><i class="bi bi-x-lg"></i></button>
            <h2>Path Creator</h2>
            <div class="inputsWrap">
                <div id="form-messages"></div>
                <div class="inputCG marg">
                    <label>Path Name</label>
                    <input type="text" name="name" required>
                </div>
                <div id="path-steps">
                    <div class="step">
                        <label>What We Cooking?</label>
                        <select class="sel1" name="steps[0][action_id]" required>
                            @foreach ($actions as $action)
                                <option value="{{ $action->id }}">{{ $action->name }}</option>
                            @endforeach
                        </select>
                        <div class="levelWrap">
                            <label>Cooking Levels</label>
                            <div class="pathLevels marg">
                                <input class="inpt1 marg" type="number" placeholder="From" name="steps[0][level_from]" min="1" required>
                                <input class="inpt1 marg" type="number" placeholder="To" name="steps[0][level_to]" min="1" required>
                            </div>
                        </div>
                        <button type="button" class="remove-step iconBtn" aria-label="Remove step"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
            </div>
        <button class="btn1" type="button" id="add-step">Add another step</button>
        <button class="btn1" type="submit">Save Path</button>
    </form>
</div>