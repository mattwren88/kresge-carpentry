<?php
/**
 * Title: Contact
 * Slug: kresge/contact
 * Categories: kresge
 * Description: Contact details from Settings → Business details, plus the Fluent Forms form.
 *
 * @package kresge
 */

$kresge_details = array(
	'phone'        => array( __( 'Call', 'kresge' ), 'phone_href' ),
	'email'        => array( __( 'Email', 'kresge' ), 'email_href' ),
	'service_area' => array( __( 'Service area', 'kresge' ), null ),
);
?>
<!-- wp:group {"tagName":"section","anchor":"contact","className":"kc-section kc-contact","align":"full","templateLock":"contentOnly","layout":{"type":"constrained","contentSize":"1100px"}} -->
<section class="wp-block-group alignfull kc-section kc-contact" id="contact">
	<!-- wp:columns {"className":"kc-contact-card"} -->
	<div class="wp-block-columns kc-contact-card">
		<!-- wp:column {"className":"kc-contact-intro"} -->
		<div class="wp-block-column kc-contact-intro">
			<!-- wp:paragraph {"className":"kc-eyebrow"} -->
			<p class="kc-eyebrow"><?php esc_html_e( 'Ready to talk', 'kresge' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php esc_html_e( 'Tell us about your project', 'kresge' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'Share ideas, rough timing, or a few photos. We reply within one business day.', 'kresge' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"kc-contact-details","layout":{"type":"default"}} -->
			<div class="wp-block-group kc-contact-details">
			<?php foreach ( $kresge_details as $kresge_key => list( $kresge_label, $kresge_href ) ) : ?>
				<!-- wp:paragraph {"className":"kc-contact-label"} -->
				<p class="kc-contact-label"><?php echo esc_html( $kresge_label ); ?></p>
				<!-- /wp:paragraph -->
				<?php if ( $kresge_href ) : ?>
				<!-- wp:buttons {"className":"kc-contact-value"} -->
				<div class="wp-block-buttons kc-contact-value">
					<!-- wp:button {"className":"kc-text-link","metadata":{"bindings":{"text":{"source":"kresge/business","args":{"key":"<?php echo esc_attr( $kresge_key ); ?>"}},"url":{"source":"kresge/business","args":{"key":"<?php echo esc_attr( $kresge_href ); ?>"}}}}} -->
					<div class="wp-block-button kc-text-link"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( $kresge_label ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<?php else : ?>
				<!-- wp:paragraph {"className":"kc-contact-value","metadata":{"bindings":{"content":{"source":"kresge/business","args":{"key":"<?php echo esc_attr( $kresge_key ); ?>"}}}}} -->
				<p class="kc-contact-value"><?php echo esc_html( $kresge_label ); ?></p>
				<!-- /wp:paragraph -->
				<?php endif; ?>
			<?php endforeach; ?>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"kc-contact-form"} -->
		<div class="wp-block-column kc-contact-form">
			<!-- wp:shortcode -->
			[fluentform id="1"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
