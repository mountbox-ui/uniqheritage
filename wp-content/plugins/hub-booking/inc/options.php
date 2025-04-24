<?php
add_action( 'admin_menu', 'hub_booking_add_admin_menu' );
add_action( 'admin_init', 'hub_booking_settings_init' );

add_action( 'admin_menu', function() {

	global $menu;
	$count = \Hub_Booking_Actions::instance()->get_pending_post_count();

	if ( $count > 0 ) {
		$menu_item = wp_list_filter(
			$menu,
			array( 2 => 'edit.php?post_type=liquid-booking' ) // 2 is the position of an array item which contains URL, it will always be 2!
		);
		if ( ! empty( $menu_item )  ) {
			$menu_item_position = key( $menu_item ); // get the array key (position) of the element
			$menu[ $menu_item_position ][0] .= ' <span class="awaiting-mod">' . $count . '</span>';
		}
	}

});


function hub_booking_add_admin_menu() { 

    add_submenu_page(
        'edit.php?post_type=liquid-booking',
        __('Settings', 'hub-booking'),
        __('Settings', 'hub-booking'),
        'manage_options',
        'booking_settings',
        'hub_booking_options_page'
    );
   
	add_submenu_page(
        'edit.php?post_type=liquid-booking',
        __('Calendar', 'hub-booking'),
        __('Calendar', 'hub-booking'),
        'manage_options',
        'booking_calendar',
        'hub_booking_calendar_page'
    );

}


function hub_booking_settings_init() { 

	register_setting( 'hub_booking_settings_group', 'hub_booking_settings' );

	add_settings_section(
		'hub_booking_settings_section', 
		null,
		'hub_booking_settings_section_callback', 
		'hub_booking_settings_group'
	);

    add_settings_field( 
		'currency', 
		__( 'Currency', 'hub-booking' ), 
		'currency_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_field( 
		'base_adult_price', 
		__( 'Base adult price', 'hub-booking' ), 
		'base_adult_price_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_field( 
		'base_child_price', 
		__( 'Base child price', 'hub-booking' ), 
		'base_child_price_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_field( 
		'slot_price', 
		__( 'Slot price', 'hub-booking' ), 
		'slot_price_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_field( 
		'disabled_date', 
		__( 'Disabled dates', 'hub-booking' ), 
		'disabled_date_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_field( 
		'slot_dates', 
		__( 'Slots', 'hub-booking' ), 
		'slot_dates_render', 
		'hub_booking_settings_group', 
		'hub_booking_settings_section' 
	);

	add_settings_section(
		'hub_booking_email_section', 
		__( 'Email Templates', 'hub-booking' ),
		'hub_booking_email_section_callback', 
		'hub_booking_settings_group'
	);

	add_settings_field( 
		'admin_email_title', 
		__( 'Admin Email Title', 'hub-booking' ), 
		'admin_email_title_render', 
		'hub_booking_settings_group', 
		'hub_booking_email_section' 
	);

	add_settings_field( 
		'admin_email_content', 
		__( 'Admin Email Content', 'hub-booking' ), 
		'admin_email_content_render', 
		'hub_booking_settings_group', 
		'hub_booking_email_section' 
	);

	add_settings_field( 
		'user_email_title', 
		__( 'User Email Title', 'hub-booking' ), 
		'user_email_title_render', 
		'hub_booking_settings_group', 
		'hub_booking_email_section' 
	);

	add_settings_field( 
		'user_email_status_title', 
		__( 'User Email Status Title', 'hub-booking' ), 
		'user_email_status_title_render', 
		'hub_booking_settings_group', 
		'hub_booking_email_section' 
	);

	add_settings_field( 
		'user_email_content', 
		__( 'User Email Content', 'hub-booking' ), 
		'user_email_content_render', 
		'hub_booking_settings_group', 
		'hub_booking_email_section' 
	);

}

