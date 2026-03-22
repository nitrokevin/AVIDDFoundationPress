<?php

/**
 * Foundation PHP template
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

// Pagination.
if (! function_exists('foundationpress_pagination')) :
	function foundationpress_pagination(): void
	{
		global $wp_query;

		$total   = (int) $wp_query->max_num_pages;
		$current = (int) max(1, get_query_var('paged'));

		if ($total <= 1) {
			return;
		}

		$mid_size = 5;
		$start    = max(1, $current - $mid_size);
		$end      = min($total, $current + $mid_size);

		$items = '';

		// Previous link
		if ($current > 1) {
			$items .= sprintf(
				'<li class="pagination-previous"><a href="%s" aria-label="%s">%s</a></li>' . "\n",
				esc_url(get_pagenum_link($current - 1)),
				esc_attr__('Previous page', 'foundationpress'),
				'&laquo;'
			);
		} else {
			$items .= '<li class="pagination-previous disabled" aria-hidden="true">&laquo;</li>' . "\n";
		}

		// First page + leading ellipsis if range doesn't start at 1
		if ($start > 1) {
			$items .= sprintf(
				'<li><a href="%s" aria-label="%s">1</a></li>' . "\n",
				esc_url(get_pagenum_link(1)),
				esc_attr__('Page 1', 'foundationpress')
			);
			if ($start > 2) {
				$items .= '<li class="ellipsis" aria-hidden="true">&hellip;</li>' . "\n";
			}
		}

		// Page number range
		for ($i = $start; $i <= $end; $i++) {
			if ($i === $current) {
				$items .= sprintf(
					'<li class="current" aria-current="page"><span class="show-for-sr">%s</span>%d</li>' . "\n",
					esc_html__("You're on page ", 'foundationpress'),
					$i
				);
			} else {
				$items .= sprintf(
					'<li><a href="%s" aria-label="%s">%d</a></li>' . "\n",
					esc_url(get_pagenum_link($i)),
					esc_attr(sprintf(__('Page %d', 'foundationpress'), $i)),
					$i
				);
			}
		}

		// Last page + trailing ellipsis if range doesn't reach the end
		if ($end < $total) {
			if ($end < $total - 1) {
				$items .= '<li class="ellipsis" aria-hidden="true">&hellip;</li>' . "\n";
			}
			$items .= sprintf(
				'<li><a href="%s" aria-label="%s">%d</a></li>' . "\n",
				esc_url(get_pagenum_link($total)),
				esc_attr(sprintf(__('Page %d', 'foundationpress'), $total)),
				$total
			);
		}

		// Next link
		if ($current < $total) {
			$items .= sprintf(
				'<li class="pagination-next"><a href="%s" aria-label="%s">%s</a></li>' . "\n",
				esc_url(get_pagenum_link($current + 1)),
				esc_attr__('Next page', 'foundationpress'),
				'&raquo;'
			);
		} else {
			$items .= '<li class="pagination-next disabled" aria-hidden="true">&raquo;</li>' . "\n";
		}

		printf(
			'<nav aria-label="%s"><ul class="pagination text-center">%s</ul></nav>' . "\n",
			esc_attr__('Pagination', 'foundationpress'),
			$items
		);
	}
endif;


// Comments Pagination — returns markup string.
if (! function_exists('foundationpress_get_the_comments_pagination')) :
	function foundationpress_get_the_comments_pagination(array $args = []): string
	{
		$args = wp_parse_args($args, [
			'prev_text'     => __('&laquo;', 'foundationpress'),
			'next_text'     => __('&raquo;', 'foundationpress'),
			'show_disabled' => true,
		]);

		$total   = (int) get_comment_pages_count();
		$current = (int) max(1, get_query_var('cpage'));

		if ($total <= 1) {
			return '';
		}

		$items = '';

		// Previous link
		if ($current > 1) {
			$items .= sprintf(
				'<li class="page-item"><a class="page-link" href="%s" aria-label="%s">%s</a></li>' . "\n",
				esc_url(get_comments_pagenum_link($current - 1, $total)),
				esc_attr__('Previous comment page', 'foundationpress'),
				wp_kses_post($args['prev_text'])
			);
		} elseif ($args['show_disabled']) {
			$items .= sprintf(
				'<li class="page-item disabled" aria-hidden="true"><span class="page-link">%s</span></li>' . "\n",
				wp_kses_post($args['prev_text'])
			);
		}

		// Page number range — comments pagination conventionally shows all pages
		// since comment page counts are typically low (rarely exceed 10–20).
		for ($i = 1; $i <= $total; $i++) {
			if ($i === $current) {
				$items .= sprintf(
					'<li class="page-item active" aria-current="page"><span class="page-link"><span class="show-for-sr">%s</span>%d</span></li>' . "\n",
					esc_html__("You're on page ", 'foundationpress'),
					$i
				);
			} else {
				$items .= sprintf(
					'<li class="page-item"><a class="page-link" href="%s" aria-label="%s">%d</a></li>' . "\n",
					esc_url(get_comments_pagenum_link($i, $total)),
					esc_attr(sprintf(__('Comment page %d', 'foundationpress'), $i)),
					$i
				);
			}
		}

		// Next link
		if ($current < $total) {
			$items .= sprintf(
				'<li class="page-item"><a class="page-link" href="%s" aria-label="%s">%s</a></li>' . "\n",
				esc_url(get_comments_pagenum_link($current + 1, $total)),
				esc_attr__('Next comment page', 'foundationpress'),
				wp_kses_post($args['next_text'])
			);
		} elseif ($args['show_disabled']) {
			$items .= sprintf(
				'<li class="page-item disabled" aria-hidden="true"><span class="page-link">%s</span></li>' . "\n",
				wp_kses_post($args['next_text'])
			);
		}

		return sprintf(
			'<nav aria-label="%s"><ul class="pagination">%s</ul></nav>' . "\n",
			esc_attr__('Comments pagination', 'foundationpress'),
			$items
		);
	}
endif;


// Comments Pagination — echoes markup.
if (! function_exists('foundationpress_the_comments_pagination')) :
	function foundationpress_the_comments_pagination(array $args = []): void
	{
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside foundationpress_get_the_comments_pagination()
		echo foundationpress_get_the_comments_pagination($args);
	}
endif;


/**
 * A fallback when no navigation is selected by default.
 */
