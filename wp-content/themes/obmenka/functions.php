<?php
    add_theme_support('menus');
    add_theme_support('post-thumbnails');

    function obmenkaAddScripts() {
        wp_enqueue_style( 'obmenka_main_style', get_template_directory_uri() . '/assets/css/style.min.css' );
        wp_enqueue_style( 'obmenka_custom_style', get_template_directory_uri() . '/custom.css' );
    
        wp_enqueue_script( 'obmenka_main_scrit', get_template_directory_uri() . '/assets/js/script.js', array(), null, true );
        wp_enqueue_script( 'obmenka_custom_scrit', get_template_directory_uri() . '/custom.js', array(), null, true );
    }

    add_action( 'wp_enqueue_scripts', 'obmenkaAddScripts' );

    function filter_nav_menu_link_attributes($atts, $item, $args) {
        if ($args->menu === 'Main') {
            if ($item->current) {
                $atts['class'] = 'active';
            }
        }
    
        return $atts;
    }
    
    add_filter('nav_menu_link_attributes', 'filter_nav_menu_link_attributes', 10, 3);

    add_action( 'template_redirect', function(){
        ob_start( function( $buffer ){
            $buffer = str_replace('type="text/javascript"', '', $buffer );
            $buffer = str_replace('type="text/css"', '', $buffer );
            $buffer = str_replace("type='text/javascript'", '', $buffer );
            $buffer = str_replace("type='text/css'", '', $buffer );
            $buffer = preg_replace("~<meta (.*?)\/>~", "<meta $1>", $buffer);
            $buffer = preg_replace("~<link (.*?)\/>~", "<link $1>", $buffer);
            $buffer = preg_replace("~<input (.*?)\/>~", "<input $1>", $buffer);
            $buffer = preg_replace("~<img (.*?)\/>~", "<img $1>", $buffer);
            $buffer = str_replace("<br />", '<br>', $buffer );
            return $buffer;
        });
    });
?>