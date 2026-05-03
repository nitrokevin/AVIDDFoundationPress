<?php

/**
 * Entry meta information for posts
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */
defined('ABSPATH') || exit;
if (! function_exists('foundationpress_entry_meta')) :
	function foundationpress_entry_meta()
	{
		/* translators: %1$s: current date, %2$s: current time */
		echo '<time class="updated" datetime="' . esc_attr(get_the_time('c')) . '">'
			. sprintf(
				esc_html__('Posted on %1$s at %2$s.', 'foundationpress'),
				esc_html(get_the_date()),
				esc_html(get_the_time())
			)
			. '</time>';
		echo '<p class="byline author">'
			. esc_html__('Written by', 'foundationpress')
			. ' <a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '"'
			. ' rel="author" class="fn">' . esc_html(get_the_author()) . '</a></p>';
	}
endif;
