document.addEventListener('DOMContentLoaded', function() {
    const angpaoForm = document.querySelector('.angpao-card .input-form');
    const angpaoBtn = document.querySelector('button[name="add_angpao"]');
    
    // Check if limit is reached by looking for disabled classes/attributes
    if (angpaoForm && angpaoForm.classList.contains('disabled-form')) {
        const inputs = angpaoForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.disabled = true;
            input.placeholder = 'Limit reached (3/3)';
        });
    }
    
    if (angpaoBtn && angpaoBtn.disabled) {
        angpaoBtn.style.opacity = '0.5';
        angpaoBtn.style.cursor = 'not-allowed';
    }
});