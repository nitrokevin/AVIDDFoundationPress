<?php
/**
 * Get color and gradient choices from theme.json for ACF / Kirki / templates
 *
 * @param array $options Options:
 *   'include_colors'   => bool, default true
 *   'include_gradients'=> bool, default true
 *   'use_css_value'    => bool, default false; for gradients, return CSS value instead of slug
 *   'for_kirki'        => bool, default false; if true, returns choices formatted for Kirki
 *   'for_acf'          => bool, default false; if true, returns choices formatted for ACF swatch fields with gradients
 *
 * @return array Flat associative array suitable for ACF 'choices' (HEX or slug keys => Name) or plain array for Kirki
 */

function get_theme_design_choices($options = []) {
    $defaults = [
        'include_colors' => true,
        'include_gradients' => true,
        'use_css_value' => false,
        'for_kirki' => false,
        'for_acf' => false,
    ];

    $options = array_merge($defaults, $options);
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    $choices_for_editor = [];

    if (!file_exists($theme_json_path)) {
        return $choices_for_editor;
    }

    $json = json_decode(file_get_contents($theme_json_path), true);

    // Colors
    if ($options['include_colors'] && isset($json['settings']['color']['palette'])) {
        foreach ($json['settings']['color']['palette'] as $color) {
            if ($options['for_acf'] && $options['use_css_value']) {
                // For ACF swatch fields with gradients and colors, use HEX => HEX
                if (isset($color['color']) && preg_match('/^#([a-f0-9]{3}){1,2}$/i', $color['color'])) {
                    $choices_for_editor[$color['color']] = $color['color'];
                }
            } else {
                $key = $color['color'] ?? sanitize_title($color['slug'] ?? '');
                if ($key) $choices_for_editor[$key] = $color['name'];
            }
        }
        if ($options['for_acf'] && $options['use_css_value'] && empty($choices_for_editor)) {
            $choices_for_editor['#000000'] = '#000000';
        }
    } elseif ($options['for_acf'] && $options['use_css_value']) {
        $choices_for_editor['#000000'] = '#000000';
    }

    // Gradients
    if ($options['include_gradients'] && isset($json['settings']['color']['gradients'])) {
        foreach ($json['settings']['color']['gradients'] as $gradient) {
            if ($options['for_acf'] && $options['use_css_value'] && isset($gradient['gradient'])) {
                // For ACF swatch fields with gradients, return CSS => CSS
                $choices_for_editor[$gradient['gradient']] = $gradient['gradient'];
            } elseif ($options['for_acf']) {
                // For ACF swatch fields without use_css_value, return slug => name
                $slug = sanitize_title($gradient['slug'] ?? '');
                if ($slug) $choices_for_editor[$slug] = $gradient['name'];
            } elseif ($options['for_kirki']) {
                $slug = sanitize_title($gradient['slug'] ?? '');
                if ($slug) $choices_for_editor[$slug] = $gradient['name'];
            } elseif ($options['use_css_value']) {
                $slug = sanitize_title($gradient['slug'] ?? '');
                if ($slug && isset($gradient['gradient'])) {
                    $choices_for_editor[$slug] = $gradient['gradient'];
                }
            } else {
                $slug = sanitize_title($gradient['slug'] ?? '');
                if ($slug) $choices_for_editor[$slug] = $gradient['name'];
            }
        }
    }

    return $choices_for_editor;
}

// ACF filter to convert HEX or gradient CSS value to slug when returning the value for swatch fields
add_filter('acf/format_value/type=swatch', function($value, $post_id, $field) {
    if (empty($value)) {
        return $value;
    }

    if (!preg_match('/^#([a-f0-9]{3}){1,2}$/i', $value) && strpos($value, 'gradient(') === false) {
        return $value;
    }

    $slug_map = [];
    $theme_json_path = get_stylesheet_directory() . '/theme.json';
    if (file_exists($theme_json_path)) {
        $json = json_decode(file_get_contents($theme_json_path), true);

        // Colors
        if (isset($json['settings']['color']['palette'])) {
            foreach ($json['settings']['color']['palette'] as $color) {
                if (!empty($color['color'])) {
                    $slug = sanitize_title($color['slug'] ?? '');
                    if ($slug) {
                        $slug_map[$color['color']] = $slug;
                    }
                }
            }
        }

        // Gradients
        if (isset($json['settings']['color']['gradients'])) {
            foreach ($json['settings']['color']['gradients'] as $gradient) {
                $slug = sanitize_title($gradient['slug'] ?? '');
                if ($slug && isset($gradient['gradient'])) {
                    $slug_map[$gradient['gradient']] = $slug;
                }
            }
        }
    }

    return $slug_map[$value] ?? $value;
}, 10, 3);
?>