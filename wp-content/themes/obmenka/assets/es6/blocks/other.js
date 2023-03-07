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
}

export default other;