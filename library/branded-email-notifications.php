<?php
// ------------------------------------------------------------
// BRANDED EMAIL NOTIFICATION
// ------------------------------------------------------------
//
// Wraps Contact Form 7 submission emails in a branded HTML
// email template pulled from ACF options.
//
// Fixes applied vs. original:
// 1. Named function for wp_mail_content_type so remove_filter()
// actually works — anonymous closures cannot be removed.
// 2. Content-type filter scoped to the CF7 send cycle only,
// not applied globally for the lifetime of the request.
// 3. $logo_url initialised before use; duplicate fallback removed.
// 4. strip_tags() replaced with wp_kses() — strip_tags() allows
// XSS through tag attributes (e.g. <a href="javascript:...">).
	// 5. $message_html escaped via wp_kses() before injection into
	// the HTML string.
	// 6. $site_name unescaped in footer anchor — now esc_html().
	// 7. HTML template extracted to a dedicated function for testability.
	// 8. Early-return guard if body is empty.
	// ------------------------------------------------------------


	// ── Helpers ──────────────────────────────────────────────────────────────────

	/**
	* Resolve the email logo URL from ACF options, falling back to the site icon.
	*
	* @return string Absolute URL, or empty string if none is configured.
	*/
	function avidd_get_email_logo_url(): string {

	// ACF image field returns an array; a plain attachment ID is also accepted.
	$logo = function_exists( 'get_field' ) ? get_field( 'email_logo', 'option' ) : null;
	$logo_id = 0;

	if ( is_array( $logo ) && ! empty( $logo['ID'] ) ) {
	$logo_id = (int) $logo['ID'];
	} elseif ( is_numeric( $logo ) && $logo > 0 ) {
	$logo_id = (int) $logo;
	}

	if ( $logo_id ) {
	$url = wp_get_attachment_image_url( $logo_id, 'full' );
	if ( $url ) {
	return $url;
	}
	}

	// Fall back to the WP site icon (set in Customizer → Site Identity).
	$site_icon_id = (int) get_option( 'site_icon' );
	if ( $site_icon_id ) {
	$url = wp_get_attachment_image_url( $site_icon_id, 'full' );
	if ( $url ) {
	return $url;
	}
	}

	return '';
	}

	/**
	* Sanitise the CF7 message body for safe injection into an HTML email.
	*
	* Uses wp_kses() with a restrictive allowlist rather than strip_tags(),
	* because strip_tags() does NOT remove dangerous attributes such as
	* href="javascript:..." on allowed tags.
	*
	* @param string $raw Raw CF7 message body.
	* @return string Safe HTML string, run through wpautop().
	*/
	function avidd_sanitise_email_body( string $raw ): string {

	if ( '' === trim( $raw ) ) {
	return '';
	}

	$allowed_tags = [
	'h2' => [],
	'h3' => [],
	'p' => [],
	'br' => [],
	'strong' => [],
	'b' => [],
	'em' => [],
	'i' => [],
	'ul' => [],
	'ol' => [],
	'li' => [],
	// Anchor: only safe href schemes; no event attributes.
	'a' => [
	'href' => true,
	'title' => true,
	'target' => true,
	'rel' => true,
	],
	];

	$clean = wp_kses( $raw, $allowed_tags );

	// Strip href="javascript:..." that wp_kses might still allow if the
	// scheme was not caught. wp_kses_bad_protocol handles this, but
	// belt-and-braces: replace any remaining javascript: href values.
	$clean = preg_replace(
	'/(<a\s[^>]*href\s*=\s*["\'])javascript:[^"\']*(["\'])/i',
		'$1#$2',
		$clean
		);

		return wpautop( $clean );
		}

		/**
		* Build the full HTML email document string.
		*
		* All dynamic values are escaped at the point of interpolation.
		* The function returns a string; nothing is echoed.
		*
		* @param string $message_html Sanitised, wpautop'd message body.
		* @param string $logo_url Absolute URL of the email logo (may be empty).
		* @param string $site_name Plain-text site name.
		* @param string $site_url Home URL.
		* @return string Complete HTML email document.
		*/
		function avidd_build_email_html(
		string $message_html,
		string $logo_url,
		string $site_name,
		string $site_url
		): string {

		// Logo block — image linked to homepage, or plain site name as fallback.
		if ( $logo_url ) {
		$logo_block = sprintf(
		'<a href="%s" style="display:block;"><img src="%s" alt="%s" style="max-height:120px;height:auto;display:block;"></a>',
		esc_url( $site_url ),
		esc_url( $logo_url ),
		esc_attr( $site_name )
		);
		} else {
		$logo_block = sprintf(
		'<div style="font-weight:700;font-size:18px;color:#222;">%s</div>',
		esc_html( $site_name )
		);
		}

		// Footer site name — plain text link.
		$footer_link = sprintf(
		'<a href="%s" style="color:#999;text-decoration:none;">%s</a>',
		esc_url( $site_url ),
		esc_html( $site_name )
		);

		// Assemble document.
		// $message_html is already sanitised by avidd_sanitise_email_body().
		// $logo_block and $footer_link are built from escaped values above.
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		return '
		<!DOCTYPE html>
		<html lang="en">

		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width,initial-scale=1">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<title>' . esc_html( $site_name ) . '</title>
		</head>

		<body style="margin:0;padding:0;background-color:#f6f6f6;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

			<!-- Outer wrapper -->
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
				style="background-color:#f6f6f6;padding:32px 12px;">
				<tr>
					<td align="center">

						<!-- Email card — max 600px wide -->
						<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
							style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;
                              box-shadow:0 2px 8px rgba(0,0,0,0.07);">

							<!-- Logo header -->
							<tr>
								<td style="padding:24px 28px;border-bottom:1px solid #f0f0f0;">
									' . $logo_block . '
								</td>
							</tr>

							<!-- Message body -->
							<tr>
								<td style="padding:28px;font-family:Arial,Helvetica,sans-serif;
                                   font-size:15px;line-height:1.5;color:#333333;">
									' . $message_html . '
								</td>
							</tr>

							<!-- Footer -->
							<tr>
								<td style="padding:16px 28px;background-color:#fafafa;
                                   border-top:1px solid #eeeeee;
                                   font-family:Arial,Helvetica,sans-serif;
                                   font-size:12px;color:#999999;text-align:center;">
									' . $footer_link . '
								</td>
							</tr>

						</table><!-- /Email card -->

					</td>
				</tr>
			</table><!-- /Outer wrapper -->

		</body>

		</html>';
		// phpcs:enable
		}


		// ── Named function for the content-type filter ────────────────────────────────

		/**
		* Force wp_mail() to send HTML.
		*
		* Named so it can be reliably removed by remove_filter() after sending.
		* An anonymous closure cannot be removed — this is a common WP gotcha.
		*
		* @return string MIME type.
		*/
		function avidd_set_html_content_type(): string {
		return 'text/html';
		}


		// ── Hooks ─────────────────────────────────────────────────────────────────────

		/**
		* Wrap the CF7 email body in the branded HTML template.
		*
		* The content-type filter is added HERE (not globally at file load),
		* so it only affects the email CF7 is about to send.
		*
		* @param array $components { 'subject', 'sender', 'body', 'recipient', ... }
		* @param WPCF7_ContactForm $contact_form
		* @param WPCF7_Mail $mail
		* @return array Modified components.
		*/
		add_filter( 'wpcf7_mail_components', function ( array $components, $contact_form, $mail ): array {

		// Gate: only process if there is actually a body to wrap.
		if ( empty( $components['body'] ) ) {
		return $components;
		}

		// Activate HTML content-type just for this send cycle.
		// Removed again in the wpcf7_mail_sent / wpcf7_mail_failed hooks below.
		add_filter( 'wp_mail_content_type', 'avidd_set_html_content_type' );

		$logo_url = avidd_get_email_logo_url();
		$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$site_url = home_url( '/' );
		$message_html = avidd_sanitise_email_body( $components['body'] );

		$components['body'] = avidd_build_email_html(
		$message_html,
		$logo_url,
		$site_name,
		$site_url
		);

		return $components;

		}, 10, 3 );


		/**
		* Remove the HTML content-type override after CF7 sends successfully.
		*
		* Using a named function is required — remove_filter() cannot target
		* an anonymous closure, so the original code's reset was silently a no-op.
		*/
		add_action( 'wpcf7_mail_sent', function (): void {
		remove_filter( 'wp_mail_content_type', 'avidd_set_html_content_type' );
		} );

		/**
		* Also remove the override if CF7 fails to send, so subsequent emails
		* (e.g. WooCommerce order notifications) are not affected.
		*/
		add_action( 'wpcf7_mail_failed', function (): void {
		remove_filter( 'wp_mail_content_type', 'avidd_set_html_content_type' );
		} );