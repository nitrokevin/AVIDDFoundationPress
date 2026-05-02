<?php

/**
 * FoundationPress Comments Walker
 *
 * @package FoundationPress
 */

if (! class_exists('Foundationpress_Comments')) :

	class Foundationpress_Comments extends Walker_Comment
	{

		public $tree_type = 'comment';

		public $db_fields = [
			'parent' => 'comment_parent',
			'id'     => 'comment_ID',
		];

		/**
		 * Constructor — MUST NOT produce output.
		 * The list opening and heading are rendered by comments.php directly.
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * Opens a child list for nested comments.
		 */
		public function start_lvl(&$output, $depth = 0, $args = []): void
		{
			$GLOBALS['comment_depth'] = $depth + 1;
			$output .= "\n<ul class=\"children\">\n";
		}

		/**
		 * Closes a child list for nested comments.
		 */
		public function end_lvl(&$output, $depth = 0, $args = []): void
		{
			$GLOBALS['comment_depth'] = $depth + 1;
			$output .= "</ul><!-- /.children -->\n";
		}

		/**
		 * Renders a single comment.
		 */
		public function start_el(&$output, $comment, $depth = 0, $args = [], $id = 0): void
		{
			$depth++;
			$GLOBALS['comment_depth'] = $depth;
			$GLOBALS['comment']       = $comment;

			$parent_class = empty($args['has_children']) ? '' : 'parent';

			$author_link = get_comment_author_link($comment);
			$avatar      = get_avatar($comment, $args['avatar_size']);
			$date        = esc_attr(get_comment_date('c', $comment));
			$link        = esc_url(get_comment_link($comment->comment_ID));

			ob_start();
?>
			<li <?php comment_class($parent_class); ?> id="comment-<?php comment_ID(); ?>">
				<article id="comment-body-<?php comment_ID(); ?>" class="comment-body">

					<header class="comment-author">
						<?php echo $avatar; // Already sanitized by WP 
						?>
						<div class="author-meta vcard author">
							<?php
							printf(
								/* translators: %s: comment author link */
								'<cite class="fn">%s</cite>',
								$author_link // WP sanitizes this
							);
							?>
							<time datetime="<?php echo $date; ?>">
								<a href="<?php echo $link; ?>">
									<?php printf('%s %s', get_comment_date(), get_comment_time()); ?>
								</a>
							</time>
						</div>
					</header>

					<section id="comment-content-<?php comment_ID(); ?>" class="comment">
						<?php if (! $comment->comment_approved) : ?>
							<div class="notice">
								<p><?php esc_html_e('Your comment is awaiting moderation.', 'foundationpress'); ?></p>
							</div>
						<?php else : ?>
							<?php comment_text(); ?>
						<?php endif; ?>
					</section>

					<div class="comment-meta comment-meta-data hide">
						<a href="<?php echo esc_url(get_comment_link(get_comment_ID())); ?>">
							<?php comment_date(); ?> at <?php comment_time(); ?>
						</a>
						<?php edit_comment_link('(Edit)'); ?>
					</div>

					<div class="reply">
						<?php
						comment_reply_link(array_merge($args, [
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
						]));
						?>
					</div>

				</article><!-- /.comment-body -->
	<?php
			$output .= ob_get_clean();
		}

		/**
		 * Closes a single comment list item.
		 */
		public function end_el(&$output, $comment, $depth = 0, $args = []): void
		{
			$output .= "</li><!-- /#comment-" . esc_attr(get_comment_ID()) . " -->\n";
		}
	}

endif;
