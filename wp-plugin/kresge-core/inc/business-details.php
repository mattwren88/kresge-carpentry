<?php
/**
 * Business details: one settings page feeding the header, contact section,
 * footer (via a Block Bindings source) and LocalBusiness structured data.
 *
 * @package kresge-core
 */

const KRESGE_BUSINESS_FIELDS = array(
	'phone'        => 'Phone',
	'email'        => 'Email',
	'service_area' => 'Service area',
);

/**
 * Returns one saved business detail, or '' when unset.
 *
 * @param string $key One of KRESGE_BUSINESS_FIELDS' keys.
 */
function kresge_business( $key ) {
	$details = get_option( 'kresge_business', array() );
	$value   = is_array( $details ) ? ( $details[ $key ] ?? '' ) : '';
	return is_string( $value ) ? $value : '';
}

/**
 * Turns a display phone number like "(570) 555-0100" into "tel:5705550100".
 *
 * @param string $phone Phone number as the owner typed it.
 */
function kresge_tel_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', $phone );
	return $digits ? 'tel:' . $digits : '';
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'kresge_business',
			'kresge_business',
			array(
				'type'              => 'array',
				'sanitize_callback' => function ( $input ) {
					$input = is_array( $input ) ? $input : array();
					return array(
						'phone'        => sanitize_text_field( $input['phone'] ?? '' ),
						'email'        => sanitize_email( $input['email'] ?? '' ),
						'service_area' => sanitize_text_field( $input['service_area'] ?? '' ),
					);
				},
			)
		);
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Business details', 'kresge-core' ),
			__( 'Business details', 'kresge-core' ),
			'manage_options',
			'kresge-business',
			'kresge_render_business_page'
		);
	}
);

/**
 * Renders Settings → Business details.
 */
function kresge_render_business_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Business details', 'kresge-core' ); ?></h1>
		<p><?php esc_html_e( 'Shown in the header, contact section, footer and search results.', 'kresge-core' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'kresge_business' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( KRESGE_BUSINESS_FIELDS as $key => $label ) : ?>
					<tr>
						<th scope="row"><label for="kresge-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td><input class="regular-text" type="<?php echo 'email' === $key ? 'email' : 'text'; ?>" id="kresge-<?php echo esc_attr( $key ); ?>" name="kresge_business[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( kresge_business( $key ) ); ?>"></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

add_action(
	'init',
	function () {
		register_block_bindings_source(
			'kresge/business',
			array(
				'label'              => __( 'Business details', 'kresge-core' ),
				'get_value_callback' => function ( array $source_args ) {
					$key = $source_args['key'] ?? '';
					switch ( $key ) {
						case 'phone_href':
							return kresge_tel_href( kresge_business( 'phone' ) );
						case 'email_href':
							$email = kresge_business( 'email' );
							return $email ? 'mailto:' . $email : '';
						default:
							$value = kresge_business( $key );
							return '' !== $value ? esc_html( $value ) : esc_html__( 'Add in Settings → Business details', 'kresge-core' );
					}
				},
			)
		);
	}
);

add_action(
	'wp_head',
	function () {
		$data = array_filter(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'LocalBusiness',
				'name'       => get_bloginfo( 'name' ),
				'url'        => home_url( '/' ),
				'image'      => wp_get_attachment_image_url( (int) get_option( 'site_logo' ), 'full' ),
				'telephone'  => kresge_business( 'phone' ),
				'email'      => kresge_business( 'email' ),
				'areaServed' => kresge_business( 'service_area' ),
			)
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
);
