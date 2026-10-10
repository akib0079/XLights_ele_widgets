<?php
/**
 * Feature explorer. The tab list is revealed by JavaScript (ARIA tabs); without
 * it every feature is listed with its photo.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Illustrations;

defined( 'ABSPATH' ) || exit;

$items = $data['items'];
$count = count( $items );
$side  = 'left' === $data['media_position'] ? 'left' : 'right';
$label = wp_strip_all_tags( \XLights\Helpers\Tokens::replace( (string) $data['heading'] ) );
$pad   = static function ( $n ) {
	return str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
};
?>
<div class="xl-container">
	<?php if ( ! $items ) : ?>
		<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php echo Context::editor_hint( __( 'Add at least one feature in the section settings.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<div class="xl-showcase xl-showcase--media-<?php echo esc_attr( $side ); ?>" data-xl-showcase>
			<div class="xl-showcase__aside" data-xl-reveal>
				<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $count > 1 ) : ?>
					<div class="xl-showcase__tabs" data-xl-showcase-tabs data-label="<?php echo esc_attr( $label ); ?>" hidden>
						<?php foreach ( $items as $i => $item ) : ?>
							<button type="button" class="xl-showcase__tab" id="<?php echo esc_attr( $ctx->id( 'tab-' . $i ) ); ?>" aria-controls="<?php echo esc_attr( $ctx->id( 'panel-' . $i ) ); ?>" data-xl-showcase-tab>
								<span class="xl-showcase__num" aria-hidden="true"><?php echo esc_html( $pad( $i + 1 ) ); ?></span>
								<span class="xl-showcase__label"><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php
				$button = Html::button( $data['primary_label'], $data['primary_link'], 'primary' );
				if ( '' !== $button ) :
					?>
					<div class="xl-actions"><?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
			<div class="xl-showcase__stage" data-xl-reveal style="--xl-i:1">
				<?php
				foreach ( $items as $i => $item ) :
					$has_image = Html::has_image( $item['image'] );
					$points    = Html::lines( $item['points'] );
					?>
					<article class="xl-showcase__panel<?php echo 0 === $i ? ' is-active' : ''; ?>" id="<?php echo esc_attr( $ctx->id( 'panel-' . $i ) ); ?>" data-xl-showcase-panel>
						<div class="xl-showcase__media">
							<?php
							if ( $has_image ) {
								$img_args = array(
									'size'  => 'large',
									'sizes' => '(max-width: 1024px) 100vw, 58vw',
									'eager' => 0 === $i && $ctx->is_first(),
									// Description applies to bundled photos; Media Library photos keep their own alt.
									'alt'   => ( is_array( $item['image'] ) && ! empty( $item['image']['id'] ) ) ? null : $item['image_alt'],
								);
								echo Html::image( $item['image'], $img_args ); // phpcs:ignore WordPress.Security.EscapeOutput
							} else {
								echo Illustrations::scene( $item['scene'], 'warm' ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</div>
						<div class="xl-showcase__copy">
							<p class="xl-showcase__count" aria-hidden="true"><?php echo esc_html( $pad( $i + 1 ) . ' / ' . $pad( $count ) ); ?></p>
							<h3 class="xl-showcase__title"><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
							<?php if ( '' !== trim( $item['text'] ) ) : ?>
								<p class="xl-showcase__text"><?php echo Html::multiline( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php endif; ?>
							<?php if ( $points ) : ?>
								<ul class="xl-checklist">
									<?php foreach ( $points as $p ) : ?>
										<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<?php echo Html::button( $item['link_label'], $item['link'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
