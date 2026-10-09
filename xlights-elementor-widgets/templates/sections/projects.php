<?php
/**
 * Project gallery with optional filters and load-more.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Helpers\Context;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\PostTypes\Projects;

defined( 'ABSPATH' ) || exit;

$query       = $data['query'];
$columns     = in_array( (string) $data['columns'], array( '2', '3', '4' ), true ) ? (string) $data['columns'] : '3';
$show_filter = 'yes' === $data['show_filters'];
$active      = $data['filter'];
$featured    = 'featured' === $data['source'];

// No-JS fallback: ?xl_filter=deck re-renders the grid server-side.
if ( $show_filter && isset( $_GET['xl_filter'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$requested = sanitize_key( wp_unslash( $_GET['xl_filter'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( Projects::filters()[ $requested ] ) && $requested !== $active ) {
		$active   = $requested;
		$featured = false;
		$query    = Projects::query(
			array(
				'filter' => $active,
				'count'  => $data['count'],
			)
		);
	}
}
$grid_id = $ctx->id( 'grid' );
?>
<div class="xl-container">
	<?php echo Html::section_head( $data, array( 'class' => 'xl-head--center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

	<div class="xl-projects" data-xl-projects data-count="<?php echo (int) $data['count']; ?>" data-filter="<?php echo esc_attr( $active ); ?>" data-featured="<?php echo $featured ? '1' : '0'; ?>" data-page="1" data-endpoint="<?php echo esc_url( rest_url( 'xlights/v1/projects' ) ); ?>">
		<?php if ( $show_filter ) : ?>
			<nav class="xl-filters" aria-label="<?php esc_attr_e( 'Filter projects', 'xlights-elementor-widgets' ); ?>">
				<ul role="list">
					<?php foreach ( Projects::filters() as $key => $label ) : ?>
						<li>
							<a class="xl-filter" href="<?php echo esc_url( add_query_arg( 'xl_filter', $key ) . '#' . $ctx->anchor ); ?>" data-xl-filter="<?php echo esc_attr( $key ); ?>" aria-controls="<?php echo esc_attr( $grid_id ); ?>"<?php echo $key === $active ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $label ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<ul class="xl-grid xl-grid--<?php echo esc_attr( $columns ); ?> xl-projects__grid" id="<?php echo esc_attr( $grid_id ); ?>" role="list" aria-live="polite" aria-busy="false">
			<?php
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					echo Projects::render_card( $post ); // phpcs:ignore WordPress.Security.EscapeOutput
				}
			}
			?>
		</ul>

		<p class="xl-projects__empty"<?php echo $query->have_posts() ? ' hidden' : ''; ?>>
			<?php echo Html::text( $data['empty_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</p>
		<?php
		if ( ! $query->have_posts() ) {
			echo Context::editor_hint( __( 'No published projects match yet. Add projects under X LIGHTS → Projects; this section stays hidden on the live site until projects exist (unless set to show a message).', 'xlights-elementor-widgets' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>

		<div class="xl-actions xl-actions--center">
			<?php if ( 'yes' === $data['load_more'] ) : ?>
				<button type="button" class="xl-btn xl-btn--secondary" data-xl-more<?php echo $query->max_num_pages > 1 ? '' : ' hidden'; ?>>
					<span><?php esc_html_e( 'Load more projects', 'xlights-elementor-widgets' ); ?></span><?php echo Icons::svg( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			<?php endif; ?>
			<?php echo Html::button( $data['link_label'], $data['link'], 'secondary', array( 'icon' => 'arrow-right' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</div>
</div>
