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
    function delScroll() {
      document.querySelector('body').classList.add('fixed');
      document.querySelector('html').classList.add('fixed');
    }
    function addScroll() {
      document.querySelector('body').classList.remove('fixed');
      document.querySelector('html').classList.remove('fixed');
    }
    function hideModals() {
      document.querySelector('.modal').classList.remove('active');
      document.querySelectorAll('.modal__item').forEach(item => item.classList.remove('active'));
    }
    function showModal(id) {
      const modalItem = document.querySelector(id);
      modalItem.classList.add('active');
      modalItem.parentElement.classList.add('active');
      delScroll();
    }
    function Calculate(Luhn) {
      let sum = 0;
      for (let i = 0; i < Luhn.length; i++) {
        sum += +Luhn.substring(i, i + 1);
      }
      let delta = [0, 1, 2, 3, 4, -4, -3, -2, -1, 0];
      for (let i = Luhn.length - 1; i >= 0; i -= 2) {
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
      return Validate(value) && value.replace(/\s/g, '').length >= 13;
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
    function currencySum(target1, target2_name) {
      let sum = document.querySelector(`input[name="${target2_name}"]`);
      sum.value = calcCurrency(target1.value, target1.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
      if (!target1.value) sum.value = '';
      let dataInp = target1.getAttribute('data-min') ? target1 : sum.getAttribute('data-min') ? sum : '',
        dataMin = dataInp ? +dataInp.getAttribute('data-min') : '';
      if (dataMin && +dataInp.value < dataMin) {
        dataInp.classList.add('invalid');
        dataInp.nextElementSibling.textContent = "Мин. сумма: " + dataMin;
      } else if (dataMin) {
        dataInp.classList.remove('invalid');
        dataInp.nextElementSibling.textContent = '';
      }
    }
    async function getData(url) {
      let res = await fetch(url, {
        method: "GET"
      });
      return await res.text();
    }
    function clearAllCookies() {
      deleteCookie('order-post-id');
      deleteCookie('step');
      deleteCookie('status');
      deleteCookie('get-curr');
      deleteCookie('send-curr');
      deleteCookie('get-bank');
      deleteCookie('send-bank');
    }
    const formCont = document.querySelector('.change-form-cont');
    let dataChange = [],
      loadingAnim = `
                <div class="main-loading loading-anim">
                    <img src="/wp-content/themes/obmenka/assets/images/load1.png" alt="">
                    <img src="/wp-content/themes/obmenka/assets/images/load2.png" alt="">
                    <img src="/wp-content/themes/obmenka/assets/images/load3.png" alt="">
                    <img src="/wp-content/themes/obmenka/assets/images/load4.png" alt="">
                </div>`;
    function rebuildForm() {
      let loader = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      hideModals();
      if (loader) formCont.innerHTML += loadingAnim;
      getData(formCont.getAttribute('data-url') + '?action=form_steps').then(res => {
        formCont.innerHTML = res;
        if (getCookie('step') == 2 && getCookie('status') == 'send-money') {
          timerOut();
        }
        if (getCookie('step') == 3) {
          setTimeout(() => {
            clearAllCookies();
          }, 2000);
        }
      });
    }
    function deleteOrder() {
      formCont.innerHTML += loadingAnim;
      getData(formCont.getAttribute('data-url') + '?action=delete_order&post_id=' + getCookie('order-post-id')).then(() => {
        clearAllCookies();
        rebuildForm(false);
      });
    }
    function checkStatus() {
      let checkStat = setInterval(() => {
        getData(formCont.getAttribute('data-url') + '?action=check_status&post_id=' + getCookie('order-post-id')).then(res => {
          res = JSON.parse(res);
          if (res) {
            setCookie('step', 3, {
              path: '/',
              expires: 2 * 60 * 60
            });
            setCookie('status', res, {
              path: '/',
              expires: 2 * 60 * 60
            });
            clearInterval(checkStat);
            rebuildForm();
          }
        });
      }, 10000);
    }
    function sendData() {
      let url = '?action=create_order';
      dataChange.forEach(dat => {
        url += '&' + dat['name'] + '=' + dat['value'];
      });
      formCont.innerHTML += loadingAnim;
      getData(formCont.getAttribute('data-url') + url).then(res => {
        setCookie('order-post-id', res, {
          path: '/',
          expires: 2 * 60 * 60
        });
        setCookie('step', 2, {
          path: '/',
          expires: 2 * 60 * 60
        });
        setCookie('status', 'send-money', {
          path: '/',
          expires: 2 * 60 * 60
        });
        rebuildForm(false);
      });
    }
    function isEmailValid(email) {
      const emailRegexp = new RegExp(/^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/i);
      return emailRegexp.test(email);
    }
    function timerOut() {
      const deadline = new Date(+document.querySelector('[data-date-out]').getAttribute('data-date-out') * 1000);
      const minutes = document.querySelector('#timer-minutes'),
        seconds = document.querySelector('#timer-seconds');
      let timerId = null;
      function countdownTimer() {
        const diff = deadline - new Date();
        if (diff <= 0) {
          clearInterval(timerId);
          deleteOrder();
        }
        let min = diff > 0 ? Math.floor(diff / 1000 / 60) % 60 : 0,
          sec = diff > 0 ? Math.floor(diff / 1000) % 60 : 0;
        minutes.textContent = min < 10 ? '0' + min : min;
        seconds.textContent = sec < 10 ? '0' + sec : sec;
      }
      countdownTimer();
      timerId = setInterval(countdownTimer, 1000);
    }
    rebuildForm();
    const checkLabel = document.querySelector('.modal__check label');
    checkLabel.addEventListener('click', e => {
      checkLabel.querySelector('.checkbox').classList.remove('invalid');
      checkLabel.classList.toggle('active');
    });
    if (getCookie('step') == 2 && getCookie('status') == 'get-money') {
      checkStatus();
    }
    if (getCookie('step') == 3) {
      setTimeout(() => {
        clearAllCookies();
      }, 2000);
    }
    window.addEventListener('keyup', e => {
      if (e.target.classList.contains('only-number')) {
        e.target.value = e.target.value.replace(/\D/g, '');
      }
      if (e.target.getAttribute('name') == 'send-sum') {
        currencySum(e.target, 'get-sum');
      }
      if (e.target.getAttribute('name') == 'get-sum') {
        currencySum(e.target, 'send-sum');
      }
      if (e.target.classList.contains('card-validate')) {
        if (!validateCreditCard(e.target.value)) {
          e.target.closest('.cards-item').querySelector('.cards-invalid').textContent = 'Проверьте номер карты';
          e.target.classList.add('invalid');
        } else {
          e.target.closest('.cards-item').querySelector('.cards-invalid').textContent = '';
          e.target.classList.remove('invalid');
        }
      }
    });
    document.body.addEventListener('click', e => {
      if (e.target.classList.contains('invalid') || e.target.closest('.invalid')) {
        let elem = e.target.classList.contains('invalid') ? e.target : e.target.closest('.invalid');
        if (!elem.classList.contains('not-click')) elem.classList.remove('invalid');
      }
      if (e.target.classList.contains('back')) {
        addScroll();
        hideModals();
      }
      if (e.target.classList.contains('list_items-val')) {
        let listTarget = e.target.closest('.list_target');
        if (listTarget.classList.contains('target-currs')) {
          setTimeout(() => {
            let currs = {
              'send-curr': document.querySelector('input[name="send-curr"]').value,
              'get-curr': document.querySelector('input[name="get-curr"]').value
            };
            if (currs["send-curr"] == currs["get-curr"] && listTarget.getAttribute('data-revert')) {
              currs[listTarget.getAttribute('data-revert')] = listTarget.getAttribute('data-old');
              let bankRevert = getCookie('send-bank');
              setCookie('send-bank', getCookie('get-bank'), {
                path: '/',
                expires: 2 * 60 * 60
              });
              setCookie('get-bank', bankRevert, {
                path: '/',
                expires: 2 * 60 * 60
              });
            } else {
              deleteCookie('get-bank');
              deleteCookie('send-bank');
            }
            setCookie('send-curr', currs["send-curr"], {
              path: '/',
              expires: 2 * 60 * 60
            });
            setCookie('get-curr', currs["get-curr"], {
              path: '/',
              expires: 2 * 60 * 60
            });
            rebuildForm();
          }, 500);
        }
        if (listTarget.classList.contains('target-banks')) {
          setTimeout(() => {
            setCookie('send-bank', document.querySelector('input[name="send-bank"]').value, {
              path: '/',
              expires: 2 * 60 * 60
            });
            setCookie('get-bank', document.querySelector('input[name="get-bank"]').value, {
              path: '/',
              expires: 2 * 60 * 60
            });
            rebuildForm();
          }, 500);
        }
      }
      if (e.target.classList.contains('delete-order')) {
        deleteOrder();
      }
      if (e.target.classList.contains('continue')) {
        let privacy = document.querySelector('#privacy');
        if (!privacy.checked) checkLabel.querySelector('.checkbox').classList.add('invalid');else {
          addScroll();
          hideModals();
          sendData();
        }
      }
      if (e.target.classList.contains('continue-pay')) {
        addScroll();
        hideModals();
        setCookie('status', 'get-money', {
          path: '/',
          expires: 2 * 60 * 60
        });
        checkStatus();
        rebuildForm();
      }
      if (e.target.classList.contains('pay-done')) {
        showModal('#pay-done');
      }
      if (e.target.classList.contains('window')) {
        showModal('#instruction');
      }
      if (e.target.classList.contains('main__form-change-button')) {
        const sendBank = formCont.querySelector('input[name="send-bank"]'),
          getBank = formCont.querySelector('input[name="get-bank"]'),
          sendSum = formCont.querySelector('input[name="send-sum"]'),
          getSum = formCont.querySelector('input[name="get-sum"]'),
          contacts = formCont.querySelector('input[name="contacts"]'),
          sendCard = formCont.querySelector('input[name="send-card"]'),
          getCard = formCont.querySelector('input[name="get-card"]'),
          sendCurr = formCont.querySelector('input[name="send-curr"]'),
          getCurr = formCont.querySelector('input[name="get-curr"]');
        let valid = true;
        let inputs = [sendSum, sendCard, getCard],
          fields = [sendBank, getBank, contacts];
        inputs.forEach(input => {
          if (!input.value || input.classList.contains('invalid')) {
            input.classList.add('invalid');
            valid = false;
          }
          ;
        });
        fields.forEach(field => {
          if (!field.value) {
            field.closest('.field').classList.add('invalid');
            valid = false;
          }
          ;
        });
        if (contacts.type == 'email' && !isEmailValid(contacts.value)) {
          contacts.closest('.field').classList.add('invalid');
          valid = false;
        }
        if (valid) {
          showModal('#how-work');
          dataChange = [];
          dataChange.push({
            name: 'send-bank',
            value: sendBank.value
          });
          dataChange.push({
            name: 'get-bank',
            value: getBank.value
          });
          dataChange.push({
            name: 'send-sum',
            value: sendSum.value
          });
          dataChange.push({
            name: 'get-sum',
            value: getSum.value
          });
          dataChange.push({
            name: 'contacts',
            value: contacts.value
          });
          dataChange.push({
            name: 'send-card',
            value: sendCard.value
          });
          dataChange.push({
            name: 'get-card',
            value: getCard.value
          });
          dataChange.push({
            name: 'send-curr',
            value: sendCurr.value
          });
          dataChange.push({
            name: 'get-curr',
            value: getCurr.value
          });
        }
      }
    });
  } catch (e) {
    console.log(e.stack);
  }
};
/* harmony default export */ __webpack_exports__["default"] = (form);

/***/ }),

