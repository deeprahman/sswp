class WPPermissionsTable extends HTMLElement {
  

    async applyRecommendedPermissions() {
        // First update local data as before
        const updatedData = {...this._data};
        Object.keys(updatedData).forEach(path => {
            if (updatedData[path].permission !== "N/A") {
                updatedData[path].permission = updatedData[path].recommended;
            }
        });

        try {
            // Send POST request
            const response = await fetch('/api/apply-permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // If using WordPress, you might need the nonce
                    'X-WP-Nonce': wpApiSettings.nonce
                },
                body: JSON.stringify(updatedData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            // Update the table with the response data (in case server made any modifications)
            this.data = result.data || updatedData;

            // Dispatch success event
            this.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: { 
                    data: this.data,
                    status: 'success',
                    message: 'Permissions updated successfully'
                },
                bubbles: true,
                composed: true
            }));

        } catch (error) {
            console.error('Error updating permissions:', error);
            
            // Dispatch error event
            this.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: { 
                    error: error.message,
                    status: 'error',
                    data: updatedData
                },
                bubbles: true,
                composed: true
            }));
        }
    }

    // Add a loading state to the button
    setButtonLoading(loading) {
        const button = this.shadowRoot.getElementById('recommendedBtn');
        if (loading) {
            button.textContent = 'Applying...';
            button.disabled = true;
        } else {
            button.textContent = 'Apply Recommended Permissions';
            button.disabled = false;
        }
    }

    // Modify the render method to include loading state
    render() {
        // ... existing render code ...

        // Update the button click handler
        this.shadowRoot.getElementById('recommendedBtn')
            .addEventListener('click', async () => {
                this.setButtonLoading(true);
                await this.applyRecommendedPermissions();
                this.setButtonLoading(false);
            });
    }
}