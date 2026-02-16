<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "off-canvas-wrap" div and all content after.
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
$contact_phone = get_theme_mod('contact_phone_number');
$contact_email = get_theme_mod('contact_email');
$footer_company_number = get_theme_mod('footer_company_number');
$footer_copyright = get_theme_mod('footer_copyright');
$contact_address_1 = get_theme_mod('contact_address_1');
$contact_address_2 = get_theme_mod('contact_address_2');
$contact_address_3 = get_theme_mod('contact_address_3');
$contact_address_4 = get_theme_mod('contact_address_4');
$contact_address_5 = get_theme_mod('contact_address_5');
$contact_address_6 = get_theme_mod('contact_address_6');
$footer_background_image = get_theme_mod('footer_background_image');
$site_name = get_bloginfo('name', 'display');

// Only show social icons if at least one is enabled
$has_social = false;
$social_networks = ['facebook', 'x', 'instagram', 'linkedin', 'pinterest', 'youtube', 'tiktok'];
foreach ($social_networks as $network) {
    if (get_theme_mod("social-{$network}", '') === '1') {
        $has_social = true;
        break;
    }
}

   
if ($footer_background_image) {
    $sizes = [
        'small' => wp_get_attachment_image_url($footer_background_image, 'fp-small'),
        'medium' => wp_get_attachment_image_url($footer_background_image, 'fp-medium'),
        'large' => wp_get_attachment_image_url($footer_background_image, 'fp-large'),
        'xlarge' => wp_get_attachment_image_url($footer_background_image, 'fp-xlarge'),
    ];
}
?>

<footer class="footer" <?php if ($footer_background_image) { ?> data-interchange="[<?php echo esc_url($sizes['small']); ?>, small], [<?php echo esc_url($sizes['medium']); ?>, medium], [<?php echo esc_url($sizes['large']); ?>, large], [<?php echo esc_url($sizes['xlarge']); ?>, xlarge]" data-type="background"<?php } ?>>
    <div class="footer-container">
        <div class="footer-grid">
            <?php dynamic_sidebar('footer-widgets'); ?>
        </div>
        <div class="footer-grid">

            <section>
                <?php foundationpress_footer_nav_l(); ?>
                <ul class="footer-contact">
                    <?php if (!empty($contact_address_1)) : ?>
                    <li><?php echo esc_html($contact_address_1); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($contact_address_2)) : ?>
                    <li><?php echo esc_html($contact_address_2); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($contact_address_3)) : ?>
                    <li><?php echo esc_html($contact_address_3); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($contact_address_4)) : ?>
                    <li><?php echo esc_html($contact_address_4); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($contact_address_5)) : ?>
                    <li><?php echo esc_html($contact_address_5); ?></li>
                    <?php endif; ?>

                    <?php if (!empty($contact_address_6)) : ?>
                    <li><?php echo esc_html($contact_address_6); ?></li>
                    <?php endif; ?>
                </ul>
                <ul class="footer-contact-phone-email">
                   <?php if ( $contact_phone ) echo '<li>T: ' . esc_html( $contact_phone ) . '</li>'; ?>
                <?php if ( $contact_email ) echo '<li><a href="mailto:' . esc_attr( $contact_email ) . '">E: ' . esc_html( $contact_email ) . '</a></li>'; ?>
                </ul>
                     

                            <?php
                    $opening_times = avidd_get_repeater_data('opening_times');

                    if (!empty($opening_times)) {
                        foreach ($opening_times as $time) {
                            if (!empty($time['day'])) {
                                echo esc_html($time['day']) . ': ';
                                if (!empty($time['opening_time'])) echo esc_html($time['opening_time']);
                                if (!empty($time['opening_time']) && !empty($time['closing_time'])) echo ' - ';
                                if (!empty($time['closing_time'])) echo esc_html($time['closing_time']);
                                echo '<br>';
                            }
                        }
                    }
                    ?>


            </section>
            <section>
             <?php

            if ($has_social) :
                $social_icons = [
                    'facebook'  => 'fa-brands fa-facebook-f fa-fw',
                    'x'         => 'fa-brands fa-x-twitter fa-fw',
                    'instagram' => 'fa-brands fa-instagram fa-fw',
                    'linkedin'  => 'fa-brands fa-linkedin-in fa-fw',
                    'pinterest' => 'fa-brands fa-pinterest fa-fw',
                    'youtube'   => 'fa-brands fa-youtube fa-fw',
                    'tiktok'    => 'fa-brands fa-tiktok fa-fw',
                ];
                ?>
                  <ul class="social-links menu footer-menu align-center">
                    <?php foreach ($social_icons as $network => $icon_class) : ?>
                        <?php 
                        // Check if this social network is enabled (checkbox is checked)
                        $is_enabled = get_theme_mod("social-{$network}", '') === '1';
                        $url = get_theme_mod("social-{$network}-url", '');
                        
                        if ($is_enabled && !empty($url)) : 
                        ?>
                            <li>
                                <a href="<?php echo esc_url($url); ?>" 
                                rel="noreferrer noopener" 
                                target="_blank" 
                                aria-label="<?php echo ucfirst($network); ?>">
                                    <i class="<?php echo esc_attr($icon_class); ?>"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            </section>
            <section>
                <?php foundationpress_footer_nav_r(); ?>
                    <?php
                    $footer_links = avidd_get_repeater_data('footer_links');

                    if (!empty($footer_links)) { ?>
                        <div class="footer-links">
                            <?php foreach ($footer_links as $footer_link) : ?>
                                <?php if (!empty($footer_link['footer_image']) ) : ?>
                                    <a href="<?php echo esc_url($footer_link['link_url']); ?>">
                                        <?php echo wp_get_attachment_image($footer_link['footer_image'], 'thumbnail', false, ["class" => "footer-icon"]); ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php } ?>
            </section>
        </div>
    </div>
</footer>

<?php if (get_theme_mod('wpt_mobile_menu_layout') === 'offcanvas') : ?>
    </div><!-- Close off-canvas content -->
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>