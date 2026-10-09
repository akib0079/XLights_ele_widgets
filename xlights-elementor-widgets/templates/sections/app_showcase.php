<?php
/**
 * App showcase with accessible tabs (WAI-ARIA tabs pattern).
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Illustrations;
use XLights\Frontend\Store_Badges;

defined( 'ABSPATH' ) || exit;

$items = $data['items'];
$pos   = 'left' === $data['phone_position'] ? 'left' : 'right';
?>
<div class="xl-container">
	<?php if ( ! $items ) : ?>
		<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo Context::editor_hint( __( 'Add app features in the section settings.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<div class="xl-app-showcase xl-app-showcase--phone-<?php echo esc_attr( $pos ); ?>" data-xl-tabs>
			<div class="xl-app-showcase__content" data-xl-reveal>
				<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="xl-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'App features', 'xlights-elementor-widgets' ); ?>">
					<?php
					foreach ( $items as $i => $item ) :
						$label = isset( $item['label'] ) && '' !== trim( $item['label'] ) ? $item['label'] : ( isset( $item['title'] ) ? $item['title'] : '' );
						?>
						<button type="button" role="tab" class="xl-tabs__tab" id="<?php echo esc_attr( $ctx->id( 'tab-' . $i ) ); ?>" aria-controls="<?php echo esc_attr( $ctx->id( 'panel-' . $i ) ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
							<?php echo Icons::svg( isset( $item['icon'] ) ? $item['icon'] : 'smartphone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo Html::text( $label ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<?php foreach ( $items as $i => $item ) : ?>
					<div class="xl-tabs__panel" role="tabpanel" id="<?php echo esc_attr( $ctx->id( 'panel-' . $i ) ); ?>" aria-labelledby="<?php echo esc_attr( $ctx->id( 'tab-' . $i ) ); ?>" tabindex="0" data-xl-screen="<?php echo (int) $i; ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
						<?php if ( ! empty( $item['title'] ) ) : ?>
							<h3 class="xl-tabs__title"><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $item['text'] ) ) : ?>
							<p><?php echo Html::multiline( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				<?php if ( '' !== trim( $data['note'] ) ) : ?>
					<p class="xl-note"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>
				<div class="xl-actions">
					<?php
					if ( Context::HIDE !== $data['store_visibility'] ) {
						echo Store_Badges::render( Context::PENDING === $data['store_visibility'] ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					echo Html::button( $data['link_label'], $data['link'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
			<div class="xl-app-showcase__device" data-xl-reveal>
				<div class="xl-app-showcase__glow" aria-hidden="true"></div>
				<?php foreach ( $items as $i => $item ) : ?>
					<div class="xl-app-showcase__screen<?php echo 0 === $i ? ' is-active' : ''; ?>" data-xl-screen-view="<?php echo (int) $i; ?>"<?php echo 0 === $i ? '' : ' aria-hidden="true"'; ?>>
						<?php echo Illustrations::phone( isset( $item['screen'] ) ? $item['screen'] : 'zones', isset( $item['screenshot'] ) ? $item['screenshot'] : null ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
