<?php
/**
 * Before/after comparison sliders.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;

$pairs = $data['pairs'];
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php if ( ! $pairs ) : ?>
		<?php echo Context::editor_hint( __( 'No comparison images yet. Add before & after photos to a project, or choose two images in this section. Hidden on the live site until images exist.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<div class="xl-grid xl-grid--<?php echo count( $pairs ) > 1 ? '2' : '1'; ?> xl-ba-list">
			<?php foreach ( $pairs as $i => $pair ) : ?>
				<figure class="xl-ba-item" data-xl-reveal>
					<div class="xl-ba" data-xl-ba style="--xl-pos:50%">
						<div class="xl-ba__layer xl-ba__after">
							<?php echo Html::image( $pair['after'], array( 'size' => 'large', 'sizes' => '(max-width: 767px) 100vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<div class="xl-ba__layer xl-ba__before">
							<?php echo Html::image( $pair['before'], array( 'size' => 'large', 'sizes' => '(max-width: 767px) 100vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<span class="xl-ba__label xl-ba__label--before" aria-hidden="true"><?php echo Html::text( $data['before_label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="xl-ba__label xl-ba__label--after" aria-hidden="true"><?php echo Html::text( $data['after_label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="xl-ba__handle" aria-hidden="true"><?php echo Icons::svg( 'compare' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<label class="xl-sr" for="<?php echo esc_attr( $ctx->id( 'ba-' . $i ) ); ?>">
							<?php
							/* translators: 1: before label, 2: after label */
							echo esc_html( sprintf( __( 'Comparison slider: move to reveal %1$s or %2$s', 'xlights-elementor-widgets' ), \XLights\Helpers\Tokens::replace( $data['before_label'] ), \XLights\Helpers\Tokens::replace( $data['after_label'] ) ) );
							?>
						</label>
						<input class="xl-ba__range" id="<?php echo esc_attr( $ctx->id( 'ba-' . $i ) ); ?>" type="range" min="0" max="100" value="50" step="1">
					</div>
					<?php if ( '' !== trim( $pair['caption'] ) ) : ?>
						<figcaption>
							<?php if ( $pair['url'] ) : ?>
								<a href="<?php echo esc_url( $pair['url'] ); ?>"><?php echo Html::text( $pair['caption'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php else : ?>
								<?php echo Html::text( $pair['caption'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
