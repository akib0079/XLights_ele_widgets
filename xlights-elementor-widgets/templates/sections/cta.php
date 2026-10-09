<?php
/**
 * CTA banner.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Frontend\Store_Badges;
use XLights\Helpers\Context;
use XLights\Helpers\Html;

defined( 'ABSPATH' ) || exit;

$style   = in_array( $data['style'], array( 'panel', 'image', 'band' ), true ) ? $data['style'] : 'panel';
$overlay = is_array( $data['overlay'] ) ? (float) $data['overlay']['size'] : (float) $data['overlay'];

$buttons  = Html::button( $data['primary_label'], $data['primary_link'], 'primary', array( 'icon' => 'arrow-right' ) );
$buttons .= Html::button( $data['secondary_label'], $data['secondary_link'], 'ghost' );
if ( 'yes' === $data['show_phone'] ) {
	$buttons .= Html::call_button( 'ghost' );
}
if ( Context::HIDE !== $data['store_visibility'] ) {
	$buttons .= Store_Badges::render( Context::PENDING === $data['store_visibility'] );
}
?>
<div class="xl-container">
	<?php if ( Context::PENDING === $data['visibility'] ) : ?>
		<?php echo Context::pending_badge( $data['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
	<div class="xl-cta" data-xl-reveal>
		<?php if ( 'image' === $style && Html::has_image( $data['image'] ) ) : ?>
			<div class="xl-cta__media" style="--xl-overlay:<?php echo esc_attr( max( 0, min( 95, $overlay ) ) / 100 ); ?>">
				<?php echo Html::image( $data['image'], array( 'size' => 'full', 'sizes' => '100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
		<div class="xl-cta__glow" aria-hidden="true"></div>
		<div class="xl-cta__inner">
			<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( '' !== $buttons ) : ?>
				<div class="xl-actions xl-actions--center"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>
