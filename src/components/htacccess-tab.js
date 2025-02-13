class HtaccessComponent extends HTMLElement {
	constructor() {
		super();
		this.attachShadow( { mode: 'open' } );
	}

	connectedCallback() {
		this.render();
	}

	render() {
		const style = document.createElement( 'style' );
		style.textContent = `
            .checkbox-item { margin-bottom: 10px; }
            .multiselect { display: none; margin-left: 20px; }
            .multiselect > div { margin-bottom: 10px; } /* Add margin between multiselects */
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
		this.shadowRoot.appendChild( style );

		const container = document.createElement( 'div' );
		this.shadowRoot.appendChild( container );

		// Save button
		const saveButton = document.createElement( 'button' );
		saveButton.textContent = 'Save';
		saveButton.addEventListener( 'click', this.handleSave.bind( this ) );
		this.shadowRoot.appendChild( saveButton );
	}

	setForm( jsonInput ) {
		const container = this.shadowRoot.querySelector( 'div' );
		container.innerHTML = ''; // Clear existing content

		jsonInput.forEach( ( item ) => {
			if ( item.type === 'checkbox' ) {
				const checkbox = this.addCheckbox(
					container,
					item.id,
					item.label
				);

				if ( item.options ) {
					const optionsDiv = this.createOptions( item.options );
					container.appendChild( optionsDiv );
					checkbox.addEventListener( 'change', ( e ) => {
						optionsDiv.style.display = e.target.checked
							? 'block'
							: 'none';
					} );
				}
			}
		} );
	}

	addCheckbox( container, id, labelText ) {
		const checkboxItem = document.createElement( 'div' );
		checkboxItem.classList.add( 'checkbox-item' );

		const checkbox = document.createElement( 'input' );
		checkbox.type = 'checkbox';
		checkbox.id = id;

		const label = document.createElement( 'label' );
		label.htmlFor = id;
		label.textContent = labelText;

		checkboxItem.appendChild( checkbox );
		checkboxItem.appendChild( label );
		container.appendChild( checkboxItem );

		return checkbox;
	}

	createOptions( options ) {
		const optionsDiv = document.createElement( 'div' );
		optionsDiv.classList.add( 'multiselect' );

		options.forEach( ( option ) => {
			// Each multiselect will be wrapped in its own div
			const selectWrapper = document.createElement( 'div' );

			const label = document.createElement( 'label' );
			label.textContent = option.label;
			selectWrapper.appendChild( label );

			const select = document.createElement( 'select' );
			select.multiple = true;

			option.choices.forEach( ( choice ) => {
				const optionElement = document.createElement( 'option' );
				optionElement.value = choice;
				optionElement.textContent = choice;
				select.appendChild( optionElement );
			} );

			selectWrapper.appendChild( select );
			optionsDiv.appendChild( selectWrapper );
		} );

		return optionsDiv;
	}

	handleSave() {
		const checkboxes = this.shadowRoot.querySelectorAll(
			'input[type="checkbox"]'
		);
		const result = Array.from( checkboxes ).map( ( checkbox ) => ( {
			id: checkbox.id,
			checked: checkbox.checked,
		} ) );

		const selects = this.shadowRoot.querySelectorAll( 'select' );
		const multiSelectResults = Array.from( selects ).map( ( select ) => ( {
			id: select.previousElementSibling.textContent,
			selected: Array.from( select.selectedOptions ).map(
				( option ) => option.value
			),
		} ) );

		console.log( 'Saved state:', [ ...result, ...multiSelectResults ] );
		// You can emit an event or perform any other action with the result
	}
}

customElements.define( 'htaccess-component', HtaccessComponent );

// Usage example:
const jsonInput = [
	{
		id: 'protectDebugLog',
		label: 'Protect Debug Log',
		type: 'checkbox',
	},
	{
		id: 'protectUpdateDir',
		label: 'Protect Update Directory',
		type: 'checkbox',
		options: [
			{
				label: 'Only execute the following files:',
				type: 'multiselect',
				choices: [ 'jpeg', 'webP', 'png', 'pdf', 'txt' ],
			},
			{
				label: 'Disable execution of scripts:',
				type: 'multiselect',
				choices: [ 'php', 'sh', 'py' ],
			},
		],
	},
	{
		id: 'protectXmlRpc',
		label: 'Protect XML-RPC',
		type: 'checkbox',
	},
	{
		id: 'protectRestEndpoint',
		label: 'Protect REST Endpoint',
		type: 'checkbox',
	},
];

// Dynamically build form
const htaccessComponent = document.querySelector( 'htaccess-component' );
htaccessComponent.setForm( jsonInput );
