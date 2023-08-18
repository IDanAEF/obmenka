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

    try {
        document.querySelector('[name="feedprice"]').addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    } catch (e) {
        console.log(e.stack);
    }
});




document.addEventListener("DOMContentLoaded", function() {
  var btnFeedsend = document.getElementById("btnfeedsend");
  var closeModalReview = document.getElementById("closereview");
  var modal = document.querySelector(".modal-review");
  var modalReview = document.querySelector(".modal__review");

  btnFeedsend.addEventListener("click", openModalReview);

  function openModalReview() {
      modal.classList.add("active");
      modalReview.classList.add("active");
  }

  closeModalReview.addEventListener("click", closeModal);

  function closeModal() {
      modal.classList.remove("active");
      modalReview.classList.remove("active");
  }

  const checkLabel = document.querySelector('.modal-review .modal__check label');
  const sendingBtn = document.querySelector('.modal-review .button');

  checkLabel.addEventListener('click', (e) => {
      checkLabel.querySelector('.checkbox').classList.remove('invalid');
      checkLabel.classList.toggle('active');
      sendingBtn.classList.toggle('block');
  });


  const phoneNumberInput = document.getElementById('phone_number');

  phoneNumberInput.addEventListener('input', function(e) {
    let inputText = e.target.value.replace(/\D/g, '');
    e.target.value = inputText;
  });

});
