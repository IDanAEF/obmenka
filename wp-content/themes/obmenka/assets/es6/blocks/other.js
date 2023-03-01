const other = () => {
    try {
        //hamburger
        const hamburger = document.querySelector('.header__hamburger'),
              headerMenu = document.querySelector('.header__nav');

        document.body.addEventListener('click', (e) => {
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
        document.body.addEventListener('click', (e) => {
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

                if (e.target.getAttribute('data-img')) 
                    list.querySelector('.list_img').src = e.target.getAttribute('data-img');

                list.querySelectorAll('.list_items-val').forEach(it => it.style.display = '');
                e.target.style.display = 'none';
            }
        });
    } catch (e) {
        console.log(e.stack);
    }
}

export default other;