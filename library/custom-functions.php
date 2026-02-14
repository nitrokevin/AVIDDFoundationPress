<?php
/**
 * Theme Custom Functions
 * ACF, TinyMCE, Embed, and Admin Tweaks
 */

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
function img_unautop($content) {
    return preg_replace('/<p>\s*(<a .*?><img.*?><\/a>|<img.*?>)\s*<\/p>/s', '<figure class="figure">$1</figure>', $content);
}
add_filter('acf_the_content', 'img_unautop', 30);

/**
 * Remove <p> wrappers around buttons (<a> elements)
 */
function a_unautop($content) {
    return preg_replace('/<p>\s*(<a .*?>.*?<\/a>)\s*<\/p>/s', '$1', $content);
}
add_filter('acf_the_content', 'a_unautop', 30);


/**
 * Dynamic ACF field population
 * Used for populating select fields with data from ACF options pages or custom sources.
 */

add_filter('acf/load_field/name=options_page_selector', function($field) {
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

add_theme_support('align-wide');

function custom_block_categories($categories) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'avidd',
                'title' => __('AVIDD Blocks', 'avidd'),
            ],
        ]
    );
}
add_action('block_categories_all', 'custom_block_categories', 10, 2);


//Custom alignment for columns
add_action( 'init', function() {
    register_block_style(
        'core/columns',
        array(
            'name'  => 'full-bleed',
            'label' => __( 'Full Bleed', 'foundationpress' ),
        )
    );
register_block_style(
    'core/media-text',
    array(
        'name'  => 'staggered',
        'label' => __( 'Staggered', 'foundationpress' ),
    )
);

register_block_style(
    'core/media-text',
    array(
        'name'  => 'scaled',
        'label' => __( 'Scaled', 'foundationpress' ),
    )
);
    
});

/**
 * Automatically generate unique anchors for ACF blocks
 */
function set_unique_acf_block_anchor($attributes) {
    if (empty($attributes['anchor'])) {
        $attributes['anchor'] = 'acf-block-' . uniqid();
    }
    return $attributes;
}
add_filter('acf/pre_save_block', 'set_unique_acf_block_anchor');

// ------------------------------------------------------------
// TINYMCE CUSTOMISATIONS
// ------------------------------------------------------------


add_filter('tiny_mce_before_init', 'customise_tinymce');

