'use strict';

window.addEventListener('DOMContentLoaded', () => {
    //reviews load
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
});