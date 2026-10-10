<?php
/**
 * Split image/content section in three layouts:
 *  - standard:  photo and text side by side
 *  - offset:    editorial — large photo with a text card overlapping its edge
 *  - cinematic: full-width inset photo with a text panel over it
 * Offset and cinematic need a photo; app mockups and illustrations fall back
 * to standard.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Sections\Components\Split;
use XLights\Sections\Section_Renderer;

defined( 'ABSPATH' ) || exit;

$layout  = Split::layout( $data );
$points  = Html::lines( $data['points'] );
$buttons = Html::button( $data['primary_label'], $data['primary_link'], 'primary' ) . Html::button( $data['secondary_label'], $data['secondary_link'], 'text' );
$visual  = Section_Renderer::visual(
	$data,
	$ctx,
	'cinematic' === $layout
		? array(
			'eager' => $ctx->is_first(),
			'size'  => 'full',
			'sizes' => '100vw',
			'class' => 'xl-cine__img',
		)
		: array(
			'eager' => $ctx->is_first(),
			'sizes' => 'offset' === $layout ? '(max-width: 1024px) 100vw, 66vw' : '(max-width: 767px) 100vw, 50vw',
		)
);

ob_start();
echo Html::section_head( array( 'eyebrow' => $data['eyebrow'], 'heading' => $data['heading'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
if ( '' !== trim( $data['text'] ) ) :
	?>
	<div class="xl-prose"><?php echo Html::rich( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php
endif;
if ( $points ) :
	?>
	<ul class="xl-checklist">
		<?php foreach ( $points as $p ) : ?>
			<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
		<?php endforeach; ?>
	</ul>
	<?php
endif;
if ( '' !== $buttons ) :
	?>
	<div class="xl-actions"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php
endif;
if ( '' !== trim( $data['note'] ) ) :
	?>
	<p class="xl-note"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php
endif;
$content = (string) ob_get_clean();

$frame = '';
if ( '' !== $visual && 'cinematic' !== $layout ) {
	ob_start();
	?>
	<div class="xl-media__frame">
		<?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( '' !== trim( $data['badge'] ) ) : ?>
			<span class="xl-media__badge"><?php echo Html::text( $data['badge'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php endif; ?>
	</div>
	<?php if ( '' !== trim( $data['caption'] ) ) : ?>
		<figcaption><?php echo Html::text( $data['caption'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figcaption>
	<?php endif; ?>
	<?php
	$frame = (string) ob_get_clean();
}
$pending = Context::PENDING === $data['visibility'] ? Context::pending_badge( $data['gate'] ) : '';
$side    = isset( $data['image_position'] ) && 'left' === $data['image_position'] ? 'left' : 'right';

if ( 'cinematic' === $layout ) :
	?>
	<?php if ( '' !== $pending ) : ?>
		<div class="xl-container"><?php echo $pending; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>
	<div class="xl-cine xl-cine--panel-<?php echo 'left' === $side ? 'right' : 'left'; ?>">
		<div class="xl-cine__media" data-xl-reveal>
			<?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( '' !== trim( $data['badge'] ) ) : ?>
				<span class="xl-media__badge"><?php echo Html::text( $data['badge'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>
		</div>
		<div class="xl-container xl-cine__inner">
			<div class="xl-cine__panel" data-xl-reveal style="--xl-i:2"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
	</div>
	<?php
elseif ( 'offset' === $layout ) :
	?>
	<div class="xl-container">
		<?php echo $pending; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="xl-offset xl-offset--media-<?php echo esc_attr( $side ); ?>">
			<figure class="<?php echo esc_attr( Section_Renderer::visual_class( $data ) ); ?> xl-offset__media" data-xl-reveal><?php echo $frame; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
			<div class="xl-offset__card" data-xl-reveal style="--xl-i:2"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</div>
	</div>
	<?php
else :
	?>
	<div class="xl-container">
		<?php echo $pending; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="xl-split<?php echo '' === $visual ? ' xl-split--no-visual' : ''; ?>">
			<div class="xl-split__content" data-xl-reveal><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php if ( '' !== $frame ) : ?>
				<figure class="<?php echo esc_attr( Section_Renderer::visual_class( $data ) ); ?> xl-split__visual" data-xl-reveal><?php echo $frame; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
			<?php endif; ?>
		</div>
	</div>
	<?php
endif;
