<?php
/**
 * Lead notification email (HTML, inline styles for email clients).
 *
 * @var array  $lead
 * @var array  $rows
 * @var array  $files
 * @var string $admin
 * @var string $type
 * @package XLights
 */

use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$accent = Settings::get( 'color_accent', '#C98A3F' );
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title><?php esc_html_e( 'New quote request', 'xlights-elementor-widgets' ); ?></title></head>
<body style="margin:0;padding:0;background:#f4f2ee;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e1e22;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f2ee;padding:24px 12px;">
	<tr><td align="center">
		<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e7e2d9;">
			<tr><td style="background:#0e0e10;padding:22px 28px;">
				<p style="margin:0;color:<?php echo esc_attr( $accent ); ?>;font-size:12px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;"><?php echo esc_html( Settings::get( 'dealer_name' ) ); ?></p>
				<h1 style="margin:6px 0 0;color:#ffffff;font-size:22px;line-height:1.3;font-weight:600;">
					<?php
					/* translators: %s: residential or commercial */
					echo esc_html( sprintf( __( 'New %s quote request', 'xlights-elementor-widgets' ), strtolower( $type ) ) );
					?>
				</h1>
			</td></tr>
			<tr><td style="padding:24px 28px 8px;">
				<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;line-height:1.5;">
					<?php foreach ( $rows as $row ) : ?>
						<?php
						if ( '' === trim( (string) $row[1] ) ) {
							continue;
						}
						?>
						<tr>
							<td style="padding:9px 12px 9px 0;border-bottom:1px solid #efebe4;color:#6b6760;width:38%;vertical-align:top;"><?php echo esc_html( $row[0] ); ?></td>
							<td style="padding:9px 0;border-bottom:1px solid #efebe4;vertical-align:top;"><?php echo nl2br( esc_html( $row[1] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</td></tr>
			<?php if ( $files ) : ?>
				<tr><td style="padding:16px 28px 0;">
					<p style="margin:0 0 8px;font-size:14px;font-weight:600;"><?php esc_html_e( 'Uploaded photos (WordPress login required)', 'xlights-elementor-widgets' ); ?></p>
					<ul style="margin:0;padding:0 0 0 18px;font-size:14px;line-height:1.7;">
						<?php foreach ( $files as $file ) : ?>
							<li><a href="<?php echo esc_url( $file['url'] ); ?>" style="color:#8a5a1f;"><?php echo esc_html( $file['name'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</td></tr>
			<?php endif; ?>
			<tr><td style="padding:24px 28px 28px;">
				<a href="<?php echo esc_url( $admin ); ?>" style="display:inline-block;background:<?php echo esc_attr( $accent ); ?>;color:#0e0e10;text-decoration:none;font-weight:600;padding:12px 20px;border-radius:8px;font-size:14px;"><?php esc_html_e( 'Open lead in WordPress', 'xlights-elementor-widgets' ); ?></a>
				<?php if ( is_email( $lead['email'] ) ) : ?>
					<p style="margin:14px 0 0;font-size:13px;color:#6b6760;"><?php esc_html_e( 'Reply to this email to respond to the customer directly.', 'xlights-elementor-widgets' ); ?></p>
				<?php endif; ?>
			</td></tr>
		</table>
		<p style="margin:14px 0 0;font-size:12px;color:#8b867e;"><?php esc_html_e( 'This message contains personal information. Please handle it according to your privacy policy.', 'xlights-elementor-widgets' ); ?></p>
	</td></tr>
</table>
</body>
</html>
