<?php
/**
 * Lead management: list columns, status filter, detail view, status/notes,
 * resend notification and CSV export (formula-injection safe).
 *
 * @package XLights
 */

namespace XLights\Admin;

use XLights\Forms\File_Access;
use XLights\Forms\Mailer;
use XLights\Forms\Quote_Form_Handler;
use XLights\PostTypes\Leads;

defined( 'ABSPATH' ) || exit;

final class Leads_Admin {

	const NONCE = 'xlights_lead_admin';

	public static function init() {
		add_filter( 'manage_' . Leads::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . Leads::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'filters' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_filters' ) );
		add_action( 'add_meta_boxes_' . Leads::POST_TYPE, array( __CLASS__, 'boxes' ) );
		add_action( 'save_post_' . Leads::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_post_xlights_export_leads', array( __CLASS__, 'export' ) );
		add_action( 'admin_post_xlights_resend_lead', array( __CLASS__, 'resend' ) );
		add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_filter( 'bulk_actions-edit-' . Leads::POST_TYPE, array( __CLASS__, 'bulk_actions' ) );
	}

	/**
	 * @param array $cols Columns.
	 * @return array
	 */
	public static function columns( $cols ) {
		return array(
			'cb'           => isset( $cols['cb'] ) ? $cols['cb'] : '',
			'title'        => __( 'Lead', 'xlights-elementor-widgets' ),
			'xl_contact'   => __( 'Contact', 'xlights-elementor-widgets' ),
			'xl_type'      => __( 'Type', 'xlights-elementor-widgets' ),
			'xl_interests' => __( 'Interest', 'xlights-elementor-widgets' ),
			'xl_status'    => __( 'Status', 'xlights-elementor-widgets' ),
			'xl_mail'      => __( 'Email', 'xlights-elementor-widgets' ),
			'date'         => __( 'Received', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @param string $col     Column.
	 * @param int    $post_id Post.
	 */
	public static function column( $col, $post_id ) {
		$get = static function ( $key ) use ( $post_id ) {
			return get_post_meta( $post_id, '_xl_' . $key, true );
		};
		switch ( $col ) {
			case 'xl_contact':
				echo esc_html( $get( 'phone' ) ) . '<br><a href="' . esc_url( 'mailto:' . $get( 'email' ) ) . '">' . esc_html( $get( 'email' ) ) . '</a>';
				break;
			case 'xl_type':
				echo 'commercial' === $get( 'property_type' ) ? esc_html__( 'Commercial', 'xlights-elementor-widgets' ) . ( $get( 'company' ) ? '<br><small>' . esc_html( $get( 'company' ) ) . '</small>' : '' ) : esc_html__( 'Residential', 'xlights-elementor-widgets' );
				break;
			case 'xl_interests':
				echo esc_html( Quote_Form_Handler::interests_label( (array) $get( 'interests' ) ) );
				$files = $get( 'files' );
				if ( is_array( $files ) && $files ) {
					echo '<br><small>' . esc_html( sprintf( /* translators: %d: photos */ _n( '%d photo', '%d photos', count( $files ), 'xlights-elementor-widgets' ), count( $files ) ) ) . '</small>';
				}
				break;
			case 'xl_status':
				$statuses = Leads::statuses();
				$status   = $get( 'status' ) ? $get( 'status' ) : 'new';
				echo '<span class="xl-lead-status xl-lead-status--' . esc_attr( $status ) . '">' . esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status ) . '</span>';
				break;
			case 'xl_mail':
				$mail = $get( 'mail_status' );
				if ( 'failed' === $mail ) {
					echo '<strong class="xl-admin__todo">' . esc_html__( 'Failed', 'xlights-elementor-widgets' ) . '</strong>';
				} elseif ( 'accepted' === $mail ) {
					echo esc_html__( 'Sent', 'xlights-elementor-widgets' );
				} else {
					echo '—';
				}
				break;
		}
	}

	/**
	 * @param string $post_type Post type.
	 */
	public static function filters( $post_type ) {
		if ( Leads::POST_TYPE !== $post_type ) {
			return;
		}
		$current = isset( $_GET['xl_status'] ) ? sanitize_key( wp_unslash( $_GET['xl_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<label class="screen-reader-text" for="xl-status-filter">' . esc_html__( 'Filter by status', 'xlights-elementor-widgets' ) . '</label>';
		echo '<select name="xl_status" id="xl-status-filter"><option value="">' . esc_html__( 'All statuses', 'xlights-elementor-widgets' ) . '</option>';
		foreach ( Leads::statuses() as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $current, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		$export = wp_nonce_url( admin_url( 'admin-post.php?action=xlights_export_leads' . ( $current ? '&xl_status=' . $current : '' ) ), 'xlights_export_leads' );
		echo ' <a class="button" href="' . esc_url( $export ) . '">' . esc_html__( 'Export CSV', 'xlights-elementor-widgets' ) . '</a>';
	}

	/**
	 * @param \WP_Query $query Query.
	 */
	public static function apply_filters( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || Leads::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}
		$status = isset( $_GET['xl_status'] ) ? sanitize_key( wp_unslash( $_GET['xl_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $status && isset( Leads::statuses()[ $status ] ) ) {
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => '_xl_status',
						'value' => $status,
					),
				)
			);
		}
	}

	/**
	 * @param array $actions Bulk actions.
	 * @return array
	 */
	public static function bulk_actions( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * @param array    $actions Actions.
	 * @param \WP_Post $post    Post.
	 * @return array
	 */
	public static function row_actions( $actions, $post ) {
		if ( Leads::POST_TYPE === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'], $actions['view'] );
		}
		return $actions;
	}

	public static function boxes() {
		remove_meta_box( 'slugdiv', Leads::POST_TYPE, 'normal' );
		add_meta_box( 'xlights-lead-details', __( 'Request details', 'xlights-elementor-widgets' ), array( __CLASS__, 'details' ), Leads::POST_TYPE, 'normal', 'high' );
		add_meta_box( 'xlights-lead-photos', __( 'Photos', 'xlights-elementor-widgets' ), array( __CLASS__, 'photos' ), Leads::POST_TYPE, 'normal', 'default' );
		add_meta_box( 'xlights-lead-status', __( 'Status & notes', 'xlights-elementor-widgets' ), array( __CLASS__, 'status' ), Leads::POST_TYPE, 'side', 'high' );
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function details( $post ) {
		$lead = Leads::get( $post->ID );
		echo '<table class="widefat striped xl-lead-table"><tbody>';
		foreach ( Mailer::lead_rows( $lead ) as $row ) {
			if ( '' === trim( (string) $row[1] ) ) {
				continue;
			}
			echo '<tr><th scope="row">' . esc_html( $row[0] ) . '</th><td>' . nl2br( esc_html( $row[1] ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
		$actions = array();
		if ( is_email( $lead['email'] ) ) {
			$actions[] = '<a class="button" href="' . esc_url( 'mailto:' . $lead['email'] ) . '">' . esc_html__( 'Email customer', 'xlights-elementor-widgets' ) . '</a>';
		}
		if ( $lead['phone'] ) {
			$actions[] = '<a class="button" href="' . esc_url( \XLights\Helpers\Tokens::tel_href( $lead['phone'] ) ) . '">' . esc_html__( 'Call', 'xlights-elementor-widgets' ) . '</a>';
		}
		$map       = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( trim( $lead['address'] . ', ' . $lead['city'] . ', ' . $lead['state'] . ' ' . $lead['zip'] ) );
		$actions[] = '<a class="button" href="' . esc_url( $map ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Open address in Maps', 'xlights-elementor-widgets' ) . '</a>';
		echo '<p class="xl-lead-actions">' . implode( ' ', $actions ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function photos( $post ) {
		$lead = Leads::get( $post->ID );
		if ( ! $lead['files'] ) {
			echo '<p>' . esc_html__( 'No photos were uploaded.', 'xlights-elementor-widgets' ) . '</p>';
			return;
		}
		echo '<p class="description">' . esc_html__( 'Stored privately; only users who can manage leads can open them.', 'xlights-elementor-widgets' ) . '</p><ul class="xl-lead-photos">';
		foreach ( $lead['files'] as $i => $file ) {
			$url = File_Access::url( $post->ID, $i );
			echo '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $file['name'] ) . '" loading="lazy"></a><span>' . esc_html( $file['name'] ) . ' · ' . esc_html( size_format( (int) $file['size'] ) ) . '</span></li>';
		}
		echo '</ul>';
	}

	/**
	 * @param \WP_Post $post Post.
	 */
	public static function status( $post ) {
		$lead = Leads::get( $post->ID );
		wp_nonce_field( self::NONCE, '_xl_lead_nonce' );
		echo '<p><label for="xl-lead-status"><strong>' . esc_html__( 'Status', 'xlights-elementor-widgets' ) . '</strong></label><br><select id="xl-lead-status" name="xl_status" class="widefat">';
		foreach ( Leads::statuses() as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $lead['status'], $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p>';
		echo '<p><label for="xl-lead-notes"><strong>' . esc_html__( 'Internal notes', 'xlights-elementor-widgets' ) . '</strong></label><textarea id="xl-lead-notes" name="xl_notes" class="widefat" rows="6">' . esc_textarea( (string) $lead['notes'] ) . '</textarea></p>';
		echo '<hr><p><strong>' . esc_html__( 'Notification email', 'xlights-elementor-widgets' ) . '</strong><br>';
		if ( 'failed' === $lead['mail_status'] ) {
			echo '<span class="xl-admin__todo">' . esc_html__( 'Failed:', 'xlights-elementor-widgets' ) . '</span> ' . esc_html( $lead['mail_error'] );
		} elseif ( 'accepted' === $lead['mail_status'] ) {
			echo esc_html__( 'Accepted by the mail server (delivery not guaranteed).', 'xlights-elementor-widgets' );
		} else {
			echo '—';
		}
		$resend = wp_nonce_url( admin_url( 'admin-post.php?action=xlights_resend_lead&lead=' . (int) $post->ID ), 'xlights_resend_' . $post->ID );
		echo '</p><p><a class="button" href="' . esc_url( $resend ) . '">' . esc_html__( 'Resend notification', 'xlights-elementor-widgets' ) . '</a></p>';
	}

	/**
	 * @param int      $post_id Post.
	 * @param \WP_Post $post    Post.
	 */
	public static function save( $post_id, $post ) {
		if ( ! isset( $_POST['_xl_lead_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_xl_lead_nonce'] ) ), self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( Leads::CAP ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		$status = isset( $_POST['xl_status'] ) ? sanitize_key( wp_unslash( $_POST['xl_status'] ) ) : 'new';
		update_post_meta( $post_id, '_xl_status', isset( Leads::statuses()[ $status ] ) ? $status : 'new' );
		update_post_meta( $post_id, '_xl_notes', isset( $_POST['xl_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['xl_notes'] ) ) : '' );
		// Leads stay private regardless of the publish box.
		if ( 'private' !== $post->post_status && 'trash' !== $post->post_status ) {
			remove_action( 'save_post_' . Leads::POST_TYPE, array( __CLASS__, 'save' ), 10 );
			wp_update_post(
				array(
					'ID'          => $post_id,
					'post_status' => 'private',
				)
			);
			add_action( 'save_post_' . Leads::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		}
	}

	public static function resend() {
		$lead_id = isset( $_GET['lead'] ) ? absint( $_GET['lead'] ) : 0;
		if ( ! current_user_can( Leads::CAP ) ) {
			wp_die( esc_html__( 'Not allowed.', 'xlights-elementor-widgets' ), 403 );
		}
		check_admin_referer( 'xlights_resend_' . $lead_id );
		if ( Leads::POST_TYPE !== get_post_type( $lead_id ) ) {
			wp_die( esc_html__( 'Lead not found.', 'xlights-elementor-widgets' ), 404 );
		}
		$mail = Mailer::send_lead( $lead_id );
		update_post_meta( $lead_id, '_xl_mail_status', $mail['ok'] ? 'accepted' : 'failed' );
		update_post_meta( $lead_id, '_xl_mail_error', $mail['ok'] ? '' : $mail['error'] );
		wp_safe_redirect( add_query_arg( 'xl_resent', $mail['ok'] ? '1' : '0', get_edit_post_link( $lead_id, 'raw' ) ) );
		exit;
	}

	public static function notices() {
		if ( isset( $_GET['xl_resent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$ok = '1' === $_GET['xl_resent']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice ' . ( $ok ? 'notice-success' : 'notice-error' ) . ' is-dismissible"><p>' . esc_html( $ok ? __( 'Notification accepted by the mail server.', 'xlights-elementor-widgets' ) : __( 'Notification failed again. Check your SMTP configuration.', 'xlights-elementor-widgets' ) ) . '</p></div>';
		}
	}

	/**
	 * Neutralize spreadsheet formulas in CSV cells.
	 *
	 * @param mixed $value Value.
	 * @return string
	 */
	public static function csv_cell( $value ) {
		$value = is_array( $value ) ? implode( ', ', $value ) : (string) $value;
		$value = str_replace( array( "\r\n", "\r" ), "\n", $value );
		if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			$value = "'" . $value;
		}
		return $value;
	}

	public static function export() {
		if ( ! current_user_can( Leads::CAP ) ) {
			wp_die( esc_html__( 'Not allowed.', 'xlights-elementor-widgets' ), 403 );
		}
		check_admin_referer( 'xlights_export_leads' );
		$status = isset( $_GET['xl_status'] ) ? sanitize_key( wp_unslash( $_GET['xl_status'] ) ) : '';
		$args   = array(
			'post_type'      => Leads::POST_TYPE,
			'post_status'    => array( 'private', 'publish', 'draft', 'pending' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		if ( $status && isset( Leads::statuses()[ $status ] ) ) {
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_xl_status',
					'value' => $status,
				),
			);
		}
		$ids = get_posts( $args );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="xlights-leads-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'X-Content-Type-Options: nosniff' );
		$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		$cols = array( 'id', 'date', 'status', 'first_name', 'last_name', 'phone', 'email', 'property_type', 'company', 'business_type', 'address', 'city', 'state', 'zip', 'interests', 'message', 'photos', 'dealer', 'variant', 'source_url', 'mail_status', 'notes' );
		fputcsv( $out, $cols );
		foreach ( $ids as $id ) {
			$lead = Leads::get( $id );
			$row  = array();
			foreach ( $cols as $col ) {
				switch ( $col ) {
					case 'date':
						$row[] = wp_date( 'Y-m-d H:i', (int) $lead['date'] );
						break;
					case 'photos':
						$row[] = count( $lead['files'] );
						break;
					case 'interests':
						$row[] = self::csv_cell( Quote_Form_Handler::interests_label( $lead['interests'] ) );
						break;
					default:
						$row[] = self::csv_cell( isset( $lead[ $col ] ) ? $lead[ $col ] : '' );
				}
			}
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
}