function admin_email_title_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='text' name='hub_booking_settings[admin_email_title]' value='<?php echo $options['admin_email_title']; ?>'>
	<p>Example: New Booking! (#[booking_id])</p>
	<?php

}

function admin_email_content_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<textarea type='text' rows='8' name='hub_booking_settings[admin_email_content]'><?php echo $options['admin_email_content']; ?></textarea>
	<p>Example: New Booking! Booking data [booking_date] and her/his name: [booking_first_name]. Phone: [booking_phone]</p>
	<?php

}

function user_email_title_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='text' name='hub_booking_settings[user_email_title]' value='<?php echo $options['user_email_title']; ?>'>
	<p>This is a new booking email title. Example: Booking Created!</p>
	<?php

}

function user_email_status_title_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='text' name='hub_booking_settings[user_email_status_title]' value='<?php echo $options['user_email_status_title']; ?>'>
	<p>Example: Your Booking (#[booking_id]) is [booking_status]!</p>
	<?php

}

function user_email_content_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<textarea type='text' rows='8' name='hub_booking_settings[user_email_content]'><?php echo $options['user_email_content']; ?></textarea>
	<p>Example: Hi [booking_first_name], your booking price: [booking_price]. </p>
	<?php

}

function base_adult_price_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='number' name='hub_booking_settings[base_adult_price]' value='<?php echo $options['base_adult_price']; ?>'>
	<?php

}

function base_child_price_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='number' name='hub_booking_settings[base_child_price]' value='<?php echo $options['base_child_price']; ?>'>
	<?php

}

function slot_price_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<input type='number' name='hub_booking_settings[slot_price]' value='<?php echo $options['slot_price']; ?>'>
	<?php

}

function slot_dates_render() { 

	$options = get_option( 'hub_booking_settings' );
	$days_of_week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');

	?>
		<p><?php esc_html_e( 'Enter the hours (with 24 hours type) and slots by each line. START_HOUR:END_HOUR*SLOT. Example: 08:00-09:00*2', 'hub-booking' ); ?></p>
		<p><?php esc_html_e( 'Leave blank to disable days.', 'hub-booking' ); ?></p>
		<br>
		<table class="widefat">
			<thead>
				<th style="padding-left:1em"><?php esc_html_e( 'Sunday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Monday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Tuesday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Wednesday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Thursday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Friday', 'hub-booking' ); ?></th>
				<th style="padding-left:1em"><?php esc_html_e( 'Saturday', 'hub-booking' ); ?></th>
			</thead>
			<tbody>
				<tr>
					<?php
						foreach ($days_of_week as $day) {
							$field_name = "hub_booking_settings[slot_dates_{$day}]";
							$field_value = $options["slot_dates_{$day}"];
							?>
							<td><textarea style="width:100%" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" rows="10"><?php echo esc_textarea($field_value); ?></textarea></td>
							<?php
						}
					?>
				</tr>
			</tbody>
		</table>
	<?php

}

function disabled_date_render() { 

	$options = get_option( 'hub_booking_settings' );

	$selected_dates = explode(", ", $options['disabled_date']);
	$selected_dates_out = [];
	foreach( $selected_dates as $date ) {
		if( !$date ) continue;
		$selected_dates_out[] = DateTime::createFromFormat("d/m/Y", $date)->format("m/d/y");
	}

	$datepicker_options = [
		'range' => false,
		'multipleDates' => true,
		'autoClose' => true,
		'locale' =>  [
			'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			'daysShort' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			'daysMin' => ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			'monthsShort' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			'today' => 'Today',
			'clear' => 'Clear',
			'dateFormat' => 'dd/MM/yyyy',
			'timeFormat' => 'hh:mm aa',
			'firstDay' => 0
		],
		'selectedDates' => [$selected_dates_out],
	];

	?>
	<input 
		id="datepicker"
		placeholder="<?php esc_html_e( 'Select dates', 'hub-booking' ) ?>"
		type="text" 
		name="hub_booking_settings[disabled_date]"
		data-options='<?php echo wp_json_encode( $datepicker_options ) ?>'
		value='<?php echo $options['disabled_date'] ?>'
	>
	<?php

}

