const abroad_form = () => {
    try {
        function isEmailValid(email) {
            const emailRegexp = new RegExp(
                /^[a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1}([a-zA-Z0-9][\-_\.\+\!\#\$\%\&\'\*\/\=\?\^\`\{\|]{0,1})*[a-zA-Z0-9]@[a-zA-Z0-9][-\.]{0,1}([a-zA-Z][-\.]{0,1})*[a-zA-Z0-9]\.[a-zA-Z0-9]{1,}([\.\-]{0,1}[a-zA-Z]){0,}[a-zA-Z0-9]{0,}$/i
            );

            return emailRegexp.test(email);
        }

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
        async function postData(url, data) {
            let res = await fetch(url, {
                method: "POST",
                body: data
            });

            return await res.text();
        }
        async function getData(url) {
            let res = await fetch(url, {
                method: "GET"
            });

            return await res.text();
        }

        const abroadForm = document.querySelector('#abroad-form form'),
              inputs = abroadForm.querySelectorAll('.input input'),
              inputEmail = abroadForm.querySelector('input[type="email"]');

        abroadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            let valid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('invalid');

                    valid = false;
                }
            });

            if (!isEmailValid(inputEmail.value)) {
                valid = false;
                inputEmail.classList.add('invalid');
            }

            if (valid) {
                const formData = new FormData(abroadForm);

                formData.append('feedconttype', document.querySelector('[name="feedcontact"]').placeholder);

                postData(abroadForm.action + '?action=create_buy_order', formData)
                .then((res) => {
                    showModal('#feedsend');
                    let checkStat = setInterval(() => {
                        getData(abroadForm.action+'?action=check_status&post_id='+res)
                        .then((res) => {
                            res = JSON.parse(res);
                            if (res) clearInterval(checkStat);
                        });
                    }, 10000);
                });
            }
        });

        document.body.addEventListener('click', (e) => {
            if (e.target.classList.contains('invalid')) e.target.classList.remove('invalid');

            if (e.target.classList.contains('back')) {
                addScroll();
                hideModals();
            }
        });
    } catch (e) {
        console.log(e.stack);
    }
}

export default abroad_form;
