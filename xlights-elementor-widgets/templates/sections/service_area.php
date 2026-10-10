<?php
/**
 * Service areas with a privacy-friendly, click-to-load map.
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
	<div class="xl-area<?php echo $data['map_url'] ? '' : ' xl-area--no-map'; ?>">
		<div class="xl-area__content" data-xl-reveal>
			<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $data['areas'] ) : ?>
				<ul class="xl-area__list" role="list">
					<?php foreach ( $data['areas'] as $area ) : ?>
						<li><?php echo Icons::svg( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $area ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<?php echo Context::editor_hint( __( 'Add verified service areas in X LIGHTS → Settings → Dealer.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
			<?php if ( '' !== trim( (string) $data['note'] ) ) : ?>
				<p class="xl-note"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<?php echo Html::button( $data['link_label'], $data['link'], 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $data['map_url'] ) : ?>
			<div class="xl-map" data-xl-map="<?php echo esc_url( $data['map_url'] ); ?>" data-xl-reveal>
				<div class="xl-map__placeholder">
					<?php
					// Decorative backdrop behind the click-to-load button (empty alt).
					echo Html::image(
						Html::asset_image( 'photos/soffit-house-dusk-wide.webp' ),
						array(
							'alt'   => '',
							'sizes' => '(max-width: 1024px) 100vw, 50vw',
							'class' => 'xl-map__photo',
						)
					); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
					<div class="xl-map__cta">
						<button type="button" class="xl-btn xl-btn--light" data-xl-map-load><?php echo Icons::svg( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Show service-area map', 'xlights-elementor-widgets' ); ?></span></button>
						<p><?php esc_html_e( 'Loads Google Maps, which may set cookies.', 'xlights-elementor-widgets' ); ?></p>
						<a href="<?php echo esc_url( str_replace( '&output=embed', '', $data['map_url'] ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open in Google Maps', 'xlights-elementor-widgets' ); ?><span class="xl-sr"><?php esc_html_e( '(opens in a new tab)', 'xlights-elementor-widgets' ); ?></span></a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
