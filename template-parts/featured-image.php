<?php
if (has_post_thumbnail($post->ID)) :
    $img_id = get_post_thumbnail_id($post->ID);
    $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
    $alt = $alt ? esc_attr($alt) : '';
    $is_front   = is_front_page();
    $hero_classes = $is_front ? 'front-hero' : 'featured-hero';
    $hero_full_height      = (bool) get_theme_mod('hero_full_height', true);

    if (! $hero_full_height)      $hero_classes .= ' front-hero--reduced-height';
?>
    <header class="<?php echo esc_attr($hero_classes); ?>">
        <picture class="hero__media">
            <source media="(min-width: 1440px)" srcset="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'featured-xxlarge')); ?>">
            <source media="(min-width: 1200px)" srcset="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'featured-xlarge')); ?>">
            <source media="(min-width: 1024px)" srcset="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'featured-large')); ?>">
            <source media="(min-width: 640px)" srcset="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'featured-medium')); ?>">
            <img
                src="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'featured-small')); ?>"
                alt="<?php echo $alt; ?>"
                class="hero__img"
                loading="eager"
                fetchpriority="high"
                decoding="async">
        </picture>

        <?php if ($is_front) : ?>
            <div class="marketing">
                <div class="tagline">
                    <h1><?php echo esc_html(get_bloginfo('name')); ?></h1>
                    <p class="subheader"><?php echo esc_html(get_bloginfo('description')); ?></p>
                    <div class="tagline__bg-overlay">
                        <div class="tagline__bg-colour"></div>
                    </div>
                </div>
            </div>
            <div class="hero__bg-overlay"></div>
        <?php else : ?>
            <div class="marketing">
                <div class="tagline">
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <div class="tagline__bg-overlay">
                        <div class="tagline__bg-colour"></div>
                    </div>
                </div>
            </div>
            <div class="hero__bg-overlay"></div>
        <?php endif; ?>

         
    </header>
<?php endif; ?>