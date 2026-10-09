<?php
/**
 * Review details meta box and list columns. A review is displayed only after
 * "Customer permission confirmed" is ticked.
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\PostTypes\Projects;
use XLights\PostTypes\Reviews;

defined( 'ABSPATH' ) || exit;

final class Review_Meta {

	const NONCE = 'xlights_review_meta';

	public static function init() {
		add_action( 'add_meta_boxes_' . Reviews::POST_TYPE, array( __CLASS__, 'boxes' ) );
		add_action( 'save_post_' . Reviews::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_filter( 'manage_' . Reviews::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . Reviews::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
		add_action( 'edit_form_after_title', array( __CLASS__, 'content_hint' ) );
	}

	/**
	 * @param string   $text Placeholder.
	 * @param \WP_Post $post Post.
	 * @return string
	 */
	public static function title_placeholder( $text, $post ) {
		return Reviews::POST_TYPE === $post->post_type ? __( 'Customer display name (e.g. "Jennifer M.")', 'xlights-elementor-widgets' ) : $text;
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function content_hint( $post ) {
		if ( Reviews::POST_TYPE === $post->post_type ) {
			echo '<p class="description" style="margin:12px 0 4px">' . esc_html__( 'Paste the review text exactly as the customer wrote it. Do not edit meaning or create reviews on a customer\'s behalf.', 'xlights-elementor-widgets' ) . '</p>';
		}
	}

	public static function boxes() {
		add_meta_box( 'xlights-review-details', __( 'Review details', 'xlights-elementor-widgets' ), array( __CLASS__, 'render' ), Reviews::POST_TYPE, 'normal', 'high' );
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function render( $post ) {
		wp_nonce_field( self::NONCE, '_xl_review_nonce' );
		$m = static function ( $key ) use ( $post ) {
			return get_post_meta( $post->ID, $key, true );
		};
		$projects = get_posts(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$photo    = (int) $m( Reviews::META_PHOTO );
		$src      = $photo ? wp_get_attachment_image_url( $photo, 'medium' ) : '';
		$attr     = 'brand' === $m( Reviews::META_ATTRIBUTION ) ? 'brand' : 'local';
		?>
		<div class="xl-review-permission">
			<label><input type="checkbox" name="xl_permission" value="1" <?php checked( '1' === $m( Reviews::META_PERMISSION ) ); ?>> <strong><?php esc_html_e( 'Customer permission confirmed — publish this review', 'xlights-elementor-widgets' ); ?></strong></label>
			<p class="description"><?php esc_html_e( 'Required. Reviews without confirmed permission never appear on the site, even when published.', 'xlights-elementor-widgets' ); ?></p>
		</div>
		<table class="form-table" role="presentation"><tbody>
			<tr><th><label for="xl-rating"><?php esc_html_e( 'Rating', 'xlights-elementor-widgets' ); ?></label></th><td>
				<select id="xl-rating" name="xl_rating">
					<option value="0"><?php esc_html_e( 'No rating shown', 'xlights-elementor-widgets' ); ?></option>
					<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
						<option value="<?php echo (int) $i; ?>" <?php selected( (int) $m( Reviews::META_RATING ), $i ); ?>><?php echo esc_html( sprintf( /* translators: %d: stars */ _n( '%d star', '%d stars', $i, 'xlights-elementor-widgets' ), $i ) ); ?></option>
					<?php endfor; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Only enter the rating the customer actually gave.', 'xlights-elementor-widgets' ); ?></p>
			</td></tr>
			<tr><th><label for="xl-source"><?php esc_html_e( 'Source / platform', 'xlights-elementor-widgets' ); ?></label></th><td>
				<select id="xl-source" name="xl_source">
					<option value=""><?php esc_html_e( '—', 'xlights-elementor-widgets' ); ?></option>
					<?php foreach ( Reviews::sources() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $m( Reviews::META_SOURCE ), $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><label for="xl-source-url"><?php esc_html_e( 'Link to original review', 'xlights-elementor-widgets' ); ?></label></th><td><input type="url" class="regular-text" id="xl-source-url" name="xl_source_url" value="<?php echo esc_attr( $m( Reviews::META_SOURCE_URL ) ); ?>"></td></tr>
			<tr><th><label for="xl-date"><?php esc_html_e( 'Review date', 'xlights-elementor-widgets' ); ?></label></th><td><input type="date" id="xl-date" name="xl_review_date" value="<?php echo esc_attr( $m( Reviews::META_DATE ) ); ?>"></td></tr>
			<tr><th><label for="xl-location"><?php esc_html_e( 'Location (city)', 'xlights-elementor-widgets' ); ?></label></th><td><input type="text" class="regular-text" id="xl-location" name="xl_location" value="<?php echo esc_attr( $m( Reviews::META_LOCATION ) ); ?>"></td></tr>
			<tr><th><label for="xl-project"><?php esc_html_e( 'Associated project', 'xlights-elementor-widgets' ); ?></label></th><td>
				<select id="xl-project" name="xl_project">
					<option value="0"><?php esc_html_e( 'None', 'xlights-elementor-widgets' ); ?></option>
					<?php foreach ( $projects as $p ) : ?>
						<option value="<?php echo (int) $p->ID; ?>" <?php selected( (int) $m( Reviews::META_PROJECT ), $p->ID ); ?>><?php echo esc_html( $p->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</td></tr>
			<tr><th><?php esc_html_e( 'Attribution', 'xlights-elementor-widgets' ); ?></th><td>
				<fieldset>
					<label><input type="radio" name="xl_attribution" value="local" <?php checked( $attr, 'local' ); ?>> <?php esc_html_e( 'Local dealer customer', 'xlights-elementor-widgets' ); ?></label><br>
					<label><input type="radio" name="xl_attribution" value="brand" <?php checked( $attr, 'brand' ); ?>> <?php esc_html_e( 'Brand-level (another X LIGHTS location) — labeled on the site', 'xlights-elementor-widgets' ); ?></label>
				</fieldset>
			</td></tr>
			<tr><th><?php esc_html_e( 'Featured', 'xlights-elementor-widgets' ); ?></th><td><label><input type="checkbox" name="xl_featured" value="1" <?php checked( '1' === $m( Reviews::META_FEATURED ) ); ?>> <?php esc_html_e( 'Show in featured review sections', 'xlights-elementor-widgets' ); ?></label></td></tr>
			<tr><th><?php esc_html_e( 'Approved photo', 'xlights-elementor-widgets' ); ?></th><td>
				<div class="xl-media-field" data-xl-media>
					<input type="hidden" name="xl_photo" value="<?php echo (int) $photo; ?>">
					<div class="xl-media-field__preview"><?php echo $src ? '<img src="' . esc_url( $src ) . '" alt="">' : ''; ?></div>
					<button type="button" class="button" data-xl-media-pick><?php esc_html_e( 'Choose image', 'xlights-elementor-widgets' ); ?></button>
					<button type="button" class="button-link-delete" data-xl-media-clear<?php echo $photo ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'xlights-elementor-widgets' ); ?></button>
				</div>
			</td></tr>
		</tbody></table>
		<?php
	}

	/**
	 * @param int      $post_id Post.
	 * @param \WP_Post $post    Post.
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['_xl_review_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_xl_review_nonce'] ) ), self::NONCE ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$rating = isset( $_POST['xl_rating'] ) ? absint( $_POST['xl_rating'] ) : 0;
		update_post_meta( $post_id, Reviews::META_RATING, min( 5, $rating ) );

		$source = isset( $_POST['xl_source'] ) ? sanitize_key( wp_unslash( $_POST['xl_source'] ) ) : '';
		update_post_meta( $post_id, Reviews::META_SOURCE, isset( Reviews::sources()[ $source ] ) ? $source : '' );
		update_post_meta( $post_id, Reviews::META_SOURCE_URL, isset( $_POST['xl_source_url'] ) ? esc_url_raw( wp_unslash( $_POST['xl_source_url'] ), array( 'http', 'https' ) ) : '' );

		$date = isset( $_POST['xl_review_date'] ) ? sanitize_text_field( wp_unslash( $_POST['xl_review_date'] ) ) : '';
		update_post_meta( $post_id, Reviews::META_DATE, preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ? $date : '' );
		update_post_meta( $post_id, Reviews::META_LOCATION, isset( $_POST['xl_location'] ) ? substr( sanitize_text_field( wp_unslash( $_POST['xl_location'] ) ), 0, 100 ) : '' );

		$project = isset( $_POST['xl_project'] ) ? absint( $_POST['xl_project'] ) : 0;
		update_post_meta( $post_id, Reviews::META_PROJECT, $project && Projects::POST_TYPE === get_post_type( $project ) ? $project : 0 );

		$attr = isset( $_POST['xl_attribution'] ) && 'brand' === $_POST['xl_attribution'] ? 'brand' : 'local';
		update_post_meta( $post_id, Reviews::META_ATTRIBUTION, $attr );
		update_post_meta( $post_id, Reviews::META_FEATURED, empty( $_POST['xl_featured'] ) ? '0' : '1' );
		update_post_meta( $post_id, Reviews::META_PERMISSION, empty( $_POST['xl_permission'] ) ? '0' : '1' );

		$photo = isset( $_POST['xl_photo'] ) ? absint( $_POST['xl_photo'] ) : 0;
		update_post_meta( $post_id, Reviews::META_PHOTO, $photo && wp_attachment_is_image( $photo ) ? $photo : 0 );
	}

	/**
	 * @param array $cols Columns.
	 * @return array
	 */
	public static function columns( $cols ) {
		$date = isset( $cols['date'] ) ? $cols['date'] : '';
		unset( $cols['date'] );
		$cols['title']          = __( 'Customer', 'xlights-elementor-widgets' );
		$cols['xl_rating']      = __( 'Rating', 'xlights-elementor-widgets' );
		$cols['xl_source']      = __( 'Source', 'xlights-elementor-widgets' );
		$cols['xl_attribution'] = __( 'Attribution', 'xlights-elementor-widgets' );
		$cols['xl_permission']  = __( 'Permission', 'xlights-elementor-widgets' );
		$cols['xl_featured']    = __( 'Featured', 'xlights-elementor-widgets' );
		if ( $date ) {
			$cols['date'] = $date;
		}
		return $cols;
	}

	/**
	 * @param string $col     Column.
	 * @param int    $post_id Post.
	 */
	public static function column( $col, $post_id ) {
		switch ( $col ) {
			case 'xl_rating':
				$r = (int) get_post_meta( $post_id, Reviews::META_RATING, true );
				echo $r ? esc_html( str_repeat( '★', $r ) ) : '—';
				break;
			case 'xl_source':
				$s = get_post_meta( $post_id, Reviews::META_SOURCE, true );
				echo esc_html( isset( Reviews::sources()[ $s ] ) ? Reviews::sources()[ $s ] : '—' );
				break;
			case 'xl_attribution':
				echo 'brand' === get_post_meta( $post_id, Reviews::META_ATTRIBUTION, true ) ? esc_html__( 'Brand-level', 'xlights-elementor-widgets' ) : esc_html__( 'Local', 'xlights-elementor-widgets' );
				break;
			case 'xl_permission':
				echo '1' === get_post_meta( $post_id, Reviews::META_PERMISSION, true ) ? '<span class="dashicons dashicons-yes" aria-label="' . esc_attr__( 'Confirmed', 'xlights-elementor-widgets' ) . '"></span>' : '<strong class="xl-admin__todo">' . esc_html__( 'Not confirmed (hidden)', 'xlights-elementor-widgets' ) . '</strong>';
				break;
			case 'xl_featured':
				echo '1' === get_post_meta( $post_id, Reviews::META_FEATURED, true ) ? '<span class="dashicons dashicons-star-filled" aria-label="' . esc_attr__( 'Featured', 'xlights-elementor-widgets' ) . '"></span>' : '—';
				break;
		}
	}
}
