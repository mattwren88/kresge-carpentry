<?php
/**
 * Title: Recent work
 * Slug: kresge/work
 * Categories: kresge
 * Description: Newest project featured, followed by the next three as cards. Filled from Projects.
 *
 * @package kresge
 */

?>
<!-- wp:group {"tagName":"section","anchor":"work","className":"kc-section","align":"full","templateLock":"contentOnly","backgroundColor":"mist","layout":{"type":"constrained","contentSize":"1200px"}} -->
<section class="wp-block-group alignfull kc-section has-mist-background-color has-background" id="work">
	<!-- wp:group {"className":"kc-section-header","layout":{"type":"constrained","contentSize":"900px"}} -->
	<div class="wp-block-group kc-section-header">
		<!-- wp:paragraph {"align":"center","className":"kc-eyebrow"} -->
		<p class="has-text-align-center kc-eyebrow">Recent work</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center"} -->
		<h2 class="wp-block-heading has-text-align-center">Built with care, start to finish</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:query {"queryId":10,"query":{"perPage":1,"pages":0,"offset":0,"postType":"project","order":"desc","orderBy":"date","inherit":false},"className":"kc-featured-query"} -->
	<div class="wp-block-query kc-featured-query">
		<!-- wp:post-template -->
			<!-- wp:columns {"verticalAlignment":"center","className":"kc-featured"} -->
			<div class="wp-block-columns are-vertically-aligned-center kc-featured">
				<!-- wp:column {"verticalAlignment":"center","width":"53%"} -->
				<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:53%">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","className":"kc-featured-photo"} /-->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"center","className":"kc-featured-body"} -->
				<div class="wp-block-column is-vertically-aligned-center kc-featured-body">
					<!-- wp:post-terms {"term":"project_type","prefix":"Featured · ","className":"kc-eyebrow"} /-->
					<!-- wp:post-title {"level":3,"isLink":true} /-->
					<!-- wp:post-excerpt {"excerptLength":60} /-->
					<!-- wp:read-more {"content":"View project →","className":"kc-more"} /-->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->

	<!-- wp:group {"className":"kc-subhead","layout":{"type":"flex","flexWrap":"nowrap"}} -->
	<div class="wp-block-group kc-subhead">
		<!-- wp:heading {"level":3} -->
		<h3 class="wp-block-heading">More recent builds</h3>
		<!-- /wp:heading -->
		<!-- wp:separator {"className":"kc-subhead-rule"} -->
		<hr class="wp-block-separator has-alpha-channel-opacity kc-subhead-rule"/>
		<!-- /wp:separator -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":1,"postType":"project","order":"desc","orderBy":"date","inherit":false},"className":"kc-projects-query"} -->
	<div class="wp-block-query kc-projects-query">
		<!-- wp:post-template {"className":"kc-projects-grid","layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"260px"}} -->
			<!-- wp:group {"className":"kc-project-card","layout":{"type":"default"}} -->
			<div class="wp-block-group kc-project-card">
				<!-- wp:post-featured-image {"isLink":true,"height":"200px","className":"kc-project-photo"} /-->
				<!-- wp:group {"className":"kc-project-body","layout":{"type":"default"}} -->
				<div class="wp-block-group kc-project-body">
					<!-- wp:post-terms {"term":"project_type","className":"kc-tag"} /-->
					<!-- wp:post-title {"level":3,"isLink":true} /-->
					<!-- wp:post-excerpt {"excerptLength":30} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
