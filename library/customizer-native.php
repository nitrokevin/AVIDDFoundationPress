<?php

/**
 * Native WordPress Customizer
 *
 * Handles live-preview settings only:
 * - Colour palette controls (with optional gradient support)
 * - Hero overlay opacity
 * - CSS output
 *
 * Non-preview settings (branding, content, company info, etc.)
 * are managed in Appearance > Site Settings (options-page.php).
 */

require_once get_template_directory() . '/library/colors.php';

// ============================================
// HELPER FUNCTIONS
// ============================================

if (! function_exists('get_native_palette')) {
	/**
	 * Returns an array of hex colour values from the theme palette.
	 * Used for text/foreground colour controls — no gradients.
	 */
	function get_native_palette()
	{
		$choices = get_theme_design_choices([
			'include_colors'    => true,
			'include_gradients' => false,
			'for_kirki'         => true,
		]);
		return (is_array($choices) && count($choices) > 0) ? array_keys($choices) : ['#000000'];
	}
}

if (! function_exists('get_native_palette_with_gradients')) {
	/**
	 * Returns an array of hex colour values AND gradient slugs from the theme.
	 * Used for background colour controls.
	 */
	function get_native_palette_with_gradients()
	{
		$choices = get_theme_design_choices([
			'include_colors'    => true,
			'include_gradients' => true,
			'for_kirki'         => true,
		]);
		return (is_array($choices) && count($choices) > 0) ? array_keys($choices) : ['#000000'];
	}
}

if (! function_exists('avidd_get_palette_hex_default')) {
	function avidd_get_palette_hex_default($var_name, $fallback = '#000000')
	{
		$choices = get_theme_design_choices([
			'include_colors'    => true,
			'include_gradients' => false,
			'for_kirki'         => true,
		]);
		$first_hex = false;
		foreach ($choices as $hex => $name) {
			if (! $first_hex) {
				$first_hex = $hex;
			}
			if (is_string($var_name) && $name && stripos($var_name, $name) !== false) {
				return $hex;
			}
		}
		if (function_exists('get_theme_color_palette')) {
			$palette = get_theme_color_palette();
			if (isset($palette[$var_name])) {
				return $palette[$var_name];
			}
		}
		return $first_hex ? $first_hex : $fallback;
	}
}

if (! function_exists('avidd_resolve_slug_to_css')) {
	/**
	 * Resolves a stored theme_mod value (hex or gradient slug) to a CSS value.
	 *
	 * - Hex values are returned as-is.
	 * - Gradient slugs are resolved to their full CSS gradient string.
	 * - Colour slugs are resolved to their hex value (edge case fallback).
	 *
	 * @param  string $value Stored theme_mod value.
	 * @return string CSS value ready for output, or empty string.
	 */
	function avidd_resolve_slug_to_css($value)
	{
		if (empty($value)) {
			return '';
		}

		if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $value)) {
			return $value;
		}

		$settings = wp_get_global_settings();

		$gradients = $settings['color']['gradients']['theme'] ?? [];
		foreach ($gradients as $gradient) {
			if (sanitize_title($gradient['slug'] ?? '') === $value) {
				return $gradient['gradient'] ?? '';
			}
		}

		$palette = $settings['color']['palette']['theme'] ?? [];
		foreach ($palette as $color) {
			if (sanitize_title($color['slug'] ?? '') === $value) {
				return $color['color'] ?? '';
			}
		}

		return $value;
	}
}

// ============================================
// CUSTOM CONTROLS
// ============================================

