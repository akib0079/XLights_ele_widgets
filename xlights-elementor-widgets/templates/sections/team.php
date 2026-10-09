<?php
/**
 * Local team.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;

defined( 'ABSPATH' ) || exit;
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php if ( ! $data['members'] ) : ?>
		<?php echo Context::editor_hint( __( 'Add team members supplied by the dealer (names, roles, bios and approved photos). This section stays hidden on the live site until at least one member is added.', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php else : ?>
		<ul class="xl-grid xl-grid--<?php echo count( $data['members'] ) >= 4 ? '4' : '3'; ?> xl-team" role="list">
			<?php foreach ( $data['members'] as $m ) : ?>
				<li class="xl-team__member<?php echo Context::PENDING === $m['visibility'] ? ' xl-is-pending' : ''; ?>" data-xl-reveal>
					<div class="xl-team__photo">
						<?php
						if ( Html::has_image( $m['photo'] ) ) {
							echo Html::image( $m['photo'], array( 'size' => 'medium_large', 'sizes' => '(max-width: 767px) 100vw, 25vw', 'alt' => $m['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						} else {
							echo '<span class="xl-team__initials" aria-hidden="true">' . Icons::svg( 'user' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
					</div>
					<?php if ( Context::PENDING === $m['visibility'] ) : ?>
						<?php echo Context::pending_badge( $m['gate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
					<h3 class="xl-team__name"><?php echo Html::text( $m['name'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h3>
					<?php if ( '' !== trim( $m['role'] ) ) : ?>
						<p class="xl-team__role"><?php echo Html::text( $m['role'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $m['bio'] ) ) : ?>
						<p class="xl-team__bio"><?php echo Html::multiline( $m['bio'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
