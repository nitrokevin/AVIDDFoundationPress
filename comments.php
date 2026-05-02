<?php

/**
 * The template for displaying comments.
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

// Guard must be FIRST — prevents direct file access.
defined('ABSPATH') || exit;

// Password-protected posts — show notice and stop.
if (post_password_required()) : ?>
	<section id="comments">
		<div class="notice">
			<p class="bottom"><?php esc_html_e('This post is password protected. Enter the password to view comments.', 'foundationpress'); ?></p>
		</div>
	</section>
<?php return;
endif;

// Existing comments.
if (have_comments()) : ?>
	<section id="comments">
		<?php
		wp_list_comments([
			'walker'      => new Foundationpress_Comments(),
			'style'       => 'ol',
			'type'        => 'all',
			'reply_text'  => __('Reply', 'foundationpress'),
			'avatar_size' => 48,
			'format'      => 'html5',
			'short_ping'  => false,
			'echo'        => true,
		]);
		foundationpress_the_comments_pagination();
		?>
	</section>
<?php endif;

// Comment form.
if (comments_open()) : ?>
	<section id="respond">
		<?php comment_form(['class_submit' => 'button']); ?>
	</section>
<?php endif;
