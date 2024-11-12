export class WPSSPermissionsTable extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this._data = {};
    }

    static get observedAttributes() {
        return ['data'];
    }

    // Getter and setter for data
    get data() {
        return this._data;
    }

    set data(newData) {
        this._data = newData;
        this.render();
    }

    connectedCallback() {
        this.render();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'data' && oldValue !== newValue) {
            try {
                this.data = JSON.parse(newValue);
            } catch (e) {
                console.error('Invalid JSON data:', e);
            }
        }
    }

    getStyles() {
        return `
            <style>
            :host {
                display: block;
                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
            }
            .wp-list-table {
                border-spacing: 0;
                width: 100%;
                clear: both;
                margin: 0;
                border-collapse: collapse;
            }
            .wp-list-table thead th {
                padding: 8px 10px;
                border-bottom: 1px solid #e1e1e1;
                font-weight: 600;
                text-align: left;
                line-height: 1.3em;
                background: #f7f7f7;
            }
            .wp-list-table td {
                padding: 8px 10px;
                vertical-align: top;
                border-bottom: 1px solid #f1f1f1;
            }
            .wp-list-table tr:nth-child(odd) {
                background-color: #f9f9f9;
            }
            .status-ok { color: #46b450; }
            .status-warning { color: #ffb900; }
            .status-error { color: #dc3232; }
            .button {
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
                text-decoration: none;
                text-shadow: none;
                padding: 6px 12px;
                border-radius: 3px;
                border: 1px solid;
                cursor: pointer;
                margin-top: 10px;
                display: inline-block;
            }
            .button:hover {
                background: #135e96;
                border-color: #135e96;
            }
            </style>
            `;
    }

    formatStatus(value) {
        if (value === "N/A") return "N/A";
        if (value === null) return '<span class="status-warning">Unknown</span>';
        return value ?
            '<span class="status-ok">Yes</span>' :
            '<span class="status-error">No</span>';
    }

    getPermissionClass(current, recommended) {
        if (current === "N/A") return "";
        if (current === recommended) return "status-ok";
        if (current > recommended) return "status-error";
        return "status-warning";
    }

    async applyRecommendedPermissions() {
        let fsData = this.getUpdatedData(); 
        const path = '/wpss/v1/file-permissions';
        const httpMethod = 'POST';
        const action = null;
        this.makeApiCall(path, httpMethod,action,fsData);
    }

    async revertToOrignal() {
        let fsData = this.getUpdatedData(); 
        const path = '/wpss/v1/file-permissions';
        const httpMethod = 'PUT';
        const action = 'revert';
        this.makeApiCall(path, httpMethod,action,fsData);

    }

    // Add a loading state to the button
    setButtonLoading(loading, buttonId = 'recommendedBtn', buttonTxt = 'Apply Recommended Permissions') {
        const button = this.shadowRoot.getElementById(buttonId);
        if (loading) {
            button.textContent = 'Applying...';
            button.disabled = true;
        } else {
            button.textContent = buttonTxt;
            button.disabled = false;
        }
    }

    render() {
        const rows = Object.entries(this._data).map(([path, info]) => `
            <tr>
            <td>${path}</td>
            <td>${this.formatStatus(info.exists)}</td>
            <td>${this.formatStatus(info.writable)}</td>
            <td><span class="${this.getPermissionClass(info.permission, info.recommended)}">${info.permission}</span></td>
            <td>${info.recommended}</td>
            <td><span class="status-error">${info.error || ''}</span></td>
            </tr>
            `).join('');

        this.shadowRoot.innerHTML = `
        ${this.getStyles()}
            <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
            <th>File Path</th>
            <th>Exists</th>
            <th>Writable</th>
            <th>Permissions</th>
            <th>Recommended</th>
            <th>Comment/Error</th>
            </tr>
            </thead>
            <tbody>
            ${rows}
            </tbody>
            </table>
            <button class="button" id="recommendedBtn">Apply Recommended Permissions</button>
            <button class="button" id="revertBtn">Revert to Original</button>
            `;

        // Update the Recommended button click handler
        this.shadowRoot.getElementById('recommendedBtn')
            .addEventListener('click', async () => {
                this.setButtonLoading(true);
                await this.applyRecommendedPermissions();
                this.setButtonLoading(false);
            });

        // Update the Recommended button click handler
        this.shadowRoot.getElementById('revertBtn')
            .addEventListener('click', async () => {
                this.setButtonLoading(true, 'revertBtn'); 
                await this.revertToOrignal();
                this.setButtonLoading(false, 'revertBtn', "Revert To Original");
            });
    }

    async makeApiCall(path, httpMethod,action,data) {

        try {
            // Send POST request using wp.apiRequest
            const result = await wp.apiRequest({
                path: path,
                method: httpMethod,
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                data: {
                    nonce: wpApiSettings.nonce,
                    fsData:data, 
                    action:action 
                }


            });

            console.log("Path: " + path,"Method: " + httpMethod,"Action: "+action,result.data);

            this.attributeChangedCallback('data', data, result.data.fs_data);

            this.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: {
                    data: this.data,
                    status: 'success',
                    message: result.message || 'Permissions updated successfully'
                },
                bubbles: true,
                composed: true
            }));

        } catch (error) {
            console.error('Error updating permissions:', error);

            // Dispatch error event
            this.dispatchEvent(new CustomEvent('permissions-updated', {
                detail: {
                    error: error.message || 'Failed to update permissions',
                    status: 'error',
                    data: updatedData
                },
                bubbles: true,
                composed: true
            }));
        }
    }

    getUpdatedData(){
        const updatedData = { ...this._data };
        Object.keys(updatedData).forEach(path => {
            if (updatedData[path].permission !== "N/A") {
                updatedData[path].permission = updatedData[path].recommended;
            }
        });
        return updatedData;
    }

}


