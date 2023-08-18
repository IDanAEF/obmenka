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
        //Валидация банковских карт
        function validateCreditCard(value) {
            return Validate(value) && value.replace(/\s/g, '').length >= 13;
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
        function setCookie(name, value, hours) {
            var expires;
            if (hours || hours === 0) {
                var date = new Date();
                date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            else {
                expires = "";
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        }
        function deleteCookie(name) {
            setCookie(name, null, 0);
        }
        //Перевод в другую валюту на основе рубля
        // function calcCurrency(count, from, to) {
        //     if (!count) count = 0;
        //     from = +from.replace(',', '.')*count;
        //     to = +to.replace(',', '.');
        //     return (from/to).toFixed(2);
        // }
        //Автоматическая подстановка суммы по валютам
        function currencySum(target1, target2_name) {
            let sum = document.querySelector(`input[name="${target2_name}"]`);
                sum.value = (target1.getAttribute('data-multi') ? target1.value * +target1.getAttribute('data-multi').replace(',', '.') : target1.value / +target1.getAttribute('data-decr').replace(',', '.')).toFixed(2);

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
        async function postData(url, data) {
            let res = await fetch(url, {
                method: "POST",
                body: data
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

        function openModalReview() {
            document.querySelector('.modal-review').classList.add('active');
            document.querySelector('.modal__review').classList.add('active');
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

        //Обновить форму
        function rebuildForm(loader = true, circleTimerStart = false) {
            hideModals();
            if (loader) formCont.innerHTML += loadingAnim;
            getData(formCont.getAttribute('data-url')+'?action=form_steps')
            .then((res) => {
                formCont.innerHTML = res;
                if (getCookie('step') == 2 && getCookie('status') == 'send-money') {
                    timerOut();
                }
                if (getCookie('step') == 3) {
                    setTimeout(() => {
                        if (getCookie('status') == 'succes') {
                            openModalReview();
                        }
                        clearAllCookies();
                    }, 2000);
                }
                if (circleTimerStart) circleTimer();
            });
        }

        //Удаление заявки
        function deleteOrder() {
            formCont.innerHTML += loadingAnim;
            getData(formCont.getAttribute('data-url')+'?action=delete_order&post_id='+getCookie('order-post-id'))
                .then(() => {
                    clearAllCookies();
                    rebuildForm(false);
                });
        }

        //Проверка статуса заявки (На 3-ем шаге)
        function checkStatus() {
            let checkStat = setInterval(() => {
                getData(formCont.getAttribute('data-url')+'?action=check_status&post_id='+getCookie('order-post-id'))
                    .then((res) => {
                        res = JSON.parse(res);
                        if (res) {
                            setCookie('step', 3, 4);
                            setCookie('status', res, 4);
                            clearInterval(checkStat);
                            rebuildForm();
                        }
                    });
            }, 10000);
        }

        //Отправление данных для создания заявки
        function sendData() {
            let url = '?action=create_order';

            dataChange.forEach(dat => {
                url += '&'+dat['name']+'='+dat['value'];
            });

            formCont.innerHTML += loadingAnim;
            getData(formCont.getAttribute('data-url')+url)
            .then((res) => {
                setCookie('order-post-id', res, 4);
                setCookie('step', 2, 4);
                setCookie('status', 'check-info', 4);
                rebuildForm(false, true);
            });
        }

        function isEmailValid(email) {
            const emailRegexp = new RegExp(
                /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/i
            );

            return emailRegexp.test(email);
        }

        //Таймер "Окно оплаты" на 2-ом шаге
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

        //Таймер обратного отсчёта после создания заявки
        function circleTimer() {
            let totalTime = 1000 * 90,
                timerTime = 1000 * +document.querySelector('.timer-container').getAttribute('data-time-count'),
                countDownInterval;

            const ringSvg = document.querySelector('.timer-container .progress-ring .progress-ring-circle'),
                  circleSeconds = document.querySelector('#timer-circle-seconds');

            const countDown = () => {
                countDownInterval = setInterval(() => {
                    timerTime = timerTime - 1000;
                    updateTime(timerTime);

                    if (timerTime <= 5000) {
                        ringSvg.style.stroke = '#D91C1C';
                    }

                    if (timerTime < 1000) {
                        setCookie('status', 'send-money', 4);
                        rebuildForm();
                        reset();
                    }
                }, 1000);
            }

            const convertToSeconds = (timeInMs) => {
                let seconds = Math.floor(timeInMs / 1000);

                seconds = (seconds < 10) ? "0" + seconds : seconds;

                return `${seconds}`;
            }

            const updateProgress = (timeLeft) => {
                const percentageLeft = 1 - (timeLeft / totalTime);
                const offset = 1602 * percentageLeft;
                ringSvg.style.strokeDashoffset = offset;
            }

            const updateTime = (timeInMs) => {
                const formattedTime = convertToSeconds(timeInMs);
                circleSeconds.innerText = formattedTime;
                updateProgress(timeInMs);
            }

            const reset = () => {
                clearInterval(countDownInterval);
                timerTime = totalTime;
                updateTime(totalTime);
            }

            updateProgress(timerTime);
            countDown();
        }

        rebuildForm(true, true);

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
                clearAllCookies();
            }, 2000);
        }

        window.addEventListener('keyup', (e) => {
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
                if (!elem.classList.contains('not-click')) elem.classList.remove('invalid');
            }
            if (e.target.classList.contains('back')) {
                addScroll();
                hideModals();
            }
            if (e.target.classList.contains('list_items-val')) {
                let listTarget = e.target.closest('.list_target');
                if (listTarget.classList.contains('target-currs')) {
                    //Если в выпадающем листе выбрана валюта - перезаписываем куки и обновляем форму
                    setTimeout(() => {
                        let currs = {
                            'send-curr': document.querySelector('input[name="send-curr"]').value,
                            'get-curr': document.querySelector('input[name="get-curr"]').value
                        };

                        if (currs["send-curr"] == currs["get-curr"] && listTarget.getAttribute('data-revert')) {
                            currs[listTarget.getAttribute('data-revert')] = listTarget.getAttribute('data-old');

                            setCookie('send-bank', document.querySelector('input[name="get-bank"]') ? document.querySelector('input[name="get-bank"]').value : '', 4);
                            setCookie('get-bank', document.querySelector('input[name="send-bank"]') ? document.querySelector('input[name="send-bank"]').value : '', 4);
                        } else {
                            deleteCookie('get-bank');
                            deleteCookie('send-bank');
                        }

                        setCookie('send-curr', currs["send-curr"], 4);
                        setCookie('get-curr', currs["get-curr"], 4);
                        rebuildForm();
                    }, 500);
                }
                //Если в выпадающем списке выбран банк - перезаписываем куки и обновляем форму
                if (listTarget.classList.contains('target-banks')) {
                    setTimeout(() => {
                        setCookie('send-bank', document.querySelector('input[name="send-bank"]') ? document.querySelector('input[name="send-bank"]').value : '', 4);
                        setCookie('get-bank', document.querySelector('input[name="get-bank"]').value, 4);
                        rebuildForm();
                    }, 500);
                }
            }
            //Кнопка отмены заявки на 2-ом шаге
            if (e.target.classList.contains('delete-order')) {
                deleteOrder();
            }
            //Пользователь подтверждает введённые данные, создаётся заявка
            if (e.target.classList.contains('continue')) {
                let privacy = document.querySelector('#privacy');
                if (!privacy.checked) checkLabel.querySelector('.checkbox').classList.add('invalid');
                else {
                    addScroll();
                    hideModals();
                    sendData();
                }
            }
            //Пользователь подтвердил, что совершил платёж
            if (e.target.classList.contains('continue-pay')) {
                addScroll();
                hideModals();
                setCookie('status', 'get-money', 4);

                getData(formCont.getAttribute('data-url')+'?action=payd_mess&order-id='+getCookie('order-post-id'))
                .then(() => {
                    checkStatus();
                    rebuildForm();
                });
            }
            if (e.target.classList.contains('pay-done')) {
                showModal('#pay-done');
            }
            if (e.target.classList.contains('window')) {
                showModal('#instruction');
            }
            //Сбор данных для заявки
            if (e.target.classList.contains('main__form-change-button')) {
                const sendBank = formCont.querySelector('input[name="send-bank"]'),
                      getBank = formCont.querySelector('input[name="get-bank"]'),
                      sendSum = formCont.querySelector('input[name="send-sum"]'),
                      getSum = formCont.querySelector('input[name="get-sum"]'),
                      contacts = formCont.querySelector('input[name="contacts"]'),
                      sendEmail = formCont.querySelector('input[name="send-email"]'),
                      sendCard = formCont.querySelector('input[name="send-card"]'),
                      getCard = formCont.querySelector('input[name="get-card"]'),
                      sendCurr = formCont.querySelector('input[name="send-curr"]'),
                      getCurr = formCont.querySelector('input[name="get-curr"]');

                let valid = true;

                let inputs = [sendSum, sendCard, getCard, sendEmail],
                    fields = [sendBank, getBank];

                inputs.forEach(input => {
                    if (input && (!input.value || input.classList.contains('invalid'))) {
                        input.classList.add('invalid');
                        valid = false;
                    }
                });
                fields.forEach(field => {
                    if (field && !field.value) {
                        field.closest('.field').classList.add('invalid');
                        valid = false;
                    }
                });

                if (!isEmailValid(sendEmail.value)) {
                    sendEmail.classList.add('invalid');
                    valid = false;
                }

                if (valid) {
                    showModal('#how-work');

                    dataChange = [];
                    dataChange.push({name: 'send-bank', value: sendBank ? sendBank.value : ''});
                    dataChange.push({name: 'get-bank', value: getBank ? getBank.value : ''});
                    dataChange.push({name: 'send-sum', value: sendSum.value});
                    dataChange.push({name: 'send-email', value: sendEmail.value});
                    dataChange.push({name: 'get-sum', value: getSum.value});
                    dataChange.push({name: 'contacts', value: contacts.value});
                    dataChange.push({name: 'conttype', value: contacts.type});
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
