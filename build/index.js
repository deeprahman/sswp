/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/components/index.js":
/*!*********************************!*\
  !*** ./src/components/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsPage: () => (/* reexport safe */ _settings_page__WEBPACK_IMPORTED_MODULE_0__.SettingsPage)
/* harmony export */ });
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./settings-page */ "./src/components/settings-page.js");
/* harmony import */ var _settings_page__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_settings_page__WEBPACK_IMPORTED_MODULE_0__);


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

/***/ "./src/wpss-files-permissions-request.js":
/*!***********************************************!*\
  !*** ./src/wpss-files-permissions-request.js ***!
  \***********************************************/
/***/ (() => {

function getFilePermissionsWP() {
  return wp.apiRequest({
    path: '/wpss/v1/file-permissions',
    method: 'GET',
    data: {
      nonce: wpApiSettings.nonce
    },
    headers: {
      'X-WP-Nonce': wpApiSettings.nonce
    }
  }).then(function (response) {
    console.log('File Permissions:', response);
    return response;
  }).catch(function (error) {
    console.error('Error fetching file permissions:', error);
    throw error;
  });
}

// Using wp.apiRequest
// getFilePermissionsWP()
//     .then(permissions => {
//         // Handle the permissions data
//         console.log(permissions);
//     })
//     .catch(error => {
//         // Handle any errors
//         console.log("REST REQ Err");
//         console.log(error.responseText);
//     });

/***/ }),

/***/ "./src/wpss-htaccess-protect-from.js":
/*!*******************************************!*\
  !*** ./src/wpss-htaccess-protect-from.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wpss_htaccess_protect_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./wpss-htaccess-protect.js */ "./src/wpss-htaccess-protect.js");

jQuery(document).ready(function ($) {
  $('.htaccess-form').on('submit', function (e) {
    e.preventDefault();

    // Custom serialization
    var serializedData = [];

    // Process checkboxes
    $(this).find('input[type="checkbox"]').each(function () {
      serializedData.push({
        name: $(this).attr('name'),
        value: $(this).prop('checked') ? "on" : "off"
      });
    });

    // Process multi-select
    $(this).find('select[multiple]').each(function () {
      serializedData.push({
        name: $(this).attr('name'),
        value: $(this).val() || [] // If nothing selected, use empty array
      });
    });

    // Process other inputs (text, radio, single select, etc.)
    $(this).find('input:not([type="checkbox"]), select:not([multiple]), textarea').each(function () {
      if ($(this).val()) {
        serializedData.push({
          name: $(this).attr('name'),
          value: $(this).val()
        });
      }
    });

    // Remove duplicates (keeping the last occurrence)
    var uniqueSerializedData = [];
    var seenKeys = {};
    for (var i = serializedData.length - 1; i >= 0; i--) {
      var item = serializedData[i];
      if (!seenKeys[item.name]) {
        seenKeys[item.name] = true;
        uniqueSerializedData.unshift(item);
      }
    }

    // Call the sendData function with the serialized data
    sendData(uniqueSerializedData);
  });

  // Toggle visibility of update directory options
  $('#protect-update-directory').on('change', function () {
    $('#update-directory-options').toggle(this.checked);
  });
});

// Placeholder for the sendData function
function sendData(data) {
  console.log('Sending data:', data);
  // Implement your data sending logic here
  (0,_wpss_htaccess_protect_js__WEBPACK_IMPORTED_MODULE_0__.checkHtaccessProtection)(data);
}

/***/ }),

/***/ "./src/wpss-htaccess-protect.js":
/*!**************************************!*\
  !*** ./src/wpss-htaccess-protect.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   checkHtaccessProtection: () => (/* binding */ checkHtaccessProtection)
/* harmony export */ });
/**
 * Function to get the htaccess protection status using WordPress REST API
 * @returns {Promise} A promise that resolves with the API response
 */
function getHtaccessProtected(data) {
  return wp.apiRequest({
    path: '/wpss/v1/htaccess-protect',
    method: 'POST',
    data: {
      nonce: wpApiSettings.nonce,
      from: data
    },
    headers: {
      'X-WP-Nonce': wpApiSettings.nonce
    }
  }).then(function (response) {
    console.log('HTACCESS Protection Status:', response);
    return response;
  }).catch(function (error) {
    console.error('Error getting HTACCESS protection status:', error);
    throw error;
  });
}

// Example usage
function checkHtaccessProtection(data) {
  getHtaccessProtected(data).then(response => {
    if (response.success) {
      console.log("Htform success response: ", response.data.message.data);
      // Update UI here, for example:
      // document.getElementById('protectionStatus').textContent = response.data.is_debug_protected;
    } else {
      console.error('Failed to get protection status');
    }
  }).catch(error => {
    console.error('REST API request error:', error);
    if (error.responseText) {
      console.error('Error details:', error.responseText);
    }
    // Update UI to show error here
  });
}

// Attach to a button click event (if applicable)
// document.getElementById('checkProtectionButton')?.addEventListener('click', checkHtaccessProtection);

// Or call immediately if needed

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
/* harmony import */ var _wpss_files_permissions_request_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./wpss-files-permissions-request.js */ "./src/wpss-files-permissions-request.js");
/* harmony import */ var _wpss_files_permissions_request_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wpss_files_permissions_request_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wpss_htaccess_protect_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./wpss-htaccess-protect.js */ "./src/wpss-htaccess-protect.js");
/* harmony import */ var _wpss_htaccess_protect_from_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./wpss-htaccess-protect-from.js */ "./src/wpss-htaccess-protect-from.js");





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

    // wp.apiRequest({
    //     path: '/custom/v1/' + formId,
    //     method: 'POST',
    //     data: formData
    // }).then(function(response) {
    //     alert('Form submitted successfully: ' + JSON.stringify(response));
    // }, function(error) {
    //     alert('Error submitting form: ' + error.responseJSON.message);
    // });
  });
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map