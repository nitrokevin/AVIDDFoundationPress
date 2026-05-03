<?php
defined('ABSPATH') || exit;
/* =========================
   COLUMN: BACKGROUND IMAGE
========================= */


function avidd_render_column_background( string $block_content, array $block ): string {
    if ( $block['blockName'] !== 'core/column' ) {
        return $block_content;
    }

    $bg_url = $block['attrs']['backgroundImage'] ?? '';
    if ( ! $bg_url ) {
        return $block_content;
    }

    $processor = new WP_HTML_Tag_Processor( $block_content );

    if ( $processor->next_tag( [ 'tag_name' => 'div', 'class_name' => 'wp-block-column' ] ) ) {
        $existing_style = $processor->get_attribute( 'style' ) ?? '';
        $bg_style       = 'background-image:url(' . esc_url( $bg_url ) . ');';
        $processor->set_attribute( 'style', trim( $existing_style . ' ' . $bg_style ) );
    }

    return $processor->get_updated_html();
}
add_filter( 'render_block', 'avidd_render_column_background', 10, 2 );


/**
 * Inject responsive-stack data attributes on core/columns blocks.
 */
function avidd_render_columns_data_attrs( string $block_content, array $block ): string {
    if ( $block['blockName'] !== 'core/columns' ) {
        return $block_content;
    }

    $attrs      = $block['attrs'] ?? [];
    $data_attrs = [];

    if ( ! empty( $attrs['stackSmallTwo'] ) )  $data_attrs['data-sm-2']  = 'true';
    if ( ! empty( $attrs['stackMediumTwo'] ) ) $data_attrs['data-md-2']  = 'true';
    if ( ! empty( $attrs['stackMediumOne'] ) ) $data_attrs['data-md-1']  = 'true';

    if ( empty( $data_attrs ) ) {
        return $block_content;
    }

    $processor = new WP_HTML_Tag_Processor( $block_content );

    if ( $processor->next_tag( [ 'tag_name' => 'div', 'class_name' => 'wp-block-columns' ] ) ) {
        foreach ( $data_attrs as $attr => $value ) {
            $processor->set_attribute( $attr, $value );
        }
    }

    return $processor->get_updated_html();
}
add_filter( 'render_block', 'avidd_render_columns_data_attrs', 10, 2 );