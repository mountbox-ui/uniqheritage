<?php
/**
 * Default footer template
 *
 * @package Hub
 */

$footer = liquid_get_footer_layout();
$footer_id = $footer['id'];
$footer_link = isset( $footer['link'] ) ? $footer['link'] : false;
unset( $footer['id'], $footer['link'] );

if( !empty( $footer ) || !empty( $footer_link ) ) {
	echo '<style>';

		if( !empty( $footer ) ) {
			printf( '.main-footer {%s}', liquid_helper()->output_css( $footer ) );
		}

		if( !empty( $footer_link ) ) {
			$css = '';
			foreach( $footer_link as $k => $v ) {

				if( 'regular' === $k ) {
					printf( '.main-footer a:not(.btn) { color: %s }', $v  );
				}
				else {
					printf( '.main-footer a:not(.btn):%s { color: %s }', $k, $v  );
				}
			}
		}

	echo '</style>';
}
?>
<footer <?php liquid_helper()->attr( 'footer' ); ?>>

	<?php 

        if( function_exists( 'icl_object_id' ) ) {
            $footer_id = icl_object_id( $footer_id, 'page', false, ICL_LANGUAGE_CODE );
        }
        if ( function_exists( 'pll_get_post' ) ) {
            $footer_id = pll_get_post( $footer_id );
        }
                
		if ( defined( 'ELEMENTOR_VERSION' ) ) : 

			liquid_helper()->get_elementor_edit_cpt( $footer_id, 'Footer' );

			echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
		
		else: 
		
			$footer_content = get_post_field( 'post_content', $footer_id );
			echo do_shortcode( $footer_content );
			
		endif;

	?>
</footer>