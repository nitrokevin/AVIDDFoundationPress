<?php
$loop            = get_field('carousel_loop');
$autoplay        = get_field('carousel_autoplay');
$autoplay_delay  = get_field('carousel_autoplay_delay') ?: 4400;
$speed           = get_field('carousel_speed') ?: 800;
$slides_per_view = get_field('carousel_slides_per_view') ?: 1;
$space_between   = get_field('carousel_space_between') ?: 0;
?>
<div class="swiper slide-carousel"
    data-loop="<?php echo $loop ? 'true' : 'false'; ?>"
    data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
    data-autoplay-delay="<?php echo esc_attr($autoplay_delay); ?>"
    data-speed="<?php echo esc_attr($speed); ?>"
    data-slides-per-view="<?php echo esc_attr($slides_per_view); ?>"
    data-space-between="<?php echo esc_attr($space_between); ?>">

    <div class="swiper-button-prev" aria-label="Go to previous slide"></div>
    <div class="swiper-button-next" aria-label="Next slide"></div>
    <div class="swiper-pagination"></div>

    <div class="swiper-wrapper">
        <?php if (have_rows('repeater_content_carousel')): ?>
            <?php while (have_rows('repeater_content_carousel')): the_row();
                $heading = get_sub_field('carousel_heading') ?: '';
                $image = get_sub_field('carousel_image');
                $has_bg = get_sub_field('background_image'); // true/false toggle
                $bg_color = get_sub_field('carousel_background_color') ?: '';
                $content = get_sub_field('carousel_content') ?: '';
                $alt = !empty($image['alt']) ? $image['alt'] : $heading;

                // Build slide classes
                $slide_classes = trim($bg_color . ($has_bg ? ' has_background_image' : ''));
            ?>
                <div class="swiper-slide <?php echo esc_attr($slide_classes); ?>">
                    <?php if ($has_bg && $image): ?>
                        <picture class="swiper-slide-background">
                            <source
                                media="(min-width: 1440px)"
                                srcset="<?php echo esc_url($image['sizes']['featured-xxlarge']); ?>">

                            <source
                                media="(min-width: 1200px)"
                                srcset="<?php echo esc_url($image['sizes']['featured-xlarge']); ?>">

                            <source
                                media="(min-width: 1024px)"
                                srcset="<?php echo esc_url($image['sizes']['featured-large']); ?>">

                            <source
                                media="(min-width: 640px)"
                                srcset="<?php echo esc_url($image['sizes']['featured-medium']); ?>">

                            <!-- Fallback -->
                            <img
                                src="<?php echo esc_url($image['sizes']['featured-small']); ?>"
                                alt="<?php echo $alt; ?>"
                                class="swiper-slide-bg-img"
                                loading="eager"
                                decoding="async">
                        </picture>
                    <?php endif; ?>
                    <div class="info" data-swiper-parallax="-600">
                        <h3><?php echo esc_html($heading); ?></h3>
                        <?php echo wp_kses_post($content); ?>

                        <?php if ($image && !$has_bg): ?>
                            <?php
                            $src = wp_get_attachment_image_url($image['id'], 'fp-large');
                            $srcset = wp_get_attachment_image_srcset($image['id'], 'fp-large');
                            $alt = esc_attr($image['alt']);
                            ?>
                            <div class="image">
                                <img src="<?php echo esc_url($src); ?>"
                                    srcset="<?php echo esc_attr($srcset); ?>"
                                    sizes="(max-width: 100vw) 480px"
                                    alt="<?php echo esc_attr($alt); ?>" />
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>