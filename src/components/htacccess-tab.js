class HtaccessComponent extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.render();
    }

    render() {
        const style = document.createElement('style');
        style.textContent = `
            .checkbox-item { margin-bottom: 10px; }
            button {
                background-color: #4CAF50;
                border: none;
                color: white;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin-top: 10px;
                cursor: pointer;
                border-radius: 5px;
            }
        `;

        this.shadowRoot.innerHTML = '';
        this.shadowRoot.appendChild(style);

        const container = document.createElement('div');
        this.shadowRoot.appendChild(container);

        const saveButton = document.createElement('button');
        saveButton.textContent = 'Save';
        saveButton.addEventListener('click', this.handleSave.bind(this));
        this.shadowRoot.appendChild(saveButton);
    }

    setCheckboxes(jsonInput) {
        const container = this.shadowRoot.querySelector('div');
        container.innerHTML = '';

        jsonInput.forEach(item => {
            const checkboxItem = document.createElement('div');
            checkboxItem.classList.add('checkbox-item');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = item.id;
            checkbox.checked = item.checked || false;

            const label = document.createElement('label');
            label.htmlFor = item.id;
            label.textContent = item.label;

            checkboxItem.appendChild(checkbox);
            checkboxItem.appendChild(label);
            container.appendChild(checkboxItem);
        });
    }

    handleSave() {
        const checkboxes = this.shadowRoot.querySelectorAll('input[type="checkbox"]');
        const result = Array.from(checkboxes).map(checkbox => ({
            id: checkbox.id,
            checked: checkbox.checked
        }));
        console.log('Saved state:', result);
        // You can emit an event or perform any other action with the result
    }
}

customElements.define('htaccess-component', HtaccessComponent);

// Example usage
const myHtaccess = document.getElementById('myHtaccess');
const exampleJson = [
    { id: 'checkbox1', label: 'Option 1', checked: true },
    { id: 'checkbox2', label: 'Option 2' },
    { id: 'checkbox3', label: 'Option 3' }
];
myHtaccess.setCheckboxes(exampleJson);