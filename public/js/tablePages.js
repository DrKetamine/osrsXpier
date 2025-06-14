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

    const showFiltersBtn = document.getElementById('show-filters-btn');
    const formWrapper = document.querySelector('.formWrapper');
    const contentWrap = document.querySelector('.contentWrap');

    if (showFiltersBtn) {
        showFiltersBtn.addEventListener('click', () => {
            formWrapper.style.display = 'flex';
            contentWrap.style.filter = 'blur(4px)';
            showFiltersBtn.style.display = 'none';
        });
    }

    const closeFiltersBtn = document.getElementById('close-filters-btn');

    if (closeFiltersBtn) {
        closeFiltersBtn.addEventListener('click', () => {
            formWrapper.style.display = 'none';
            contentWrap.style.filter = 'none';
            if (showFiltersBtn) showFiltersBtn.style.display = 'inline-block';
        });
    }

    if (showFiltersBtn) {
        showFiltersBtn.addEventListener('click', () => {
            formWrapper.style.display = 'flex';
            document.body.classList.add('no-scroll');
        });
    }

    if (closeFiltersBtn) {
        closeFiltersBtn.addEventListener('click', () => {
            formWrapper.style.display = 'none';
            document.body.classList.remove('no-scroll');
        });
    }
});
