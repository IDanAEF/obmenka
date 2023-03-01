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
            document.querySelectorAll('.modal__item').forEach(item => item.classList.remove());
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
            return Validate(value);
        }
        function getCookie(name, json=false) {
            if (!name) return undefined;

            let matches = document.cookie.match(new RegExp(
              "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\\/+^])/g, '\\$1') + "=([^;]*)"
            ));

            if (matches) {
              let res = decodeURIComponent(matches[1]);
              if (json) {
                try {
                  return JSON.parse(res);
                }
                catch(e) {}
              }
              return res;
            }
          
            return undefined;
        }
        function setCookie(name, value, options = {path: '/'}) {
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
            from = +from.replace(',', '.')*count;
            to = +to.replace(',', '.');
            return (from/to).toFixed(2);
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

        let dataChange = [];

        function rebuildForm(loader = true) {
            hideModals();
            if (loader) formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
            getData(formCont.getAttribute('data-url')+'?action=form_steps')
            .then((res) => {
                formCont.innerHTML = res;
            });
        }

        // function setPay() {
        //     formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
        //     getData(formCont.getAttribute('data-url')+'?action=update_pay&post_id='+getCookie('order-post-id'))
        //     .then(() => {
        //         setCookie('status', 'get-money', {path: '/', expires: 24*60*60*30});
        //         rebuildForm(false);
        //     });
        // }

        function deleteOrder() {
            formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
            getData(formCont.getAttribute('data-url')+'?action=delete_order&post_id='+getCookie('order-post-id'))
            .then(() => {
                clearAllCookies();
                rebuildForm(false);
            });
        }

        function checkStatus() {
            setInterval(() => {
                getData(formCont.getAttribute('data-url')+'?action=check_status&post_id='+getCookie('order-post-id'))
                .then((res) => {
                    res = JSON.parse(res);
                    if (Array.isArray(res)) {
                        setCookie('step', 3, {path: '/', expires: 24*60*60*30});
                        setCookie('status', 'fail', {path: '/', expires: 24*60*60*30});
                        rebuildForm();
                    } else if (res) {
                        setCookie('step', 3, {path: '/', expires: 24*60*60*30});
                        setCookie('status', 'succes', {path: '/', expires: 24*60*60*30});
                        rebuildForm();
                    }
                });
            }, 10000);
        }

        function sendData() {
            let url = '?action=create_order';

            dataChange.forEach(dat => {
                url += '&'+dat['name']+'='+dat['value'];
            });

            formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
            getData(formCont.getAttribute('data-url')+url)
            .then((res) => {
                setCookie('order-post-id', res, {path: '/', expires: 24*60*60*30});
                setCookie('step', 2, {path: '/', expires: 24*60*60*30});
                rebuildForm(false);
            });
        }

        rebuildForm();
        formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';

        const checkLabel = document.querySelector('.modal__check label');

        checkLabel.addEventListener('click', (e) => {
            checkLabel.querySelector('.checkbox').classList.remove('invalid');
            checkLabel.classList.toggle('active');
        });

        if (getCookie('step') == 2 && getCookie('status') == 'get-money') {
            checkStatus();
        }

        if (getCookie('step') == 3) {
            setTimeout(() => {
                dclearAllCookies();
            }, 5000);
        }

        window.addEventListener('keyup', (e) => {
            if (e.target.getAttribute('name') == 'send-sum') {
                let sum = document.querySelector('input[name="get-sum"]');
                sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
            }
            if (e.target.getAttribute('name') == 'get-sum') {
                let sum = document.querySelector('input[name="send-sum"]');
                sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
            }
            if (e.target.classList.contains('card-validate')) {
                if (!validateCreditCard(e.target.value)) {
                    e.target.closest('.cards-item').querySelector('.cards-invalid').textContent = 'Проверьте номер карты';
                    e.target.classList.add('invalid');
                }
                else {
                    e.target.closest('.cards-item').querySelector('.cards-invalid').textContent = '';
                    e.target.classList.remove('invalid');    
                }
            }
        });

        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('invalid') || e.target.closest('.invalid')) {
                let elem = e.target.classList.contains('invalid') ? e.target : e.target.closest('.invalid');
                elem.classList.remove('invalid');
            }
            if (e.target.classList.contains('back')) {
                addScroll();
                hideModals();
            }
            if (e.target.classList.contains('list_items-val')) {
                if (e.target.closest('.list_target').classList.contains('target-currs')) {
                    setTimeout(() => {
                        setCookie('send-curr', document.querySelector('input[name="send-curr"]').value, {path: '/', expires: 24*60*60*30});
                        setCookie('get-curr', document.querySelector('input[name="get-curr"]').value, {path: '/', expires: 24*60*60*30});
                        deleteCookie('get-bank');
                        deleteCookie('send-bank');
                        rebuildForm();
                    }, 500);
                }
                if (e.target.closest('.list_target').classList.contains('target-banks')) {
                    setTimeout(() => {
                        setCookie('send-bank', document.querySelector('input[name="send-bank"]').value, {path: '/', expires: 24*60*60*30});
                        setCookie('get-bank', document.querySelector('input[name="get-bank"]').value, {path: '/', expires: 24*60*60*30});
                        rebuildForm();
                    }, 500);
                }
            }
            if (e.target.classList.contains('delete-order')) {
                deleteOrder();
            }
            if (e.target.classList.contains('continue')) {
                let privacy = document.querySelector('#privacy');
                if (!privacy.checked) checkLabel.querySelector('.checkbox').classList.add('invalid');
                else {
                    addScroll();
                    hideModals();
                    sendData();
                }
            }
            if (e.target.classList.contains('continue-pay')) {
                addScroll();
                hideModals();
                setCookie('status', 'get-money', {path: '/', expires: 24*60*60*30});
                checkStatus();
                rebuildForm();
            }
            if (e.target.classList.contains('pay-done')) {
                showModal('#pay-done');
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

                let inputs = [sendSum, getSum, sendCard, getCard],
                    fields = [sendBank, getBank, contacts];

                inputs.forEach(input => {
                    if (!input.value || input.classList.contains('invalid')) {
                        input.classList.add('invalid');
                        valid = false;
                    };
                });
                fields.forEach(field => {
                    if (!field.value) {
                        field.closest('.field').classList.add('invalid');
                        valid = false;
                    };
                });

                if (valid) {
                    showModal('#how-work');

                    dataChange = [];
                    dataChange.push({name: 'send-bank', value: sendBank.value});
                    dataChange.push({name: 'get-bank', value: getBank.value});
                    dataChange.push({name: 'send-sum', value: sendSum.value});
                    dataChange.push({name: 'get-sum', value: getSum.value});
                    dataChange.push({name: 'contacts', value: contacts.value});
                    dataChange.push({name: 'send-card', value: sendCard.value});
                    dataChange.push({name: 'get-card', value: getCard.value});
                    dataChange.push({name: 'send-curr', value: sendCurr.value});
                    dataChange.push({name: 'get-curr', value: getCurr.value});
                }
            }
        });
    } catch (e) {
        console.log(e.stack);
    }
}

export default form;