<?php
/**
 * Project case study: headline and facts, the story, a takeaway line and a
 * photo gallery (first photo large). Photos open in the lightbox; without
 * JavaScript they are plain images.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Html;

defined( 'ABSPATH' ) || exit;

$photos = $data['photos'];
$items  = array();
foreach ( $photos as $i => $photo ) {
	$item = Html::lightbox_item( $photo['image'], $photo['image_alt'], $photo['caption'] );
	if ( $item ) {
		$items[ $i ] = $item;
	}
}
$json    = wp_json_encode( array_values( $items ) );
$count   = count( $photos );
$heading = wp_strip_all_tags( \XLights\Helpers\Tokens::replace( (string) $data['heading'] ) );
?>
<div class="xl-container">
	<div class="xl-case">
		<div class="xl-case__intro" data-xl-reveal>
			<?php echo Html::section_head( array( 'eyebrow' => $data['eyebrow'], 'heading' => $data['heading'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $data['facts'] ) : ?>
				<dl class="xl-case__facts">
					<?php foreach ( $data['facts'] as $fact ) : ?>
						<div class="xl-case__fact">
							<dt><?php echo Html::text( $fact['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dt>
							<dd><?php echo Html::text( $fact['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>
		<div class="xl-case__story" data-xl-reveal style="--xl-i:1">
			<?php if ( '' !== trim( (string) $data['text'] ) ) : ?>
				<div class="xl-prose"><?php echo Html::rich( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php if ( '' !== trim( (string) $data['takeaway'] ) ) : ?>
				<p class="xl-case__takeaway"><?php echo Html::text( $data['takeaway'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<?php
			$button = Html::button( $data['primary_label'], $data['primary_link'], 'primary', array( 'icon' => 'arrow-right' ) );
			if ( '' !== $button ) :
				?>
				<div class="xl-actions"><?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $photos ) : ?>
			<ul class="xl-case__gallery xl-case__gallery--<?php echo (int) min( $count, 4 ); ?>" role="list" data-xl-reveal style="--xl-i:2">
				<?php foreach ( $photos as $i => $photo ) : ?>
					<li class="xl-case__photo">
						<figure>
							<?php
							$img = Html::image(
								$photo['image'],
								array(
									'size'  => 'large',
									'sizes' => 0 === $i ? '(max-width: 767px) 100vw, 45vw' : '(max-width: 767px) 50vw, 28vw',
									// Description applies to bundled photos; Media Library photos keep their own alt.
									'alt'   => ( is_array( $photo['image'] ) && ! empty( $photo['image']['id'] ) ) ? null : $photo['image_alt'],
								)
							);
							if ( isset( $items[ $i ] ) ) :
								$position = array_search( $i, array_keys( $items ), true );
								?>
								<button type="button" class="xl-case__open" data-xl-lightbox="<?php echo esc_attr( $json ); ?>" data-xl-index="<?php echo (int) $position; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: photo number, 2: section heading */ __( 'Enlarge photo %1$d: %2$s', 'xlights-elementor-widgets' ), $i + 1, $heading ) ); ?>">
									<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</button>
							<?php else : ?>
								<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $photo['caption'] ) ) : ?>
								<figcaption><?php echo Html::text( $photo['caption'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figcaption>
							<?php endif; ?>
						</figure>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
