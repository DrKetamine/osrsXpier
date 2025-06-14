document.addEventListener('DOMContentLoaded', () => {

    const rows = document.querySelectorAll('table tr');

    rows.forEach(row => {
        const buyCell = row.querySelector('td[data-buy]');
        const sellCell = row.querySelector('td[data-sell]');
        const marginCell = row.querySelector('td.margin');
        const marginPercentCell = row.querySelector('td.margin-percent');

        if (!buyCell || !sellCell || !marginCell || !marginPercentCell) return;

        const buy = parseFloat(buyCell.dataset.buy) || 0;
        const sell = parseFloat(sellCell.dataset.sell) || 0;

        const margin = sell - buy;
        const marginPercent = buy !== 0 ? (margin / buy) * 100 : 0;

        marginCell.textContent = margin.toFixed(2);
        marginPercentCell.textContent = marginPercent.toFixed(2) + '%';
    });

    const btnLevel = document.getElementById('btn-level');
    const btnXp = document.getElementById('btn-xp');
    const levelInputs = document.getElementById('level-inputs');
    const xpInputs = document.getElementById('xp-inputs');
    const buttons = document.querySelectorAll('.mode-btn');

    function setMode(mode) {
        if(mode === 'level') {
            levelInputs.style.display = 'block';
            xpInputs.style.display = 'none';

            levelInputs.querySelectorAll('input').forEach(i => i.disabled = false);
            xpInputs.querySelectorAll('input').forEach(i => i.disabled = true);
        } else {
            levelInputs.style.display = 'none';
            xpInputs.style.display = 'block';

            levelInputs.querySelectorAll('input').forEach(i => i.disabled = true);
            xpInputs.querySelectorAll('input').forEach(i => i.disabled = false);
        }

        buttons.forEach(b => b.classList.remove('active'));
        if(mode === 'level') btnLevel.classList.add('active');
        else btnXp.classList.add('active');
    }

    btnLevel.addEventListener('click', () => setMode('level'));
    btnXp.addEventListener('click', () => setMode('xp'));

    // Initialize
    setMode('level');
    const table = document.querySelector('#actions-table');
    const toggleBtn = document.getElementById('toggle-rows-btn');

    const allRows = Array.from(table.querySelectorAll('tbody tr'));
    const totalRows = allRows.length;
    let visibleCount = 15;

    function updateVisibility() {
        allRows.forEach((row, index) => {
            row.style.display = index < visibleCount ? 'table-row' : 'none';
        });
    }

    // Update button text based on visibleCount
    function updateButtonText() {
        if (visibleCount < totalRows) {
            toggleBtn.textContent = 'Show more';
            toggleBtn.disabled = false;  // enable button
        } else {
            toggleBtn.textContent = 'No More Recipes';
            toggleBtn.disabled = true;   // disable button
        }
    }

    toggleBtn.addEventListener('click', () => {
        if (visibleCount < totalRows) {
            visibleCount = Math.min(visibleCount + 15, totalRows);
            updateVisibility();
            updateButtonText();
        }
        // When visibleCount == totalRows, clicking does nothing or you can disable button
    });

    // Set initial visibility and button text
    updateVisibility();
    updateButtonText();

    toggleBtn.addEventListener('click', () => {
        if (visibleCount < totalRows) {
            visibleCount = Math.min(visibleCount + 15, totalRows);
            updateVisibility();
            if (visibleCount === totalRows) {
                toggleBtn.textContent = 'Show less';
            }
        } else {
            visibleCount = 15;
            updateVisibility();
            toggleBtn.textContent = 'Show more';
        }
    });

    updateVisibility();

const contentWrap = document.querySelector('.contentWrap');
const toggleFormButtons = document.querySelectorAll('.toggleFormBtn');
const closeFormButtons = document.querySelectorAll('.closeFormBtn');

toggleFormButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const form = document.getElementById(targetId);
        if (form) {
            form.style.display = 'flex';
            contentWrap.style.filter = 'blur(4px)';
            btn.style.display = 'none';
        }
    });
});

closeFormButtons.forEach(btn => {
    btn.addEventListener('click', (event) => {
        const form = event.currentTarget.closest('.formWrapper');
        form.style.display = 'none';
        contentWrap.style.filter = 'none';

        const toggleBtn = document.querySelector(`.toggleFormBtn[data-target="${form.id}"]`);
        if (toggleBtn) toggleBtn.style.display = 'inline-block';
    });
});

// Scroll lock logic (applies to all forms)
document.querySelectorAll('.formWrapper').forEach(formWrapper => {
    const updateScrollState = () => {
        const isVisible = window.getComputedStyle(formWrapper).display !== 'none';
        document.body.classList.toggle('no-scroll', isVisible);
        if (contentWrap) {
            contentWrap.style.pointerEvents = isVisible ? 'none' : 'auto';
        }
    };

    updateScrollState();

    const observer = new MutationObserver(updateScrollState);
    observer.observe(formWrapper, { attributes: true, attributeFilter: ['style', 'class'] });
});

    //If show now is clicked remove requered 
    const showAllBtn = document.querySelector('button[name="show_all"]');
    const currentLevelInput = document.getElementById('current_level');
    const goalLevelInput = document.getElementById('goal_level');

    if (showAllBtn && goalLevelInput && currentLevelInput) {
        showAllBtn.addEventListener('click', () => {
            currentLevelInput.removeAttribute('required')
            goalLevelInput.removeAttribute('required');

        });
    }

    let stepIndex = 1;

    const firstSelect = document.querySelector('#path-steps .step select.sel1');
    const actionOptions = firstSelect ? firstSelect.innerHTML : '';

    document.getElementById('add-step').addEventListener('click', () => {
    const stepsDiv = document.getElementById('path-steps');

    const step = document.createElement('div');
    step.className = 'step';

    step.innerHTML = `
        <label>What We Cooking?</label>
        <select class="sel1" name="steps[${stepIndex}][action_id]" required>
        ${actionOptions}
        </select>
        <div class="levelWrap">
        <label>Cooking Levels</label>
        <div class="pathLevels marg">
            <input class="inpt1 marg" type="number" placeholder="From" name="steps[${stepIndex}][level_from]" min="1" required>
            <input class="inpt1 marg" type="number" placeholder="To" name="steps[${stepIndex}][level_to]" min="1" required>
        </div>
        </div>
        <button type="button" class="remove-step iconBtn" aria-label="Remove step"><i class="bi bi-x-lg"></i></button>
    `;

    stepsDiv.appendChild(step);
    stepIndex++;
    });

    document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-step')) {
        const step = e.target.closest('.step');
        if (step) step.remove();
    }
    });

    document.querySelector('#pathForm form').addEventListener('submit', function () {
        document.getElementById('returnToInput').value = window.location.href;
    });
});
