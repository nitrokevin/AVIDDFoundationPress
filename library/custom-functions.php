<?php

/**
 * Theme Custom Functions
 * ACF, TinyMCE, Embed, and Admin Tweaks
 */
defined('ABSPATH') || exit; 
// ------------------------------------------------------------
// ACF CONTENT FILTERS
// ------------------------------------------------------------

/**
 * Make ACF WYSIWYG content images responsive
 */
add_filter('acf_the_content', 'wp_make_content_images_responsive');

/**
 * Remove <p> wrappers around images and replace with <figure>
 */
function img_unautop($content)
{
    return preg_replace('/<p>\s*(<a .*?><img.*?><\/a>|<img.*?>)\s*<\/p>/s', '<figure class="figure">$1</figure>', $content);
}
add_filter('acf_the_content', 'img_unautop', 30);

/**
 * Remove <p> wrappers around buttons (<a> elements)
 */
function a_unautop($content)
{
    return preg_replace('/<p>\s*(<a .*?>.*?<\/a>)\s*<\/p>/s', '$1', $content);
}
add_filter('acf_the_content', 'a_unautop', 30);


/**
 * Dynamic ACF field population
 * Used for populating select fields with data from ACF options pages or custom sources.
 */

add_filter('acf/load_field/name=options_page_selector', function ($field) {
    $field['choices'] = [];

    if (function_exists('acf_get_options_pages')) {
        $options_pages = acf_get_options_pages();
        if ($options_pages) {
            foreach ($options_pages as $slug => $page) {
                $field['choices'][$slug] = $page['page_title'];
            }
        }
    }

    return $field;
});


// ------------------------------------------------------------
// GUTENBERG SUPPORT
// ------------------------------------------------------------

//Custom alignment for columns
add_action('init', function () {
    register_block_style(
        'core/columns',
        array(
            'name'  => 'full-bleed',
            'label' => __('Full Bleed', 'foundationpress'),
        )
    );
    register_block_style(
        'core/media-text',
        array(
            'name'  => 'staggered',
            'label' => __('Staggered', 'foundationpress'),
        )
    );

    register_block_style(
        'core/media-text',
        array(
            'name'  => 'scaled',
            'label' => __('Scaled', 'foundationpress'),
        )
    );
});

/**
 * Automatically generate unique anchors for ACF blocks
 */
function set_unique_acf_block_anchor($attributes)
{
    if (empty($attributes['anchor'])) {
        $attributes['anchor'] = 'acf-block-' . substr(md5(serialize($attributes)), 0, 8);
    }
    return $attributes;
}
add_filter('acf/pre_save_block', 'set_unique_acf_block_anchor');

// ------------------------------------------------------------
// TINYMCE CUSTOMISATIONS
// ------------------------------------------------------------


add_filter('tiny_mce_before_init', 'customise_tinymce');

function customise_tinymce($init)
{
    // Always paste as plain text
    $init['paste_as_text'] = true;

    // Load custom colour palette via helper (returns ['#hex' => 'Name', ...])
    $default_colours = [];

    if (function_exists('get_theme_design_choices')) {
        $choices = get_theme_design_choices([
            'include_colors'    => true,
            'include_gradients' => false,
            'key'               => 'color', // we want HEX keys for the editor
            'for_acf'           => false,   // not needed here
        ]);

        if (is_array($choices) && count($choices)) {
            foreach ($choices as $key => $label) {
                // $key should be a HEX like '#ffffff'
                if (!is_string($key)) {
                    continue;
                }
                $hex = trim($key);
                if (strtolower($hex) === 'transparent') {
                    continue;
                }
                // guard: only include hex values that start with #
                if (strpos($hex, '#') !== 0) {
                    continue;
                }
                $default_colours[] = '"' . ltrim($hex, '#') . '"';
                $default_colours[] = '"' . esc_js($label) . '"';
            }
        }
    }

    // fallback: if no colours found, use a single neutral so editor won't fall back to core defaults
    if (empty($default_colours)) {
        $default_colours[] = '"000000"';
        $default_colours[] = '"Black"';
    }

    $init['textcolor_map'] = '[' . implode(', ', $default_colours) . ']';

    // Add custom style formats
    $init['style_formats'] = json_encode([
        [
            'title' => 'Primary Button',
            'selector' => 'a',
            'classes' => 'button',
        ],
        [
            'title' => 'Secondary button',
            'selector' => 'a',
            'classes' => 'button secondary',
        ],
        [
            'title' => 'Theme color 1 button',
            'selector' => 'a',
            'classes' => 'button theme-color-1',
        ],
        [
            'title' => 'Theme color 2 button',
            'selector' => 'a',
            'classes' => 'button theme-color-2',
        ],
    ]);

    return $init;
}

