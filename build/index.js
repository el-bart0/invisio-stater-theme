/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/navigation.js"
/*!******************************!*\
  !*** ./src/js/navigation.js ***!
  \******************************/
() {

/**
 * Primary navigation behaviour: mobile toggle + keyboard support for submenus.
 *
 * Dependency-free. Enhances the markup emitted by wp_nav_menu() in header.php.
 */

const DESKTOP_QUERY = '(min-width: 48em)';

/**
 * Walk up from a focused/blurred link, toggling `.focus` on ancestor <li>s so
 * submenus stay open while a descendant has focus.
 *
 * @param {FocusEvent} event Focus or blur event.
 */
function handleSubmenuFocus(event) {
  let el = event.currentTarget;
  while (el && !el.classList.contains('main-navigation')) {
    if ('li' === el.tagName.toLowerCase()) {
      el.classList.toggle('focus');
    }
    el = el.parentElement;
  }
}

/**
 * Wire up the primary navigation.
 */
function initNavigation() {
  const nav = document.getElementById('site-navigation');
  if (!nav) {
    return;
  }
  const button = nav.querySelector('.menu-toggle');
  const menu = nav.querySelector('ul');
  if (!button || !menu) {
    if (button) {
      button.style.display = 'none';
    }
    return;
  }
  if (!menu.id) {
    menu.id = 'primary-menu';
  }
  const close = () => {
    if (!nav.classList.contains('is-toggled')) {
      return;
    }
    nav.classList.remove('is-toggled');
    button.setAttribute('aria-expanded', 'false');
  };
  button.setAttribute('aria-expanded', 'false');
  button.addEventListener('click', () => {
    const opened = nav.classList.toggle('is-toggled');
    button.setAttribute('aria-expanded', opened ? 'true' : 'false');
  });

  // Escape closes and returns focus to the toggle.
  nav.addEventListener('keydown', event => {
    if ('Escape' === event.key && nav.classList.contains('is-toggled')) {
      close();
      button.focus();
    }
  });

  // A click/tap outside the nav closes it.
  document.addEventListener('click', event => {
    if (!nav.contains(event.target)) {
      close();
    }
  });

  // Resizing up to the desktop layout resets the toggle state.
  window.matchMedia(DESKTOP_QUERY).addEventListener('change', event => {
    if (event.matches) {
      close();
    }
  });
  nav.querySelectorAll('.menu-item-has-children > a').forEach(link => {
    link.addEventListener('focus', handleSubmenuFocus);
    link.addEventListener('blur', handleSubmenuFocus);
  });
}
if ('loading' !== document.readyState) {
  initNavigation();
} else {
  document.addEventListener('DOMContentLoaded', initNavigation);
}

/***/ },

/***/ "./src/scss/main.scss"
/*!****************************!*\
  !*** ./src/scss/main.scss ***!
  \****************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	const __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		const cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		const module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			const e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = (module) => {
/******/ 		const getter = module && module.__esModule ?
/******/ 			() => (module['default']) :
/******/ 			() => (module);
/******/ 		__webpack_require__.d(getter, { a: getter });
/******/ 		return getter;
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	// define getter/value functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	__webpack_require__.o = (obj, prop) => (Object.hasOwn(obj, prop));
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ 	
/************************************************************************/
let __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*************************!*\
  !*** ./src/js/index.js ***!
  \*************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _scss_main_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../scss/main.scss */ "./src/scss/main.scss");
/* harmony import */ var _navigation_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./navigation.js */ "./src/js/navigation.js");
/* harmony import */ var _navigation_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_navigation_js__WEBPACK_IMPORTED_MODULE_1__);
/**
 * Invisio front-end entry point.
 *
 * Imports the main stylesheet (extracted to build/index.css) and front-end
 * behaviour modules.
 */


})();

/******/ })()
;
//# sourceMappingURL=index.js.map