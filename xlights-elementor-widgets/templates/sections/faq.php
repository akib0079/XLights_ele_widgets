<?php
/**
 * FAQ accordion built on <details>/<summary> (works without JavaScript).
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;

$split = 'split' === $data['layout'];
?>
<div class="xl-container<?php echo $split ? '' : ' xl-container--narrow'; ?>">
	<div class="xl-faq<?php echo $split ? ' xl-faq--split' : ''; ?>">
		<div class="xl-faq__intro" data-xl-reveal>
			<?php echo Html::section_head( $data, array( 'class' => $split ? '' : 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo Html::button( $data['link_label'], $data['link'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="xl-accordion" data-xl-accordion data-xl-reveal>
			<?php if ( ! $data['items'] ) : ?>
				<?php echo Context::editor_hint( __( 'Add questions in the section settings.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
			<?php foreach ( $data['items'] as $item ) : ?>
				<details class="xl-accordion__item<?php echo Context::PENDING === $item['visibility'] ? ' xl-is-pending' : ''; ?>">
					<summary class="xl-accordion__q">
						<span><?php echo Html::text( $item['question'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php echo Icons::svg( 'plus', 'xl-accordion__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</summary>
					<div class="xl-accordion__a">
						<?php if ( Context::PENDING === $item['visibility'] ) : ?>
							<?php echo Context::pending_badge( $item['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>
						<?php echo Html::rich( $item['answer'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</div>
