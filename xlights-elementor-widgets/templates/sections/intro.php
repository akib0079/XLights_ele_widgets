<?php
/**
 * Intro / editorial section.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;

$layout = in_array( $data['layout'], array( 'centered', 'split', 'note' ), true ) ? $data['layout'] : 'centered';
$points = Html::lines( $data['points'] );
$link   = Html::button( $data['link_label'], $data['link'], 'text' );
?>
<div class="xl-container<?php echo 'split' === $layout ? '' : ' xl-container--narrow'; ?>">
	<?php if ( 'split' === $layout ) : ?>
		<div class="xl-intro__split">
			<div data-xl-reveal>
				<?php echo Html::section_head( array( 'eyebrow' => $data['eyebrow'], 'heading' => $data['heading'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="xl-intro__body" data-xl-reveal>
				<div class="xl-prose"><?php echo Html::rich( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php if ( $points ) : ?>
					<ul class="xl-checklist">
						<?php foreach ( $points as $p ) : ?>
							<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	<?php else : ?>
		<div class="xl-intro__center" data-xl-reveal>
			<?php if ( 'note' === $layout ) : ?>
				<div class="xl-intro__icon"><?php echo Icons::svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $points ) : ?>
				<ul class="xl-pills">
					<?php foreach ( $points as $p ) : ?>
						<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	<?php endif; ?>
</div>
