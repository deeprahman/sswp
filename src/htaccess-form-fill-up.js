
export function populateProtectionForm(response) {
    // Check if response is valid and has "Form Saved" message
    if (!response?.success || 
        !response?.data?.message?.message || 
        response.data.message.message !== "Form Saved") {
        console.error("Invalid response or message not saved");
        return;
    }

    // Get the form element
    const form = document.querySelector('.htaccess-form');
    if (!form) {
        console.error("Form with class 'htaccess-form' not found");
        return;
    }

    try {
        // Parse the data string if it's not already parsed
        let formData;
        try {
            formData = typeof response.data.message.data === 'string' 
                ? JSON.parse(response.data.message.data) 
                : response.data.message.data;
        } catch (e) {
            console.error('Error parsing form data:', e);
            return;
        }

        // Process each form field
        formData.forEach(item => {
            const { name, value } = item;
            
            // Handle checkbox fields
            if (name.startsWith('protect-')) {
                const checkbox = form.querySelector(`#${name}`);
                if (checkbox) {
                    // Check the checkbox if value is "on", uncheck if "off"
                    checkbox.checked = value === "on";
                }

                // Special handling for update-directory-options visibility
                if (name === 'protect-update-directory') {
                    const optionsDiv = form.querySelector('#update-directory-options');
                    if (optionsDiv) {
                        optionsDiv.style.display = value === "on" ? 'block' : 'none';
                    }
                }
            }
            
            // Handle multi-select for allowed files
            if (name === 'allowed_files') {
                const multiSelect = form.querySelector('#mySelect');
                if (multiSelect) {
                    // Clear all existing selections
                    Array.from(multiSelect.options).forEach(option => {
                        option.selected = false;
                    });

                    // Convert value to array if it's not already
                    const selectedValues = Array.isArray(value) ? value : [value];

                    // Select the specified options
                    selectedValues.forEach(fileType => {
                        const option = Array.from(multiSelect.options)
                            .find(opt => opt.value === fileType.toLowerCase());
                        if (option) {
                            option.selected = true;
                        }
                    });
                }
            }
        });
    } catch (error) {
        console.error('Error populating form:', error);
    }
}
