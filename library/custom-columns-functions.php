<?php
/**
 * Column Background + XY Grid Integration
 * Add this code to your theme's functions.php file
 */

/**
 * Enqueue Foundation XY Grid CSS and custom styles
 */
function foundationpress_column_bg_enqueue_assets() {
    // Custom inline styles for column backgrounds and XY grid
    wp_add_inline_style('wp-block-library', '
        /* Fixed-width Foundation columns (1–12) */
        .wp-block-columns.grid-margin-x {
         
        }
        .wp-block-columns.grid-x .wp-block-column.cbg-xy-grid{
            flex-grow: unset;
            flex-basis: unset;
        

        }
            .wp-block-columns.grid-x .wp-block-column .wp-block {
    max-width: none !important;
    width: auto !important;
}

        /* Auto columns should fill remaining space equally */
        .wp-block-columns.grid-x .wp-block-column.cbg-xy-grid:not[class*="-auto"] {
            flex: unset;
        }

        .wp-block-column.has-background-image {
            background-image: var(--column-bg);
            background-size: cover;
            background-position: center;
        }
    ');
}
add_action('enqueue_block_assets', 'foundationpress_column_bg_enqueue_assets');

/**
 * Enqueue editor JavaScript
 */
function foundationpress_column_bg_enqueue_editor_assets() {
    $theme_dir = get_stylesheet_directory();
    $asset_path = $theme_dir . '/dist/assets/js/editor.js';
    $asset_url  = get_stylesheet_directory_uri() . '/dist/assets/js/editor.js';

    if (!file_exists($asset_path)) {
        error_log('FoundationPress Column BG: JavaScript file not found at ' . $asset_path);
        return;
    }

    wp_enqueue_script(
        'foundationpress-column-bg-editor',
        $asset_url,
        [
            'wp-blocks',
            'wp-element',
            'wp-i18n',
            'wp-components',
            'wp-compose',
            'wp-block-editor',
            'wp-data',
            'wp-hooks',
        ],
        filemtime($asset_path),
        true
    );
}
add_action('enqueue_block_editor_assets', 'foundationpress_column_bg_enqueue_editor_assets');

/**
 * Render column background + XY-grid classes
 */
function foundationpress_render_column_background($block_content, $block) {
    if (empty($block['blockName']) || 'core/column' !== $block['blockName']) {
        return $block_content;
    }

    $attrs = $block['attrs'] ?? [];
    $classes = ' cbg-xy-grid';
    $style = '';

    // Find parent columns block for stacked on mobile logic
    $parent_block = null;
    $post_id = get_the_ID();
    if (!$post_id && isset($_REQUEST['post_id'])) {
        $post_id = intval($_REQUEST['post_id']);
    }
    if ($post_id) {
        $post = get_post($post_id);
        if ($post && $post->post_content) {
            $blocks = parse_blocks($post->post_content);
            $parent_block = foundationpress_find_parent_columns_block($blocks, $block['attrs']['clientId'] ?? '');
        }
    }

    // Determine if parent columns block is stacked on mobile (default true)
    $parent_is_not_stacked = false;
    if ($parent_block && isset($parent_block['attrs']['isNotStackedOnMobile'])) {
        $parent_is_not_stacked = (bool) $parent_block['attrs']['isNotStackedOnMobile'];
    }
    $force_small_12 = !$parent_is_not_stacked;

    // Output explicit xyGrid classes
    if (!empty($attrs['xyGrid']) && is_array($attrs['xyGrid'])) {
        foreach (['small', 'medium', 'large'] as $bp) {
            $val = $attrs['xyGrid'][$bp] ?? null;
            $offsetKey = 'offset' . ucfirst($bp);
            if ($bp === 'small') {
                if ($force_small_12 && ($val === null || $val === '')) {
                    $classes .= ' small-12';
                } elseif ($val !== null && $val !== '') {
                    if ($val === 'auto') {
                        $classes .= ' small-auto';
                    } elseif (is_numeric($val) && intval($val) >= 1 && intval($val) <= 12) {
                        $classes .= ' small-' . intval($val);
                    }
                }
            } else {
                if ($force_small_12 && ($val === null || $val === '')) {
                    $classes .= ' ' . $bp . '-auto';
                } elseif ($val !== null && $val !== '') {
                    if ($val === 'auto') {
                        $classes .= ' ' . $bp . '-auto';
                    } elseif (is_numeric($val) && intval($val) >= 1 && intval($val) <= 12) {
                        $classes .= ' ' . $bp . '-' . intval($val);
                    }
                }
            }
            if (!empty($attrs['xyGrid'][$offsetKey])) {
                $classes .= ' ' . $bp . '-offset-' . esc_attr($attrs['xyGrid'][$offsetKey]);
            }
        }
    }

    // Add background image
    if (!empty($attrs['backgroundImage'])) {
        $style .= '--column-bg: url(' . esc_url_raw($attrs['backgroundImage']) . ');';
        $classes .= ' has-background-image';
    }

    // Handle flex-basis
    $parent_use_foundation_grid = false;
    if ($parent_block && isset($parent_block['attrs']['useFoundationGrid'])) {
        $parent_use_foundation_grid = $parent_block['attrs']['useFoundationGrid'];
    }
    if (!$parent_use_foundation_grid && !empty($attrs['width'])) {
        $style .= 'flex-basis: ' . esc_attr($attrs['width']) . ';';
    } elseif ($parent_use_foundation_grid) {
        $style .= 'flex-basis: unset; flex-grow: unset;';
    }

    // Inject classes and styles
    $pattern = '/(<\s*\w+[^>]*class=["\'])([^"\']*wp-block-column[^"\']*)(["\'][^>]*)(>)/i';
    $block_content = preg_replace_callback($pattern, function($matches) use ($classes, $style) {
        $before = $matches[1];
        $blockClasses = $matches[2] . $classes;
        $after = $matches[3];
        $closing = $matches[4];

        if ($style) {
            if (preg_match('/style=["\']([^"\']*)["\']/', $after, $style_match)) {
                $existing_style = trim($style_match[1]);
                $existing_style = preg_replace('/flex-(?:basis|grow)\s*:\s*[^;]+;?/i', '', $existing_style);
                if ($existing_style !== '' && substr($existing_style, -1) !== ';') {
                    $existing_style .= ';';
                }
                $existing_style = preg_replace('/--column-bg\s*:\s*[^;]+;?/i', '', $existing_style);
                $new_style = trim($existing_style . ' ' . $style);
                $after = preg_replace('/style=["\'][^"\']*["\']/', 'style="' . esc_attr($new_style) . '"', $after);
            } else {
                $after .= ' style="' . esc_attr(trim($style)) . '"';
            }
        }

        return $before . $blockClasses . $after . $closing;
    }, $block_content);

    return $block_content;
}
add_filter('render_block', 'foundationpress_render_column_background', 10, 2);

/**
 * Add Foundation grid classes to columns wrapper
 */
function foundationpress_columns_add_xy_grid($block_content, $block) {
    if (empty($block['blockName']) || 'core/columns' !== $block['blockName']) {
        return $block_content;
    }

    $attrs = $block['attrs'] ?? [];
    $useGrid = $attrs['useFoundationGrid'] ?? false;
    $collapse = $attrs['collapseGutters'] ?? false;

    if (!$useGrid) {
        return $block_content;
    }

    $pattern = '/(<\s*\w+[^>]*class=["\'])([^"\']*)(["\'][^>]*)(>)/i';
    $block_content = preg_replace_callback($pattern, function($matches) use ($collapse) {
        $before = $matches[1];
        $classes = $matches[2];
        $after = $matches[3];
        $closing = $matches[4];

        // Remove existing foundation classes to avoid duplication
        $classes = preg_replace('/\s*grid-x\s*/', ' ', $classes);
        $classes = preg_replace('/\s*grid-margin-x\s*/', ' ', $classes);
        
        // Add foundation classes
        $classes .= ' grid-x';
        if (!$collapse) {
            $classes .= ' grid-margin-x';
        }

        return $before . trim($classes) . $after . $closing;
    }, $block_content, 1);

    return $block_content;
}
add_filter('render_block', 'foundationpress_columns_add_xy_grid', 10, 2);

/**
 * Find parent columns block
 */
function foundationpress_find_parent_columns_block($blocks, $client_id) {
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/columns' && !empty($block['innerBlocks'])) {
            foreach ($block['innerBlocks'] as $inner_block) {
                if (isset($inner_block['attrs']['clientId']) && $inner_block['attrs']['clientId'] === $client_id) {
                    return $block;
                }
            }
        }
        if (!empty($block['innerBlocks'])) {
            $result = foundationpress_find_parent_columns_block($block['innerBlocks'], $client_id);
            if ($result) return $result;
        }
    }
    return null;
}