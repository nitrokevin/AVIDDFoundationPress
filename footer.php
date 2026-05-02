<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the "off-canvas-wrap" div and all content after.
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
$contact_phone = function_exists('avidd_get_setting') ? avidd_get_setting('contact_phone_number') : '';
$contact_email = function_exists('avidd_get_setting') ? avidd_get_setting('contact_email') : '';
$footer_company_number = function_exists('avidd_get_setting') ? avidd_get_setting('footer_company_number') : '';
$footer_copyright = function_exists('avidd_get_setting') ? avidd_get_setting('footer_copyright') : '';
$contact_address_1 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_1') : '';
$contact_address_2 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_2') : '';
$contact_address_3 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_3') : '';
$contact_address_4 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_4') : '';
$contact_address_5 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_5') : '';
$contact_address_6 = function_exists('avidd_get_setting') ? avidd_get_setting('contact_address_6') : '';

$site_name = get_bloginfo('name', 'display');



?>

<footer class="footer">
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
                    <?php if ($contact_phone) echo '<li>T: ' . esc_html($contact_phone) . '</li>'; ?>
                    <?php if ($contact_email) echo '<li><a href="mailto:' . esc_attr($contact_email) . '">E: ' . esc_html($contact_email) . '</a></li>'; ?>
                </ul>


                <?php
                $opening_times = get_field('opening_times', 'option');

                if (!empty($opening_times)) {
                    foreach ($opening_times as $time) {
                        if (!empty($time['day'])) {
                            echo esc_html($time['day']) . ': ';
                            if (!empty($time['opening_time'])) echo esc_html($time['opening_time']);
                            if (!empty($time['opening_time']) && !empty($time['closing_time'])) echo ' - ';
                            if (!empty($time['closing_time'])) echo esc_html($time['closing_time']);
                            if (!empty($time['note'])) echo ' <em>(' . esc_html($time['note']) . ')</em>';
                            echo '<br>';
                        }
                    }
                }
                ?>


            </section>
            <section>
                <?php

                $social_networks = function_exists('avidd_get_social_networks') ? avidd_get_social_networks() : [];
                $has_social      = ! empty($social_networks);

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

                <?php if ($has_social) : ?>
                    <ul class="social-links menu footer-menu align-left">
                        <?php foreach ($social_networks as $row) :
                            $network = $row['network'] ?? '';
                            $url     = $row['url'] ?? '';
                            if (! $network || ! $url || ! isset($social_icons[$network])) {
                                continue;
                            }
                        ?>
                            <li>
                                <a href="<?php echo esc_url($url); ?>"
                                    rel="noreferrer noopener"
                                    target="_blank"
                                    aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
                                    <i class="<?php echo esc_attr($social_icons[$network]); ?>"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
            <section>
                <?php foundationpress_footer_nav_r(); ?>

                <?php
                $footer_links = get_field('footer_links', 'option');

                if (!empty($footer_links)) { ?>
                    <div class="footer-links">
                        <?php foreach ($footer_links as $footer_link) : ?>
                            <?php if (!empty($footer_link['footer_image'])) : ?>
                                <a href="<?php echo esc_url($footer_link['link_url']); ?>">
                                    <?php echo wp_get_attachment_image($footer_link['footer_image'], 'fp-small', false, ["class" => "footer-icon"]); ?>
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