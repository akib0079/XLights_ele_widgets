<?php
/**
 * Quote form section: form + optional side panel.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Forms\Quote_Form_Handler;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Tokens;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$aside  = 'yes' === $data['show_aside'];
$points = Html::lines( $data['aside_points'] );
$phone  = (string) Settings::get( 'phone' );
$email  = (string) Settings::get( 'email' );
?>
<div class="xl-container">
	<div class="xl-quote<?php echo $aside ? ' xl-quote--aside' : ''; ?>">
		<div class="xl-quote__main" data-xl-reveal>
			<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php
			$form_html = Quote_Form_Handler::render_form(
				array(
					'variant'      => $data['variant'],
					'submit_label' => $data['submit_label'],
					'uid'          => $ctx->uid,
					'source'       => $ctx->template ? $ctx->template : 'widget',
				)
			);
			echo $form_html; // phpcs:ignore WordPress.Security.EscapeOutput -- Escaped in the form partial.
			?>
		</div>
		<?php if ( $aside ) : ?>
			<?php $aside_photo = ! empty( $data['aside_image'] ) && Html::has_image( $data['aside_image'] ); ?>
			<aside class="xl-quote__aside<?php echo $aside_photo ? ' xl-quote__aside--photo' : ''; ?>" data-xl-reveal>
				<?php if ( $aside_photo ) : ?>
					<div class="xl-quote__aside-media" aria-hidden="true">
						<?php
						$aside_img = Html::image(
							$data['aside_image'],
							array(
								'alt'   => '',
								'sizes' => '(max-width: 900px) 100vw, 40vw',
							)
						);
						echo $aside_img; // phpcs:ignore WordPress.Security.EscapeOutput -- Built by Html::image().
						?>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( $data['aside_heading'] ) ) : ?>
					<h3 class="xl-quote__aside-title"><?php echo Html::text( $data['aside_heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $data['aside_text'] ) ) : ?>
					<p><?php echo Html::multiline( $data['aside_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>
				<?php if ( $points ) : ?>
					<ul class="xl-checklist">
						<?php foreach ( $points as $p ) : ?>
							<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( 'yes' === $data['show_contact'] ) : ?>
					<div class="xl-quote__contact">
						<?php if ( '' !== trim( $phone ) ) : ?>
							<a class="xl-quote__contact-link" href="<?php echo esc_url( Tokens::tel_href( $phone ) ); ?>"><?php echo Icons::svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><small><?php esc_html_e( 'Call us', 'xlights-elementor-widgets' ); ?></small><?php echo esc_html( $phone ); ?></span></a>
						<?php endif; ?>
						<?php if ( Settings::enabled( 'show_email' ) && is_email( $email ) ) : ?>
							<a class="xl-quote__contact-link" href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>"><?php echo Icons::svg( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><small><?php esc_html_e( 'Email', 'xlights-elementor-widgets' ); ?></small><?php echo esc_html( antispambot( $email ) ); ?></span></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
</div>