if (! function_exists('foundationpress_menu_fallback')) :
	function foundationpress_menu_fallback(): void
	{
		echo '<div class="alert-box secondary">';
		/* translators: %1$s: link to menus, %2$s: link to customize. */
		printf(
			__('Please assign a menu to the primary menu location under %1$s or %2$s the design.', 'foundationpress'),
			/* translators: %s: menu url */
			sprintf(
				__('<a href="%s">Menus</a>', 'foundationpress'),
				get_admin_url(get_current_blog_id(), 'nav-menus.php')
			),
			/* translators: %s: customize url */
			sprintf(
				__('<a href="%s">Customize</a>', 'foundationpress'),
				get_admin_url(get_current_blog_id(), 'customize.php')
			)
		);
		echo '</div>';
	}
endif;


// Add Foundation 'is-active' class for the current menu item.
if (! function_exists('foundationpress_active_nav_class')) :
	function foundationpress_active_nav_class(array $classes, object $item): array
	{
		if ($item->current == 1 || $item->current_item_ancestor == true) {
			$classes[] = 'is-active';
		}
		return $classes;
	}
	add_filter('nav_menu_css_class', 'foundationpress_active_nav_class', 10, 2);
endif;


/**
 * Use the is-active class of ZURB Foundation on wp_list_pages output.
 * From required+ Foundation http://themes.required.ch.
 */
if (! function_exists('foundationpress_active_list_pages_class')) :
	function foundationpress_active_list_pages_class(string $input): string
	{
		return preg_replace('/current_page_item/', 'current_page_item is-active', $input);
	}
	add_filter('wp_list_pages', 'foundationpress_active_list_pages_class', 10, 2);
endif;


/**
 * Get mobile menu ID.
 */
if (! function_exists('foundationpress_mobile_menu_id')) :
	function foundationpress_mobile_menu_id(): void
	{
		echo get_theme_mod('wpt_mobile_menu_layout') === 'offcanvas'
			? 'off-canvas-menu'
			: 'mobile-menu';
	}
endif;


/**
 * Get title bar responsive toggle attribute.
 */
if (! function_exists('foundationpress_title_bar_responsive_toggle')) :
	function foundationpress_title_bar_responsive_toggle(): void
	{
		$layout = get_theme_mod('wpt_mobile_menu_layout');
		if (! $layout || $layout === 'topbar') {
			echo 'data-responsive-toggle="mobile-menu"';
		}
	}
endif;
