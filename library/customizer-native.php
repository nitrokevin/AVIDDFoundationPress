<?php

/**
 * Complete Native WordPress Customizer - Kirki Replacement
 *
 * This file provides a complete replacement for Kirki functionality including:
 * - Custom color palette controls (with optional gradient support)
 * - Repeater controls
 * - Live preview JavaScript
 * - CSS output
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

		// Already a hex — return as-is
		if (preg_match('/^#([a-f0-9]{3}){1,2}$/i', $value)) {
			return $value;
		}

		$settings = wp_get_global_settings();

		// Try to resolve as a gradient slug
		$gradients = $settings['color']['gradients']['theme'] ?? [];
		foreach ($gradients as $gradient) {
			if (sanitize_title($gradient['slug'] ?? '') === $value) {
				return $gradient['gradient'] ?? '';
			}
		}

		// Try to resolve as a colour slug (edge case — colour controls store hex
		// directly, but handles any legacy or unusual values)
		$palette = $settings['color']['palette']['theme'] ?? [];
		foreach ($palette as $color) {
			if (sanitize_title($color['slug'] ?? '') === $value) {
				return $color['color'] ?? '';
			}
		}

		// Return as-is if unresolvable
		return $value;
	}
}

// Helper: Safely get repeater field data
if (! function_exists('avidd_get_repeater_data')) {
	function avidd_get_repeater_data($setting_id)
	{
		$data = get_theme_mod($setting_id);

		if (empty($data)) {
			return array();
		}

		if (is_string($data)) {
			$decoded = json_decode($data, true);
			return is_array($decoded) ? $decoded : array();
		}

		if (is_array($data)) {
			return $data;
		}

		return array();
	}
}

// ============================================
// CUSTOM CONTROLS
// ============================================

if (class_exists('WP_Customize_Control')) {

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
			// Resolve palette: explicit > gradient-aware auto > colours-only auto
			if (! empty($this->palette)) {
				$palette = $this->palette;
			} elseif ($this->include_gradients) {
				$palette = get_native_palette_with_gradients();
			} else {
				$palette = get_native_palette();
			}

			// Build a lookup map: stored value => CSS value for swatch rendering
			$settings = wp_get_global_settings();
			$css_map  = [];

			// Colours: stored as hex, css value = hex
			foreach ($settings['color']['palette']['theme'] ?? [] as $color) {
				$hex = $color['color'] ?? '';
				if ($hex) {
					$css_map[$hex] = $hex;
				}
			}

			// Gradients: stored as slug, css value = gradient string
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

				/* Gradient swatches: always square and slightly wider so the gradient is legible */
				.color-palette-item.is-gradient .color-swatch {
					border-radius: 3px !important;
					width: 40px;
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

				/* Clear button */
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
						var value = $(this).val();
						wp.customize(settingId).set(value);
					});
				})(jQuery);
			</script>
		<?php
		}
	}

	/**
	 * Repeater Control
	 */
	class Avidd_Repeater_Control extends WP_Customize_Control
	{
		public $type   = 'repeater';
		public $fields = array();

		public function render_content()
		{
			$raw_value = $this->value();

			if (is_string($raw_value)) {
				$value = json_decode($raw_value, true);
			} elseif (is_array($raw_value)) {
				$value = $raw_value;
			} else {
				$value = array();
			}

			if (! is_array($value)) {
				$value = array();
			}
		?>
			<label>
				<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
			</label>
			<div class="repeater-container" data-setting="<?php echo esc_attr($this->id); ?>">
				<div class="repeater-items">
					<?php foreach ($value as $index => $item) : ?>
						<?php $this->render_repeater_item($index, $item); ?>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button add-repeater-item"><?php esc_html_e('Add Item', 'avidd'); ?></button>
			</div>
			<style>
				.repeater-container {
					margin-top: 10px;
				}

				.repeater-item {
					background: #f9f9f9;
					padding: 15px;
					margin-bottom: 10px;
					border: 1px solid #ddd;
					position: relative;
				}

				.repeater-item-controls {
					margin-top: 10px;
				}

				.remove-repeater-item {
					color: #a00;
					text-decoration: none;
					position: absolute;
					top: 10px;
					right: 10px;
				}

				.repeater-item input[type="text"],
				.repeater-item input[type="url"],
				.repeater-item input[type="time"] {
					width: 100%;
					margin-top: 5px;
				}

				.repeater-item img {
					max-width: 100%;
					height: auto;
					margin-top: 10px;
				}

				.repeater-field {
					margin-bottom: 10px;
				}
			</style>
			<script type="text/javascript">
				(function($) {
					var container = $('.repeater-container[data-setting="<?php echo esc_js($this->id); ?>"]');
					var itemsContainer = container.find('.repeater-items');

					container.find('.add-repeater-item').on('click', function() {
						var index = itemsContainer.find('.repeater-item').length;
						var template = <?php echo json_encode($this->get_repeater_item_template()); ?>;
						itemsContainer.append(template.replace(/INDEX/g, index));
						updateRepeaterValue();
					});

					itemsContainer.on('click', '.remove-repeater-item', function(e) {
						e.preventDefault();
						$(this).closest('.repeater-item').remove();
						updateRepeaterValue();
					});

					itemsContainer.on('click', '.upload-image-button', function(e) {
						e.preventDefault();
						var button = $(this);
						var input = button.siblings('input[type="hidden"]');
						var preview = button.siblings('.image-preview');

						var mediaUploader = wp.media({
							title: 'Select Image',
							button: {
								text: 'Select'
							},
							multiple: false
						});

						mediaUploader.on('select', function() {
							var attachment = mediaUploader.state().get('selection').first().toJSON();
							input.val(attachment.id);
							preview.html('<img src="' + attachment.url + '" />');
							updateRepeaterValue();
						});

						mediaUploader.open();
					});

					itemsContainer.on('change', 'input, select, textarea', function() {
						updateRepeaterValue();
					});

					function updateRepeaterValue() {
						var items = [];
						itemsContainer.find('.repeater-item').each(function() {
							var item = {};
							$(this).find('input, select, textarea').each(function() {
								var name = $(this).attr('name');
								if (name) {
									item[name] = $(this).val();
								}
							});
							items.push(item);
						});
						wp.customize('<?php echo esc_js($this->id); ?>').set(JSON.stringify(items));
					}
				})(jQuery);
			</script>
		<?php
		}

		protected function render_repeater_item($index, $item)
		{
		?>
			<div class="repeater-item">
				<a href="#" class="remove-repeater-item">&#x2715;</a>
				<?php foreach ($this->fields as $field_id => $field) :
					$field_value = isset($item[$field_id]) ? $item[$field_id] : '';
					$field_type  = isset($field['type']) ? $field['type'] : 'text';
					$field_label = isset($field['label']) ? $field['label'] : ucfirst($field_id);
				?>
					<div class="repeater-field">
						<label>
							<?php echo esc_html($field_label); ?>
							<?php if ($field_type === 'image') :
								$image_url = $field_value ? wp_get_attachment_url($field_value) : '';
							?>
								<input type="hidden" name="<?php echo esc_attr($field_id); ?>" value="<?php echo esc_attr($field_value); ?>" />
								<button type="button" class="button upload-image-button"><?php esc_html_e('Select Image', 'avidd'); ?></button>
								<div class="image-preview">
									<?php if ($image_url) : ?>
										<img src="<?php echo esc_url($image_url); ?>" />
									<?php endif; ?>
								</div>
							<?php else : ?>
								<input
									type="<?php echo esc_attr($field_type); ?>"
									name="<?php echo esc_attr($field_id); ?>"
									value="<?php echo esc_attr($field_value); ?>" />
							<?php endif; ?>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
		<?php
		}

		protected function get_repeater_item_template()
		{
			ob_start();
		?>
			<div class="repeater-item">
				<a href="#" class="remove-repeater-item">&#x2715;</a>
				<?php foreach ($this->fields as $field_id => $field) :
					$field_type  = isset($field['type']) ? $field['type'] : 'text';
					$field_label = isset($field['label']) ? $field['label'] : ucfirst($field_id);
				?>
					<div class="repeater-field">
						<label>
							<?php echo esc_html($field_label); ?>
							<?php if ($field_type === 'image') : ?>
								<input type="hidden" name="<?php echo esc_attr($field_id); ?>" value="" />
								<button type="button" class="button upload-image-button"><?php esc_html_e('Select Image', 'avidd'); ?></button>
								<div class="image-preview"></div>
							<?php else : ?>
								<input
									type="<?php echo esc_attr($field_type); ?>"
									name="<?php echo esc_attr($field_id); ?>"
									value="" />
							<?php endif; ?>
						</label>
					</div>
				<?php endforeach; ?>
			</div>
	<?php
			return ob_get_clean();
		}
	}
}

