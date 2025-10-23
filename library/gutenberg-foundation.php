<?php 
/**
 * Integrate Gutenberg Columns with Foundation XY Grid + gutter control
 */
add_filter('render_block_core/columns', function ($block_content, $block) {
    if (empty($block_content)) return $block_content;

    // Add grid-x by default
    $block_content = str_replace('wp-block-columns', 'wp-block-columns grid-x', $block_content);
    $block_content = str_replace('wp-block-column', 'wp-block-column cell', $block_content);

    // If collapse_gutters attribute exists, adjust classes accordingly
    if (!empty($block['attrs']['className']) && str_contains($block['attrs']['className'], 'has-collapsed-gutters')) {
        $block_content = str_replace('grid-x', 'grid-x grid-padding-x', $block_content);
    } else {
        $block_content = str_replace('grid-x', 'grid-x grid-margin-x', $block_content);
    }

    return $block_content;
}, 10, 2);
?>