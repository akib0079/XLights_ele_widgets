<?php
/**
 * Technical specifications (approved rows only on the live site).
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;

defined( 'ABSPATH' ) || exit;
?>
<div class="xl-container xl-container--narrow">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php if ( ! $data['rows'] ) : ?>
		<?php echo Context::editor_hint( __( 'Add specification rows. Only rows marked "Verified" appear on the live site.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<?php
		$pending_rows = array_filter(
			$data['rows'],
			static function ( $r ) {
				return ! $r['is_approved'];
			}
		);
		if ( $pending_rows ) {
			echo Context::editor_hint( __( 'Rows marked "pending" are hidden on the live site until verified for this market.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
		<dl class="xl-specs" data-xl-reveal>
			<?php foreach ( $data['rows'] as $row ) : ?>
				<div class="xl-specs__row<?php echo $row['is_approved'] ? '' : ' xl-is-pending'; ?>">
					<dt><?php echo Html::text( $row['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dt>
					<dd>
						<?php echo '' !== trim( $row['value'] ) ? Html::text( $row['value'] ) : '&mdash;'; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php if ( ! $row['is_approved'] ) : ?>
							<em class="xl-specs__pending"><?php esc_html_e( 'pending verification', 'xlights-elementor-widgets' ); ?></em>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	<?php endif; ?>
	<?php if ( '' !== trim( $data['note'] ) ) : ?>
		<p class="xl-note xl-note--center"><?php echo Html::text( $data['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php endif; ?>
</div>
