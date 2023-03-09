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
        }

        setTranslate();

        slideField && window.addEventListener('scroll', setTranslate);
    } catch (e) {
        console.log(e.stack);
    }
}

export default other;