document.addEventListener('DOMContentLoaded', () => {
    let stepIndex = window.initialStepIndex ?? 1;

    const firstSelect = document.querySelector('#path-steps .step select.sel1');
    const actionOptions = firstSelect ? firstSelect.innerHTML : '';

    document.getElementById('add-step').addEventListener('click', () => {
    const stepsDiv = document.getElementById('path-steps');

    const step = document.createElement('div');
    step.className = 'step';

    step.innerHTML = `
        <label>Whats Next?</label>
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
        const removeBtn = e.target.closest('.remove-step');
        if (removeBtn) {
            const step = removeBtn.closest('.step');
            if (step) {
                step.remove();
                stepIndex--;
            }
        }
    });

    document.querySelector('.pathForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const url = form.action;
        const formData = new FormData(form);
        const errorDiv = document.querySelector('#form-messages');
        if (errorDiv) errorDiv.innerHTML = '';
        // Send the form data using fetch with CSRF and AJAX headers
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData
        })
        // Handle the response
        .then(response => {
            // If validation failed, parse errors and display them
            if (!response.ok) {
                return response.json().then(data => {
                    if(data.errors) {
                        let html = '<ul style="color:red">';
                        for (const key in data.errors) {
                            data.errors[key].forEach(msg => {
                                html += `<li>${msg}</li>`;
                            });
                        }
                        html += '</ul>';
                        if(errorDiv) errorDiv.innerHTML = html;
                    }
                    throw new Error('Validation error');
                });
            }
            return response.json();
        })
        // Process the JSON response
        .then(data => {
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            }
        })
        .catch(() => {
        });
    });
});

