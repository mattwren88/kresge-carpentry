<?php
/**
 * Title: Hero
 * Slug: kresge/hero
 * Categories: kresge
 * Description: Split hero with headline, call to action and project photo.
 *
 * @package kresge
 */

?>
<!-- wp:group {"tagName":"section","className":"kc-hero","align":"full","templateLock":"contentOnly","backgroundColor":"forest","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull kc-hero has-forest-background-color has-background">
	<!-- wp:columns {"className":"kc-hero-grid","style":{"spacing":{"blockGap":{"left":"0"}}}} -->
	<div class="wp-block-columns kc-hero-grid">
		<!-- wp:column {"className":"kc-hero-copy"} -->
		<div class="wp-block-column kc-hero-copy">
			<!-- wp:paragraph {"className":"kc-eyebrow"} -->
			<p class="kc-eyebrow"><?php esc_html_e( 'Local · licensed · insured', 'kresge' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":1} -->
			<h1 class="wp-block-heading"><?php echo wp_kses_post( __( 'Custom carpentry,<br>built to last a lifetime.', 'kresge' ) ); ?></h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"kc-lede"} -->
			<p class="kc-lede"><?php esc_html_e( 'Decks, kitchens, additions, and the small details that make a home feel finished — built by a hands-on owner who shows up when promised.', 'kresge' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"className":"kc-hero-actions"} -->
			<div class="wp-block-buttons kc-hero-actions">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php esc_html_e( 'Request a site visit', 'kresge' ); ?></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"kc-link-quiet"} -->
				<div class="wp-block-button kc-link-quiet"><a class="wp-block-button__link wp-element-button" href="#work"><?php esc_html_e( 'See recent work →', 'kresge' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"kc-hero-photo"} -->
		<div class="wp-block-column kc-hero-photo">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Finished covered deck with white railings and gray composite boards.', 'kresge' ); ?>"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
