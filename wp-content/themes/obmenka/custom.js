'use strict';

window.addEventListener('DOMContentLoaded', () => {
    //reviews load
    try {
        const reviewsItems = document.querySelectorAll('.reviews__items .main__reviews-slider-item'),
              reviewsBtn = document.querySelector('.reviews__show');

        if (reviewsItems) {
            let count = reviewsItems.length,
                curr = 0;

            reviewsItems.forEach((item, i) => {
                if (i >= 8) {
                    curr = 8;
                    item.style.display = 'none';
                }
            });

            if (curr < 8) reviewsBtn.style.display = 'none';

            reviewsBtn.addEventListener('click', () => {
                for (let i = curr; i < curr + 8; i++) {
                    if (reviewsItems[i])
                        reviewsItems[i].style.display = '';
                    else {
                        reviewsBtn.style.display = 'none';
                        break;
                    }
                }
                curr += 8;
            });
        }
    } catch (e) {
        console.log(e.stack);
    }

    try {
        const currencyTarg = document.querySelectorAll('[data-currency]');

        currencyTarg.forEach(curr => {
            curr.addEventListener('click', () => {
                let lists = document.querySelectorAll('.list_items-val');
                
                for (let i = 0; i < lists.length; i++) {
                    if (lists[i].textContent.trim() == curr.getAttribute('data-currency')) {
                        lists[i].click();
                        break;
                    }
                }
            });
        });
    } catch (e) {
        console.log(e.stack);
    }
});