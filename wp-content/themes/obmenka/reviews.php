<?php
    /*
        Template Name: Отзывы
    */
    get_header();
?>
<main class="reviews">
    <section class="abroad-pay__promo faq__promo">
        <img src="<?=bloginfo('template_url')?>/assets/images/blue_back.png" alt="" class="faq__promo-back img_bg">
        <img src="<?php the_field('promo_image') ?>" alt="" class="reviews__promo-image abroad-pay__promo-image faq__promo-image">
        <div class="abroad-pay__promo-text faq__promo-text text_center">
            <h2 class="faq__promo-uppertitle text_colorfull text_fz28 text_fw600 text_ls005 text_upper"><?php the_field('promo_title') ?></h2>
            <h1 class="faq__promo-title text_fz44 text_fw600"><?php the_field('promo_undertitle') ?></h1>
            <h3 class="faq__promo-undertitle text_fz20"><?php the_field('promo_descr') ?></h3>
        </div>
    </section>
    <?php
        $result = json_decode(file_get_contents("https://api.vk.com/method/board.getComments?access_token=34c3df9934c3df9934c3df99d237d0cac6334c334c3df9950d14100463f582d4acaecb2&group_id=219360112&topic_id=50177684&v=5.131"), true);
    ?>
    <section class="reviews__list">
        <div class="container">
            <h2 class="reviews__list-title text_center text_fz44 text_fw600">Нам доверяют</h2>
            <div class="reviews__list-all text_fz20">
                <span>Всего отзывов:</span>
                <div class="reviews__list-count text_fz20 text_fw500">
                    <?=count($result['response']['items'])?>
                    <img src="<?=bloginfo('template_url')?>/assets/images/chat_check.svg" alt="">
                </div>
            </div>
            <div class="reviews__items">
                <?php
                    foreach ($result['response']['items'] as $key => $value) {
                        $user = json_decode(file_get_contents("https://api.vk.com/method/users.get?access_token=34c3df9934c3df9934c3df99d237d0cac6334c334c3df9950d14100463f582d4acaecb2&user_ids=".$value['from_id']."&fields=photo_100,first_name,last_name&v=5.131"), true)['response'][0];
                        ?>
                        <div class="main__reviews-slider-item">
                            <div class="main__reviews-slider-item-top">
                                <div class="main__reviews-slider-item-image">
                                    <?php
                                        $userPhoto = $user['photo_100'] && strpos($user['photo_100'], 'camera_100.png') === false ? $user['photo_100'] : "/wp-content/themes/obmenka/assets/images/user_circle.png";
                                    ?>
                                    <img src="<?=$userPhoto?>" alt="" class="img_bg">
                                </div>
                                <div class="main__reviews-slider-item-info">
                                    <div class="main__reviews-slider-item-name text_fz20 text_fw500"><?=$user['last_name']?> <?=$user['first_name']?></div>
                                    <div class="main__reviews-slider-item-date text_fz14"><?=date('d.m.Y, H:i', $value['date'])?></div>
                                </div>
                            </div>
                            <div class="main__reviews-slider-item-body text_fz16">
                                <?=$value['text']?>
                            </div>
                            <a href="https://vk.com/topic-219360112_50177684?post=<?=$value['id']?>" target="_blank" class="reviews__item-vk text_fz16 text_fw500 text_colorfull">
                                Перейти к отзыву
                                <img src="<?=bloginfo('template_url')?>/assets/images/vk.svg" alt="">
                            </a>
                        </div>
                        <?php
                    }
                ?>
                <?php
                    $names = [];
                    $my_posts = get_posts(array(
                        'numberposts' => -1,
                        'category_name'    => 'review',
                        'orderby'     => 'date',
                        'order'       => 'DESC',
                        'post_type'   => 'post',
                        'suppress_filters' => true
                    ));

                    foreach ($my_posts as $post) {
                        setup_postdata($post);
                        ?>
                            <div class="main__reviews-slider-item">
                                <div class="main__reviews-slider-item-top">
                                    <div class="main__reviews-slider-item-image">
                                        <img src="<?=get_field('photo') ?: '/wp-content/themes/obmenka/assets/images/user_circle.png'?>" alt="" class="img_bg">
                                    </div>
                                    <div class="main__reviews-slider-item-info">
                                        <div class="main__reviews-slider-item-name text_fz20 text_fw500"><?php the_title(); ?></div>
                                        <div class="main__reviews-slider-item-date text_fz14"><?=get_the_date('d.m.Y, H:i')?></div>
                                    </div>
                                </div>
                                <div class="main__reviews-slider-item-body text_fz16">
                                    <?php the_field('text') ?>
                                </div>
                            </div>
                        <?php
                    }

                    wp_reset_postdata();
                ?>
            </div>
            <div class="reviews__show text_colorfull text_fz20 text_fw500">
                Показать еще 
                <img src="<?=bloginfo('template_url')?>/assets/images/color_arrow.svg" alt="">
            </div>
        </div>
    </section>
    <section class="faq__translation text_white text_fw600">
        <div class="container">
            <?php
                while(have_rows('translation', 15)) {
                    the_row();
                    ?>
                        <div class="faq__translation-item">
                            <div class="faq__translation-item-type text_fz16 text_fw400">Переводы</div>
                            <h2 class="faq__translation-item-title text_fz28 text_fw700"><?php the_sub_field('title'); ?></h2>
                            <div class="faq__translation-item-descr text_fz20"><?php the_sub_field('descr'); ?></div>
                            <a href="<?php the_sub_field('link'); ?>" class="faq__translation-item-button button text_fz16 text_center">Подробнее</a>
                        </div>
                    <?php
                }
            ?>
        </div>
    </section>
</main>
<?php
    get_footer();
?>