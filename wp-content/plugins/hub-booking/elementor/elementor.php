<?php 

add_action( 'elementor/widgets/widgets_registered', function() {

    require __DIR__ . '/widgets/booking-form.php';

} );