/***/ "./assets/es6/blocks/lists.js":
/*!************************************!*\
  !*** ./assets/es6/blocks/lists.js ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
const lists = () => {
  try {
    document.body.addEventListener('click', e => {
      //values list
      if (!e.target.closest('.list_target') && !e.target.classList.contains('list_target')) {
        document.querySelectorAll('.list_target').forEach(list => list.classList.remove('active'));
      } else {
        const list = e.target.classList.contains('list_target') ? e.target : e.target.closest('.list_target');
        list.classList.toggle('active');
      }
      if (e.target.classList.contains('list_items-val')) {
        const list = e.target.closest('.list_target'),
          listInput = list.querySelector('.list_input');
        if (list.classList.contains('input-change')) {
          listInput.value = '';
          listInput.placeholder = e.target.getAttribute('data-value').trim();
          listInput.type = e.target.getAttribute('data-type').trim();
          e.target.getAttribute('data-mask') ? listInput.setAttribute('data-mask', e.target.getAttribute('data-mask').trim()) : '';
        } else {
          list.setAttribute('data-old', listInput.value);
          listInput.value = e.target.getAttribute('data-value').trim();
          list.querySelector('.list_text').innerHTML = e.target.getAttribute('data-value').trim();
        }
        if (e.target.getAttribute('data-img')) list.querySelector('.list_img').src = e.target.getAttribute('data-img');
        list.querySelectorAll('.list_items-val').forEach(it => it.style.display = '');
        e.target.style.display = 'none';
      }

      //open list
      if (e.target.classList.contains('list_open_btn')) {
        e.target.classList.toggle('active');
        e.target.nextElementSibling.classList.toggle('active');
      }
    });
  } catch (e) {
    console.log(e.stack);
  }
};
/* harmony default export */ __webpack_exports__["default"] = (lists);

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
  document.body.addEventListener('input', initMask);
  document.body.addEventListener('focus', initMask);
  document.body.addEventListener('blur', initMask);
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
    //col scroll
    const slideField = document.querySelector('.slide-field.on-scroll'),
      slideElem = slideField.querySelector('.slide-elem');
    let contPos;
    const setTranslate = () => {
      contPos = slideField.getBoundingClientRect().y + window.pageYOffset;
      if (window.screen.width >= 992 && window.pageYOffset >= contPos && window.pageYOffset + window.screen.height <= contPos + slideField.clientHeight) {
        slideElem.style.cssText = `transform: translateY(${window.pageYOffset - contPos}px)`;
      } else if (window.screen.width < 992) {
        slideElem.style.cssText = 'transform: translateY(0px)';
      }
    };
    setTranslate();
    slideField && window.addEventListener('scroll', setTranslate);
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
      }, 7000);
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
/* harmony import */ var _blocks_lists__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./blocks/lists */ "./assets/es6/blocks/lists.js");
/* harmony import */ var _blocks_other__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./blocks/other */ "./assets/es6/blocks/other.js");






'use strict';
window.addEventListener('DOMContentLoaded', () => {
  (0,_blocks_mask__WEBPACK_IMPORTED_MODULE_0__["default"])();
  (0,_blocks_scrolling__WEBPACK_IMPORTED_MODULE_1__["default"])();
  (0,_blocks_slider__WEBPACK_IMPORTED_MODULE_2__["default"])();
  (0,_blocks_form__WEBPACK_IMPORTED_MODULE_3__["default"])();
  (0,_blocks_lists__WEBPACK_IMPORTED_MODULE_4__["default"])();
  (0,_blocks_other__WEBPACK_IMPORTED_MODULE_5__["default"])();
});
}();
/******/ })()
;
//# sourceMappingURL=script.js.map