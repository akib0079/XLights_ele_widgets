<?php
/**
 * Connected system diagram: products orbit the app hub.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;

$items = array_values( array_filter( (array) $data['items'] ) );
$count = count( $items );
?>
<div class="xl-container">
	<div class="xl-connected">
		<div class="xl-connected__intro" data-xl-reveal>
			<?php echo Html::section_head( $data ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( '' !== trim( $data['note'] ) ) : ?>
				<p class="xl-note"><?php echo Icons::svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<?php echo Html::button( $data['link_label'], $data['link'], 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="xl-connected__diagram" data-xl-reveal style="--xl-n:<?php echo (int) max( 1, $count ); ?>">
			<div class="xl-connected__hub">
				<span class="xl-connected__hub-icon"><?php echo Icons::svg( 'smartphone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<strong><?php echo Html::text( $data['hub_label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></strong>
				<?php if ( '' !== trim( $data['hub_text'] ) ) : ?>
					<span><?php echo Html::text( $data['hub_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>
			</div>
			<ul class="xl-connected__nodes" role="list">
				<?php
				foreach ( $items as $i => $item ) :
					$item = wp_parse_args(
						(array) $item,
						array(
							'title' => '',
							'text'  => '',
							'icon'  => 'lightbulb',
							'link'  => '',
						)
					);
					$link = Html::link_attrs( $item['link'] );
					?>
					<li class="xl-connected__node xl-connected__node--<?php echo (int) $i; ?>" style="--xl-i:<?php echo (int) $i; ?>">
						<span class="xl-connected__icon"><?php echo Icons::svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="xl-connected__text">
							<strong>
								<?php if ( $link ) : ?>
									<a<?php echo Html::attrs( $link ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<?php else : ?>
									<?php echo Html::text( $item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php endif; ?>
							</strong>
							<?php if ( '' !== trim( $item['text'] ) ) : ?>
								<small><?php echo Html::text( $item['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></small>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
