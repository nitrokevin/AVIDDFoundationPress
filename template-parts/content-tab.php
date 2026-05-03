<?php
defined('ABSPATH') || exit;

$id = get_query_var('block_id');
if (empty($id)) {
	// Stable fallback: hash of current post ID + template name avoids rand() instability
	$id = 'tabs-' . absint(get_the_ID()) . '-' . substr(md5(__FILE__), 0, 6);
}

// Cache all tab data in a single ACF loop pass
$tabs = [];
if (have_rows('repeater_content_tab')) {
	while (have_rows('repeater_content_tab')) {
		the_row();
		$tabs[] = [
			'heading'          => get_sub_field('tab_heading'),
			'background_color' => get_sub_field('tab_background_color'),
			'content'          => get_sub_field('tab_content'),
		];
	}
}

if (empty($tabs)) {
	return;
}

$tab_bar_bg = esc_attr(get_field('tab_bar_background_color'));
?>

<ul class="tabs <?php echo $tab_bar_bg; ?>" data-tabs id="<?php echo esc_attr($id); ?>">
	<?php foreach ($tabs as $index => $tab) :
		$is_first   = $index === 0;
		$tab_id     = 'tab' . absint($index + 1) . '-' . esc_attr($id);
		$tab_bg     = esc_attr($tab['background_color'] ?? '');
	?>
		<li class="tabs-title <?php echo $is_first ? 'is-active ' : ''; ?><?php echo $tab_bg; ?>">
			<a href="#<?php echo esc_attr($tab_id); ?>"
				<?php echo $is_first ? 'aria-selected="true"' : ''; ?>>
				<?php echo esc_html($tab['heading']); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<div class="tabs-content <?php echo $tab_bar_bg; ?>"
	data-tabs-content="<?php echo esc_attr($id); ?>">
	<?php foreach ($tabs as $index => $tab) :
		$is_first = $index === 0;
		$tab_id   = 'tab' . absint($index + 1) . '-' . esc_attr($id);
	?>
		<div class="tabs-panel <?php echo $is_first ? 'is-active' : ''; ?>"
			id="<?php echo esc_attr($tab_id); ?>">
			<?php echo wp_kses_post($tab['content']); ?>
		</div>
	<?php endforeach; ?>
</div>