if (class_exists('WP_Customize_Control')) {

	/**
	 * Options Page Link Control
	 *
	 * Renders a button linking to a related options sub-page.
	 * Used at the bottom of customiser sections that have been partially
	 * migrated to the ACF options pages.
	 */
	class Avidd_Options_Link_Control extends WP_Customize_Control
	{
		public $type        = 'options-link';
		public $button_text = '';
		public $options_url = '';

		public function render_content()
		{
			if (empty($this->options_url)) {
				return;
			}
			$label = ! empty($this->button_text)
				? $this->button_text
				: __('More settings', 'avidd');
?>
			<div class="avidd-options-link">
				<a
					href="<?php echo esc_url($this->options_url); ?>"
					class="button button-secondary avidd-options-link__btn"
					target="_blank"
					rel="noopener">
					<?php echo esc_html($label); ?>
					<span class="dashicons dashicons-external" style="vertical-align: middle; margin-left: 4px;"></span>
				</a>
			</div>
			<style>
				.avidd-options-link {
					margin-top: 12px;
					padding-top: 12px;
					border-top: 1px solid #ddd;
				}

				.avidd-options-link__btn {
					display: inline-flex;
					align-items: center;
					gap: 4px;
				}
			</style>
		<?php
		}
	}

	/**
	 * Color Palette Control
	 *
	 * Renders a row of colour/gradient swatches as radio inputs.
	 *
	 * Props:
	 *   $palette           array   Explicit list of hex values or gradient slugs.
	 *                              If empty, auto-populated based on $include_gradients.
	 *   $style             string  'round' or 'square'. Gradient swatches are always square.
	 *   $allow_clear       bool    Show a clear/none option.
	 *   $include_gradients bool    When true and $palette is empty, includes gradient slugs
	 *                              from theme.json. Use for background controls only.
	 *
	 * Stored value: hex string (e.g. '#1a1a2e') for colours,
	 *               slug string (e.g. 'primary-radial') for gradients.
	 */
	class Avidd_Color_Palette_Control extends WP_Customize_Control
	{
		public $type               = 'color-palette';
		public $palette            = array();
		public $style              = 'round';
		public $allow_clear        = true;
		public $include_gradients  = false;

		public function render_content()
		{
			if (! empty($this->palette)) {
				$palette = $this->palette;
			} elseif ($this->include_gradients) {
				$palette = get_native_palette_with_gradients();
			} else {
				$palette = get_native_palette();
			}

			$settings = wp_get_global_settings();
			$css_map  = [];

			foreach ($settings['color']['palette']['theme'] ?? [] as $color) {
				$hex = $color['color'] ?? '';
				if ($hex) {
					$css_map[$hex] = $hex;
				}
			}

			foreach ($settings['color']['gradients']['theme'] ?? [] as $gradient) {
				$slug = sanitize_title($gradient['slug'] ?? '');
				if ($slug && ! empty($gradient['gradient'])) {
					$css_map[$slug] = $gradient['gradient'];
				}
			}

			$unique_id = 'color_palette_' . str_replace('-', '_', $this->id);
		?>
			<label>
				<?php if (! empty($this->label)) : ?>
					<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<?php endif; ?>
				<?php if (! empty($this->description)) : ?>
					<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php endif; ?>
			</label>
			<div class="color-palette-container" data-style="<?php echo esc_attr($this->style); ?>" data-setting-id="<?php echo esc_attr($this->id); ?>">

				<?php if ($this->allow_clear) : ?>
					<label class="color-palette-item clear-item <?php echo $this->style === 'round' ? 'round' : 'square'; ?>" title="<?php esc_attr_e('Clear selection', 'avidd'); ?>">
						<input
							type="radio"
							name="<?php echo esc_attr($unique_id); ?>"
							value=""
							data-setting-id="<?php echo esc_attr($this->id); ?>"
							<?php checked($this->value(), ''); ?> />
						<span class="color-swatch clear-swatch">
							<span class="dashicons dashicons-no"></span>
						</span>
					</label>
				<?php endif; ?>

				<?php foreach ($palette as $slug_or_hex) :
					$css_value   = $css_map[$slug_or_hex] ?? $slug_or_hex;
					$is_gradient = strpos($css_value, 'gradient(') !== false;
					$bg_style    = $is_gradient
						? 'background: ' . esc_attr($css_value) . ';'
						: 'background-color: ' . esc_attr($css_value) . ';';
					$item_class  = implode(' ', array_filter([
						'color-palette-item',
						$this->style === 'round' ? 'round' : 'square',
						$is_gradient ? 'is-gradient' : '',
					]));
				?>
					<label class="<?php echo esc_attr($item_class); ?>">
						<input
							type="radio"
							name="<?php echo esc_attr($unique_id); ?>"
							value="<?php echo esc_attr($slug_or_hex); ?>"
							data-setting-id="<?php echo esc_attr($this->id); ?>"
							<?php checked($this->value(), $slug_or_hex); ?> />
						<span class="color-swatch" style="<?php echo $bg_style; ?>"></span>
					</label>
				<?php endforeach; ?>

			</div>
			<style>
				.color-palette-container {
					display: flex;
					flex-wrap: wrap;
					gap: 8px;
					margin-top: 8px;
				}

				.color-palette-item {
					cursor: pointer;
					position: relative;
				}

				.color-palette-item input[type="radio"] {
					position: absolute;
					opacity: 0;
					width: 0;
					height: 0;
				}

				.color-palette-item .color-swatch {
					display: block;
					width: 30px;
					height: 30px;
					border: 2px solid #ddd;
					transition: all 0.2s;
				}

				.color-palette-item.round .color-swatch {
					border-radius: 50%;
				}

				.color-palette-item.square .color-swatch {
					border-radius: 3px;
				}

				.color-palette-item.is-gradient .color-swatch {
					border-radius: 50% !important;
					width: 30px;
					height: 30px;
				}

				.color-palette-item input[type="radio"]:checked+.color-swatch {
					border-color: #0073aa;
					box-shadow: 0 0 0 2px #0073aa;
					transform: scale(1.1);
				}

				.color-palette-item:hover .color-swatch {
					border-color: #0073aa;
				}

				.color-palette-item.clear-item .color-swatch {
					background: #fff !important;
					position: relative;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.color-palette-item.clear-item .color-swatch .dashicons {
					color: #dc3232;
					font-size: 20px;
					width: 20px;
					height: 20px;
				}

				.color-palette-item.clear-item input[type="radio"]:checked+.color-swatch {
					background: #f0f0f0 !important;
				}
			</style>
			<script type="text/javascript">
				(function($) {
					var settingId = '<?php echo esc_js($this->id); ?>';
					var container = $('.color-palette-container[data-setting-id="' + settingId + '"]');
					container.find('.color-palette-item input[type="radio"]').on('change', function() {
						wp.customize(settingId).set($(this).val());
					});
				})(jQuery);
			</script>
<?php
		}
	}
}

// ============================================
// CUSTOMIZER REGISTRATION
// ============================================

function avidd_customize_register($wp_customize)
{
	$palette_keys = get_native_palette();

	$default_1 = in_array(avidd_get_palette_hex_default('$primary-color', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('$primary-color', $palette_keys[0])
		: $palette_keys[0];
	$default_footer = in_array(avidd_get_palette_hex_default('#fefefe', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('#fefefe', $palette_keys[0])
		: $palette_keys[0];
	$default_settings = in_array(avidd_get_palette_hex_default('#fefefe', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('#fefefe', $palette_keys[0])
		: $palette_keys[0];

	// Shared options page URLs — generated here so they're consistent across all link controls
	$options_base    = admin_url('admin.php?page=');
	$url_header      = $options_base . 'site-settings-header';
	$url_footer      = $options_base . 'site-settings-footer';
	$url_company     = $options_base . 'site-settings-company';
	$url_hero        = $options_base . 'site-settings-hero';
	$url_filters     = $options_base . 'site-settings-filters';
	$url_notifs      = $options_base . 'site-settings-notifications';

	// ============================================
	// PANELS
	// ============================================

	$wp_customize->add_panel('header_navigation_panel', array(
		'title'       => __('Header & Navigation', 'avidd'),
		'description' => __('Customise your site header and navigation colours. For branding and layout settings, see Site Settings.', 'avidd'),
		'priority'    => 30,
	));

	$wp_customize->add_panel('footer_panel', array(
		'title'       => __('Footer', 'avidd'),
		'description' => __('Customise footer colours. For footer content, see Site Settings.', 'avidd'),
		'priority'    => 40,
	));

	$wp_customize->add_panel('design_layout_panel', array(
		'title'       => __('Design & Layout', 'avidd'),
		'description' => __('Customise colours and hero overlay settings.', 'avidd'),
		'priority'    => 50,
	));

	// ============================================
	// SECTIONS
	// ============================================

	$wp_customize->add_section('navigation_colors_section', array(
		'title' => __('Navigation Colors', 'avidd'),
		'panel' => 'header_navigation_panel',
	));

	$wp_customize->add_section('footer_colors_section', array(
		'title' => __('Footer Colors', 'avidd'),
		'panel' => 'footer_panel',
	));

	$wp_customize->add_section('site_colors_section', array(
		'title' => __('Site Colors', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('front_hero_section', array(
		'title' => __('Front page hero', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('inner_hero_section', array(
		'title' => __('Inner page hero', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	// ============================================
	// NAVIGATION COLORS
	// ============================================

	// Nav background colour — supports gradients
	$wp_customize->add_setting('color_palette_setting_0', array(
		'default'           => $palette_keys[0],
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_0', array(
		'label'             => __('Nav background colour', 'avidd'),
		'section'           => 'navigation_colors_section',
		'style'             => 'round',
		'include_gradients' => true,
	)));

	// Nav menu item colour
	$wp_customize->add_setting('color_palette_setting_1', array(
		'default'           => $default_1,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_1', array(
		'label'   => __('Nav menu item colour', 'avidd'),
		'section' => 'navigation_colors_section',
		'palette' => $palette_keys,
		'style'   => 'round',
	)));

	// Link to header options page
	$wp_customize->add_setting('_nav_options_link', array('sanitize_callback' => '__return_empty_string'));
	$wp_customize->add_control(new Avidd_Options_Link_Control($wp_customize, '_nav_options_link', array(
		'section'     => 'navigation_colors_section',
		'button_text' => __('Header & branding settings →', 'avidd'),
		'options_url' => $url_header,
	)));

	// ============================================
	// FOOTER COLORS
	// ============================================

	// Footer background colour
	$wp_customize->add_setting('color_palette_setting_3', array(
		'default'           => $default_footer,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_3', array(
		'label'   => __('Footer background colour', 'avidd'),
		'section' => 'footer_colors_section',
		'palette' => $palette_keys,
		'style'   => 'round',
	)));

	// Footer text colour
	$wp_customize->add_setting('color_palette_setting_4', array(
		'default'           => $default_1,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_4', array(
		'label'   => __('Footer text colour', 'avidd'),
		'section' => 'footer_colors_section',
		'palette' => $palette_keys,
		'style'   => 'round',
	)));

	// Footer link colour
	$wp_customize->add_setting('color_palette_setting_5', array(
		'default'           => $default_1,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_5', array(
		'label'   => __('Footer link colour', 'avidd'),
		'section' => 'footer_colors_section',
		'palette' => $palette_keys,
		'style'   => 'round',
	)));

	// Link to footer options page
	$wp_customize->add_setting('_footer_options_link', array('sanitize_callback' => '__return_empty_string'));
	$wp_customize->add_control(new Avidd_Options_Link_Control($wp_customize, '_footer_options_link', array(
		'section'     => 'footer_colors_section',
		'button_text' => __('Footer content settings →', 'avidd'),
		'options_url' => $url_footer,
	)));

	// ============================================
	// SITE COLORS
	// ============================================

	// Page background colour
	$wp_customize->add_setting('color_palette_setting_10', array(
		'default'           => $default_settings,
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_10', array(
		'label'   => __('Page background colour', 'avidd'),
		'section' => 'site_colors_section',
		'palette' => $palette_keys,
		'style'   => 'round',
	)));

	// Link to header options page (dark mode lives there now)
	$wp_customize->add_setting('_site_colors_options_link', array('sanitize_callback' => '__return_empty_string'));
	$wp_customize->add_control(new Avidd_Options_Link_Control($wp_customize, '_site_colors_options_link', array(
		'section'     => 'site_colors_section',
		'button_text' => __('Dark mode & branding settings →', 'avidd'),
		'options_url' => $url_header,
	)));

	// ============================================
	// FRONT PAGE HERO
	// ============================================

	$wp_customize->add_setting('hero_overlay_opacity', array(
		'default'           => 40,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control('hero_overlay_opacity', array(
		'label'       => __('Hero image overlay opacity', 'avidd'),
		'description' => __('Controls the darkness of the overlay on the hero image (0 = none, 100 = fully dark).', 'avidd'),
		'section'     => 'front_hero_section',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 100,
			'step' => 5,
		),
	));

	// Link to hero options page
	$wp_customize->add_setting('_front_hero_options_link', array('sanitize_callback' => '__return_empty_string'));
	$wp_customize->add_control(new Avidd_Options_Link_Control($wp_customize, '_front_hero_options_link', array(
		'section'     => 'front_hero_section',
		'button_text' => __('Hero layout settings →', 'avidd'),
		'options_url' => $url_hero,
	)));

	// ============================================
	// INNER PAGE HERO
	// ============================================

	$wp_customize->add_setting('inner_hero_overlay_opacity', array(
		'default'           => 40,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control('inner_hero_overlay_opacity', array(
		'label'       => __('Hero image overlay opacity', 'avidd'),
		'description' => __('Controls the darkness of the overlay on the hero image (0 = none, 100 = fully dark).', 'avidd'),
		'section'     => 'inner_hero_section',
		'type'        => 'range',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 100,
			'step' => 5,
		),
	));

	// Link to hero options page
	$wp_customize->add_setting('_inner_hero_options_link', array('sanitize_callback' => '__return_empty_string'));
	$wp_customize->add_control(new Avidd_Options_Link_Control($wp_customize, '_inner_hero_options_link', array(
		'section'     => 'inner_hero_section',
		'button_text' => __('Hero layout settings →', 'avidd'),
		'options_url' => $url_hero,
	)));
}
add_action('customize_register', 'avidd_customize_register');

// ============================================
// CSS OUTPUT
// ============================================

/**
 * Validate that a resolved CSS value is safe for output in a <style> block.
 *
 * Accepts:
 *  - Hex colours:              #rgb or #rrggbb
 *  - Linear/radial gradients:  linear-gradient(...) / radial-gradient(...)
 *
 * @param  string $value Resolved CSS value.
 * @return string Safe value, or empty string if rejected.
 */
function avidd_validate_css_value(string $value): string
{
	if (empty($value)) {
		return '';
	}

	if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $value)) {
		return $value;
	}

	if (preg_match('/^(linear|radial)-gradient\([^;{}()<>]+\)$/i', $value)) {
		return $value;
	}

	return '';
}

/**
 * Build a single CSS rule safely.
 *
 * @param string $selector  CSS selector (hardcoded/trusted).
 * @param string $property  CSS property name.
 * @param string $raw_value Raw resolved value — validated before output.
 */
function avidd_safe_css_rule(string $selector, string $property, string $raw_value): void
{
	$value = avidd_validate_css_value($raw_value);
	if (! $value) {
		return;
	}
	printf(
		'%s { %s: %s; }' . "\n",
		$selector,
		esc_html($property),
		esc_html($value)
	);
}

function avidd_customizer_css(): void
{
	$bg_rules = [
		['color_palette_setting_3',     '.footer'],
		['color_palette_setting_10',    'body'],
		['hero_trust_signals_bg',       '.front-hero--no-overlay + .hero_overlay--below'],
		['inner_hero_trust_signals_bg', '.featured-hero--no-overlay + .hero_overlay--below'],
	];

	$color_rules = [
		['color_palette_setting_1',  '.top-bar, .top-bar .desktop-menu a:not(.button), .title-bar .mobile-menu a:not(.button)', 'color'],
		['color_palette_setting_4',  '.footer, .footer li',                                                                      'color'],
		['color_palette_setting_5',  '.footer a',                                                                                'color'],
	];

	echo '<style type="text/css" id="avidd-customizer-styles">' . "\n";

	// Nav background — set directly on the elements, same as preview JS
	$nav_bg_raw       = avidd_resolve_slug_to_css(get_theme_mod('color_palette_setting_0'));
	$nav_bg_validated = avidd_validate_css_value($nav_bg_raw);
	if ($nav_bg_validated) {
		$nav_property = strpos($nav_bg_validated, 'gradient(') !== false ? 'background' : 'background-color';
		avidd_safe_css_rule('.top-bar, .title-bar', $nav_property, $nav_bg_validated);
	}

	// Background rules — value may be hex or gradient
	foreach ($bg_rules as [$mod_key, $selector]) {
		$raw = avidd_resolve_slug_to_css(get_theme_mod($mod_key));
		if (! $raw) {
			continue;
		}
		$property = (strpos($raw, 'gradient(') !== false) ? 'background' : 'background-color';
		avidd_safe_css_rule($selector, $property, $raw);
	}

	// Colour-only rules
	foreach ($color_rules as [$mod_key, $selector, $property]) {
		$raw = get_theme_mod($mod_key);
		if (! $raw) {
			continue;
		}
		avidd_safe_css_rule($selector, $property, $raw);
	}

	// Hero overlay opacities
	$front_opacity = get_theme_mod('hero_overlay_opacity', 40) / 100;
	printf('.front-hero .hero__bg-overlay { opacity: %s; }' . "\n", esc_attr($front_opacity));

	$inner_opacity = get_theme_mod('inner_hero_overlay_opacity', 40) / 100;
	printf('.featured-hero .hero__bg-overlay { opacity: %s; }' . "\n", esc_attr($inner_opacity));

	echo '</style>' . "\n";
}
add_action('wp_head', 'avidd_customizer_css');