function currency_render() { 

	$options = get_option( 'hub_booking_settings' );
	?>
	<select name='hub_booking_settings[currency]'>
        <option value="USD" <?php selected( $options['currency'], 'USD' ); ?>>(USD)</option>
        <option value="EUR" <?php selected( $options['currency'], 'EUR' ); ?>>(EUR)</option>
        <option value="GBP" <?php selected( $options['currency'], 'GBP' ); ?>>(GBP)</option>
        <option value="JPY" <?php selected( $options['currency'], 'JPY' ); ?>>(JPY)</option>
        <option value="AUD" <?php selected( $options['currency'], 'AUD' ); ?>>(AUD)</option>
        <option value="CAD" <?php selected( $options['currency'], 'CAD' ); ?>>(CAD)</option>
        <option value="CHF" <?php selected( $options['currency'], 'CHF' ); ?>>(CHF)</option>
        <option value="CNY" <?php selected( $options['currency'], 'CNY' ); ?>>(CNY)</option>
        <option value="INR" <?php selected( $options['currency'], 'INR' ); ?>>(INR)</option>
        <option value="SGD" <?php selected( $options['currency'], 'SGD' ); ?>>(SGD)</option>
        <option value="RUB" <?php selected( $options['currency'], 'RUB' ); ?>>(RUB)</option>
        <option value="TRY" <?php selected( $options['currency'], 'TRY' ); ?>>(TRY)</option>
    </select>

<?php

}

function hub_booking_settings_section_callback() { 

	echo __( 'Fill options and save', 'hub-booking' );

}

function hub_booking_email_section_callback() { 

	?>
		<p><?php esc_html_e('Allowed Tags', 'hub-booking') ?>: 
			<code>[booking_date]</code>,
			<code>[booking_person_adult]</code>,
			<code>[booking_person_child]</code>,
			<code>[booking_phone]</code>,
			<code>[booking_email]</code>,
			<code>[booking_firstname]</code>,
			<code>[booking_lastname]</code>,
			<code>[booking_price]</code>,
			<code>[booking_currency]</code>,
			<code>[booking_status]</code>,
			<code>[booking_message]</code>,
			<code>[booking_admin_url]</code>,
		</p>
	<?php

}


function hub_booking_options_page() { 

		?>
		<form action='options.php' method='post'>

			<h2><?php echo __('Booking Settings', 'hub-booking'); ?></h2>

			<?php
				settings_fields( 'hub_booking_settings_group' );
				do_settings_sections( 'hub_booking_settings_group' );
				submit_button();
			?>

		</form>
		<?php

}


function hub_booking_calendar_page() { 


	?>
		<h2><?php echo __('Booking Calendar View', 'hub-booking'); ?></h2>

		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var calendarEl = document.getElementById('hub-booking-calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
					headerToolbar: { center: 'multiMonthYear,dayGridMonth,listWeek' },
					initialView: 'dayGridMonth',
					dayMaxEventRows: false, // :bool - integer
					timeZone: 'UTC',
					eventClick: function(info) {
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								action: 'booking_get_calendar_event',
								booking_id: info.event.id
							},
							success: function(response) {
								if ( response.title && response.content ) {
									jQuery.confirm({
										columnClass: 'hub-booking-calendar-event',
										type: 'default',
										title: response.title,
										content: response.content,
										buttons: {
											new: {
												text: 'Close',
											},
										}
									});
								}
								if ( response.error && response.alert ) {
									console.log( response.alert );
								}
							}.bind(this)
						});
					},
					events: <?php echo \Hub_Booking_Actions::instance()->get_booking_for_calendar(); ?>
				});
				calendar.render();
			});
		</script>
		
		<div id="hub-booking-calendar"></div>
	<?php

}
