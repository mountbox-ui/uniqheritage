<header class="lqd-lp-header d-flex flex-column flex-md-row w-md-60 mb-4">

	<div class="lqd-lp-meta d-flex align-items-center p-0 w-md-30">
		<?php
			$time_string = '<time class="published updated lqd-lp-date" datetime="%1$s">%2$s</time>';
			printf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				get_the_date()
			);
		?>
	</div>

	<?php $this->entry_title( 'm-0 h5' ); ?>

</header>

<div class="lqd-lp-img mb-5 w-md-35 pos-md-abs pos-md-tr overflow-hidden">
	<?php $this->entry_thumbnail(); ?>
</div>

