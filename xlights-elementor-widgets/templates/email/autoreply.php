<?php
/**
 * Customer confirmation / test email.
 *
 * @var string $first
 * @var string $message
 * @package XLights
 */

use XLights\Settings\Settings;
use XLights\Helpers\Tokens;

defined( 'ABSPATH' ) || exit;

$accent = Settings::get( 'color_accent', '#C98A3F' );
$phone  = (string) Settings::get( 'phone' );
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title><?php echo esc_html( Settings::get( 'dealer_name' ) ); ?></title></head>
<body style="margin:0;padding:0;background:#f4f2ee;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1e1e22;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f2ee;padding:24px 12px;">
	<tr><td align="center">
		<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e7e2d9;">
			<tr><td style="background:#0e0e10;padding:22px 28px;">
				<p style="margin:0;color:<?php echo esc_attr( $accent ); ?>;font-size:13px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;"><?php echo esc_html( Settings::get( 'dealer_name' ) ); ?></p>
			</td></tr>
			<tr><td style="padding:28px;font-size:15px;line-height:1.6;">
				<p style="margin:0 0 14px;">
					<?php
					/* translators: %s: first name */
					echo esc_html( sprintf( __( 'Hi %s,', 'xlights-elementor-widgets' ), $first ) );
					?>
				</p>
				<?php echo wpautop( esc_html( $message ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( '' !== trim( $phone ) ) : ?>
					<p style="margin:20px 0 0;"><a href="<?php echo esc_url( Tokens::tel_href( $phone ) ); ?>" style="color:#8a5a1f;font-weight:600;"><?php echo esc_html( $phone ); ?></a></p>
				<?php endif; ?>
			</td></tr>
		</table>
	</td></tr>
</table>
</body>
</html>
