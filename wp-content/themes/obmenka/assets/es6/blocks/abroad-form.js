const abroad_form = () => {
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
        async function postData(url, data) {
            let res = await fetch(url, {
                method: "POST",
                body: data
            });
        
            return await res.text();
        }

        const abroadForm = document.querySelector('#abroad-form form'),
              inputs = abroadForm.querySelectorAll('.input input');

        abroadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            let valid = true;

            inputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('invalid');

                    valid = false;
                }
            }); 

            if (valid) {
                const formData = new FormData(abroadForm);

                formData.append('post-type', 'abroad');

                postData(abroadForm.action, formData)
                .then(() => {
                    showModal('#feedsend');
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