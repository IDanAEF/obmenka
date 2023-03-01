/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/es6/blocks/form.js":
/*!***********************************!*\
  !*** ./assets/es6/blocks/form.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const form = () => {
  try {
    function Calculate(Luhn) {
      let sum = 0;
      for (i = 0; i < Luhn.length; i++) {
        sum += +Luhn.substring(i, i + 1);
      }
      let delta = [0, 1, 2, 3, 4, -4, -3, -2, -1, 0];
      for (i = Luhn.length - 1; i >= 0; i -= 2) {
        let deltaIndex = +Luhn.substring(i, i + 1),
          deltaValue = delta[deltaIndex];
        sum += deltaValue;
      }
      let mod10 = sum % 10;
      mod10 = 10 - mod10;
      if (mod10 == 10) mod10 = 0;
      return mod10;
    }
    function Validate(Luhn) {
      Luhn = Luhn.replace(/\s/g, '');
      let LuhnDigit = +Luhn.substring(Luhn.length - 1, Luhn.length);
      let LuhnLess = Luhn.substring(0, Luhn.length - 1);
      if (Calculate(LuhnLess) == +LuhnDigit) return true;
      return false;
    }
    function validateCreditCard(value) {
      const result = Validate(value);
      return result;
    }
    function getCookie(name) {
      let json = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      if (!name) return undefined;
      let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\\/+^])/g, '\\$1') + "=([^;]*)"));
      if (matches) {
        let res = decodeURIComponent(matches[1]);
        if (json) {
          try {
            return JSON.parse(res);
          } catch (e) {}
        }
        return res;
      }
      return undefined;
    }
    function setCookie(name, value) {
      let options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {
        path: '/'
      };
      if (!name) return;
      options = options || {};
      if (options.expires instanceof Date) options.expires = options.expires.toUTCString();
      if (value instanceof Object) value = JSON.stringify(value);
      let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
      for (let optionKey in options) {
        updatedCookie += "; " + optionKey;
        let optionValue = options[optionKey];
        if (optionValue !== true) updatedCookie += "=" + optionValue;
      }
      document.cookie = updatedCookie;
    }
    function deleteCookie(name) {
      setCookie(name, null, {
        expires: new Date(),
        path: '/'
      });
    }
    function calcCurrency(count, from, to) {
      if (!count) count = 0;
      from = +from.replace(',', '.') * count;
      to = +to.replace(',', '.');
      return (from / to).toFixed(2);
    }
    async function getData(url) {
      let res = await fetch(url, {
        method: "GET"
      });
      return await res.text();
    }
    const formCont = document.querySelector('.change-form-cont');
    const rebuildForm = () => {
      formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
      getData(formCont.getAttribute('data-url') + '?action=form_steps').then(res => {
        formCont.innerHTML = res;
      });
    };
    rebuildForm();
    formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
    window.addEventListener('keyup', e => {
      if (e.target.getAttribute('name') == 'send-sum') {
        let sum = document.querySelector('input[name="get-sum"]');
        sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
      }
      if (e.target.getAttribute('name') == 'get-sum') {
        let sum = document.querySelector('input[name="send-sum"]');
        sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
      }
    });
  } catch (e) {
    console.log(e.stack);
  }
};
/* harmony default export */ __webpack_exports__["default"] = (form);

/***/ }),

/***/ "./assets/es6/blocks/mask.js":
/*!***********************************!*\
  !*** ./assets/es6/blocks/mask.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const mask = () => {
  let setCursorPosition = (pos, elem) => {
    elem.focus();
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      let range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  };
  function createMask(event) {
    const inp = event.target;
    let matrix = inp.getAttribute('data-mask'),
      i = 0,
      def = matrix.replace(/\D/g, ''),
      val = inp.value.replace(/\D/g, '');
    if (def.length >= val.length) {
      val = def;
    }
    inp.value = matrix.replace(/./g, function (a) {
      return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? '' : a;
    });
    if (event.type === 'blur') {
      if (inp.value.length == 2) inp.value = '';
    } else {
      setCursorPosition(inp.value.length, inp);
    }
  }
  function initMask(e) {
    if (e.target.getAttribute('data-mask') && (e.target.getAttribute('type') == 'tel' || e.target.getAttribute('type') == 'text' && e.target.classList.contains('card-validate'))) {
      createMask(e);
    }
  }
  window.addEventListener('input', initMask);
  window.addEventListener('focus', initMask);
  window.addEventListener('blur', initMask);
};
/* harmony default export */ __webpack_exports__["default"] = (mask);

/***/ }),

