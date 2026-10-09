<?php
/**
 * Split image/content section.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Sections\Section_Renderer;

defined( 'ABSPATH' ) || exit;

$points  = Html::lines( $data['points'] );
$visual  = Section_Renderer::visual( $data, $ctx, array( 'eager' => $ctx->is_first() ) );
$buttons = Html::button( $data['primary_label'], $data['primary_link'], 'primary' ) . Html::button( $data['secondary_label'], $data['secondary_link'], 'text' );
?>
<div class="xl-container">
	<?php if ( Context::PENDING === $data['visibility'] ) : ?>
		<?php echo Context::pending_badge( $data['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>
	<div class="xl-split<?php echo '' === $visual ? ' xl-split--no-visual' : ''; ?>">
		<div class="xl-split__content" data-xl-reveal>
			<?php echo Html::section_head( array( 'eyebrow' => $data['eyebrow'], 'heading' => $data['heading'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( '' !== trim( $data['text'] ) ) : ?>
				<div class="xl-prose"><?php echo Html::rich( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php if ( $points ) : ?>
				<ul class="xl-checklist">
					<?php foreach ( $points as $p ) : ?>
						<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( '' !== $buttons ) : ?>
				<div class="xl-actions"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php if ( '' !== trim( $data['note'] ) ) : ?>
				<p class="xl-note"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
		</div>
		<?php if ( '' !== $visual ) : ?>
			<figure class="<?php echo esc_attr( Section_Renderer::visual_class( $data ) ); ?> xl-split__visual" data-xl-reveal>
				<div class="xl-media__frame">
					<?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php if ( '' !== trim( $data['badge'] ) ) : ?>
						<span class="xl-media__badge"><?php echo Html::text( $data['badge'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
				</div>
				<?php if ( '' !== trim( $data['caption'] ) ) : ?>
					<figcaption><?php echo Html::text( $data['caption'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
	</div>
</div>
