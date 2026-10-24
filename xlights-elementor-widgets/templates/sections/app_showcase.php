<?php
/**
 * App showcase: accessible tabs beside one phone (WAI-ARIA tabs pattern), or a
 * scrolling gallery of every app screen.
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
	<?php elseif ( 'gallery' === $data['layout'] ) : ?>
		<?php
		// Gallery: every app screen side by side in a scrolling row, each with its caption.
		$count = count( $items );
		?>
		<div class="xl-app-gallery">
			<div class="xl-app-gallery__head" data-xl-reveal>
				<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="xl-actions">
					<?php
					if ( Context::HIDE !== $data['store_visibility'] ) {
						echo Store_Badges::render( Context::PENDING === $data['store_visibility'] ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					echo Html::button( $data['link_label'], $data['link'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>
			</div>
			<div class="xl-carousel xl-carousel--phones" data-xl-carousel role="region" aria-roledescription="<?php esc_attr_e( 'carousel', 'xlights-elementor-widgets' ); ?>" aria-label="<?php esc_attr_e( 'X LIGHTS app screens', 'xlights-elementor-widgets' ); ?>" data-xl-reveal style="--xl-i:1">
				<ul class="xl-carousel__track" role="list" tabindex="0">
					<?php foreach ( $items as $i => $item ) : ?>
						<li class="xl-carousel__slide xl-app-gallery__slide" aria-roledescription="<?php esc_attr_e( 'slide', 'xlights-elementor-widgets' ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: index, 2: total */ __( '%1$d of %2$d', 'xlights-elementor-widgets' ), $i + 1, $count ) ); ?>">
							<div class="xl-app-gallery__phone">
								<?php echo Illustrations::phone( isset( $item['screen'] ) ? $item['screen'] : 'zones', isset( $item['screenshot'] ) ? $item['screenshot'] : null ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>
							<h3 class="xl-app-gallery__title">
								<?php echo Icons::svg( isset( $item['icon'] ) ? $item['icon'] : 'smartphone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span><?php echo Html::text( ! empty( $item['title'] ) ? $item['title'] : $item['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							</h3>
							<?php if ( ! empty( $item['text'] ) ) : ?>
								<p class="xl-app-gallery__text"><?php echo Html::multiline( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $count > 1 ) : ?>
					<div class="xl-carousel__nav">
						<button type="button" class="xl-icon-btn" data-xl-prev aria-label="<?php esc_attr_e( 'Previous app screens', 'xlights-elementor-widgets' ); ?>"><?php echo Icons::svg( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
						<button type="button" class="xl-icon-btn" data-xl-next aria-label="<?php esc_attr_e( 'Next app screens', 'xlights-elementor-widgets' ); ?>"><?php echo Icons::svg( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( '' !== trim( $data['note'] ) ) : ?>
				<p class="xl-note xl-app-gallery__note"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
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
