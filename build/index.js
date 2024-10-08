/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/components/htacccess-tab.js":
/*!*****************************************!*\
  !*** ./src/components/htacccess-tab.js ***!
  \*****************************************/
/***/ (() => {

class HtaccessComponent extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({
      mode: 'open'
    });
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
const exampleJson = [{
  id: 'checkbox1',
  label: 'Option 1',
  checked: true
}, {
  id: 'checkbox2',
  label: 'Option 2'
}, {
  id: 'checkbox3',
  label: 'Option 3'
}];
myHtaccess.setCheckboxes(exampleJson);

/***/ }),

/***/ "./src/components/index.js":
/*!*********************************!*\
  !*** ./src/components/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HtaccessTab: () => (/* reexport safe */ _htacccess_tab_js__WEBPACK_IMPORTED_MODULE_1__.HtaccessTab),
/* harmony export */   SettingsPage: () => (/* reexport safe */ _settings_page__WEBPACK_IMPORTED_MODULE_0__.SettingsPage)
/* harmony export */ });
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings-page */ "./src/components/settings-page.js");
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_settings_page__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _htacccess_tab_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./htacccess-tab.js */ "./src/components/htacccess-tab.js");
/* harmony import */ var _htacccess_tab_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_htacccess_tab_js__WEBPACK_IMPORTED_MODULE_1__);



/***/ }),

/***/ "./src/components/settings-page.js":
/*!*****************************************!*\
  !*** ./src/components/settings-page.js ***!
  \*****************************************/
/***/ (() => {

class WPPermissionsTable extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({
      mode: 'open'
    });
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
    return value ? '<span class="status-ok">Yes</span>' : '<span class="status-error">No</span>';
  }
  getPermissionClass(current, recommended) {
    if (current === "N/A") return "";
    if (current === recommended) return "status-ok";
    if (current > recommended) return "status-error";
    return "status-warning";
  }
  applyRecommendedPermissions() {
    const updatedData = {
      ...this._data
    };
    Object.keys(updatedData).forEach(path => {
      if (updatedData[path].permission !== "N/A") {
        updatedData[path].permission = updatedData[path].recommended;
      }
    });
    this.data = updatedData;

    // Dispatch custom event
    this.dispatchEvent(new CustomEvent('permissions-updated', {
      detail: {
        data: this.data
      },
      bubbles: true,
      composed: true
    }));
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
        `;

    // Add event listener for the button
    this.shadowRoot.getElementById('recommendedBtn').addEventListener('click', () => this.applyRecommendedPermissions());
  }
}

// Register the web component
customElements.define('wp-permissions-table', WPPermissionsTable);

// Example usage
const permissionsTable = document.querySelector('wp-permissions-table');
permissionsTable.data = {
  "wp-config.php": {
    "exists": 1,
    "writable": 1,
    "permission": 666,
    "recommended": 644
  },
  "wp-login.php": {
    "exists": 1,
    "writable": null,
    "permission": 444,
    "recommended": 644,
    "error": "Path is outside WordPress installation"
  },
  "wp-content": {
    "exists": 1,
    "writable": 1,
    "permission": 777,
    "recommended": 755
  },
  "wp-content/uploads": {
    "exists": 1,
    "writable": 1,
    "permission": 777,
    "recommended": 755
  },
  "wp-content/plugins": {
    "exists": 1,
    "writable": 1,
    "permission": 777,
    "recommended": 755
  },
  "wp-content/themes": {
    "exists": 1,
    "writable": 1,
    "permission": 777,
    "recommended": 755
  },
  "wp-cat.php": {
    "exists": "N/A",
    "writable": "N/A",
    "permission": "N/A",
    "recommended": "N/A",
    "error": "Path is outside WordPress installation"
  }
};

// Example of listening for permission updates
permissionsTable.addEventListener('permissions-updated', e => {
  console.log('Permissions updated:', e.detail.data);
});

/***/ }),

/***/ "./src/index.scss":
/*!************************!*\
  !*** ./src/index.scss ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_index__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/index */ "./src/components/index.js");
/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.scss */ "./src/index.scss");


jQuery(document).ready(function ($) {
  console.log("Script Loaded");
  // Initialize tabs
  $("#my-tabs").tabs();

  // Handle form submissions
  $(".tab-form").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var formId = $form.attr("id");
    var formData = $form.serialize();
    wp.apiRequest({
      path: '/custom/v1/' + formId,
      method: 'POST',
      data: formData
    }).then(function (response) {
      alert('Form submitted successfully: ' + JSON.stringify(response));
    }, function (error) {
      alert('Error submitting form: ' + error.responseJSON.message);
    });
  });
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map