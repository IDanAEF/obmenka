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
    <?php
        wp_footer();
    ?>
</body>
</html>