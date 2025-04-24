<?php


defined( 'ABSPATH' ) || exit;

class Hub_Booking_Actions {

    protected static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }

    public function __construct() {

        $this->hooks();

    }

    public function hooks() {

        add_action( 'wp_ajax_booking_get_data', [ $this, 'hub_booking_get_data' ] );
        add_action( 'wp_ajax_booking_set_data', [ $this, 'hub_booking_set_data' ] );
        add_action( 'wp_ajax_booking_set_status', [ $this, 'hub_booking_set_status' ] );
        add_action( 'wp_ajax_booking_get_calendar_event', [ $this, 'hub_booking_get_calendar_event' ] );
        add_action( 'wp_ajax_get_available_slots', [ $this, 'get_available_slots' ] );
    
        add_action( 'wp_ajax_nopriv_booking_get_data', [ $this, 'hub_booking_get_data' ] );
        add_action( 'wp_ajax_nopriv_booking_set_data', [ $this, 'hub_booking_set_data' ] );
        add_action( 'wp_ajax_nopriv_booking_set_status', [ $this, 'hub_booking_set_status' ] );
        add_action( 'wp_ajax_nopriv_booking_get_calendar_event', [ $this, 'hub_booking_get_calendar_event' ] );
        add_action( 'wp_ajax_nopriv_get_available_slots', [ $this, 'get_available_slots' ] );
        
    }

    function hub_booking_get_data() {

        check_ajax_referer( 'hub_booking_form_nonce', 'nonce' );

        $options = get_option( 'hub_booking_settings' );
        $currency = $options['currency'] ?? 'USD';

        $booking_date = sanitize_text_field( $_POST['booking_date'] );
        $adult = intval( sanitize_text_field( $_POST['adult'] ) );
        $child = intval( sanitize_text_field( $_POST['child'] ) ) ?? 1;

        if ( 
            empty( $adult ) ||
            empty( $booking_date )
        ) {
            wp_send_json( [
                'error' => true,
                'message' => __( 'Missing Fileds! Check your form values, please.', 'hub-booking' ),
            ] );
        }

        wp_send_json( [
            'error' => false,
            'out' => sprintf( '(%s: %s %s)', __('Total','hub-booking'), $this->calculate_total_price( $booking_date, $adult, $child ), $currency ),
        ] );
    
        wp_send_json( [
            'error' => true,
            'message' => __( 'Sorry, no appointment matched your criteria.', 'hub-booking' ),
        ] );
    
    }
    
    function hub_booking_set_data() {

        check_ajax_referer( 'hub_booking_form_nonce', 'nonce' );

        $booking_type = sanitize_text_field( $_POST['booking_type'] );
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name = sanitize_text_field( $_POST['last_name'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $email = sanitize_email( $_POST['email'] );
        $adult = intval( sanitize_text_field( $_POST['adult'] ) );
        $child = intval( sanitize_text_field( $_POST['child'] ) );
        $message = sanitize_textarea_field( $_POST['message'] );
        $booking_date = sanitize_text_field( $_POST['booking_date'] );
        $time = sanitize_text_field( $_POST['time'] );
        $disabled_inputs = sanitize_text_field( $_POST['disabled_inputs'] );
        $disabled_inputs = !empty( $disabled_inputs ) ? explode( ',', $disabled_inputs ) : array();

        // Disabled checker
        $disabled_message = $disabled_last_name = $disabled_child = $disabled_email = $disabled_phone = false;

		if ( in_array('message', $disabled_inputs) ) { $disable_message = true; }
		if ( in_array('last_name', $disabled_inputs) ) { $disabled_last_name = true; }
		if ( in_array('child', $disabled_inputs) ) { $disabled_child = true; }
		if ( in_array('email', $disabled_inputs) ) { $disabled_email = true; }
		if ( in_array('phone', $disabled_inputs) ) { $disabled_phone = true; }

        if ( $booking_type === 'slot' ) {
            $adult = 1;
            if ( empty( $time ) ) {
                wp_send_json( [
                    'error' => true,
                    'alert' => __( 'Time is missing. Select the available time, please!' ),
                ] );
            }
        }

        // Check email validation
        if ( empty( $email ) && !$disabled_email ) {
            wp_send_json( [
                'error' => true,
                'alert' => __('Email is not valid! Use a reputable email service!', 'hub-booking'),
            ] );
        }

        // Check data validation
        if ( 
            empty( $first_name ) || 
            ( empty( $last_name ) && !$disabled_last_name ) || 
            ( empty( $phone ) && !$disabled_phone ) ||
            empty( $adult ) ||
            empty( $booking_date )
        ) {
            wp_send_json( [
                'error' => true,
                'alert' => __( 'Your personal data is empty! Please enter all fields before booking process.' ),
            ] );
        }

        if ( $booking_type === 'day' ) {
            $booking_date = str_replace( ' ', '', $booking_date );
            $booking_date = explode( ',' , $booking_date );
            // Check date validation
            if( count( $booking_date ) < 2 ){
                wp_send_json( [
                    'error' => true,
                    'alert' => __( 'Date is missing. Check in or out date, please!' ),
                ] );
            }
        }

        // insert new booking
        $booked_id = wp_insert_post( [
            'post_status'  => 'publish',
            'post_type'    => 'liquid-booking',
            'post_author'  => get_post_field( 'post_author', $post_id ),
        ] );
    
        wp_update_post( array( 'ID' => $booked_id, 'post_title' => '#' . $booked_id ) );

        foreach ( array('first_name', 'last_name', 'phone', 'email', 'adult', 'child', 'booking_date', 'message') as $meta ){
            update_post_meta( $booked_id, $meta, ${$meta} );
        }

        if ( $booking_type === 'slot' ) {
            update_post_meta( $booked_id, 'hours', $time );
        }

        update_post_meta( $booked_id, 'status', 'pending' ); // pending, approved, unapproved, completed, cancelled

        $child = $child ?? 1;
        $booking_date = sanitize_text_field( $_POST['booking_date'] );
        if ( $booking_type === 'slot' ) {
            update_post_meta( $booked_id, 'price', get_option( 'hub_booking_settings' )['slot_price'] ?? '' );
        } else {
            update_post_meta( $booked_id, 'price', $this->calculate_total_price( $booking_date, $adult, $child ) );
        }

        $options = get_option( 'hub_booking_settings' );
        $currency = $options['currency'] ?? 'USD';
        update_post_meta( $booked_id, 'currency', $currency );

        $admin_email_title = ! empty( $options['admin_email_title'] ) ? str_replace( '[booking_id]', $booked_id, $options['admin_email_title'] ) : sprintf( 'New Booking! (#%s)', $booked_id );
        $user_email_title = ! empty( $options['user_email_title'] ) ? str_replace( '[booking_id]', $booked_id, $options['user_email_title'] ) : 'Booking Created!';

        wp_mail(
            $to = get_option( 'admin_email' ),
            $subject = $admin_email_title,
            $body = $this->email_template( $booked_id, true ),
            $headers = array('Content-Type: text/html; charset=UTF-8')
        );
        
        if ( !$disabled_email ) {
            wp_mail(
                $to = $email,
                $subject = $user_email_title,
                $body = $this->email_template( $booked_id, false ),
                $headers = array('Content-Type: text/html; charset=UTF-8')
            );
        }

        wp_send_json( [
            'error' => false,
            'out' => $this->print_message( 
                'yes-alt',
                __( 'Booking Successful!', 'hub-booking' ),
                __( 'Your booking has been successfully created. You can follow status on your email. Your booking id:', 'hub-booking' ) . ' #' . $booked_id
            )
        ] );

    }

    function hub_booking_set_status() {

        $post_id = intval( $_POST['post_id'] );
        $status = sanitize_text_field( $_POST['status'] );
        $options = get_option( 'hub_booking_settings' );

        if ( empty( $post_id ) || empty( $status ) ) {
            wp_send_json( [
                'error' => true,
                'alert' => __('Someting went wrong!', 'hub-booking'),
            ] );
        }

        update_post_meta( $post_id, 'status', $status );

        $user_email_status_title = !empty( $options['user_email_status_title'] ) ? str_replace( ['[booking_id]','[booking_status]'], [$post_id,$status], $options['user_email_status_title'] ) : sprintf( 'Your Booking (#%s) is %s!', $post_id, $status );

        wp_mail(
            $to = get_post_meta( $post_id, 'email', true ),
            $subject = $user_email_status_title,
            $body = $this->email_template( $post_id, false ),
            $headers = array('Content-Type: text/html; charset=UTF-8')
        );

        wp_send_json( [
            'out' => "<span class='hub-booking-status $status'>$status</span>",
        ] );

    }

    function print_message( $icon, $title, $message ) {
        $out  = sprintf( '<div class="hub-booking-alert"><span class="dashicons %s"></span>', esc_attr( 'dashicons-' . $icon ) );
        $out .= sprintf( '<h3>%s</h3>', $title );
        $out .= sprintf( '<p>%s</p>', $message );
        $out .= '</div>';

        return $out;
    }

    function get_booking_for_calendar() {

        $posts = get_posts( array(
            'post_type' => 'liquid-booking',
            'posts_per_page' => -1,
        ) );

        $json = [];

        foreach( $posts as $post ) {

            $booking_id = $post->ID;
            $title = sprintf( '%s %s', get_post_meta( $booking_id, 'first_name', true ), get_post_meta( $booking_id, 'last_name', true ) );
            $colors = $this->sanitize_colors( get_post_meta( $booking_id, 'status', true ) );
            $hours = get_post_meta( $booking_id, 'hours', true );

            if ( $hours ) {
                $date = $this->sanitize_date( [get_post_meta( $booking_id, 'booking_date', true ), get_post_meta( $booking_id, 'booking_date', true )] );
                $hours = explode( '-', $hours );
                $date['start'].= 'T' . $hours[0] . '+00:00';
                $date['end'].= 'T' . $hours[1] . '+00:00';
            } else {
                $date = $this->sanitize_date( get_post_meta( $booking_id, 'booking_date', true ) );
            }

            $json[] = [
                'id' => $booking_id,
                'title' => $title,
                'start' => $date['start'],
                'end' => $date['end'],
                'backgroundColor' => $colors['bg'],
                'borderColor' => $colors['bg'],
            ];


        }

        return json_encode( $json );

    }

    function hub_booking_get_calendar_event() {

        $post_id = intval( $_POST['booking_id'] );

        if ( empty( $post_id ) ) return;

        $html = '';

        $booking_date = get_post_meta( $post_id, 'booking_date', true );
        $adult = get_post_meta( $post_id, 'adult', true );
        $child = get_post_meta( $post_id, 'child', true );
        $message = get_post_meta( $post_id, 'message', true );
        $phone = get_post_meta( $post_id, 'phone', true );
        $email = get_post_meta( $post_id, 'email', true );
        $first_name = get_post_meta( $post_id, 'first_name', true );
        $last_name = get_post_meta( $post_id, 'last_name', true );
        $status = get_post_meta( $post_id, 'status', true ); // pending, approved, unapproved, completed, cancelled
        $price = get_post_meta( $post_id, 'price', true ); 
        $currency = get_post_meta( $post_id, 'currency', true );
        $hours = get_post_meta( $post_id, 'hours', true );
        
        $html .= sprintf( '<strong>%s %s</strong><br><br>', ucfirst($first_name), ucfirst($last_name ));
        if ( $hours ) {
            $html .= sprintf( '<span class="dashicons dashicons-calendar"></span> <strong>%s</strong> %s (%s)<br>', __('Booking Date:', 'hub-booking'), $booking_date, $hours );
        } else {
            $html .= sprintf( '<span class="dashicons dashicons-calendar"></span> <strong>%s</strong> %s - %s<br>', __('Booking Date:', 'hub-booking'), $booking_date[0], $booking_date[1] );
        }
        $html .= sprintf( '<span class="dashicons dashicons-admin-users"></span> <strong>%s</strong> %s (Adult: %s, Child: %s)<br>', __('Person:', 'hub-booking'), (intval( $adult ) + intval( $child )), $adult, $child );
        
        $html .= sprintf( '<span class="dashicons dashicons-phone"></span> <strong>%s</strong> %s<br><span class="dashicons dashicons-email"></span> <strong>%s</strong> %s<br>', __('Phone:', 'hub-booking'), $phone, __('Email:', 'hub-booking'), $email );
        $html .= sprintf( '<span class="dashicons dashicons-money-alt"></span> <strong>%s</strong> %s %s<br>', __('Total:', 'hub-booking'), $price, $currency );
        if ( !empty( $message ) ) {
            $html .= sprintf( '<span class="dashicons dashicons-admin-comments"></span> <strong>%s</strong> %s', __('Message:', 'hub-booking'), $message );
        }

        wp_send_json( [
            'error' => false,
            'title' => __('Booking', 'hub-booking') . ' #' . $post_id . sprintf( '<span class="hub-booking-status %1$s">%1$s</span>', $status ),
            'content' => $html
        ] );

    }

    function email_template( $post_id, $is_admin = false ) {

        if ( empty( $post_id ) ) return;

        $html = '<style>body{font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;}</style>';

        $booking_date = get_post_meta( $post_id, 'booking_date', true );
        $adult = get_post_meta( $post_id, 'adult', true );
        $child = get_post_meta( $post_id, 'child', true );
        $message = get_post_meta( $post_id, 'message', true );
        $phone = get_post_meta( $post_id, 'phone', true );
        $email = get_post_meta( $post_id, 'email', true );
        $first_name = get_post_meta( $post_id, 'first_name', true );
        $last_name = get_post_meta( $post_id, 'last_name', true );
        $status = get_post_meta( $post_id, 'status', true ); // pending, approved, unapproved, completed, cancelled
        $price = get_post_meta( $post_id, 'price', true ); 
        $currency = get_post_meta( $post_id, 'currency', true );

        $options = get_option( 'hub_booking_settings' );

        $admin_email_content = $options['admin_email_content'];
        $user_email_content = $options['user_email_content'];

        if ( empty( $admin_email_content ) || empty( $user_email_content ) ) {
            if ( $is_admin ){
                $html .= sprintf( '<strong>%s %s</strong><br><br>',ucfirst($first_name), ucfirst($last_name ) );
            } else {
                $html .= sprintf( '<strong>%s %s %s,</strong> %s<br><br>', __('Hi'), ucfirst($first_name), ucfirst($last_name ), __('Your booking details:', 'hub-booking') );
            }
            $html .= sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M15 4h3v14H2V4h3V3c0-.83.67-1.5 1.5-1.5S8 2.17 8 3v1h4V3c0-.83.67-1.5 1.5-1.5S15 2.17 15 3v1zM6 3v2.5c0 .28.22.5.5.5s.5-.22.5-.5V3c0-.28-.22-.5-.5-.5S6 2.72 6 3zm7 0v2.5c0 .28.22.5.5.5s.5-.22.5-.5V3c0-.28-.22-.5-.5-.5s-.5.22-.5.5zm4 14V8H3v9h14zM7 16V9H5v7h2zm4 0V9H9v7h2zm4 0V9h-2v7h2z"/></svg> <strong>%s</strong> %s - %s<br>', __('Booking Date:', 'hub-booking'), $booking_date[0], $booking_date[1] );
            $html .= sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M10 9.25c-2.27 0-2.73-3.44-2.73-3.44C7 4.02 7.82 2 9.97 2c2.16 0 2.98 2.02 2.71 3.81c0 0-.41 3.44-2.68 3.44zm0 2.57L12.72 10c2.39 0 4.52 2.33 4.52 4.53v2.49s-3.65 1.13-7.24 1.13c-3.65 0-7.24-1.13-7.24-1.13v-2.49c0-2.25 1.94-4.48 4.47-4.48z"/></svg> <strong>%s</strong> %s (Adult: %s, Child: %s)<br>', __('Person:', 'hub-booking'), (intval( $adult ) + intval( $child )), $adult, $child );
            
            $html .= sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="m12.06 6l-.21-.2c-.52-.54-.43-.79.08-1.3l2.72-2.75c.81-.82.96-1.21 1.73-.48l.21.2zm.53.45l4.4-4.4c.7.94 2.34 3.47 1.53 5.34c-.73 1.67-1.09 1.75-2 3c-1.85 2.11-4.18 4.37-6 6.07c-1.26.91-1.31 1.33-3 2c-1.8.71-4.4-.89-5.38-1.56l4.4-4.4l1.18 1.62c.34.46 1.2-.06 1.8-.66c1.04-1.05 3.18-3.18 4-4.07c.59-.59 1.12-1.45.66-1.8zM1.57 16.5l-.21-.21c-.68-.74-.29-.9.52-1.7l2.74-2.72c.51-.49.75-.6 1.27-.11l.2.21z"/></svg> <strong>%s</strong> %s<br><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M3.87 4h13.25C18.37 4 19 4.59 19 5.79v8.42c0 1.19-.63 1.79-1.88 1.79H3.87c-1.25 0-1.88-.6-1.88-1.79V5.79c0-1.2.63-1.79 1.88-1.79zm6.62 8.6l6.74-5.53c.24-.2.43-.66.13-1.07c-.29-.41-.82-.42-1.17-.17l-5.7 3.86L4.8 5.83c-.35-.25-.88-.24-1.17.17c-.3.41-.11.87.13 1.07z"/></svg> <strong>%s</strong> %s<br>', __('Phone:', 'hub-booking'), $phone, __('Email:', 'hub-booking'), $email );
            $html .= sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M10.6 9c-.4-.1-.8-.3-1.1-.6c-.3-.1-.4-.4-.4-.6c0-.2.1-.5.3-.6c.3-.2.6-.4.9-.3c.6 0 1.1.3 1.4.7l.9-1.2c-.3-.3-.6-.5-.9-.7c-.3-.2-.7-.3-1.1-.3V4H9.4v1.4c-.5.1-1 .4-1.4.8c-.4.5-.7 1.1-.6 1.7c0 .6.2 1.2.6 1.6c.5.5 1.2.8 1.8 1.1c.3.1.7.3 1 .5c.2.2.3.5.3.8c0 .3-.1.6-.3.9c-.3.3-.7.4-1 .4c-.4 0-.9-.1-1.2-.4c-.3-.2-.6-.5-.8-.8l-1 1.1c.3.4.6.7 1 1c.5.3 1.1.6 1.7.6V16h1.1v-1.5c.6-.1 1.1-.4 1.5-.8c.5-.5.8-1.3.8-2c0-.6-.2-1.3-.7-1.7c-.5-.5-1-.8-1.6-1zM10 2c-4.4 0-8 3.6-8 8s3.6 8 8 8s8-3.6 8-8s-3.6-8-8-8zm0 14.9c-3.8 0-6.9-3.1-6.9-6.9S6.2 3.1 10 3.1s6.9 3.1 6.9 6.9s-3.1 6.9-6.9 6.9z"/></svg> <strong>%s</strong> %s %s<br>', __('Total:', 'hub-booking'), $price, $currency );
            if ( !empty( $message ) ) {
                $html .= sprintf( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="currentColor" d="M5 2h9c1.1 0 2 .9 2 2v7c0 1.1-.9 2-2 2h-2l-5 5v-5H5c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2z"/></svg> <strong>%s</strong> %s', __('Message:', 'hub-booking'), $message );
            }
    
            if ( $is_admin ) {
                $html .= sprintf(
                    '<br><br>%s<a href="%s">%s', __( 'Check: ', 'hub-booking' ), 
                    admin_url( 'edit.php?post_type=liquid-booking' ), 
                    admin_url( 'edit.php?post_type=liquid-booking' )
                );
            }
            return $html;
        }

        if ( ! empty( $admin_email_content ) ) {
            $html .= str_replace( [
                '[booking_date]',
                '[booking_person_adult]',
                '[booking_person_child]',
                '[booking_phone]',
                '[booking_email]',
                '[booking_firstname]',
                '[booking_lastname]',
                '[booking_price]',
                '[booking_currency]',
                '[booking_status]',
                '[booking_message]',
                '[booking_admin_url]',
            ], [
                $booking_date[0] . ' - ' . $booking_date[1],
                intval( $adult ),
                intval( $child ),
                $phone,
                $email,
                $first_name,
                $last_name,
                $price,
                $currency,
                $status,
                $message,
                admin_url( 'edit.php?post_type=liquid-booking' )
            ] , $admin_email_content );
            return $html;
        }

        if ( ! empty( $user_email_content ) ) {
            $html .= str_replace( [
                '[booking_date]',
                '[booking_person_adult]',
                '[booking_person_child]',
                '[booking_phone]',
                '[booking_email]',
                '[booking_firstname]',
                '[booking_lastname]',
                '[booking_price]',
                '[booking_currency]',
                '[booking_status]',
                '[booking_message]',
                '[booking_admin_url]',
            ], [
                $booking_date[0] . ' - ' . $booking_date[1],
                intval( $adult ),
                intval( $child ),
                $phone,
                $email,
                $first_name,
                $last_name,
                $price,
                $currency,
                $status,
                $message,
                admin_url( 'edit.php?post_type=liquid-booking' )
            ] , $user_email_content );
            return $html;
        }


    }

    function sanitize_date( $date ) {

        return [ 
            'start' => DateTime::createFromFormat("d/m/Y", $date[0])->format("Y-m-d"),
            'end' => DateTime::createFromFormat("d/m/Y", $date[1])->format("Y-m-d")
        ];

    }

    function sanitize_colors( $status ) {

        $colors = [
            'pending' => [
                'color' => '#ffc600', 'bg' => '#ffc600'
            ],
            'approved' => [
                'color' => '#00990d', 'bg' => '#00990d'
            ],
            'unapproved' => [
                'color' => '#910099', 'bg' => '#910099'
            ],
            'completed' => [
                'color' => '#444', 'bg' => '#aaaaaa'
            ],
            'cancelled' => [
                'color' => '#9f3333', 'bg' => '#9f3333'
            ]
        ];

        return $colors[$status];

    }

    function calculate_total_price( $booking_date, $adult, $child ) {

        $options = get_option( 'hub_booking_settings' );

        $base_adult_price = intval( $options['base_adult_price'] ) ?? 100;
        $base_child_price = intval( $options['base_child_price'] ) ?? 1;
        $currency = $options['currency'] ?? 'USD';

        $booking_date = str_replace( ' ', '' , $booking_date );
        $booking_date = explode( ',' , $booking_date );

        $firstDate = DateTime::createFromFormat('d/m/Y', $booking_date[0]);
        $secondDate = DateTime::createFromFormat('d/m/Y', $booking_date[1]);
        $diff = $firstDate->diff($secondDate);
        $daysDiff = $diff->days;

        $total = 0;

        if ( $base_adult_price ){
            $total += ($base_adult_price * $adult) * $daysDiff;
        }

        if ( $base_child_price && $base_child_price > 1 ){
            $total += ($base_child_price * $child) * $daysDiff;
        }

        return $total;
    }

    function get_pending_post_count() {

        $args = array(
            'post_type'      => 'liquid-booking',
            'post_status'    => 'publish',
            'posts_per_page' => '-1',
            'meta_key'       => 'status',
            'meta_value'     => 'pending',
            'fields'         => 'ids',
        );

        $pending_booking_ids = get_posts($args);
        $pending_booking_count = count($pending_booking_ids);
        return $pending_booking_count;

    }

    /**
     * Slots
     */

    function get_available_slots() {

        check_ajax_referer( 'hub_booking_form_nonce', 'nonce' );

        $options = get_option( 'hub_booking_settings' );
        $day_name = sanitize_text_field( $_POST['day_name'] );
        $booking_date = sanitize_text_field( $_POST['booking_date'] );
        $booked = $this->get_booking_by_slots( $booking_date );

        if ( empty( $day_name ) ) {
            wp_send_json( [
                'error' => true,
                'alert' => __('Someting went wrong!', 'hub-booking'),
            ] );
        }

        $slots = $options["slot_dates_{$day_name}"];

        if ( empty( $slots ) ){
            wp_send_json( [
                'error' => true,
                'alert' => __('There is no available slots! Try with different dates.', 'hub-booking'),
                'out' => sprintf( '<option value="">%1$s</option>', __( 'No time slots available', 'hub-booking' ) )
            ] );
        }

        $slots = explode("\r\n", trim($slots));
        $defined_slots = $out = [];
        $html = '';
        foreach( $slots as $slot ){
            $slot = explode( '*', $slot );
            $hours = $slot[0];
            $count = $slot[1];
            $defined_slots[$hours] = $count;
        }

        foreach ( $defined_slots as $defined_slot_hours => $defined_slot_count ) {
            $defined_slot_count = intval($defined_slot_count);
            if ( isset( $booked[$booking_date] ) ) {
                if ( isset( $booked[$booking_date][$defined_slot_hours] ) ) {
                    if ( count( $booked[$booking_date][$defined_slot_hours] ) < $defined_slot_count ){
                        $out[$defined_slot_hours] = $defined_slot_count - count( $booked[$booking_date][$defined_slot_hours] );
                        $html .= sprintf( '<option value="%1$s">%1$s (%3$s %2$s)</option>', $defined_slot_hours, __( 'slots', 'hub-booking' ), ( $defined_slot_count - count( $booked[$booking_date][$defined_slot_hours] ) ) );
                    }
                } else {
                    $out[$defined_slot_hours] = $defined_slot_count;
                    $html .= sprintf( '<option value="%1$s">%1$s (%3$s %2$s)</option>', $defined_slot_hours, __( 'slots', 'hub-booking' ), $defined_slot_count );
                }
            } else {
                $out[$defined_slot_hours] = $defined_slot_count;
                $html .= sprintf( '<option value="%1$s">%1$s (%3$s %2$s)</option>', $defined_slot_hours, __( 'slots', 'hub-booking' ), $defined_slot_count );
            }
        }

        wp_send_json( [
            'out' => $html,
        ] );
        
    }

    function get_booking_by_slots( $date ) {
        $args = array(
            'post_type' => 'liquid-booking',
            'meta_query' => array(
                'relation' => 'AND',
                // array(
                //     'key' => 'hours',
                //     'value' => $hours,
                //     'compare' => '===',
                // ),
                array(
                    'key' => 'booking_date',
                    'value' => $date,
                    'compare' => '===',
                ),
            ),
            'posts_per_page' => -1,
        );

        $data = [];
        $booking_posts = get_posts($args);

        foreach ( $booking_posts as $post ) {
            if ( get_post_meta($post->ID, 'status', true) !== 'cancelled' ) {
                $data[$date][get_post_meta($post->ID, 'hours', true)][] = [$post->ID];
            }
        }

        return $data;
        
    }


}

Hub_Booking_Actions::instance();
