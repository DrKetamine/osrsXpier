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
    const modeInput = document.getElementById('mode');
    function setMode(mode) {
        if (mode === 'level') {
            levelInputs.style.display = 'flex';
            xpInputs.style.display = 'none';
            levelInputs.querySelectorAll('input').forEach(i => i.disabled = false);
            xpInputs.querySelectorAll('input').forEach(i => i.disabled = true);
            modeInput.value = 'level';
            btnLevel.classList.add('active');
            btnXp.classList.remove('active');
        } else {
            levelInputs.style.display = 'none';
            xpInputs.style.display = 'flex';
            levelInputs.querySelectorAll('input').forEach(i => i.disabled = true);
            xpInputs.querySelectorAll('input').forEach(i => i.disabled = false);
            modeInput.value = 'xp';
            btnLevel.classList.remove('active');
            btnXp.classList.add('active');
        }
    }

    btnLevel.addEventListener('click', () => setMode('level'));
    btnXp.addEventListener('click', () => setMode('xp'));

    // Load mode from hidden input
    setMode(modeInput.value === 'xp' ? 'xp' : 'level');
    
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
            toggleBtn.disabled = false;
        } else {
            toggleBtn.textContent = 'No More Recipes';
            toggleBtn.disabled = true;
        }
    }

    toggleBtn.addEventListener('click', () => {
        if (visibleCount < totalRows) {
            visibleCount = Math.min(visibleCount + 15, totalRows);
            updateVisibility();
            updateButtonText();
        }
    });

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

    // Scroll lock logic
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

    // Resets all filter inputs except current/goal level, then submits the form
    const resetBtn = document.querySelector('button[name="reset_filters"]');
    const filterForm = document.getElementById('filters');
    if (resetBtn && filterForm) {
        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            const currentLevel = document.getElementById('current_level').value;
            const goalLevel = document.getElementById('goal_level').value;
            Array.from(filterForm.elements).forEach(el => {
                if (el.name !== 'current_level' && el.name !== 'goal_level') {
                    if (el.type === 'checkbox') {
                        if (el.name.startsWith('show_')) {
                            el.checked = true;
                        } else {
                            el.checked = false;
                        }
                    } else if (el.type !== 'hidden') {
                        el.value = '';
                    }
                }
            });

            document.getElementById('current_level').value = currentLevel;
            document.getElementById('goal_level').value = goalLevel;
            filterForm.submit();
        });
    }

    function mergeAndRedirect(triggerFormId) {
        const triggerForm = document.getElementById(triggerFormId);
        const otherForm = document.getElementById(triggerFormId === 'lvlxp' ? 'filters' : 'lvlxp');

        triggerForm.addEventListener('submit', e => {
            e.preventDefault();

            const formData = new FormData(triggerForm);
            const otherFormData = new FormData(otherForm);
            const urlParams = new URLSearchParams(window.location.search);

            // Detect mode and remove opposite params
            const isLevelMode = document.getElementById('btn-level').classList.contains('active');
            const isXpMode = document.getElementById('btn-xp').classList.contains('active');

            if (isLevelMode) {
                urlParams.delete('current_xp');
                urlParams.delete('goal_xp');
            } else if (isXpMode) {
                urlParams.delete('current_level');
                urlParams.delete('goal_level');
            }

            // Merge current form fields
            for (const [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    urlParams.set(key, value);
                } else {
                    urlParams.delete(key);
                }
            }

            urlParams.set('apply_filters', '1');

            // Merge other form fields (preserve if not already set)
            for (const [key, value] of otherFormData.entries()) {
                if (!urlParams.has(key) && value.trim() !== '') {
                    const el = otherForm.querySelector(`[name="${key}"]`);

                    if (!el) continue;

                    if (el.type === 'checkbox') {
                        if (el.checked) {
                            urlParams.set(key, el.value || 'on');
                        }
                    } else if (el.type === 'radio') {
                        if (el.checked) {
                            urlParams.set(key, value);
                        }
                    } else {
                        if (value !== 'off') {
                            urlParams.set(key, value);
                        }
                    }
                }
            }

            window.location.href = window.location.pathname + '?' + urlParams.toString();
        });
    }

    mergeAndRedirect('lvlxp');
    mergeAndRedirect('filters');

    document.getElementById('saveFilterBtn').addEventListener('click', function () {
        const params = window.location.search.slice(1);

        if (!params) {
            alert("No filters found in URL");
            return;
        }

        const name = prompt("Enter a name for this filter:");
        if (!name) {
            alert("Filter name is required");
            return;
        }
        const description = prompt("Enter a description for this filter (optional):") || '';

        fetch('/filters/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: name,
                description: description,
                params: params
            })
        })
        .then(res => {
            if (!res.ok) throw res;
            return res.json();
        })
        .then(data => {
            if (data.success) {
                alert('Filter saved');
            }
        })
        .catch(async err => {
            const text = await err.text();
            console.error('Request failed:', text);
        });
    });
});
