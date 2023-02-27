<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php the_title(); ?></title>
    <?php
        wp_head();
    ?>
</head>
<body>
    <header class="header text_fz16 text_fw500">
        <div class="container">
            <a href="/" class="header__logo">
                <img src="<?php the_field('logo', 27) ?>" alt="">
            </a>
            <nav class="header__nav">
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
            <div class="header__right">
                <a href="" class="header__button button text_white">
                    Поддержка
                </a>
                <div class="header__hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>