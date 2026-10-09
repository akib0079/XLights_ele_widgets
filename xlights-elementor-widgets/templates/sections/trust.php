<?php
/**
 * Local trust band.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;

$show_license = Context::HIDE !== $data['license_visibility'] && '' !== trim( (string) $data['statement'] );
?>
<div class="xl-container">
	<div class="xl-trust" data-xl-reveal>
		<div class="xl-trust__lead">
			<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $show_license ) : ?>
				<div class="xl-trust__statement<?php echo Context::PENDING === $data['license_visibility'] ? ' xl-is-pending' : ''; ?>">
					<?php if ( Context::PENDING === $data['license_visibility'] ) : ?>
						<?php echo Context::pending_badge( 'credentials' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
					<span class="xl-trust__seal"><?php echo Icons::svg( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<p>
						<strong><?php echo Html::text( $data['statement'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></strong>
						<?php if ( '' !== trim( (string) $data['license'] ) ) : ?>
							<span>
								<?php
								/* translators: %s: license number */
								echo esc_html( sprintf( __( 'License #%s', 'xlights-elementor-widgets' ), $data['license'] ) );
								?>
							</span>
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
			<?php echo Html::button( $data['link_label'], $data['link'], 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $data['items'] ) : ?>
			<ul class="xl-trust__items" role="list">
				<?php foreach ( $data['items'] as $item ) : ?>
					<li class="<?php echo Context::PENDING === $item['visibility'] ? 'xl-is-pending' : ''; ?>">
						<span class="xl-trust__icon"><?php echo Icons::svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div>
							<?php if ( Context::PENDING === $item['visibility'] ) : ?>
								<?php echo Context::pending_badge( $item['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
							<h3><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
							<?php if ( '' !== trim( $item['text'] ) ) : ?>
								<p><?php echo Html::text( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
