<?php
/**
 * Hero section.
 *
 * @var array                          $data
 * @var \XLights\Sections\Render_Context $ctx
 * @package XLights
 */

use XLights\Frontend\Breadcrumbs;
use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Tokens;
use XLights\Sections\Section_Renderer;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$layout   = in_array( $data['layout'], array( 'media', 'split', 'centered' ), true ) ? $data['layout'] : 'media';
$tag      = $data['tag'];
$align    = 'center' === $data['align'] || 'centered' === $layout ? 'center' : 'left';
$title_id = $ctx->id( 'title' );
$in_h1    = 'yes' === $data['eyebrow_in_h1'] && '' !== trim( $data['eyebrow'] );
$eager    = $ctx->is_first();
$crumbs   = ( 'yes' === $data['breadcrumbs'] && Settings::enabled( 'breadcrumbs' ) ) ? Breadcrumbs::render( $ctx->page_id, 'composer' === $ctx->source ? $ctx->template : '' ) : '';
$phone    = (string) Settings::get( 'phone' );

ob_start();
?>
<div class="xl-hero__content" data-xl-reveal>
	<?php if ( '' !== trim( $data['eyebrow'] ) && ! $in_h1 ) : ?>
		<p class="xl-eyebrow"><?php echo Html::text( $data['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php endif; ?>
	<<?php echo esc_html( $tag ); ?> class="xl-hero__title xl-title" id="<?php echo esc_attr( $title_id ); ?>">
		<?php if ( $in_h1 ) : ?>
			<span class="xl-eyebrow"><?php echo Html::text( $data['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php endif; ?>
		<?php echo Html::heading_text( $data['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</<?php echo esc_html( $tag ); ?>>
	<?php if ( '' !== trim( $data['text'] ) ) : ?>
		<p class="xl-hero__text"><?php echo Html::multiline( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
	<?php endif; ?>
	<?php
	$buttons  = Html::button( $data['primary_label'], $data['primary_link'], 'primary', array( 'icon' => 'arrow-right' ) );
	$buttons .= Html::button( $data['secondary_label'], $data['secondary_link'], 'dark' === $data['bg'] || 'media' === $layout ? 'ghost' : 'secondary' );
	if ( '' !== $buttons ) :
		?>
		<div class="xl-actions"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>
	<?php if ( 'yes' === $data['show_phone'] && '' !== trim( $phone ) ) : ?>
		<p class="xl-hero__call">
			<?php esc_html_e( 'Prefer to talk?', 'xlights-elementor-widgets' ); ?>
			<a href="<?php echo esc_url( Tokens::tel_href( $phone ) ); ?>"><?php echo Icons::svg( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $phone ); ?></span></a>
		</p>
	<?php endif; ?>
	<?php
	$highlights = Html::lines( $data['highlights'] );
	if ( $highlights ) :
		?>
		<ul class="xl-hero__highlights">
			<?php foreach ( $highlights as $line ) : ?>
				<li><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo Html::text( $line ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php
$content = ob_get_clean();

$string_line = 'yes' === $data['string_line'] ? '<div class="xl-string" aria-hidden="true"><span></span></div>' : '';

// Quick estimate card: a plain GET form that opens step 1 of the quote form prefilled.
$estimate = '';
if ( 'media' === $layout && isset( $data['estimate'] ) && 'yes' === $data['estimate'] ) {
	$services = \XLights\Sections\Components\Hero::estimate_services();
	$chosen   = isset( $data['estimate_service'], $services[ $data['estimate_service'] ] ) ? $data['estimate_service'] : '';
	$eid      = $ctx->id( 'estimate' );
	// A GET form replaces the action's query string, so carry it over as hidden
	// fields (plain permalinks: ?page_id=12#quote).
	$action = Tokens::url( '{quote_url}' );
	$keep   = array();
	$query  = (string) wp_parse_url( $action, PHP_URL_QUERY );
	if ( '' !== $query ) {
		parse_str( $query, $keep );
		$action = remove_query_arg( array_keys( $keep ), $action );
		unset( $keep['service'], $keep['zip'] );
	}
	ob_start();
	?>
	<form class="xl-estimate" action="<?php echo esc_url( $action ); ?>" method="get" aria-labelledby="<?php echo esc_attr( $eid . '-title' ); ?>" data-xl-reveal>
		<?php foreach ( $keep as $name => $value ) : ?>
			<?php if ( is_scalar( $value ) ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>">
			<?php endif; ?>
		<?php endforeach; ?>
		<h2 class="xl-estimate__title" id="<?php echo esc_attr( $eid . '-title' ); ?>"><?php echo Html::text( $data['estimate_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php if ( '' !== trim( (string) $data['estimate_text'] ) ) : ?>
			<p class="xl-estimate__text"><?php echo Html::text( $data['estimate_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<?php endif; ?>
		<ol class="xl-estimate__steps" aria-hidden="true">
			<li class="is-current"><span>1</span><?php esc_html_e( 'Project', 'xlights-elementor-widgets' ); ?></li>
			<li><span>2</span><?php esc_html_e( 'Property', 'xlights-elementor-widgets' ); ?></li>
			<li><span>3</span><?php esc_html_e( 'Details', 'xlights-elementor-widgets' ); ?></li>
		</ol>
		<div class="xl-estimate__field">
			<label for="<?php echo esc_attr( $eid . '-service' ); ?>"><?php esc_html_e( 'What would you like to light?', 'xlights-elementor-widgets' ); ?></label>
			<select class="xl-input" id="<?php echo esc_attr( $eid . '-service' ); ?>" name="service">
				<option value=""><?php esc_html_e( 'Choose a lighting type', 'xlights-elementor-widgets' ); ?></option>
				<?php foreach ( $services as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $chosen, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="xl-estimate__field">
			<label for="<?php echo esc_attr( $eid . '-zip' ); ?>"><?php esc_html_e( 'Property ZIP code', 'xlights-elementor-widgets' ); ?></label>
			<input class="xl-input" id="<?php echo esc_attr( $eid . '-zip' ); ?>" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" maxlength="5" pattern="\d{5}" placeholder="<?php esc_attr_e( 'e.g. 92672', 'xlights-elementor-widgets' ); ?>">
		</div>
		<button type="submit" class="xl-btn xl-btn--primary xl-btn--block"><span><?php echo Html::text( $data['estimate_button'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo Icons::svg( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
	</form>
	<?php
	$estimate = (string) ob_get_clean();
}

if ( 'media' === $layout ) :
	$overlay = isset( $data['overlay'] ) ? ( is_array( $data['overlay'] ) ? (float) $data['overlay']['size'] : (float) $data['overlay'] ) : 60;
	$focal   = in_array( $data['focal'], array( 'center', 'top', 'bottom', 'left', 'right' ), true ) ? $data['focal'] : 'center';
	$video   = is_array( $data['video_url'] ) ? ( isset( $data['video_url']['url'] ) ? $data['video_url']['url'] : '' ) : (string) $data['video_url'];
	$has_img = 'image' === $data['visual'] && Html::has_image( $data['image'] );
	?>
	<div class="xl-hero__media xl-focal--<?php echo esc_attr( $focal ); ?>" style="--xl-overlay:<?php echo esc_attr( max( 0, min( 95, $overlay ) ) / 100 ); ?>">
		<?php
		if ( $has_img ) {
			$hero_img = Html::image(
				$data['image'],
				array(
					'size'  => 'full',
					'sizes' => '100vw',
					'eager' => $eager,
					'class' => 'xl-hero__img',
					'alt'   => ( empty( $data['image']['id'] ) && isset( $data['image_alt'] ) ) ? $data['image_alt'] : null,
				)
			);
			echo $hero_img; // phpcs:ignore WordPress.Security.EscapeOutput -- Built with escaped attributes.
		} else {
			echo Section_Renderer::visual( array_merge( $data, array( 'visual' => 'illustration' ) ), $ctx ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( '' !== $video ) :
			?>
			<video class="xl-hero__video" muted loop playsinline preload="none" data-xl-video aria-hidden="true">
				<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
			</video>
		<?php endif; ?>
	</div>
	<div class="xl-container xl-hero__inner xl-align--<?php echo esc_attr( $align ); ?><?php echo '' !== $estimate ? ' xl-hero__inner--estimate' : ''; ?>">
		<?php echo $crumbs; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( '' !== $estimate ) : ?>
			<div class="xl-hero__row"><?php echo $content . $estimate; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php else : ?>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php endif; ?>
	</div>
	<?php
	echo $string_line; // phpcs:ignore WordPress.Security.EscapeOutput
elseif ( 'split' === $layout ) :
	?>
	<div class="xl-container">
		<?php echo $crumbs; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="xl-hero__split">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="<?php echo esc_attr( Section_Renderer::visual_class( $data ) ); ?> xl-hero__visual" data-xl-reveal>
				<?php echo Section_Renderer::visual( $data, $ctx, array( 'eager' => $eager, 'sizes' => '(max-width: 1024px) 100vw, 50vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</div>
	<?php
else :
	?>
	<div class="xl-container xl-container--narrow xl-hero__inner xl-align--center">
		<?php echo $crumbs . $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<?php
	echo $string_line; // phpcs:ignore WordPress.Security.EscapeOutput
endif;
