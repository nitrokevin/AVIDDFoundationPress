<?php

/**
 * Enqueue all styles and scripts
 *
 * Learn more about enqueue_script: {@link https://codex.wordpress.org/Function_Reference/wp_enqueue_script}
 * Learn more about enqueue_style: {@link https://codex.wordpress.org/Function_Reference/wp_enqueue_style }
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */


// Check to see if rev-manifest exists for CSS and JS static asset revisioning
//https://github.com/sindresorhus/gulp-rev/blob/master/integration.md

if (! function_exists('foundationpress_asset_path')) :
	function foundationpress_asset_path($filename)
	{
		static $manifests = [];

		$ext           = pathinfo($filename, PATHINFO_EXTENSION);
		$manifest_path = dirname(dirname(__FILE__)) . '/dist/assets/' . $ext . '/rev-manifest.json';

		if (! isset($manifests[$manifest_path])) {
			$manifests[$manifest_path] = file_exists($manifest_path)
				? json_decode(file_get_contents($manifest_path), true) ?? []
				: [];
		}

		return $manifests[$manifest_path][$filename] ?? $filename;
	}
endif;


if (! function_exists('foundationpress_scripts')) :
	function foundationpress_scripts()
	{

		// Enqueue the main Stylesheet.
		wp_enqueue_style('main-stylesheet', get_stylesheet_directory_uri() . '/dist/assets/css/' . foundationpress_asset_path('app.css'), array(), '1.0.0', 'all');
		wp_enqueue_style(
			'google-font',
			'https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;600&display=swap',
			[],
			null,
			'all'
		);


		// Enqueue Foundation scripts
		wp_enqueue_script('foundation', get_stylesheet_directory_uri() . '/dist/assets/js/' . foundationpress_asset_path('app.js'), array('jquery'), '2.10.4', true);

		// Enqueue FontAwesome from CDN. Uncomment the line below if you need FontAwesome.
	
		if (defined('AVIDD_FA_KIT_URL') && AVIDD_FA_KIT_URL) {
			wp_enqueue_script('font-awesome-kit', AVIDD_FA_KIT_URL, [], null, true);
			// Kit scripts must be treated as crossorigin
			add_filter('script_loader_tag', function (string $tag, string $handle): string {
				if ($handle === 'font-awesome-kit') {
					return str_replace('<script ', '<script crossorigin="anonymous" ', $tag);
				}
				return $tag;
			}, 10, 2);
		}

		// Add the comment-reply library on pages where it is necessary
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}

	add_action('wp_enqueue_scripts', 'foundationpress_scripts');
endif;

// Block editor scripts.
if (! function_exists('foundationpress_editor_scripts')) :
	function foundationpress_editor_scripts()
	{
		wp_enqueue_script(
			'foundation-editor',
			get_template_directory_uri() . '/dist/assets/js/editor.js',
			array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-hooks', 'wp-compose', 'wp-data', 'wp-i18n'),
			null,
			true
		);
	}
	add_action('enqueue_block_editor_assets', 'foundationpress_editor_scripts');
endif;


// Customizer live preview.
add_action('customize_preview_init', function () {
	wp_enqueue_script(
		'avidd-customizer-preview',
		get_template_directory_uri() . '/dist/assets/js/customizer-preview.js',
		array('jquery'),
		null,
		true
	);

	// Inject gradient slug => CSS map for live preview resolution
	$settings  = wp_get_global_settings();
	$gradients = $settings['color']['gradients']['theme'] ?? [];
	$map       = [];

	foreach ($gradients as $gradient) {
		$slug = sanitize_title($gradient['slug'] ?? '');
		if ($slug && ! empty($gradient['gradient'])) {
			$map[$slug] = $gradient['gradient'];
		}
	}

	wp_localize_script('avidd-customizer-preview', 'aviddGradients', $map);
});