// ============================================
// CUSTOMIZER REGISTRATION
// ============================================

function avidd_customize_register($wp_customize)
{

	// Colour-only palette (hex values) — for text/foreground controls
	$palette_keys = get_native_palette();

	// Colour + gradient palette (hex values + gradient slugs) — for background controls
	$palette_keys_with_gradients = get_native_palette_with_gradients();

	$default_1 = in_array(avidd_get_palette_hex_default('$primary-color', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('$primary-color', $palette_keys[0])
		: $palette_keys[0];
	$default_footer = in_array(avidd_get_palette_hex_default('#fefefe', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('#fefefe', $palette_keys[0])
		: $palette_keys[0];
	$default_settings = in_array(avidd_get_palette_hex_default('#fefefe', $palette_keys[0]), $palette_keys)
		? avidd_get_palette_hex_default('#fefefe', $palette_keys[0])
		: $palette_keys[0];

	// ============================================
	// PANELS
	// ============================================

	$wp_customize->add_panel('header_navigation_panel', array(
		'title'       => __('Header & Navigation', 'avidd'),
		'description' => __('Customize your site header, logo, and navigation.', 'avidd'),
		'priority'    => 30,
	));

	$wp_customize->add_panel('footer_panel', array(
		'title'       => __('Footer', 'avidd'),
		'description' => __('Customize your site footer.', 'avidd'),
		'priority'    => 40,
	));

	$wp_customize->add_panel('design_layout_panel', array(
		'title'       => __('Design & Layout', 'avidd'),
		'description' => __('Customize colors, typography, and layout.', 'avidd'),
		'priority'    => 50,
	));

	$wp_customize->add_panel('company_information_panel', array(
		'title'       => __('Company Information', 'avidd'),
		'description' => __('Manage contact information, social media, and opening hours.', 'avidd'),
		'priority'    => 60,
	));

	$wp_customize->add_panel('notifications_panel', array(
		'title'       => __('Notifications', 'avidd'),
		'description' => __('Manage site notifications.', 'avidd'),
		'priority'    => 70,
	));

	// ============================================
	// SECTIONS
	// ============================================

	// Header & Navigation
	$wp_customize->add_section('site_header_section', array(
		'title' => __('Header Settings', 'avidd'),
		'panel' => 'header_navigation_panel',
	));

	$wp_customize->add_section('navigation_colors_section', array(
		'title' => __('Navigation Colors', 'avidd'),
		'panel' => 'header_navigation_panel',
	));

	// Footer
	$wp_customize->add_section('footer_colors_section', array(
		'title' => __('Footer Colors', 'avidd'),
		'panel' => 'footer_panel',
	));

	$wp_customize->add_section('footer_content_section', array(
		'title' => __('Footer Content', 'avidd'),
		'panel' => 'footer_panel',
	));

	// Design & Layout
	$wp_customize->add_section('site_colors_section', array(
		'title' => __('Site Colors', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('default_content_section', array(
		'title' => __('Default content', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('front_hero_section', array(
		'title' => __('Front page hero', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('project_filters_section', array(
		'title' => __('Project filters', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_section('project_stats_section', array(
		'title'       => __('Project Stats Defaults', 'avidd'),
		'description' => __('Define the default stat fields pre-populated when creating a new project. Valid units: sqft, acres, apartments, stores, year, gbp, or any custom text.', 'avidd'),
		'panel'       => 'design_layout_panel',
	));

	// Company Information
	$wp_customize->add_section('contact_section', array(
		'title' => __('Contact Details', 'avidd'),
		'panel' => 'company_information_panel',
	));

	$wp_customize->add_section('social_media_section', array(
		'title' => __('Social Media', 'avidd'),
		'panel' => 'company_information_panel',
	));

	$wp_customize->add_section('opening_times', array(
		'title' => __('Opening Times', 'avidd'),
		'panel' => 'company_information_panel',
	));

	// Notifications
	$wp_customize->add_section('notifications_section', array(
		'title' => __('Header Notifications', 'avidd'),
		'panel' => 'notifications_panel',
	));

	$wp_customize->add_section('email_notifications_section', array(
		'title' => __('Email Notifications', 'avidd'),
		'panel' => 'notifications_panel',
	));

	// ============================================
	// HEADER & NAVIGATION
	// ============================================

	// Header background image
	$wp_customize->add_setting('header_background_image', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'header_background_image', array(
		'label'     => __('Header background image', 'avidd'),
		'section'   => 'site_header_section',
		'mime_type' => 'image',
	)));

	// Header logo
	$wp_customize->add_setting('header_logo', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_logo', array(
		'label'   => __('Header logo', 'avidd'),
		'section' => 'site_header_section',
	)));

	// Contained Header
	$wp_customize->add_setting('contained_header', array(
		'default'           => 'on',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('contained_header', array(
		'label'   => __('Contained Header', 'avidd'),
		'section' => 'site_header_section',
		'type'    => 'checkbox',
	));

	// Sticky Header
	$wp_customize->add_setting('sticky_header', array(
		'default'           => 'on',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('sticky_header', array(
		'label'   => __('Sticky Header', 'avidd'),
		'section' => 'site_header_section',
		'type'    => 'checkbox',
	));

	// Fixed Header
	$wp_customize->add_setting('fixed_header', array(
		'default'           => 'on',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('fixed_header', array(
		'label'   => __('Sticky header over featured image', 'avidd'),
		'section' => 'site_header_section',
		'type'    => 'checkbox',
	));

	// Transparent Header
	$wp_customize->add_setting('transparent_header', array(
		'default'           => 'on',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('transparent_header', array(
		'label'   => __('Transparent header over hero section', 'avidd'),
		'section' => 'site_header_section',
		'type'    => 'checkbox',
	));

	// ----------------------------------------
	// Navigation Colors
	// ----------------------------------------

	// Nav background colour — supports gradients; sanitize_text_field to accept slugs
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

	// Nav menu item colour — text colour, no gradients
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

	// ============================================
	// FOOTER
	// ============================================

	// Footer background colour — supports gradients
	$wp_customize->add_setting('color_palette_setting_3', array(
		'default'           => $default_footer,
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_3', array(
		'label'             => __('Footer background colour', 'avidd'),
		'section'           => 'footer_colors_section',
		'style'             => 'round',
		'include_gradients' => true,
	)));

	// Footer text colour — no gradients
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

	// Footer link colour — no gradients
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

	// Footer background image
	$wp_customize->add_setting('footer_background_image', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'footer_background_image', array(
		'label'     => __('Footer background image', 'avidd'),
		'section'   => 'footer_content_section',
		'mime_type' => 'image',
	)));

	// Footer links repeater
	$wp_customize->add_setting('footer_links', array(
		'default'           => array(),
		'sanitize_callback' => 'avidd_sanitize_repeater',
	));
	$wp_customize->add_control(new Avidd_Repeater_Control($wp_customize, 'footer_links', array(
		'label'   => __('Footer images', 'avidd'),
		'section' => 'footer_content_section',
		'fields'  => array(
			'footer_image' => array(
				'type'  => 'image',
				'label' => __('Footer Image', 'avidd'),
			),
			'link_url'     => array(
				'type'  => 'url',
				'label' => __('Link URL', 'avidd'),
			),
		),
	)));

	// ============================================
	// COMPANY INFORMATION
	// ============================================

	// Contact Phone Number
	$wp_customize->add_setting('contact_phone_number', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('contact_phone_number', array(
		'label'   => __('Phone Number', 'avidd'),
		'section' => 'contact_section',
		'type'    => 'tel',
	));

	// Contact Email
	$wp_customize->add_setting('contact_email', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_email',
	));
	$wp_customize->add_control('contact_email', array(
		'label'   => __('Email Address', 'avidd'),
		'section' => 'contact_section',
		'type'    => 'email',
	));

	// Address Lines
	for ($i = 1; $i <= 6; $i++) {
		$wp_customize->add_setting("contact_address_{$i}", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control("contact_address_{$i}", array(
			'label'   => sprintf(__('Address Line %d', 'avidd'), $i),
			'section' => 'contact_section',
			'type'    => 'text',
		));
	}

	// Footer Company Number
	$wp_customize->add_setting('footer_company_number', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('footer_company_number', array(
		'label'   => __('Company Number', 'avidd'),
		'section' => 'contact_section',
		'type'    => 'text',
	));

	// Footer Copyright
	$wp_customize->add_setting('footer_copyright', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('footer_copyright', array(
		'label'   => __('Copyright Text', 'avidd'),
		'section' => 'contact_section',
		'type'    => 'text',
	));

	// Social Media
	$social_networks = array(
		'instagram' => array('label' => 'Instagram', 'default' => 'https://instagram.com/'),
		'facebook'  => array('label' => 'Facebook',  'default' => 'https://facebook.com/'),
		'x'         => array('label' => 'X',         'default' => 'https://x.com/'),
		'linkedin'  => array('label' => 'LinkedIn',  'default' => 'https://linkedin.com/'),
		'pinterest' => array('label' => 'Pinterest', 'default' => 'https://pinterest.com/'),
		'youtube'   => array('label' => 'YouTube',   'default' => 'https://youtube.com/'),
		'tiktok'    => array('label' => 'TikTok',    'default' => 'https://tiktok.com/'),
	);

	foreach ($social_networks as $network => $data) {
		$wp_customize->add_setting("social-{$network}", array(
			'default'           => '',
			'sanitize_callback' => function ($checked) {
				return ((isset($checked) && true == $checked) ? '1' : '');
			},
		));
		$wp_customize->add_control("social-{$network}", array(
			'label'   => $data['label'],
			'section' => 'social_media_section',
			'type'    => 'checkbox',
		));

		$wp_customize->add_setting("social-{$network}-url", array(
			'default'           => $data['default'],
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control("social-{$network}-url", array(
			'label'           => sprintf(__('%s URL', 'avidd'), $data['label']),
			'section'         => 'social_media_section',
			'type'            => 'url',
			'active_callback' => function () use ($network) {
				return get_theme_mod("social-{$network}", '') === '1';
			},
		));
	}

	// Opening Times
	$wp_customize->add_setting('opening_times', array(
		'default'           => '',
		'sanitize_callback' => 'avidd_sanitize_repeater',
	));
	$wp_customize->add_control(new Avidd_Repeater_Control($wp_customize, 'opening_times', array(
		'label'   => __('Opening Hours', 'avidd'),
		'section' => 'opening_times',
		'fields'  => array(
			'day'          => array('type' => 'text', 'label' => __('Day', 'avidd')),
			'opening_time' => array('type' => 'time', 'label' => __('Opening Time', 'avidd')),
			'closing_time' => array('type' => 'time', 'label' => __('Closing Time', 'avidd')),
			'note'         => array('type' => 'text', 'label' => __('Note', 'avidd')),
		),
	)));

	$wp_customize->add_setting('special_opening_times', array(
		'default'           => '',
		'sanitize_callback' => 'avidd_sanitize_repeater',
	));
	$wp_customize->add_control(new Avidd_Repeater_Control($wp_customize, 'special_opening_times', array(
		'label'   => __('Special Opening Hours', 'avidd'),
		'section' => 'opening_times',
		'fields'  => array(
			'day'          => array('type' => 'text', 'label' => __('Day', 'avidd')),
			'opening_time' => array('type' => 'time', 'label' => __('Opening Time', 'avidd')),
			'closing_time' => array('type' => 'time', 'label' => __('Closing Time', 'avidd')),
			'note'         => array('type' => 'text', 'label' => __('Note', 'avidd')),
		),
	)));

	// ============================================
	// DESIGN & LAYOUT
	// ============================================

	// Page background colour — supports gradients
	$wp_customize->add_setting('color_palette_setting_10', array(
		'default'           => $default_settings,
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'color_palette_setting_10', array(
		'label'             => __('Page background colour', 'avidd'),
		'section'           => 'site_colors_section',
		'style'             => 'round',
		'include_gradients' => true,
	)));

	// Dark Mode
	$wp_customize->add_setting('dark_mode', array(
		'default'           => 'off',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('dark_mode', array(
		'label'   => __('Dark Mode', 'avidd'),
		'section' => 'site_colors_section',
		'type'    => 'checkbox',
	));

	// ============================================
	// FRONT PAGE HERO
	// ============================================

	$wp_customize->add_setting('hero_trust_signals_overlay', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('hero_trust_signals_overlay', array(
		'label'       => __('Overlay trust signals on hero image', 'avidd'),
		'description' => __('When enabled, the trust signals sit over the bottom of the hero. Disable to place them below.', 'avidd'),
		'section'     => 'front_hero_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('hero_full_height', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('hero_full_height', array(
		'label'       => __('Full height hero', 'avidd'),
		'description' => __('When disabled, the hero image is shorter. Recommended when trust signals are placed below.', 'avidd'),
		'section'     => 'front_hero_section',
		'type'        => 'checkbox',
	));

	// Trust signals background — supports gradients
	$wp_customize->add_setting('hero_trust_signals_bg', array(
		'default'           => $palette_keys[0],
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'hero_trust_signals_bg', array(
		'label'             => __('Trust signals background (when below hero)', 'avidd'),
		'section'           => 'front_hero_section',
		'style'             => 'round',
		'include_gradients' => true,
	)));

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

	// ============================================
	// INNER PAGE HERO
	// ============================================

	$wp_customize->add_section('inner_hero_section', array(
		'title' => __('Inner Page Hero', 'avidd'),
		'panel' => 'design_layout_panel',
	));

	$wp_customize->add_setting('inner_hero_trust_signals_overlay', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('inner_hero_trust_signals_overlay', array(
		'label'       => __('Overlay trust signals on hero image', 'avidd'),
		'description' => __('When enabled, trust signals sit over the bottom of the hero. Disable to place them below.', 'avidd'),
		'section'     => 'inner_hero_section',
		'type'        => 'checkbox',
	));

	$wp_customize->add_setting('inner_hero_full_height', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('inner_hero_full_height', array(
		'label'       => __('Full height hero', 'avidd'),
		'description' => __('When disabled, the hero image is shorter. Recommended when trust signals are placed below.', 'avidd'),
		'section'     => 'inner_hero_section',
		'type'        => 'checkbox',
	));

	// Inner trust signals background — supports gradients
	$wp_customize->add_setting('inner_hero_trust_signals_bg', array(
		'default'           => $palette_keys[0],
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control(new Avidd_Color_Palette_Control($wp_customize, 'inner_hero_trust_signals_bg', array(
		'label'             => __('Trust signals background (when below hero)', 'avidd'),
		'section'           => 'inner_hero_section',
		'style'             => 'round',
		'include_gradients' => true,
	)));

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

	// ============================================
	// PROJECT FILTERS
	// ============================================

	$wp_customize->add_setting('project_location_filter', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('project_location_filter', array(
		'label'   => __('Project Location Filter', 'avidd'),
		'section' => 'project_filters_section',
		'type'    => 'checkbox',
	));

	$wp_customize->add_setting('project_type_filter', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('project_type_filter', array(
		'label'   => __('Project Type Filter', 'avidd'),
		'section' => 'project_filters_section',
		'type'    => 'checkbox',
	));

	$wp_customize->add_setting('project_status_filter', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('project_status_filter', array(
		'label'   => __('Project Status Filter', 'avidd'),
		'section' => 'project_filters_section',
		'type'    => 'checkbox',
	));

	$wp_customize->add_setting('project_strategy_filter', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	));
	$wp_customize->add_control('project_strategy_filter', array(
		'label'   => __('Project Strategy Filter', 'avidd'),
		'section' => 'project_filters_section',
		'type'    => 'checkbox',
	));

	// ============================================
	// PROJECT STATS DEFAULTS
	// ============================================

	$wp_customize->add_setting('project_stats_defaults', array(
		'default'           => json_encode(array(
			array('label' => 'Site Area',         'unit' => 'acres'),
			array('label' => 'Office Space',      'unit' => 'sqft'),
			array('label' => 'Residential Space', 'unit' => 'apartments'),
			array('label' => 'Retail Space',      'unit' => 'stores'),
		)),
		'sanitize_callback' => 'avidd_sanitize_repeater',
	));
	$wp_customize->add_control(new Avidd_Repeater_Control($wp_customize, 'project_stats_defaults', array(
		'label'   => __('Default Stat Fields', 'avidd'),
		'section' => 'project_stats_section',
		'fields'  => array(
			'label' => array('type' => 'text', 'label' => __('Label', 'avidd')),
			'unit'  => array('type' => 'text', 'label' => __('Unit (sqft, acres, apartments, stores, year, gbp)', 'avidd')),
		),
	)));

	// ============================================
	// NOTIFICATIONS
	// ============================================

	$wp_customize->add_setting('notifications', array(
		'default'           => '',
		'sanitize_callback' => 'avidd_sanitize_repeater',
	));
	$wp_customize->add_control(new Avidd_Repeater_Control($wp_customize, 'notifications', array(
		'label'   => __('Notification messages', 'avidd'),
		'section' => 'notifications_section',
		'fields'  => array(
			'notification_header' => array('type' => 'text', 'label' => __('Header', 'avidd')),
			'notification_text'   => array('type' => 'text', 'label' => __('Text', 'avidd')),
			'notification_link'   => array('type' => 'url',  'label' => __('Link', 'avidd')),
		),
	)));

	// Email logo
	$wp_customize->add_setting('email_logo', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'email_logo', array(
		'label'   => __('Email logo', 'avidd'),
		'section' => 'email_notifications_section',
	)));
}
add_action('customize_register', 'avidd_customize_register');

// ============================================
// SANITIZATION
// ============================================

function avidd_sanitize_repeater($input)
{
	$decoded = json_decode($input, true);
	if (! is_array($decoded)) {
		return '';
	}
	array_walk_recursive($decoded, function (&$value, $key) {
		if (filter_var($value, FILTER_VALIDATE_URL)) {
			$value = esc_url_raw($value);
		} elseif (wp_strip_all_tags($value) !== $value) {
			$value = wp_kses_post($value);
		} else {
			$value = sanitize_text_field($value);
		}
	});
	return wp_json_encode($decoded);
}

// ============================================
// CSS OUTPUT
// ============================================

/**
 * Output customizer values as inline CSS.
 *
 * Background settings may store either a hex value or a gradient slug.
 * avidd_resolve_slug_to_css() handles both, returning the CSS-ready value.
 * The correct property (background-color vs background) is chosen based on
 * whether the resolved value contains 'gradient('.
 */
function avidd_customizer_css()
{
	?>
	<style type="text/css" id="avidd-customizer-styles">
		<?php

		// --- Nav background ---
		$nav_bg = avidd_resolve_slug_to_css(get_theme_mod('color_palette_setting_0'));
		if ($nav_bg) {
			$prop = strpos($nav_bg, 'gradient(') !== false ? 'background' : 'background-color';
			echo '.top-bar, .title-bar { ' . $prop . ': ' . esc_attr($nav_bg) . '; }';
		}

		// --- Nav menu item colour (text — hex only, no gradient resolution needed) ---
		$nav_color = get_theme_mod('color_palette_setting_1');
		if ($nav_color) {
			echo '.top-bar, .top-bar .desktop-menu a:not(.button), .title-bar .mobile-menu a:not(.button) { color: ' . esc_attr($nav_color) . '; }';
		}

		// --- Footer background ---
		$footer_bg = avidd_resolve_slug_to_css(get_theme_mod('color_palette_setting_3'));
		if ($footer_bg) {
			$prop = strpos($footer_bg, 'gradient(') !== false ? 'background' : 'background-color';
			echo '.footer { ' . $prop . ': ' . esc_attr($footer_bg) . '; }';
		}

		// --- Footer text colour (text — hex only) ---
		$footer_text = get_theme_mod('color_palette_setting_4');
		if ($footer_text) {
			echo '.footer, .footer li { color: ' . esc_attr($footer_text) . '; }';
		}

		// --- Footer link colour (text — hex only) ---
		$footer_link = get_theme_mod('color_palette_setting_5');
		if ($footer_link) {
			echo '.footer a { color: ' . esc_attr($footer_link) . '; }';
		}

		// --- Page background ---
		$page_bg = avidd_resolve_slug_to_css(get_theme_mod('color_palette_setting_10'));
		if ($page_bg) {
			$prop = strpos($page_bg, 'gradient(') !== false ? 'background' : 'background-color';
			echo 'body { ' . $prop . ': ' . esc_attr($page_bg) . '; }';
		}

		// --- Front page trust signals background ---
		$front_trust_bg = avidd_resolve_slug_to_css(get_theme_mod('hero_trust_signals_bg'));
		if ($front_trust_bg) {
			$prop = strpos($front_trust_bg, 'gradient(') !== false ? 'background' : 'background-color';
			echo '.front-hero--no-overlay + .hero_overlay--below { ' . $prop . ': ' . esc_attr($front_trust_bg) . '; }';
		}

		// --- Inner page trust signals background ---
		$inner_trust_bg = avidd_resolve_slug_to_css(get_theme_mod('inner_hero_trust_signals_bg'));
		if ($inner_trust_bg) {
			$prop = strpos($inner_trust_bg, 'gradient(') !== false ? 'background' : 'background-color';
			echo '.featured-hero--no-overlay + .hero_overlay--below { ' . $prop . ': ' . esc_attr($inner_trust_bg) . '; }';
		}

		?>
	</style>
<?php
}
add_action('wp_head', 'avidd_customizer_css');

// ============================================
// CUSTOMIZER PREVIEW JAVASCRIPT
// ============================================