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
                    <a target="_blank" href="<?=(strpos(get_field('contacts_telegram', 27), 'http') !== false ? get_field('contacts_telegram') : 'https://t.me/'.get_field('contacts_telegram'))?>">
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
            <div class="button back text_fz16 text_white text_fw500 text_center">
                Закрыть
            </div>
        </div>
        <div class="modal__item" id="pay-done">
            <div class="modal__title text_fz16 text_fw600">Подтверждение оплаты</div>
            <div class="modal__descr text_fz16">
            После подтверждения оплаты, мы проверим перевод
            в течении 15 минут и переведем сумму в другой валюте
            на указанные реквизиты.
            <br>
            Пожалуйста, нажмите <b>«Я оплатил»</b> только если вы
            действительно сделали перевод.
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
    <?php
        wp_footer();
    ?>
</body>
</html>