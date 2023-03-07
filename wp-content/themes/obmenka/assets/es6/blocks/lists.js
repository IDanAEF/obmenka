const lists = () => {
    try {
        document.body.addEventListener('click', (e) => {
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

                if (e.target.getAttribute('data-img')) 
                    list.querySelector('.list_img').src = e.target.getAttribute('data-img');

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
}

export default lists;