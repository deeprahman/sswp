export function handleFormData(formData) {
    // Reset all checkboxes first
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reset multiple select
    const multiSelect = document.getElementById('mySelect');
    if (multiSelect) {
        Array.from(multiSelect.options).forEach(option => {
            option.selected = false;
        });
    }

    formData.forEach(item => {
        // Skip processing for protect-update-directory checkbox directly
        if (item.name === 'protect-update-directory') {
            return;
        }

        // Handle allowed_files specially
        if (item.name === 'allowed_files') {
            const updateDirCheckbox = document.getElementById('protect-update-directory');
            const updateDirOptions = document.getElementById('update-directory-options');
            
            // If value property doesn't exist or is an empty array
            if (!item.value || (Array.isArray(item.value) && item.value.length === 0)) {
                updateDirCheckbox.checked = false;
                updateDirOptions.style.display = 'none';
            } else {
                // If value exists and is not empty
                updateDirCheckbox.checked = true;
                updateDirOptions.style.display = 'block';

                // Select the specified files in the multiple select
                if (Array.isArray(item.value)) {
                    item.value.forEach(fileType => {
                        const option = Array.from(multiSelect.options)
                            .find(opt => opt.value === fileType);
                        if (option) {
                            option.selected = true;
                        }
                    });
                }
            }
            return;
        }

        // Handle all other checkboxes
        const checkbox = document.getElementById(item.name);
        if (checkbox && item.value === 'on') {
            checkbox.checked = true;
        }
    });

    // Add event listener for protect-update-directory checkbox
    const updateDirCheckbox = document.getElementById('protect-update-directory');
    const updateDirOptions = document.getElementById('update-directory-options');
    
    updateDirCheckbox.addEventListener('change', function() {
        updateDirOptions.style.display = this.checked ? 'block' : 'none';
    });
}