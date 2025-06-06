<div class="ld-pf-single ld-pf-single-2">

	<div class="container ld-container">
		<div class="row">
			<div class="col-md-12">

				<div class="pf-single-contents clearfix">

					<div class="lqd-pf-single-gallery pos-rel mb-6">
						<?php liquid_portfolio_media() ?>
					</div>

					<div class="row d-md-flex align-items-center">

						<div class="col-md-6">

							<div class="pf-single-header pull-up bg-solid mb-5">
								<h2 class="pf-single-title mt-0 mb-4 font-weight-bold">
									<?php the_title() ?>
								</h2>

								<?php liquid_portfolio_the_content() ?>

								<div class="clearfix mb-3"></div>

								<div class="pf-info d-lg-flex justify-content-between">
									<?php liquid_portfolio_date() ?>
									<?php liquid_portfolio_atts() ?>
								</div>

							</div>

							<div class="d-md-flex align-items-center justify-content-end mb-4">
								<?php if (function_exists('liquid_portfolio_share')) : ?>
									<small class="text-uppercase ltr-sp-1 mr-3"><?php esc_html_e('Share on', 'hub'); ?></small>
									<?php liquid_portfolio_share(get_post_type()); ?>
								<?php endif; ?>
							</div>

							<?php liquid_render_post_nav(get_post_type()) ?>

						</div>

					</div>

				</div>

			</div>
		</div>
	</div>

</div>