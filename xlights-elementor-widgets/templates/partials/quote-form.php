<?php
/**
 * Quote request form markup. Works without JavaScript (posts to admin-post.php
 * and redirects back); JavaScript adds inline validation and AJAX submission.
 *
 * @var array $form
 * @package XLights
 */

use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use XLights\Helpers\Tokens;
use XLights\Settings\Settings;

defined( 'ABSPATH' ) || exit;

$args       = $form['args'];
$uid        = sanitize_html_class( $args['uid'] );
$variant    = $args['variant'];
$commercial = 'commercial' === $variant;
$preselect  = in_array( $variant, array( 'soffit', 'bistro', 'deck', 'landscape' ), true ) ? $variant : '';
$id         = static function ( $name ) use ( $uid ) {
	return $uid . '-' . $name;
};
$req        = '<span class="xl-req" aria-hidden="true">*</span>';

/**
 * Text-like input field.
 */
$field = static function ( $name, $label, $type = 'text', array $attrs = array(), $required = true, $class = '' ) use ( $id, $req ) {
	$input = array_merge(
		array(
			'type'             => $type,
			'id'               => $id( $name ),
			'name'             => $name,
			'class'            => 'xl-input',
			'required'         => $required ? true : null,
			'aria-describedby' => $id( $name . '-err' ),
		),
		$attrs
	);
	echo '<div class="xl-field ' . esc_attr( $class ) . '" data-xl-field="' . esc_attr( $name ) . '">';
	echo '<label class="xl-label" for="' . esc_attr( $id( $name ) ) . '">' . esc_html( $label ) . ( $required ? $req : '' ) . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<input' . Html::attrs( $input ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<p class="xl-field__error" id="' . esc_attr( $id( $name . '-err' ) ) . '" hidden></p>';
	echo '</div>';
};
?>
<form class="xl-form" id="<?php echo esc_attr( $id( 'form' ) ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate
	data-xl-form
	data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	data-max-files="<?php echo (int) $form['max_files']; ?>"
	data-max-mb="<?php echo (int) $form['max_mb']; ?>"
	data-preselect="<?php echo esc_attr( $preselect ); ?>">

	<div class="xl-form__status<?php echo $form['notice'] ? ' is-' . esc_attr( $form['notice']['type'] ) : ''; ?>" role="status" aria-live="polite" tabindex="-1"<?php echo $form['notice'] ? '' : ' hidden'; ?>>
		<?php
		if ( $form['notice'] ) {
			echo Icons::svg( 'success' === $form['notice']['type'] ? 'check-circle' : 'alert' ) . '<p>' . esc_html( $form['notice']['message'] ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
	</div>

	<?php
	$steps = array(
		1 => array( __( 'Project', 'xlights-elementor-widgets' ), __( 'Your project', 'xlights-elementor-widgets' ) ),
		2 => array( __( 'Property', 'xlights-elementor-widgets' ), __( 'Your property', 'xlights-elementor-widgets' ) ),
		3 => array( __( 'Details', 'xlights-elementor-widgets' ), __( 'Your details', 'xlights-elementor-widgets' ) ),
	);
	$step_nav = static function ( $n ) {
		echo '<div class="xl-step__nav" data-xl-step-nav hidden>';
		if ( $n > 1 ) {
			echo '<button type="button" class="xl-btn xl-btn--back" data-xl-prev>' . Icons::svg( 'arrow-left' ) . '<span>' . esc_html__( 'Back', 'xlights-elementor-widgets' ) . '</span></button>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo '<button type="button" class="xl-btn xl-btn--primary" data-xl-next><span>' . esc_html__( 'Continue', 'xlights-elementor-widgets' ) . '</span>' . Icons::svg( 'arrow-right' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div>';
	};
	$step_open = static function ( $n ) use ( $steps, $id ) {
		echo '<div class="xl-step" data-xl-step="' . (int) $n . '" role="group" aria-labelledby="' . esc_attr( $id( 'step' . $n ) ) . '">';
		echo '<h3 class="xl-step__title" id="' . esc_attr( $id( 'step' . $n ) ) . '" tabindex="-1"><span class="xl-step__count">' . esc_html( sprintf( /* translators: 1: step number, 2: total steps */ __( 'Step %1$d of %2$d', 'xlights-elementor-widgets' ), $n, count( $steps ) ) ) . '</span>' . esc_html( $steps[ $n ][1] ) . '</h3>';
	};
	?>
	<ol class="xl-stepper" data-xl-stepper hidden>
		<?php foreach ( $steps as $n => $step ) : ?>
			<li class="xl-stepper__item" data-xl-stepper-item="<?php echo (int) $n; ?>">
				<button type="button" class="xl-stepper__btn" data-xl-goto="<?php echo (int) $n; ?>" disabled>
					<span class="xl-stepper__dot" aria-hidden="true"><span class="xl-stepper__num"><?php echo (int) $n; ?></span><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="xl-stepper__label"><?php echo esc_html( $step[0] ); ?></span>
				</button>
			</li>
		<?php endforeach; ?>
	</ol>
	<p class="xl-sr" data-xl-step-live aria-live="polite"></p>

	<div class="xl-form__fields" data-xl-form-fields>
		<input type="hidden" name="action" value="xlights_quote">
		<input type="hidden" name="_xl_nonce" value="<?php echo esc_attr( $form['nonce'] ); ?>">
		<input type="hidden" name="_xl_t" value="<?php echo esc_attr( $form['token'] ); ?>">
		<input type="hidden" name="variant" value="<?php echo esc_attr( $variant ); ?>">
		<input type="hidden" name="source_url" value="">

		<div class="xl-form__hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $id( 'website' ) ); ?>"><?php esc_html_e( 'Leave this field empty', 'xlights-elementor-widgets' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $id( 'website' ) ); ?>" name="xl_website" tabindex="-1" autocomplete="off" value="">
		</div>

		<p class="xl-form__required"><?php echo wp_kses( sprintf( /* translators: %s: asterisk */ __( 'Fields marked %s are required.', 'xlights-elementor-widgets' ), '<span class="xl-req">*</span>' ), array( 'span' => array( 'class' => true ) ) ); ?></p>

		<?php $step_open( 1 ); ?>
		<fieldset class="xl-field xl-field--full xl-choice-group" data-xl-field="property_type" aria-describedby="<?php echo esc_attr( $id( 'property_type-err' ) ); ?>">
			<legend class="xl-label"><?php esc_html_e( 'Type of property', 'xlights-elementor-widgets' ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></legend>
			<div class="xl-segmented">
				<label class="xl-segment">
					<input type="radio" name="property_type" value="residential" required <?php checked( ! $commercial ); ?>>
					<span><?php echo Icons::svg( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Residential', 'xlights-elementor-widgets' ); ?></span>
				</label>
				<label class="xl-segment">
					<input type="radio" name="property_type" value="commercial" <?php checked( $commercial ); ?>>
					<span><?php echo Icons::svg( 'building' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Commercial', 'xlights-elementor-widgets' ); ?></span>
				</label>
			</div>
			<p class="xl-field__error" id="<?php echo esc_attr( $id( 'property_type-err' ) ); ?>" hidden></p>
		</fieldset>

		<div class="xl-form__commercial" data-xl-commercial<?php echo $commercial ? '' : ' hidden'; ?>>
			<?php $field( 'company', __( 'Company name', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'organization', 'maxlength' => 120, 'data-xl-commercial-required' => '1', 'required' => $commercial ? true : null ), $commercial ); ?>
			<div class="xl-field" data-xl-field="business_type">
				<label class="xl-label" for="<?php echo esc_attr( $id( 'business_type' ) ); ?>"><?php esc_html_e( 'Type of business', 'xlights-elementor-widgets' ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
				<select class="xl-input" id="<?php echo esc_attr( $id( 'business_type' ) ); ?>" name="business_type" data-xl-commercial-required="1" aria-describedby="<?php echo esc_attr( $id( 'business_type-err' ) ); ?>"<?php echo $commercial ? ' required' : ''; ?>>
					<option value=""><?php esc_html_e( 'Choose one', 'xlights-elementor-widgets' ); ?></option>
					<?php foreach ( $form['business'] as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="xl-field__error" id="<?php echo esc_attr( $id( 'business_type-err' ) ); ?>" hidden></p>
			</div>
		</div>

		<fieldset class="xl-field xl-field--full xl-choice-group" data-xl-field="interests" aria-describedby="<?php echo esc_attr( $id( 'interests-err' ) ); ?>">
			<legend class="xl-label"><?php esc_html_e( 'Lighting interest (choose all that apply)', 'xlights-elementor-widgets' ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></legend>
			<div class="xl-chips">
				<?php foreach ( $form['interests'] as $key => $label ) : ?>
					<label class="xl-chip">
						<input type="checkbox" name="interests[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $preselect, $key ); ?>>
						<span><?php echo Icons::svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
			<p class="xl-field__error" id="<?php echo esc_attr( $id( 'interests-err' ) ); ?>" hidden></p>
		</fieldset>

		<div class="xl-field xl-field--full" data-xl-field="message">
			<label class="xl-label" for="<?php echo esc_attr( $id( 'message' ) ); ?>"><?php esc_html_e( 'Tell us about your project', 'xlights-elementor-widgets' ); ?></label>
			<textarea class="xl-input" id="<?php echo esc_attr( $id( 'message' ) ); ?>" name="message" rows="5" maxlength="5000" placeholder="<?php esc_attr_e( 'Rooflines, patio size, areas you would like to light, timing, questions…', 'xlights-elementor-widgets' ); ?>" aria-describedby="<?php echo esc_attr( $id( 'message-err' ) ); ?>"></textarea>
			<p class="xl-field__error" id="<?php echo esc_attr( $id( 'message-err' ) ); ?>" hidden></p>
		</div>
		<?php $step_nav( 1 ); ?>
		</div>

		<?php $step_open( 2 ); ?>
		<div class="xl-form__grid">
			<?php
			$field( 'address', __( 'Property address', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'address-line1', 'maxlength' => 200 ), true, 'xl-field--full' );
			$field( 'city', __( 'City', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'address-level2', 'maxlength' => 100 ) );
			?>
			<div class="xl-field xl-field--split">
				<div class="xl-field" data-xl-field="state">
					<label class="xl-label" for="<?php echo esc_attr( $id( 'state' ) ); ?>"><?php esc_html_e( 'State', 'xlights-elementor-widgets' ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?></label>
					<select class="xl-input" id="<?php echo esc_attr( $id( 'state' ) ); ?>" name="state" autocomplete="address-level1" required aria-describedby="<?php echo esc_attr( $id( 'state-err' ) ); ?>">
						<?php foreach ( $form['states'] as $code => $name ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $form['state'], $code ); ?>><?php echo esc_html( $code ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="xl-field__error" id="<?php echo esc_attr( $id( 'state-err' ) ); ?>" hidden></p>
				</div>
				<?php $field( 'zip', __( 'ZIP code', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'postal-code', 'inputmode' => 'numeric', 'maxlength' => 10, 'pattern' => '\d{5}(-\d{4})?' ) ); ?>
			</div>
		</div>

		<?php if ( $form['max_files'] > 0 ) : ?>
			<div class="xl-field xl-field--full" data-xl-field="photos">
				<span class="xl-label" id="<?php echo esc_attr( $id( 'photos-label' ) ); ?>"><?php esc_html_e( 'Photos of your home, patio or property (optional)', 'xlights-elementor-widgets' ); ?></span>
				<label class="xl-drop" for="<?php echo esc_attr( $id( 'photos' ) ); ?>" data-xl-drop>
					<?php echo Icons::svg( 'camera' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="xl-drop__title"><?php esc_html_e( 'Add photos', 'xlights-elementor-widgets' ); ?></span>
					<span class="xl-drop__hint">
						<?php
						/* translators: 1: max files, 2: max MB */
						echo esc_html( sprintf( __( 'Up to %1$d photos · JPG, PNG or WEBP · %2$d MB each', 'xlights-elementor-widgets' ), $form['max_files'], $form['max_mb'] ) );
						?>
					</span>
					<input class="xl-drop__input" type="file" id="<?php echo esc_attr( $id( 'photos' ) ); ?>" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple aria-labelledby="<?php echo esc_attr( $id( 'photos-label' ) ); ?>" aria-describedby="<?php echo esc_attr( $id( 'photos-err' ) ); ?>">
				</label>
				<ul class="xl-drop__list" data-xl-file-list aria-live="polite"></ul>
				<p class="xl-field__error" id="<?php echo esc_attr( $id( 'photos-err' ) ); ?>" hidden></p>
			</div>
		<?php endif; ?>

		<?php $step_nav( 2 ); ?>
		</div>

		<?php $step_open( 3 ); ?>
		<div class="xl-form__grid">
			<?php
			$field( 'first_name', __( 'First name', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'given-name', 'maxlength' => 80 ) );
			$field( 'last_name', __( 'Last name', 'xlights-elementor-widgets' ), 'text', array( 'autocomplete' => 'family-name', 'maxlength' => 80 ) );
			$field( 'phone', __( 'Phone', 'xlights-elementor-widgets' ), 'tel', array( 'autocomplete' => 'tel', 'inputmode' => 'tel', 'maxlength' => 20, 'placeholder' => '(555) 555-5555' ) );
			$field( 'email', __( 'Email', 'xlights-elementor-widgets' ), 'email', array( 'autocomplete' => 'email', 'maxlength' => 200, 'spellcheck' => 'false' ) );
			?>
		</div>

		<?php if ( '' !== $form['turnstile'] ) : ?>
			<div class="xl-field xl-field--full">
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $form['turnstile'] ); ?>"></div>
			</div>
		<?php endif; ?>

		<div class="xl-field xl-field--full" data-xl-field="privacy">
			<label class="xl-check">
				<input type="checkbox" name="privacy" value="1" required aria-describedby="<?php echo esc_attr( $id( 'privacy-err' ) ); ?>">
				<span>
					<?php echo esc_html( Tokens::replace( (string) Settings::get( 'privacy_ack_text' ) ) ); ?>
					<?php if ( '' !== $form['privacy'] ) : ?>
						<a href="<?php echo esc_url( $form['privacy'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Privacy Policy', 'xlights-elementor-widgets' ); ?><span class="xl-sr"> <?php esc_html_e( '(opens in a new tab)', 'xlights-elementor-widgets' ); ?></span></a>
					<?php endif; ?>
					<?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</span>
			</label>
			<p class="xl-field__error" id="<?php echo esc_attr( $id( 'privacy-err' ) ); ?>" hidden></p>
		</div>

		<div class="xl-form__submit">
			<button type="button" class="xl-btn xl-btn--back" data-xl-prev hidden><?php echo Icons::svg( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Back', 'xlights-elementor-widgets' ); ?></span></button>
			<button type="submit" class="xl-btn xl-btn--primary xl-btn--lg" data-xl-submit>
				<span class="xl-btn__label"><?php echo esc_html( Tokens::replace( $args['submit_label'] ) ); ?></span>
				<span class="xl-spinner" aria-hidden="true"></span>
				<?php echo Icons::svg( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</button>
		</div>
		</div>
	</div>
</form>