function customise_tinymce($init) {
    // Always paste as plain text
    $init['paste_as_text'] = true;

    // Load custom colour palette via helper (returns ['#hex' => 'Name', ...])
    $default_colours = [];

    if ( function_exists( 'get_theme_design_choices' ) ) {
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
function my_mce_buttons_2($buttons) {
    array_unshift($buttons, 'styleselect');
    return $buttons;
}
add_filter('mce_buttons_2', 'my_mce_buttons_2');

// ------------------------------------------------------------
// EMBED AND VIDEO RESPONSIVENESS
// ------------------------------------------------------------

/**
 * Wrap oEmbed content in a responsive container
 */
function wrap_embed_html($html) {
    return '<div class="responsive-embed">' . $html . '</div>';
}
add_filter('embed_oembed_html', 'wrap_embed_html', 10, 3);
add_filter('video_embed_html', 'wrap_embed_html');

/**
 * Add YouTube oEmbed parameters for cleaner display
 */
function modify_oembed_youtube($html, $url, $attr, $post_id) {
    if (strpos($html, 'feature=oembed') !== false) {
        return str_replace(
            'feature=oembed',
            'feature=oembed&amp;rel=0&modestbranding=1&showinfo=0',
            $html
        );
    }
    return $html;
}
add_filter('embed_oembed_html', 'modify_oembed_youtube', 10, 4);

// ------------------------------------------------------------
// ACF FIXES & GOOGLE MAP KEY
// ------------------------------------------------------------

/**
 * Fix issue with ACF fields missing in preview
 */
if (class_exists('acf_revisions')) {
    $acf_revs_cls = acf()->revisions;
    remove_filter('acf/validate_post_id', [$acf_revs_cls, 'acf_validate_post_id'], 10);
}

/**
 * Set ACF Google Maps API key
 * Replace with an environment variable or ACF options field for safety
 */
function my_acf_google_map_api($api) {
    $api['key'] = getenv('GOOGLE_MAPS_API_KEY'); // Use env var or ACF option
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

// ------------------------------------------------------------
// MISC
// ------------------------------------------------------------
/**
 * Enable dark mode
 */
add_filter( 'body_class', function( $classes ) {
	$dark_mode = get_theme_mod( 'dark_mode', 'off' );

	if ( $dark_mode === '1' || $dark_mode === 1 || $dark_mode === true ) {
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
function my_remove_admin_menus() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'my_remove_admin_menus');


function avidd_social_links_inline_shortcode($atts) {
    $atts = shortcode_atts([
        'class' => '', // allow user to pass a class
    ], $atts, 'social_links');

    $links = [];

    $social_sites = [
        'facebook' => 'fa-brands fa-facebook-f',
        'twitter'  => 'fa-brands fa-x-twitter',
        'instagram'=> 'fa-brands fa-instagram',
        'linkedin' => 'fa-brands fa-linkedin-in',
        'pinterest'=> 'fa-brands fa-pinterest',
        'tiktok'   => 'fa-brands fa-tiktok',
    ];

    foreach ($social_sites as $key => $icon_class) {
        if (get_theme_mod('social-' . $key)) {
            $url = esc_url(get_theme_mod('social-' . $key . '-url'));
            $links[] = '<a href="' . $url . '" target="_blank" rel="noreferrer" aria-label="' . ucfirst($key) . '" class="social-inline ' . esc_attr($atts['class']) . '"><i class="' . $icon_class . '"></i></a>';
        }
    }

    return implode(' ', $links);
}
add_shortcode('social_links', 'avidd_social_links_inline_shortcode');


add_filter( 'nav_menu_link_attributes', function( $atts, $item, $args, $depth ) {

    // Only target your top-bar menu
    if ( 'top-bar-r' !== $args->theme_location ) {
        return $atts;
    }

    // Only process items with a hash
    if ( strpos( $item->url, '#' ) === false ) {
        return $atts;
    }

    // Get current page path (trailing slash normalized)
    $current_path = trailingslashit( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

    // Parse menu item URL
    $item_parts = parse_url( $item->url );
    $item_path = isset( $item_parts['path'] ) ? trailingslashit( $item_parts['path'] ) : '/';
    $item_hash = isset( $item_parts['fragment'] ) ? $item_parts['fragment'] : '';

    // If menu item points to the same page, convert href to just #hash
    if ( $item_path === $current_path && $item_hash ) {
        $atts['href'] = '#' . $item_hash;
    }

    return $atts;

}, 10, 4 );


/**
 * DIAGNOSTIC TOOL - Add this to a test page template or functions.php temporarily
 * 
 * This will show you exactly what's being saved in the database
 * 
 * Usage: Create a page template with this code, or add to functions.php
 * and visit yoursite.com/?customizer_debug=1
 */

// Add this to functions.php to create a debug page
add_action( 'template_redirect', function() {
    if ( isset( $_GET['customizer_debug'] ) && current_user_can( 'manage_options' ) ) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Customizer Debug</title>
            <style>
                body { font-family: monospace; padding: 20px; background: #f5f5f5; }
                h1 { color: #333; }
                h2 { color: #666; margin-top: 30px; }
                table { background: white; border-collapse: collapse; width: 100%; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background: #0073aa; color: white; }
                .value { font-weight: bold; color: #0073aa; }
                .empty { color: #999; font-style: italic; }
                .success { color: #46b450; }
                .warning { color: #f56e28; }
                .error { color: #dc3232; }
            </style>
        </head>
        <body>
            <h1>🔍 Customizer Debug Information</h1>
            
            <h2>Color Palette Settings</h2>
            <table>
                <tr>
                    <th>Setting Name</th>
                    <th>Saved Value</th>
                    <th>Status</th>
                </tr>
                <?php
                $color_settings = [
                    'color_palette_setting_0'  => 'Nav background colour',
                    'color_palette_setting_1'  => 'Nav menu item colour',
                    'color_palette_setting_3'  => 'Footer background colour',
                    'color_palette_setting_4'  => 'Footer text colour',
                    'color_palette_setting_5'  => 'Footer link colour',
                    'color_palette_setting_10' => 'Page background colour',
                ];
                
                foreach ( $color_settings as $setting => $label ) {
                    $value = get_theme_mod( $setting );
                    $status = $value ? '<span class="success">✓ Set</span>' : '<span class="warning">⚠ Empty</span>';
                    $display_value = $value ? '<span class="value">' . esc_html( $value ) . '</span>' : '<span class="empty">Not set</span>';
                    echo "<tr><td>{$label} ({$setting})</td><td>{$display_value}</td><td>{$status}</td></tr>";
                }
                ?>
            </table>
            
            <h2>Social Media Settings</h2>
            <table>
                <tr>
                    <th>Network</th>
                    <th>Enabled (Checkbox)</th>
                    <th>URL</th>
                    <th>Will Display?</th>
                </tr>
                <?php
                $social_networks = ['facebook', 'x', 'instagram', 'linkedin', 'pinterest', 'youtube', 'tiktok'];
                
                foreach ( $social_networks as $network ) {
                    $enabled = get_theme_mod( "social-{$network}", '' );
                    $url = get_theme_mod( "social-{$network}-url", '' );
                    
                    $enabled_display = $enabled === '1' 
                        ? '<span class="success">✓ Checked (value: "1")</span>' 
                        : '<span class="error">✗ Unchecked (value: "' . esc_html( $enabled ) . '")</span>';
                    
                    $url_display = !empty( $url ) 
                        ? '<span class="value">' . esc_html( $url ) . '</span>' 
                        : '<span class="empty">Empty</span>';
                    
                    $will_show = ( $enabled === '1' && !empty( $url ) ) 
                        ? '<span class="success">✓ YES</span>' 
                        : '<span class="error">✗ NO</span>';
                    
                    echo "<tr><td>" . ucfirst( $network ) . "</td><td>{$enabled_display}</td><td>{$url_display}</td><td>{$will_show}</td></tr>";
                }
                ?>
            </table>
            
            <h2>Other Settings</h2>
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <?php
                $other_settings = [
                    'header_logo' => 'Header Logo',
                    'header_background_image' => 'Header Background Image',
                    'footer_background_image' => 'Footer Background Image',
                    'contained_header' => 'Contained Header',
                    'sticky_header' => 'Sticky Header',
                    'fixed_header' => 'Fixed Header',
                    'transparent_header' => 'Transparent Header',
                    'dark_mode' => 'Dark Mode',
                ];
                
                foreach ( $other_settings as $setting => $label ) {
                    $value = get_theme_mod( $setting );
                    $display_value = $value ? '<span class="value">' . esc_html( $value ) . '</span>' : '<span class="empty">Not set</span>';
                    echo "<tr><td>{$label} ({$setting})</td><td>{$display_value}</td></tr>";
                }
                ?>
            </table>
            
            <h2>Repeater Fields</h2>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Data</th>
                </tr>
                <?php
                $footer_links = get_theme_mod( 'footer_links' );
                $opening_times = get_theme_mod( 'opening_times' );
                
                echo "<tr><td>Footer Links</td><td><pre>" . esc_html( $footer_links ? $footer_links : 'Empty' ) . "</pre></td></tr>";
                echo "<tr><td>Opening Times</td><td><pre>" . esc_html( $opening_times ? $opening_times : 'Empty' ) . "</pre></td></tr>";
                ?>
            </table>
            
            <h2>CSS Output Test</h2>
            <p>This is what should be in your &lt;head&gt;:</p>
            <pre style="background: white; padding: 20px; border: 1px solid #ddd;"><?php
            ob_start();
            avidd_customizer_css();
            $css_output = ob_get_clean();
            echo esc_html( $css_output );
            ?></pre>
            
            <p><a href="<?php echo home_url(); ?>">← Back to site</a></p>
        </body>
        </html>
        <?php
        exit;
    }
});