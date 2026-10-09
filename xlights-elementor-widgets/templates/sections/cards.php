<?php
/**
 * Card grid.
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

$style   = in_array( $data['style'], array( 'icon', 'image', 'overlay', 'steps', 'minimal', 'bento', 'strip' ), true ) ? $data['style'] : 'icon';
$columns = in_array( (string) $data['columns'], array( '2', '3', '4' ), true ) ? (string) $data['columns'] : '3';
$align   = 'left' === $data['align'] ? '' : ' xl-head--center';
$heading = 'h3';
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => trim( $align ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php if ( ! $data['items'] ) : ?>
		<?php echo Context::editor_hint( __( 'Add cards in the section settings.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<ul class="xl-grid xl-grid--<?php echo esc_attr( $columns ); ?> xl-cards xl-cards--<?php echo esc_attr( $style ); ?>" role="list">
			<?php
			foreach ( $data['items'] as $i => $item ) :
				$link    = Html::link_attrs( $item['link'] );
				$pending = Context::PENDING === $item['visibility'];
				$classes = array( 'xl-card' );
				if ( $link ) {
					$classes[] = 'xl-card--linked';
				}
				if ( $pending ) {
					$classes[] = 'xl-is-pending';
				}
				$has_image = Html::has_image( $item['image'] );
				?>
				<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-xl-reveal style="--xl-i:<?php echo (int) $i; ?>">
					<?php if ( in_array( $style, array( 'image', 'overlay', 'bento' ), true ) ) : ?>
						<div class="xl-card__media<?php echo $has_image ? '' : ' xl-media--illustration'; ?>">
							<?php
							if ( $has_image ) {
								echo Html::image( $item['image'], array( 'size' => 'large', 'sizes' => '(max-width: 767px) 100vw, (max-width: 1024px) 50vw, 33vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							} else {
								echo Illustrations::scene( $item['scene'], $item['palette'] ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</div>
					<?php elseif ( 'steps' === $style ) : ?>
						<span class="xl-card__step" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<?php elseif ( '' !== $item['icon'] || ! empty( $item['icon_custom']['value'] ) ) : ?>
						<span class="xl-card__icon"><?php echo Html::icon( $item['icon'], isset( $item['icon_custom'] ) ? $item['icon_custom'] : null ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
					<div class="xl-card__body">
						<?php if ( $pending ) : ?>
							<?php echo Context::pending_badge( $item['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>
						<?php if ( '' !== trim( $item['title'] ) ) : ?>
							<<?php echo esc_html( $heading ); ?> class="xl-card__title">
								<?php if ( $link ) : ?>
									<a<?php echo Html::attrs( array_merge( $link, array( 'class' => 'xl-card__link' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<?php else : ?>
									<?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php endif; ?>
							</<?php echo esc_html( $heading ); ?>>
						<?php endif; ?>
						<?php if ( '' !== trim( $item['text'] ) && 'strip' !== $style ) : ?>
							<p class="xl-card__text"><?php echo Html::multiline( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php elseif ( '' !== trim( $item['text'] ) ) : ?>
							<p class="xl-card__text"><?php echo Html::text( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
						<?php if ( $link && '' !== trim( $item['link_label'] ) ) : ?>
							<span class="xl-card__more" aria-hidden="true"><?php echo Html::text( $item['link_label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo Icons::svg( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ( '' !== trim( $data['note'] ) ) : ?>
		<p class="xl-note xl-note--center"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php endif; ?>
	<?php
	$footer = Html::button( $data['footer_label'], $data['footer_link'], 'secondary', array( 'icon' => 'arrow-right' ) );
	if ( '' !== $footer ) :
		?>
		<div class="xl-actions xl-actions--center"><?php echo $footer; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>
</div>
