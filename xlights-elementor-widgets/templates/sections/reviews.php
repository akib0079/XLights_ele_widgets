<?php
/**
 * Reviews: carousel, grid, or featured + grid.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\PostTypes\Reviews;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$reviews = $data['reviews'];
$layout  = in_array( $data['layout'], array( 'carousel', 'grid', 'featured' ), true ) ? $data['layout'] : 'carousel';
$opts    = array(
	'rating' => 'yes' === $data['show_rating'],
	'source' => 'yes' === $data['show_source'],
	'photo'  => 'yes' === $data['show_photo'],
);
$google  = 'yes' === $data['google_link'] ? (string) Settings::get( 'google_reviews_url' ) : '';
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

	<?php if ( ! $reviews ) : ?>
		<?php if ( 'message' === $data['empty_behavior'] ) : ?>
			<p class="xl-empty"><?php echo Html::text( $data['empty_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php endif; ?>
		<?php echo Context::editor_hint( __( 'No approved reviews yet. Add real customer reviews under X LIGHTS → Reviews (with permission confirmed). This section is hidden on the live site until reviews exist.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php elseif ( 'carousel' === $layout ) : ?>
		<div class="xl-carousel" data-xl-carousel role="region" aria-roledescription="<?php esc_attr_e( 'carousel', 'xlights-elementor-widgets' ); ?>" aria-label="<?php esc_attr_e( 'Customer reviews', 'xlights-elementor-widgets' ); ?>">
			<ul class="xl-carousel__track" role="list" tabindex="0">
				<?php foreach ( $reviews as $i => $review ) : ?>
					<li class="xl-carousel__slide" aria-roledescription="<?php esc_attr_e( 'slide', 'xlights-elementor-widgets' ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: index, 2: total */ __( '%1$d of %2$d', 'xlights-elementor-widgets' ), $i + 1, count( $reviews ) ) ); ?>">
						<?php echo Reviews::render_card( $review, $opts ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( count( $reviews ) > 1 ) : ?>
				<div class="xl-carousel__nav">
					<button type="button" class="xl-icon-btn" data-xl-prev aria-label="<?php esc_attr_e( 'Previous reviews', 'xlights-elementor-widgets' ); ?>"><?php echo Icons::svg( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					<button type="button" class="xl-icon-btn" data-xl-next aria-label="<?php esc_attr_e( 'Next reviews', 'xlights-elementor-widgets' ); ?>"><?php echo Icons::svg( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				</div>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php
		$list = $reviews;
		if ( 'featured' === $layout ) :
			$first = array_shift( $list );
			?>
			<div class="xl-review-feature" data-xl-reveal>
				<?php echo Reviews::render_card( $first, array_merge( $opts, array( 'large' => true ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
		<?php if ( $list ) : ?>
			<ul class="xl-grid xl-grid--3 xl-review-grid" role="list">
				<?php foreach ( $list as $review ) : ?>
					<li data-xl-reveal><?php echo Reviews::render_card( $review, $opts ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$actions = Html::button( $data['link_label'], $data['link'], 'secondary', array( 'icon' => 'arrow-right' ) );
	if ( '' !== $google && $reviews ) {
		$actions .= Html::button(
			__( 'Read reviews on Google', 'xlights-elementor-widgets' ),
			array(
				'url'         => $google,
				'is_external' => 'on',
			),
			'text',
			array( 'icon' => 'external' )
		);
	}
	if ( '' !== $actions ) :
		?>
		<div class="xl-actions xl-actions--center"><?php echo $actions; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>
</div>
