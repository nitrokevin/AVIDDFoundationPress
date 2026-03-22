<?php

/**
 * Clean up WordPress defaults
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

if (! function_exists('foundationpress_start_cleanup')) :
    /**
     * Setup all cleanup actions
     */
    function foundationpress_start_cleanup()
    {
        foundationpress_cleanup_head();

        add_filter('the_generator', 'foundationpress_remove_rss_version');
        add_action('wp_head', 'foundationpress_remove_wp_widget_recent_comments_style', 1);
        add_action('wp_head', 'foundationpress_remove_recent_comments_style', 1);
    }
    add_action('after_setup_theme', 'foundationpress_start_cleanup');
endif;

/**
 * Clean up wp_head by removing unnecessary tags
 */
if (! function_exists('foundationpress_cleanup_head')) :
    function foundationpress_cleanup_head()
    {
        $actions = [
            'rsd_link',
            'feed_links_extra',
            'feed_links',
            'wlwmanifest_link',
            'index_rel_link',
            'parent_post_rel_link',
            'start_post_rel_link',
            'rel_canonical',
            'wp_shortlink_wp_head',
            'adjacent_posts_rel_link_wp_head',
            'wp_generator',
            'print_emoji_detection_script',
        ];

        foreach ($actions as $action) {
            remove_action('wp_head', $action);
        }

        remove_action('wp_print_styles', 'print_emoji_styles');
    }
endif;

/**
 * Remove WordPress version from RSS feeds for security
 */
if (! function_exists('foundationpress_remove_rss_version')) :
    function foundationpress_remove_rss_version()
    {
        return '';
    }
endif;

/**
 * Remove injected CSS for recent comments widget in the head
 */
if (! function_exists('foundationpress_remove_wp_widget_recent_comments_style')) :
    function foundationpress_remove_wp_widget_recent_comments_style()
    {
        if (has_filter('wp_head', 'wp_widget_recent_comments_style')) {
            remove_filter('wp_head', 'wp_widget_recent_comments_style');
        }
    }
endif;

/**
 * Remove injected CSS from recent comments widget styles in the head
 */
if (! function_exists('foundationpress_remove_recent_comments_style')) :
    function foundationpress_remove_recent_comments_style()
    {
        global $wp_widget_factory;
        if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
            remove_action('wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']);
        }
    }
endif;
