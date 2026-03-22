<?php

/**
 * Foundation PHP template
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

// Pagination.
if (! function_exists('foundationpress_pagination')) :
	function foundationpress_pagination()
	{
		global $wp_query;

		$big = 999999999; // This needs to be an unlikely integer

		// For more options and info view the docs for paginate_links()
		// http://codex.wordpress.org/Function_Reference/paginate_links
		$paginate_links = paginate_links(
			array(
				'base'      => str_replace($big, '%#%', html_entity_decode(get_pagenum_link($big))),
				'current'   => max(1, get_query_var('paged')),
				'total'     => $wp_query->max_num_pages,
				'mid_size'  => 5,
				'prev_next' => true,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'type'      => 'array',
			)
		);

		if (! empty($paginate_links)) {
			$pagination = '<nav aria-label="Pagination"><ul class="pagination text-center">';

			foreach ($paginate_links as $link) {
				$class = '';
				$aria_current = '';
				$link_text = $link;

				if (strpos($link, 'current') !== false) {
					$class = 'current';
					$aria_current = ' aria-current="page"';
					// Remove the <a> tag and replace with <span> for current page
					$link_text = preg_replace('#<a[^>]*>(.*?)</a>#i', '<span class="show-for-sr">You\'re on page </span>$1', $link);
					// Wrap with <li> and aria-current
					$pagination .= '<li class="' . esc_attr($class) . '"' . $aria_current . '>' . $link_text . '</li>';
				} elseif (strpos($link, 'prev page-numbers') !== false) {
					// Previous link
					preg_match('#href=[\'"]([^\'"]+)[\'"]#', $link, $matches);
					$url = isset($matches[1]) ? esc_url($matches[1]) : '#';
					$pagination .= '<li class="pagination-previous"><a href="' . $url . '" aria-label="Previous page">&laquo;</a></li>';
				} elseif (strpos($link, 'next page-numbers') !== false) {
					// Next link
					preg_match('#href=[\'"]([^\'"]+)[\'"]#', $link, $matches);
					$url = isset($matches[1]) ? esc_url($matches[1]) : '#';
					$pagination .= '<li class="pagination-next"><a href="' . $url . '" aria-label="Next page">&raquo;</a></li>';
				} elseif (strpos($link, 'dots') !== false) {
					// Ellipsis
					$pagination .= '<li class="ellipsis" aria-hidden="true">&hellip;</li>';
				} else {
					// Regular page links
					preg_match('#href=[\'"]([^\'"]+)[\'"]#', $link, $matches);
					preg_match('#>(\d+)<#', $link, $num_matches);
					$url = isset($matches[1]) ? esc_url($matches[1]) : '#';
					$page_num = isset($num_matches[1]) ? intval($num_matches[1]) : '';
					$pagination .= '<li><a href="' . $url . '" aria-label="Page ' . esc_attr($page_num) . '">' . esc_html($page_num) . '</a></li>';
				}
			}

			$pagination .= '</ul></nav>';

			echo wp_kses_post($pagination);
		}
	}
endif;

// Custom Comments Pagination.
if (! function_exists('foundationpress_get_the_comments_pagination')) :
	function foundationpress_get_the_comments_pagination($args = array())
	{
		$navigation = '';
		$args = wp_parse_args($args, array(
			'prev_text'				=> __('&laquo;', 'foundationpress'),
			'next_text'				=> __('&raquo;', 'foundationpress'),
			'size'					=> 'default',
			'show_disabled'			=> true,
		));
		$args['type'] = 'array';
		$args['echo'] = false;
		$links = paginate_comments_links($args);
		if ($links) {
			$link_count = count($links);
			$pagination_class = 'pagination';
			if ('large' == $args['size']) {
				$pagination_class .= ' pagination-lg';
			} elseif ('small' == $args['size']) {
				$pagination_class .= ' pagination-sm';
			}
			$current = get_query_var('cpage') ? intval(get_query_var('cpage')) : 1;
			$total = get_comment_pages_count();
			$navigation .= '<ul class="' . $pagination_class . '">';
			if ($args['show_disabled'] && 1 === $current) {
				$navigation .= '<li class="page-item disabled">' . $args['prev_text'] . '</li>';
			}
			foreach ($links as $index => $link) {
				if (0 == $index && 0 === strpos($link, '<a class="prev')) {
					$navigation .= '<li class="page-item">' . str_replace('prev page-numbers', 'page-link', $link) . '</li>';
				} elseif ($link_count - 1 == $index && 0 === strpos($link, '<a class="next')) {
					$navigation .= '<li class="page-item">' . str_replace('next page-numbers', 'page-link', $link) . '</li>';
				} else {
					$link = preg_replace("/(class|href)='(.*)'/U", '$1=\"$2\"', $link);
					if (0 === strpos($link, '<span class="page-numbers current')) {
						$navigation .= '<li class="page-item active">' . str_replace(array('<span class="page-numbers current">', '</span>'), array('<a class="page-link" href="#">', '</a>'), $link) . '</li>';
					} elseif (0 === strpos($link, '<span class="page-numbers dots')) {
						$navigation .= '<li class="page-item disabled">' . str_replace(array('<span class="page-numbers dots">', '</span>'), array('<a class="page-link" href="#">', '</a>'), $link) . '</li>';
					} else {
						$navigation .= '<li class="page-item">' . str_replace('class="page-numbers', 'class="page-link', $link) . '</li>';
					}
				}
			}
			if ($args['show_disabled'] && $current == $total) {
				$navigation .= '<li class="page-item disabled">' . $args['next_text'] . '</li>';
			}
			$navigation .= '</ul>';
			$navigation = _navigation_markup($navigation, 'comments-pagination');
		}
		return $navigation;
	}
endif;

// Custom Comments Pagination.
if (! function_exists('foundationpress_the_comments_pagination')) :
	function foundationpress_the_comments_pagination($args = array())
	{
		echo foundationpress_get_the_comments_pagination($args);
	}
endif;


/**
 * A fallback when no navigation is selected by default.
 */

if (! function_exists('foundationpress_menu_fallback')) :
	function foundationpress_menu_fallback()
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
	function foundationpress_active_nav_class($classes, $item)
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
	function foundationpress_active_list_pages_class($input)
	{

		$pattern = '/current_page_item/';
		$replace = 'current_page_item is-active';

		$output = preg_replace($pattern, $replace, $input);

		return $output;
	}
	add_filter('wp_list_pages', 'foundationpress_active_list_pages_class', 10, 2);
endif;



/**
 * Get mobile menu ID
 */

if (! function_exists('foundationpress_mobile_menu_id')) :
	function foundationpress_mobile_menu_id()
	{
		if (get_theme_mod('wpt_mobile_menu_layout') === 'offcanvas') {
			echo 'off-canvas-menu';
		} else {
			echo 'mobile-menu';
		}
	}
endif;

/**
 * Get title bar responsive toggle attribute
 */

if (! function_exists('foundationpress_title_bar_responsive_toggle')) :
	function foundationpress_title_bar_responsive_toggle()
	{
		if (! get_theme_mod('wpt_mobile_menu_layout') || get_theme_mod('wpt_mobile_menu_layout') === 'topbar') {
			echo 'data-responsive-toggle="mobile-menu"';
		}
	}
endif;