/***/ "./assets/es6/blocks/other.js":
/*!************************************!*\
  !*** ./assets/es6/blocks/other.js ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const other = () => {
  try {
    //hamburger
    const hamburger = document.querySelector('.header__hamburger'),
      headerMenu = document.querySelector('.header__nav');
    document.body.addEventListener('click', e => {
      if (e.target == hamburger) {
        hamburger.classList.toggle('active');
        headerMenu.classList.toggle('active');
      }
      if (e.target != hamburger && !e.target.closest('header__nav') && e.target != headerMenu) {
        hamburger.classList.remove('active');
        headerMenu.classList.remove('active');
      }
    });
  } catch (e) {
    console.log(e.stack);
  }
  try {
    //lists
    document.body.addEventListener('click', e => {
      if (!e.target.closest('.list_target') && !e.target.classList.contains('list_target')) {
        document.querySelectorAll('.list_target').forEach(list => list.classList.remove('active'));
      } else {
        const list = e.target.classList.contains('list_target') ? e.target : e.target.closest('.list_target');
        list.classList.toggle('active');
      }
      if (e.target.classList.contains('list_items-val')) {
        const list = e.target.closest('.list_target');
        if (list.classList.contains('input-change')) {
          list.querySelector('.list_input').value = '';
          list.querySelector('.list_input').placeholder = e.target.getAttribute('data-value').trim();
          list.querySelector('.list_input').type = e.target.getAttribute('data-type').trim();
        } else {
          list.querySelector('.list_input').value = e.target.getAttribute('data-value').trim();
          list.querySelector('.list_text').innerHTML = e.target.getAttribute('data-value').trim();
        }
        if (e.target.getAttribute('data-img')) list.querySelector('.list_img').src = e.target.getAttribute('data-img');
        list.querySelectorAll('.list_items-val').forEach(it => it.style.display = '');
        e.target.style.display = 'none';
      }
    });
  } catch (e) {
    console.log(e.stack);
  }
};
/* harmony default export */ __webpack_exports__["default"] = (other);

/***/ }),

/***/ "./assets/es6/blocks/scrolling.js":
/*!****************************************!*\
  !*** ./assets/es6/blocks/scrolling.js ***!
  \****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const scrolling = function () {
  let upSelector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  if (upSelector) {
    const upElem = document.querySelector(upSelector);
    window.addEventListener('scroll', () => {
      if (document.documentElement.scrollTop > 1650) {
        upElem.classList.add('animated', 'fadeIn');
        upElem.classList.remove('fadeOut');
      } else {
        upElem.classList.add('fadeOut');
        upElem.classList.remove('fadeIn');
      }
    });
  }
  let links = document.querySelectorAll('[href^="#"]'),
    speed = 0.3;
  links.forEach(link => {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      let widthTop = document.documentElement.scrollTop,
        hash = this.hash;
      if (document.querySelector(hash)) {
        let toBlock = document.querySelector(hash).getBoundingClientRect().top,
          start = null;
        requestAnimationFrame(step);
        function step(time) {
          if (start === null) {
            start = time;
          }
          let progress = time - start,
            r = toBlock < 0 ? Math.max(widthTop - progress / speed, widthTop + toBlock) : Math.min(widthTop + progress / speed, widthTop + toBlock);
          document.documentElement.scrollTo(0, r);
          if (r != widthTop + toBlock) {
            requestAnimationFrame(step);
          } else {
            location.hash = hash;
          }
        }
      } else if (event.target.getAttribute('data-url')) {
        window.location.href = event.target.getAttribute('data-url');
      }
    });
  });
};
/* harmony default export */ __webpack_exports__["default"] = (scrolling);

/***/ }),

/***/ "./assets/es6/blocks/slider.js":
/*!*************************************!*\
  !*** ./assets/es6/blocks/slider.js ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const slider = () => {
  try {
    const setSlider = function (items) {
      let points = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
      let i = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
      items.forEach(item => item.classList.remove('active'));
      items[i].classList.add('active');
      if (points) {
        points.forEach(point => point.classList.remove('active'));
        points[i].classList.add('active');
      }
    };

    //default slider
    const sliders = document.querySelectorAll('.slider-default');
    sliders.forEach(slider => {
      const sliderItems = slider.querySelectorAll('.slider-item'),
        sliderPoints = slider.querySelector('.slider-points');
      let points = [];
      sliderPoints && sliderItems.forEach(item => {
        const span = document.createElement('span');
        sliderPoints.append(span);
        points.push(span);
      });
      setSlider(sliderItems, points);
      let count = sliderItems.length - 1,
        j = 0;
      setInterval(() => {
        j == count ? j = 0 : j++;
        setSlider(sliderItems, points, j);
      }, 4000);
      points.forEach((point, i) => {
        point.addEventListener('click', () => {
          setSlider(sliderItems, points, i);
          j = i;
        });
      });
    });
  } catch (e) {
    console.log(e.stack);
  }
};
/* harmony default export */ __webpack_exports__["default"] = (slider);

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
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!****************************!*\
  !*** ./assets/es6/main.js ***!
  \****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _blocks_mask__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/mask */ "./assets/es6/blocks/mask.js");
/* harmony import */ var _blocks_scrolling__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./blocks/scrolling */ "./assets/es6/blocks/scrolling.js");
/* harmony import */ var _blocks_slider__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./blocks/slider */ "./assets/es6/blocks/slider.js");
/* harmony import */ var _blocks_form__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./blocks/form */ "./assets/es6/blocks/form.js");
/* harmony import */ var _blocks_other__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./blocks/other */ "./assets/es6/blocks/other.js");





'use strict';
window.addEventListener('DOMContentLoaded', () => {
  (0,_blocks_mask__WEBPACK_IMPORTED_MODULE_0__["default"])();
  (0,_blocks_scrolling__WEBPACK_IMPORTED_MODULE_1__["default"])();
  (0,_blocks_slider__WEBPACK_IMPORTED_MODULE_2__["default"])();
  (0,_blocks_form__WEBPACK_IMPORTED_MODULE_3__["default"])();
  (0,_blocks_other__WEBPACK_IMPORTED_MODULE_4__["default"])();
});
}();
/******/ })()
;
//# sourceMappingURL=script.js.map