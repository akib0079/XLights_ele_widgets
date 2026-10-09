<?php
/**
 * Financing callout.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;
?>
<div class="xl-container">
	<?php if ( Context::PENDING === $data['visibility'] ) : ?>
		<?php echo Context::pending_badge( 'financing' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
	<div class="xl-financing" data-xl-reveal>
		<span class="xl-financing__icon"><?php echo Icons::svg( 'money' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<div class="xl-financing__body">
			<?php if ( '' !== trim( $data['eyebrow'] ) ) : ?>
				<p class="xl-eyebrow"><?php echo Html::text( $data['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<h2 class="xl-title xl-h3"><?php echo Html::heading_text( $data['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php if ( '' !== trim( $data['text'] ) ) : ?>
				<p><?php echo Html::multiline( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<?php if ( '' !== trim( $data['partner'] ) ) : ?>
				<p class="xl-financing__partner">
					<?php
					/* translators: %s: financing partner name */
					echo esc_html( sprintf( __( 'Financing provided through %s.', 'xlights-elementor-widgets' ), $data['partner'] ) );
					?>
				</p>
			<?php endif; ?>
			<?php if ( '' !== trim( $data['disclaimer'] ) ) : ?>
				<p class="xl-financing__disclaimer"><?php echo Html::text( $data['disclaimer'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
		<div class="xl-financing__action">
			<?php echo Html::button( $data['button_label'], $data['button_link'], 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</div>
