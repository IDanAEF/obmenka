    <footer class="footer header text_fz14">
        <div class="footer__top">
            <div class="container">
                <a href="/" class="footer__logo header__logo">
                    <img src="<?php the_field('logo-light', 27) ?>" alt="">
                </a>
                <nav class="footer__nav header__nav">
                    <?php
                        wp_nav_menu( [
                            'menu'            => 'Main',
                            'container'       => false,
                            'menu_class'      => 'header__nav-list',
                            'echo'            => true,
                            'fallback_cb'     => 'wp_page_menu',
                            'items_wrap'      => '<ul class="header__nav-list">%3$s</ul>',
                            'depth'           => 2
                        ] );
                    ?>
                </nav>
                <div class="footer__social">
                    <a target="_blank" href="<?=(strpos(get_field('contacts_telegram', 27), 'http') !== false ? get_field('contacts_telegram', 27) : 'https://t.me/'.get_field('contacts_telegram', 27))?>">
                        <img src="<?=bloginfo('template_url')?>/assets/images/telegram.svg" alt="">
                    </a>
                    <a href="mailto:<?php the_field('contacts_email', 27) ?>">
                        <img src="<?=bloginfo('template_url')?>/assets/images/mail.svg" alt="">
                    </a>
                </div>
            </div>
        </div>
        <div class="footer__bott">
            © <?=date('Y')?>, <?php the_field('copyright', 27) ?>
            <div class="company">
                Разработка <a class="text_fw600" href="https://german-web.org/" target="_blank">German Web</a>
            </div>
        </div>
    </footer>
    <div class="modal">
        <div class="modal__item" id="how-work">
            <div class="modal__title text_fz16 text_fw600">Как работает OBMENKA</div>
            <div class="modal__descr text_fz16">Мы используем принцип P2P. Это полностью безопасно! Вам не нужно указывать CVV-код и другие персональные данные вашей карты. </div>
            <div class="modal__check text_fz14">
                <input type="checkbox" id="privacy" required hidden checked>
                <label for="privacy" class="active">
                    <div class="checkbox">
                        <img src="<?=bloginfo('template_url')?>/assets/images/check.svg" alt="">
                    </div>
                    Я согласен <a href="">с условиями и правилами сервиса</a>
                </label>
            </div>
            <div class="modal__buttons">
                <div class="button back button_red text_fz16 text_fw500 text_center">
                    Отмена
                </div>
                <div class="button continue text_white text_fz16 text_fw500 text_center">
                    Продолжить
                </div>
            </div>
        </div>
        <div class="modal__item" id="feedsend">
            <div class="main__products-item-number icon text_white">
                <img src="/wp-content/themes/obmenka/assets/images/double_check.svg" alt="">
            </div>
            <div class="modal__title text_fz20 text_fw600 text_center">Заявка успешно отправлена, <br>ожидайте подтверждения</div>
            <div class="button back close-succes text_fz16 text_white text_fw500 text_center" onclick="openModalReview()" id="btnfeedsend">
                Закрыть
            </div>
        </div>
        <div class="modal__item" id="pay-done">
            <div class="modal__title text_fz16 text_fw600">Подтверждение оплаты</div>
            <div class="modal__descr text_fz16">
            Пожалуйста, нажмите <b>«Я оплатил»</b> только если вы
            действительно сделали перевод.
            <br><br>
            После оплаты просим предоставить чек на почту <a href="mailto:info@topobmenka.com" class="text_underline text_fw600">info@topobmenka.com</a>, либо телеграмм <a href="https://t.me/TopObmenka" class="text_underline text_fw600" target="_blank">https://t.me/TopObmenka</a>.
            <br>
            Мы проверим перевод
            в течении 15 минут и переведем сумму в другой валюте
            на указанные реквизиты.
            <br><br>
            Благодарим за использование нашего сервиса
            </div>
            <div class="modal__buttons mt">
                <div class="button back button_red text_fz16 text_fw500 text_center">
                    Отмена
                </div>
                <div class="button continue-pay text_white text_fz16 text_fw500 text_center">
                    Продолжить
                </div>
            </div>
        </div>
        <div class="modal__item instr" id="instruction">
            <div class="modal__title text_fz16 text_fw600">Инструкция для оплаты</div>
            <div class="modal__descr text_fz16">
            Вам нужно выполнить перевод вручную, с помощью приложения вашего способа обмена.
            <br>
            <div class="main__form-change-way text_fz16">
                <div class="number text_white text_fz12">1</div>
                Перейдите в приложение
            </div>
            <div class="main__form-change-way text_fz16">
                <div class="number text_white text_fz12">2</div>
                Выполните перевод на указанную карту
            </div>
            <div class="main__form-change-way text_fz16">
                <div class="number text_white text_fz12">3</div>
                Нажмите на кнопку <b>«Я оплатил»</b>
            </div>
            <div class="main__form-change-way text_fz16">
                <div class="number text_white text_fz12">4</div>
                Мы проверим перевод в течении 15 минут<br>
                и переведем сумму в другой валюте на
                указанные реквизиты
            </div>
            <br>
            Если вы не сделаете перевод в период окна оплаты, заявка будет отменена автоматически
            </div>
            <div class="modal__buttons mt">
                <div class="back text_fz16 text_fw600 text_center">
                    Понятно
                </div>
            </div>
        </div>
      </div>
    <div class="modal-review">
        <div class="modal__review" id="reviews">
          <div class="close-reviews">
            <div id="closereview" onclick="closeModal()">
              <svg width="32px" height="32px" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.96967 16.4697C6.67678 16.7626 6.67678 17.2374 6.96967 17.5303C7.26256 17.8232 7.73744 17.8232 8.03033 17.5303L6.96967 16.4697ZM13.0303 12.5303C13.3232 12.2374 13.3232 11.7626 13.0303 11.4697C12.7374 11.1768 12.2626 11.1768 11.9697 11.4697L13.0303 12.5303ZM11.9697 11.4697C11.6768 11.7626 11.6768 12.2374 11.9697 12.5303C12.2626 12.8232 12.7374 12.8232 13.0303 12.5303L11.9697 11.4697ZM18.0303 7.53033C18.3232 7.23744 18.3232 6.76256 18.0303 6.46967C17.7374 6.17678 17.2626 6.17678 16.9697 6.46967L18.0303 7.53033ZM13.0303 11.4697C12.7374 11.1768 12.2626 11.1768 11.9697 11.4697C11.6768 11.7626 11.6768 12.2374 11.9697 12.5303L13.0303 11.4697ZM16.9697 17.5303C17.2626 17.8232 17.7374 17.8232 18.0303 17.5303C18.3232 17.2374 18.3232 16.7626 18.0303 16.4697L16.9697 17.5303ZM11.9697 12.5303C12.2626 12.8232 12.7374 12.8232 13.0303 12.5303C13.3232 12.2374 13.3232 11.7626 13.0303 11.4697L11.9697 12.5303ZM8.03033 6.46967C7.73744 6.17678 7.26256 6.17678 6.96967 6.46967C6.67678 6.76256 6.67678 7.23744 6.96967 7.53033L8.03033 6.46967ZM8.03033 17.5303L13.0303 12.5303L11.9697 11.4697L6.96967 16.4697L8.03033 17.5303ZM13.0303 12.5303L18.0303 7.53033L16.9697 6.46967L11.9697 11.4697L13.0303 12.5303ZM11.9697 12.5303L16.9697 17.5303L18.0303 16.4697L13.0303 11.4697L11.9697 12.5303ZM13.0303 11.4697L8.03033 6.46967L6.96967 7.53033L11.9697 12.5303L13.0303 11.4697Z" fill="#000000"/>
              </svg>
            </div>
          </div>
          <form id="add-review-form" method="post" action="" class="reviews-modal">
            <div class="modal-titile">Отправить</div>
            <div class="review-input">
              <span>Ваше имя</span>
              <input type="text" name="author_name" id="author_name" required>
            </div>
            <div class="review-input">
              <span>Телефон</span>
              <input type="text" name="phone_number" id="phone_number" required>
            </div>
            <div class="review-input">
              <span>Комментарий</span>
              <textarea name="review_content"  id="review_content" required></textarea>
            </div>
            <div class="modal__check text_fz14">
                <input type="checkbox" id="privacy-review" name="privacy-review" required hidden >
                <label for="privacy-review" class="privacy-review">
                    <div class="checkbox">
                        <img src="<?=bloginfo('template_url')?>/assets/images/check.svg" alt="">
                    </div>
                    Я согласен на <a href="/terms/">обработку персональных данных</a>
                </label>
            </div>
            <div id="review_message"></div>
            <input type="text" name="g-recaptcha-response" class="g-recaptcha-response" hidden>
            <input class="button block text_fz16 text_fw500 text_white" type="submit" name="submit-review" value="Отправить">
          </form>
        </div>
    </div>
    <?php
        wp_footer();
    ?>
</body>
</html>
