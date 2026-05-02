<?php

/* =========================
   COLUMN: BACKGROUND IMAGE
========================= */

function cbg_render_column($block_content, $block)
{
    if ($block['blockName'] !== 'core/column') {
        return $block_content;
    }

    $attrs = $block['attrs'] ?? [];

    if (empty($attrs['backgroundImage'])) {
        return $block_content;
    }

    $style = 'background-image:url(' . esc_url($attrs['backgroundImage']) . ');';

    $block_content = preg_replace(
        '/(<div[^>]*class="[^"]*wp-block-column[^"]*"[^>]*)(>)/i',
        '$1 style="' . esc_attr($style) . '"$2',
        $block_content,
        1
    );

    return $block_content;
}
add_filter('render_block', 'cbg_render_column', 10, 2);


/* =========================
   COLUMNS: DATA ATTRIBUTES
========================= */

function cbg_render_columns($block_content, $block)
{
    if ($block['blockName'] !== 'core/columns') {
        return $block_content;
    }

    $attrs = $block['attrs'] ?? [];

    $data = '';

    if (!empty($attrs['stackSmallTwo'])) {
        $data .= ' data-sm-2="true"';
    }

    if (!empty($attrs['stackMediumTwo'])) {
        $data .= ' data-md-2="true"';
    }

    if (!empty($attrs['stackMediumOne'])) {
        $data .= ' data-md-1="true"';
    }

    if ($data) {
        $block_content = preg_replace(
            '/(<div[^>]*class="[^"]*wp-block-columns[^"]*"[^>]*?)>/i',
            '$1' . $data . '>',
            $block_content,
            1
        );
    }

    return $block_content;
}
add_filter('render_block', 'cbg_render_columns', 10, 2);
