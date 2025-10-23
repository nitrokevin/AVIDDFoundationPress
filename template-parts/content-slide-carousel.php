<div class="swiper slide-carousel">
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

                // Build slide classes
                $slide_classes = trim($bg_color . ($has_bg ? ' has_background_image' : ''));

                // Prepare image sizes if needed
                if ($image) {
                    $small = $image['sizes']['featured-small'];
                    $medium = $image['sizes']['featured-medium'];
                    $large = $image['sizes']['featured-large']; 
                    $xlarge = $image['sizes']['featured-xlarge'];
                }
                ?>
                <div class="swiper-slide <?php echo esc_attr($slide_classes); ?>" >
                     <?php if ($has_bg && $image): ?>
                      <div class="swiper-slide-background" data-interchange="[<?php echo esc_url($small); ?>, small], [<?php echo esc_url($medium); ?>, medium], [<?php echo esc_url($large); ?>, large], [<?php echo esc_url($xlarge); ?>, xlarge]" data-type="background"></div>
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
                                 alt="<?php echo $alt; ?>" />
                        </div>
                    <?php endif; ?>
					</div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>