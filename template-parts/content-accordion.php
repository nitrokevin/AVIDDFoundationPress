<?php

/**
 * ACF Accordion Content
 */
?>
<?php if (get_field('accordion_type') == 'faq') { ?>
	<ul class="accordion" data-accordion data-allow-all-closed="true">
		<?php if (have_rows('repeater_content_accordion', 'option')) :
			while (have_rows('repeater_content_accordion', 'option')) : the_row();
				$header = get_sub_field('accordion_heading');
				$content = get_sub_field('accordion_content');
			$categories = get_sub_field('categories'); // returns array of term objects
			$cat_classes = '';
			if (!empty($categories)) {
				$cat_classes = implode(' ', array_map(
					fn($term) => 'category-' . esc_attr($term->slug),
					$categories
				));
			}
			
		?>

				<li data-filter="<?php echo esc_attr($cat_classes); ?>" class="accordion-item <?php echo esc_attr($cat_classes); ?> filter-simple-item" data-accordion-item>
					<!-- Accordion tab title -->
					<a href="#" class="accordion-title"><?php echo esc_html($header); ?></a>

					<!-- Accordion tab content: it would start in the open state due to using the `is-active` state class. -->
					<div class="accordion-content" data-tab-content>
						<?php echo wp_kses_post($content); ?>
					</div>
				</li>

		<?php endwhile;
		endif; ?>
	</ul>
<?php } elseif (get_field('accordion_type') == 'custom') { ?>
	<ul class="accordion" data-accordion data-allow-all-closed="true">
		<?php
		if (have_rows('repeater_content_accordion')) {
			$counter = 0;

			while (have_rows('repeater_content_accordion')) {
				the_row();
				
				$accordion_heading = get_sub_field('accordion_heading');
				$accordion_content = get_sub_field('accordion_content');
				$accordion_heading_background_color = get_sub_field('accordion_heading_background_color');
				$counter++;
		?>

				<li class="accordion-item <?php echo esc_attr(sanitize_html_class($accordion_heading_background_color)); ?>" data-accordion-item>
					<a href="#" class="accordion-title">
						<?php echo esc_html($accordion_heading); ?>
					</a>
					<div class="accordion-content" data-tab-content>
						<div class="accordion-content-container">
							<div class="accordion-content-inner">
								<?php echo wp_kses_post($accordion_content); ?>
							</div>
						</div>
					</div>
				</li>
			<?php } ?>
		<?php } ?>
	</ul>
<?php } ?>