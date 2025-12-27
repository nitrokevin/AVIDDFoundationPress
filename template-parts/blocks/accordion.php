<?php

/**
 * Accordion Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
// Create id attribute allowing for custom "anchor" value.
$id = 'accordion-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'block-accordion';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Color + gradient support.
$style = '';
$classes = [];

if (!empty($block['backgroundColor'])) {
    $classes[] = 'has-background';
    $classes[] = 'has-' . esc_attr($block['backgroundColor']) . '-background-color';
    $style .= 'background-color: var(--wp--preset--color--' . esc_attr($block['backgroundColor']) . ');';
}

if (!empty($block['gradient'])) {
    $classes[] = 'has-background';
    $classes[] = 'has-' . esc_attr($block['gradient']) . '-gradient-background';
    $style .= 'background: var(--wp--preset--gradient--' . esc_attr($block['gradient']) . ');';
}

if (!empty($block['textColor'])) {
    $classes[] = 'has-text-color';
    $classes[] = 'has-' . esc_attr($block['textColor']) . '-color';
    $style .= 'color: var(--wp--preset--color--' . esc_attr($block['textColor']) . ');';
}

$className .= ' ' . implode(' ', $classes);

?>
<section id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <div class="block-accordion-container "> 
		<div class="block-accordion-grid" >
		<div class="block-accordion-content " >
			<?php get_template_part( 'template-parts/content', 'accordion' ); ?>
	
		</div>
	</div>
</section>
