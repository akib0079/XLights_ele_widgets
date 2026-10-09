<?php
/**
 * Project details meta box (city, featured, completion date, gallery,
 * before/after photos) and admin list columns.
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\PostTypes\Projects;

defined( 'ABSPATH' ) || exit;

final class Project_Meta {

	const NONCE = 'xlights_project_meta';

	public static function init() {
		add_action( 'add_meta_boxes_' . Projects::POST_TYPE, array( __CLASS__, 'boxes' ) );
		add_action( 'save_post_' . Projects::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_filter( 'manage_' . Projects::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . Projects::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
	}

	public static function boxes() {
		add_meta_box( 'xlights-project-details', __( 'Project details', 'xlights-elementor-widgets' ), array( __CLASS__, 'details' ), Projects::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'xlights-project-gallery', __( 'Project photos', 'xlights-elementor-widgets' ), array( __CLASS__, 'gallery' ), Projects::POST_TYPE, 'normal', 'high' );
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function details( $post ) {
		wp_nonce_field( self::NONCE, '_xl_project_nonce' );
		$city      = get_post_meta( $post->ID, Projects::META_CITY, true );
		$featured  = '1' === get_post_meta( $post->ID, Projects::META_FEATURED, true );
		$completed = get_post_meta( $post->ID, Projects::META_COMPLETED, true );
		?>
		<p>
			<label for="xl-city"><strong><?php esc_html_e( 'City / area', 'xlights-elementor-widgets' ); ?></strong></label><br>
			<input type="text" id="xl-city" name="xl_city" class="regular-text" value="<?php echo esc_attr( $city ); ?>" placeholder="San Clemente">
			<br><span class="description"><?php esc_html_e( 'City or neighborhood only. Never publish a homeowner\'s street address without written approval.', 'xlights-elementor-widgets' ); ?></span>
		</p>
		<p>
			<label for="xl-completed"><strong><?php esc_html_e( 'Completion date (optional)', 'xlights-elementor-widgets' ); ?></strong></label><br>
			<input type="month" id="xl-completed" name="xl_completed" value="<?php echo esc_attr( $completed ); ?>">
		</p>
		<p>
			<label><input type="checkbox" name="xl_featured" value="1" <?php checked( $featured ); ?>> <strong><?php esc_html_e( 'Featured project', 'xlights-elementor-widgets' ); ?></strong></label>
			<br><span class="description"><?php esc_html_e( 'Featured projects appear on the home page and first in every gallery. Use "Order" (Page Attributes) to fine-tune the sequence.', 'xlights-elementor-widgets' ); ?></span>
		</p>
		<p class="description"><?php esc_html_e( 'Assign Lighting Categories and Property Type in the sidebar so the project appears in the right galleries and filters. Use the main project photo (Featured image) for the gallery thumbnail.', 'xlights-elementor-widgets' ); ?></p>
		<?php
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function gallery( $post ) {
		$ids    = Projects::gallery_ids( $post->ID );
		$before = (int) get_post_meta( $post->ID, Projects::META_BEFORE, true );
		$after  = (int) get_post_meta( $post->ID, Projects::META_AFTER, true );
		?>
		<div class="xl-gallery-field" data-xl-gallery>
			<p><strong><?php esc_html_e( 'Gallery photos', 'xlights-elementor-widgets' ); ?></strong> <span class="description"><?php esc_html_e( 'Drag to reorder. Images are stored in the Media Library; add alt text there.', 'xlights-elementor-widgets' ); ?></span></p>
			<input type="hidden" name="xl_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-xl-gallery-input>
			<ul class="xl-gallery-field__list" data-xl-gallery-list>
				<?php foreach ( $ids as $id ) : ?>
					<li data-id="<?php echo (int) $id; ?>"><?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?><button type="button" class="xl-gallery-field__remove" aria-label="<?php esc_attr_e( 'Remove image', 'xlights-elementor-widgets' ); ?>">&times;</button></li>
				<?php endforeach; ?>
			</ul>
			<button type="button" class="button" data-xl-gallery-add><?php esc_html_e( 'Add photos', 'xlights-elementor-widgets' ); ?></button>
		</div>
		<hr>
		<p><strong><?php esc_html_e( 'Before & after', 'xlights-elementor-widgets' ); ?></strong> <span class="description"><?php esc_html_e( 'Add both to include this project in Before & After comparisons and filters.', 'xlights-elementor-widgets' ); ?></span></p>
		<div class="xl-ba-fields">
			<?php
			foreach ( array(
				'xl_before' => array( __( 'Before photo', 'xlights-elementor-widgets' ), $before ),
				'xl_after'  => array( __( 'After photo', 'xlights-elementor-widgets' ), $after ),
			) as $name => $f ) :
				$src = $f[1] ? wp_get_attachment_image_url( $f[1], 'medium' ) : '';
				?>
				<div class="xl-media-field" data-xl-media>
					<p><?php echo esc_html( $f[0] ); ?></p>
					<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo (int) $f[1]; ?>">
					<div class="xl-media-field__preview"><?php echo $src ? '<img src="' . esc_url( $src ) . '" alt="">' : ''; ?></div>
					<button type="button" class="button" data-xl-media-pick><?php esc_html_e( 'Choose image', 'xlights-elementor-widgets' ); ?></button>
					<button type="button" class="button-link-delete" data-xl-media-clear<?php echo $f[1] ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'xlights-elementor-widgets' ); ?></button>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * @param int      $post_id Post.
	 * @param \WP_Post $post    Post.
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['_xl_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_xl_project_nonce'] ) ), self::NONCE ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$city = isset( $_POST['xl_city'] ) ? sanitize_text_field( wp_unslash( $_POST['xl_city'] ) ) : '';
		update_post_meta( $post_id, Projects::META_CITY, substr( $city, 0, 100 ) );

		$completed = isset( $_POST['xl_completed'] ) ? sanitize_text_field( wp_unslash( $_POST['xl_completed'] ) ) : '';
		update_post_meta( $post_id, Projects::META_COMPLETED, preg_match( '/^\d{4}-\d{2}$/', $completed ) ? $completed : '' );

		update_post_meta( $post_id, Projects::META_FEATURED, empty( $_POST['xl_featured'] ) ? '0' : '1' );

		$gallery = isset( $_POST['xl_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['xl_gallery'] ) ) : '';
		$ids     = array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $gallery ) ), 'wp_attachment_is_image' ) ) );
		update_post_meta( $post_id, Projects::META_GALLERY, implode( ',', array_slice( $ids, 0, 60 ) ) );

		foreach ( array(
			'xl_before' => Projects::META_BEFORE,
			'xl_after'  => Projects::META_AFTER,
		) as $field => $meta ) {
			$id = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0;
			update_post_meta( $post_id, $meta, $id && wp_attachment_is_image( $id ) ? $id : 0 );
		}
		Projects::sync_flags( $post_id );
	}

	/**
	 * @param array $cols Columns.
	 * @return array
	 */
	public static function columns( $cols ) {
		$new = array();
		foreach ( $cols as $key => $label ) {
			if ( 'title' === $key ) {
				$new['xl_thumb'] = '<span class="screen-reader-text">' . esc_html__( 'Photo', 'xlights-elementor-widgets' ) . '</span>';
			}
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['xl_city']     = __( 'City', 'xlights-elementor-widgets' );
				$new['xl_featured'] = __( 'Featured', 'xlights-elementor-widgets' );
				$new['xl_photos']   = __( 'Photos', 'xlights-elementor-widgets' );
			}
		}
		return $new;
	}

	/**
	 * @param string $col     Column.
	 * @param int    $post_id Post.
	 */
	public static function column( $col, $post_id ) {
		switch ( $col ) {
			case 'xl_thumb':
				echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
				break;
			case 'xl_city':
				echo esc_html( get_post_meta( $post_id, Projects::META_CITY, true ) );
				break;
			case 'xl_featured':
				echo '1' === get_post_meta( $post_id, Projects::META_FEATURED, true ) ? '<span class="dashicons dashicons-star-filled" aria-label="' . esc_attr__( 'Featured', 'xlights-elementor-widgets' ) . '"></span>' : '—';
				break;
			case 'xl_photos':
				$count = count( Projects::gallery_ids( $post_id ) ) + ( has_post_thumbnail( $post_id ) ? 1 : 0 );
				echo (int) $count . ( '1' === get_post_meta( $post_id, Projects::META_HAS_BA, true ) ? ' · ' . esc_html__( 'B/A', 'xlights-elementor-widgets' ) : '' );
				break;
		}
	}
}