/**
 * Add 'styleselect' dropdown to TinyMCE toolbar
 */
function avidd_mce_buttons_2($buttons)
{
    array_unshift($buttons, 'styleselect');
    return $buttons;
}
add_filter('mce_buttons_2', 'avidd_mce_buttons_2');

// ------------------------------------------------------------
// EMBED AND VIDEO RESPONSIVENESS
// ------------------------------------------------------------

/**
 * Wrap oEmbed content in a responsive container
 */
function wrap_embed_html($html)
{
    return '<div class="responsive-embed">' . $html . '</div>';
}
add_filter('embed_oembed_html', 'wrap_embed_html', 10, 3);
add_filter('video_embed_html', 'wrap_embed_html');

/**
 * Add YouTube oEmbed parameters for cleaner display
 */
function avidd_modify_oembed_youtube(string $html, string $url, array $attr, int $post_id): string
{
    if (strpos($html, 'feature=oembed') !== false) {
        return str_replace('feature=oembed', 'feature=oembed&rel=0', $html);
    }
    return $html;
}
add_filter('embed_oembed_html', 'avidd_modify_oembed_youtube', 10, 4);

// ------------------------------------------------------------
// GOOGLE MAP KEY
// ------------------------------------------------------------


/**
 * Set ACF Google Maps API key
 * Replace with an environment variable or ACF options field for safety
 */
function avidd_acf_google_map_api($api)
{
    $api['key'] = getenv('GOOGLE_MAPS_API_KEY'); // Use env var or ACF option
    return $api;
}
add_filter('acf/fields/google_map/api', 'avidd_acf_google_map_api');

// ------------------------------------------------------------
// MISC
// ------------------------------------------------------------
/**
 * Enable dark mode
 */
add_filter('body_class', function (array $classes): array {
    if (function_exists('avidd_get_setting') && avidd_get_setting('dark_mode', false)) {
        $classes[] = 'dark-enabled';
    }
    return $classes;
});
/**
 * Enable excerpts on pages
 */
add_post_type_support('page', 'excerpt');

/**
 * Remove Comments from Admin Menu
 */
function avidd_remove_admin_menus()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'avidd_remove_admin_menus');


function avidd_social_links_inline_shortcode(array $atts): string
{
    $atts = shortcode_atts(['class' => ''], $atts, 'social_links');
    if (!function_exists('avidd_get_social_networks')) return '';

    $social_icons = [
        'facebook'  => 'fa-brands fa-facebook-f fa-fw',
        'x'         => 'fa-brands fa-x-twitter fa-fw',
        'instagram' => 'fa-brands fa-instagram fa-fw',
        'linkedin'  => 'fa-brands fa-linkedin-in fa-fw',
        'pinterest' => 'fa-brands fa-pinterest fa-fw',
        'youtube'   => 'fa-brands fa-youtube fa-fw',
        'tiktok'    => 'fa-brands fa-tiktok fa-fw',];
    $links = [];

    foreach (avidd_get_social_networks() as $row) {
        $network = $row['network'] ?? '';
        $url     = $row['url'] ?? '';
        if (!$network || !$url || !isset($social_icons[$network])) continue;
        $links[] = sprintf(
            '<a href="%s" target="_blank" rel="noreferrer" aria-label="%s" class="social-inline %s"><i class="%s"></i></a>',
            esc_url($url),
            esc_attr(ucfirst($network)),
            esc_attr($atts['class']),
            esc_attr($social_icons[$network])
        );
    }
    return implode(' ', $links);
}


add_filter('nav_menu_link_attributes', function ($atts, $item, $args, $depth) {

    // Only target your top-bar menu
    if ('top-bar-r' !== $args->theme_location) {
        return $atts;
    }

    // Only process items with a hash
    if (strpos($item->url, '#') === false) {
        return $atts;
    }

    // Get current page path (trailing slash normalized)
    $current_path = trailingslashit(wp_parse_url(
        sanitize_url($_SERVER['REQUEST_URI']),
        PHP_URL_PATH
    ));

    // Parse menu item URL
    $item_parts = parse_url($item->url);
    $item_path = isset($item_parts['path']) ? trailingslashit($item_parts['path']) : '/';
    $item_hash = isset($item_parts['fragment']) ? $item_parts['fragment'] : '';

    // If menu item points to the same page, convert href to just #hash
    if ($item_path === $current_path && $item_hash) {
        $atts['href'] = '#' . $item_hash;
    }

    return $atts;
}, 10, 4);
