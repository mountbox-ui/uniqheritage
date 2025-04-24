<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class LD_BookingForm_Widget extends Widget_Base {

	protected static $datepicker_options = array();

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

		wp_register_style( 'air-datepicker',
			LD_BOOKING_URL . 'assets/vendors/air-datepicker/air-datepicker.css',
			[ ],
			'3.4'
		);

		wp_register_script( 'air-datepicker',
			LD_BOOKING_URL . 'assets/vendors/air-datepicker/air-datepicker.js',
			[ 'jquery' ],
			'3.4',
			true
		);

		wp_register_script( 'hub-booking-form',
			LD_BOOKING_URL . 'assets/js/hub-booking-form.js',
			[ 'air-datepicker' ],
			'1.0',
			true
		);

		wp_register_style( 'hub-booking-form',
			LD_BOOKING_URL . 'assets/css/hub-booking-form.css',
			[ ],
			'1.0'
		);

	}

	public function get_style_depends() {
		return [ 'air-datepicker', 'hub-booking-form' ];
	}

	public function get_script_depends() {
		return [ 'air-datepicker', 'hub-booking-form' ];
	}

	public function get_name() {
		return 'ld_booking_form';
	}

	public function get_title() {
		return __( 'Booking Form', 'hub-booking' );
	}

	public function get_icon() {
		return 'eicon-tel-field lqd-element';
	}

	public function get_categories() {
		return [ 'hub-booking' ];
	}

	public function get_keywords() {
		return [ 'title', 'post' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'position_section',
            [
				'label' => __( 'Data & Positioning', 'hub-booking' ),
            ]
		);

		$this->add_control(
			'booking_type',
			[
				'label' => esc_html__( 'Booking type', 'hub-booking' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'slot' => [
						'title' => esc_html__( 'Time Slot', 'hub-booking' ),
						'icon' => ' eicon-clock-o',
					],
					'day' => [
						'title' => esc_html__( 'Day', 'hub-booking' ),
						'icon' => ' eicon-calendar',
					],
				],
				'default' => 'day',
				'toggle' => false,
				'separator' => 'after'
			]
		);

		$this->add_control(
			'direction',
			[
				'label' => esc_html__( 'Direction', 'hub-booking' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'horizontal' => [
						'title' => esc_html__( 'Horizontal', 'hub-booking' ),
						'icon' => 'eicon-form-horizontal',
					],
					'vertical' => [
						'title' => esc_html__( 'Vertical', 'hub-booking' ),
						'icon' => 'eicon-form-vertical',
					],
				],
				'default' => 'horizontal',
				'toggle' => false,
			]
		);

		$this->add_responsive_control(
			'wrap',
			[
				'label' => esc_html__( 'Wrap', 'hub-booking' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'nowrap' => [
						'title' => esc_html__( 'No Wrap', 'hub-booking' ),
						'icon' => 'eicon-nowrap',
					],
					'wrap' => [
						'title' => esc_html__( 'Wrap', 'hub-booking' ),
						'icon' => 'eicon-wrap',
					],
				],
				'default' => 'nowrap',
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} form' => 'flex-wrap: {{VALUE}};',
				],
				'condition' => [
					'direction' => 'vertical'
				]
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'textdomain' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'textdomain' ),
						'icon' => 'eicon-align-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'textdomain' ),
						'icon' => 'eicon-align-center-h',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'textdomain' ),
						'icon' => 'eicon-align-end-h',
					],
				],
				'default' => 'center',
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} form' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'direction' => 'vertical'
				]
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Gap', 'hub-booking' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} form' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'direction' => 'vertical'
				]
			]
		);

		$this->add_responsive_control(
			'input_width',
			[
				'label' => esc_html__( 'Input Width', 'hub-booking' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .form-field' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'direction' => 'vertical'
				]
			]
		);

		$this->add_control(
			'hide_labels',
			[
				'label' => esc_html__( 'Hide Labels?', 'hub-booking' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'hub-booking' ),
				'label_off' => esc_html__( 'No', 'hub-booking' ),
				'return_value' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'labels_inline',
			[
				'label' => esc_html__( 'Inline Labels?', 'hub-booking' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'hub-booking' ),
				'label_off' => esc_html__( 'No', 'hub-booking' ),
				'return_value' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .form-field' => 'display: flex',
					'{{WRAPPER}} .form-field label' => 'min-width: fit-content',
				],
				'condition' => [
					'hide_labels' => '',
					'direction' => 'vertical'
				]
			]
		);

		$this->add_responsive_control(
			'inline_gap',
			[
				'label' => esc_html__( 'Gap', 'hub-booking' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .form-field' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'labels_inline' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'inline_alingment',
			[
				'label' => esc_html__( 'Alignment', 'hub-booking' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Top', 'hub-booking' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'hub-booking' ),
						'icon' => ' eicon-v-align-middle',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'hub-booking' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'mid',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .form-field' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'labels_inline' => 'yes'
				]
			]
		);

		$this->add_control(
			'disabled_inputs',
			[
				'label' => esc_html__( 'Disable Inputs', 'hub-booking' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'last_name' => esc_html__( 'Last Name', 'hub-booking' ),
					'email' => esc_html__( 'Email', 'hub-booking' ),
					'child' => esc_html__( 'Child', 'hub-booking' ),
					'message' => esc_html__( 'Message', 'hub-booking' ),
					'phone' => esc_html__( 'Phone', 'hub-booking' ),
				],
			]
		);

		$this->add_responsive_control(
			'icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'hub-booking' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'hub-booking' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'hub-booking' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle' => false,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'icon_position_h',
			[
				'label' => esc_html__( 'Horizontal', 'hub-booking' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .form-icon-wrapper' => '{{icon_position.VALUE}}: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_position_v',
			[
				'label' => esc_html__( 'Vertical', 'hub-booking' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 33,
				],
				'selectors' => [
					'{{WRAPPER}} .form-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'inputs_section',
            [
				'label' => __( 'Input & Labels', 'hub-booking' ),
            ]
		);

		// Datepicker
		$this->add_control(
			'h_datepicker',
			[
				'label' => esc_html__( 'Datepicker', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_datepicker',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Select date', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_datepicker',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Select date', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_datepicker',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Time
		$this->add_control(
			'h_time',
			[
				'label' => esc_html__( 'Time', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'booking_type' => 'slot'
				]
			]
		);

		$this->add_control(
			'label_time',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Select Slot', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
				'condition' => [
					'booking_type' => 'slot'
				]
			]
		);

		$this->add_control(
			'placeholder_time',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Select Slot', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
				'condition' => [
					'booking_type' => 'slot'
				]
			]
		);

		$this->add_control(
			'icon_time',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'condition' => [
					'booking_type' => 'slot'
				]
			]
		);

		// First Name
		$this->add_control(
			'h_first_name',
			[
				'label' => esc_html__( 'First Name', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_first_name',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'First Name', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_first_name',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'First Name', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_first_name',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Last Name
		$this->add_control(
			'h_last_name',
			[
				'label' => esc_html__( 'Last Name', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_last_name',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Last Name', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_last_name',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Last Name', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_last_name',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Phone
		$this->add_control(
			'h_phone',
			[
				'label' => esc_html__( 'Phone', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_phone',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Phone', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_phone',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Phone', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_phone',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Email
		$this->add_control(
			'h_email',
			[
				'label' => esc_html__( 'Email', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_email',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Email', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_email',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Email', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_email',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Adult
		$this->add_control(
			'h_adult',
			[
				'label' => esc_html__( 'Adult', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_adult',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Adult', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_adult',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Adult', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_adult',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Child
		$this->add_control(
			'h_child',
			[
				'label' => esc_html__( 'Child', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_child',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Child', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_child',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Child', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_child',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Message
		$this->add_control(
			'h_message',
			[
				'label' => esc_html__( 'Message', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_message',
			[
				'label' => esc_html__( 'Label', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Message', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'placeholder_message',
			[
				'label' => esc_html__( 'Placeholder', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Write message if you need.', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_message',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		// Button
		$this->add_control(
			'heading_button',
			[
				'label' => esc_html__( 'Button', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button', 'hub-booking' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Book now', 'hub-booking' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'icon_button',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline'
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section_input',
            [
				'label' => __( 'Label & Input', 'hub-booking' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
		);

		// Label
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Label Typography', 'hub-booking' ),
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} label',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__( 'Margin', 'hub-booking' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Input
		$this->add_control(
			'heading_style_input',
			[
				'label' => esc_html__( 'Input', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Input Typography', 'hub-booking' ),
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select',
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => esc_html__( 'Input', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'input_placeholder_color',
			[
				'label' => esc_html__( 'Input placeholder', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input::placeholder, {{WRAPPER}} textarea::placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'input_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} input,{{WRAPPER}} textarea, {{WRAPPER}} select',
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__( 'Padding', 'hub-booking' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => '1',
					'right' => '1',
					'bottom' => '1',
					'left' => '1',
					'unit' => 'em',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_margin',
			[
				'label' => esc_html__( 'Margin', 'hub-booking' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'bottom' => '1',
					'unit' => 'em',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} select' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Icon
		$this->add_control(
			'heading_style_icon',
			[
				'label' => esc_html__( 'Icon', 'hub-booking' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .form-icon-wrapper' => 'color: {{VALUE}}; fill:{{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section_output',
            [
				'label' => __( 'Output Message', 'hub-booking' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'hub-booking' ),
				'name' => 'output_title_typography',
				'selector' => '{{WRAPPER}} .hub-booking-alert h3',
			]
		);

		$this->add_control(
			'output_title_color',
			[
				'label' => esc_html__( 'Color', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hub-booking-alert h3' => 'color: {{VALUE}}',
					'{{WRAPPER}} .hub-booking-alert .dashicons' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Content Typography', 'hub-booking' ),
				'name' => 'output_content_typography',
				'selector' => '{{WRAPPER}} .hub-booking-alert p',
			]
		);

		$this->add_control(
			'output_content_color',
			[
				'label' => esc_html__( 'Color', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .hub-booking-alert p' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section_btn',
            [
				'label' => __( 'Button', 'hub-booking' ),
				'tab' => Controls_Manager::TAB_STYLE,
            ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'hub-booking' ),
				'name' => 'btn_typography',
				'selector' => '{{WRAPPER}} .btn',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border',
				'selector' => '{{WRAPPER}} .btn',
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label' => esc_html__( 'Color', 'hub-booking' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'btn_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .btn',
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label' => esc_html__( 'Padding', 'hub-booking' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render_label( $label ) {
		if ( $this->get_settings_for_display( 'hide_labels' ) ) return;
		$out = $this->get_settings_for_display( 'label_' . $label );
		if ( !empty( $out ) ){
			?>
				<label for="<?php echo esc_attr( $label );?>"><?php echo $out; ?></label>
			<?php
		}
	}

	protected function render_placeholder( $placeholder ) {
		$out = $this->get_settings_for_display( 'placeholder_' . $placeholder );
		if ( !empty( $out ) ){
			echo 'placeholder="' . esc_attr( $out ) . '"';
		}
	}

	protected function render_form() {
		$direction = $this->get_settings_for_display('direction');
		$disabled_inputs = $this->get_settings_for_display('disabled_inputs') ? $this->get_settings_for_display('disabled_inputs') : array();
		$disabled_inputs_serialize = implode(',', $disabled_inputs);
		$disabled_message = $disabled_last_name = $disabled_child = $disabled_email = $disabled_phone = false;

		if ( in_array('message', $disabled_inputs) ) { $disabled_message = true; }
		if ( in_array('last_name', $disabled_inputs) ) { $disabled_last_name = true; }
		if ( in_array('child', $disabled_inputs) ) { $disabled_child = true; }
		if ( in_array('email', $disabled_inputs) ) { $disabled_email = true; }
		if ( in_array('phone', $disabled_inputs) ) { $disabled_phone = true; }

		if ( $direction == 'horizontal' ) { ?>

			<form class="hub-booking-form form-init form-step-user" action="" method="post">

				<?php $this->render_label( 'datepicker' ); ?>
				<div class="form-input pos-rel">
					<input
						<?php $this->render_placeholder( 'datepicker' ); ?>
						type="text"
						name="datepicker"
						data-options='<?php echo wp_json_encode( self::$datepicker_options ) ?>'
						readonly
					>
					<div class="form-icon-wrapper">
						<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_datepicker'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
					</div>
				</div>

				<?php if ( $this->get_settings_for_display('booking_type') === 'slot' ): ?>
				<?php $this->render_label( 'time' ); ?>
				<div class="form-input pos-rel h-100">
					<select name="time" class="h-100" required>
						<option value="" selected disabled><?php echo $this->get_settings_for_display('placeholder_time'); ?></option>
					</select>
					<div class="form-icon-wrapper">
						<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_time'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
					</div>
				</div>
				<?php endif; ?>

				<span class="loader-clock"></span>

				<div class="user-form">
					<div class="d-flex" style="gap:1em;">
						<div class="<?php echo esc_attr($disabled_last_name ? 'w-100' : 'w-50'); ?>">
							<?php $this->render_label( 'first_name' ); ?>
							<div class="form-input pos-rel">
								<div class="form-icon-wrapper">
									<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_first_name'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
								</div>
								<input type="text" name="first_name" required <?php $this->render_placeholder( 'first_name' ); ?>>
							</div>
						</div>

						<?php if ( !$disabled_last_name ): ?>
						<div class="w-50">
							<?php $this->render_label( 'last_name' ); ?>
							<div class="form-input pos-rel">
								<div class="form-icon-wrapper">
									<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_last_name'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
								</div>
								<input type="text" name="last_name" required <?php $this->render_placeholder( 'last_name' ); ?>>
							</div>
						</div>
						<?php endif; ?>
					</div>

					<?php if ( !$disabled_phone ): ?>
					<?php $this->render_label( 'phone' ); ?>
					<div class="form-input pos-rel">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_phone'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input type="tel" name="phone" required <?php $this->render_placeholder( 'phone' ); ?>>
					</div>
					<?php endif; ?>

					<?php if ( !$disabled_email ): ?>
					<?php $this->render_label( 'email' ); ?>
					<div class="form-input pos-rel">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_email'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input type="email" name="email" required <?php $this->render_placeholder( 'email' ); ?>>
					</div>
					<?php endif; ?>

					<?php if ( $this->get_settings_for_display( 'booking_type' ) === 'day' ): ?>
					<div class="d-flex" style="gap:1em;">
						<div class="<?php echo esc_attr($disabled_child ? 'w-100' : 'w-50'); ?>">
							<?php $this->render_label( 'adult' ); ?>
							<div class="form-input pos-rel">
								<div class="form-icon-wrapper">
									<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_adult'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
								</div>
								<input type="number" name="adult" min="1" max="4" value="2" <?php $this->render_placeholder( 'adult' ); ?>>
							</div>
						</div>
						<?php if ( !$disabled_child ): ?>
						<div class="w-50">
							<?php $this->render_label( 'child' ); ?>
							<div class="form-input pos-rel">
								<div class="form-icon-wrapper">
									<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_child'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
								</div>
								<input type="number" name="child" min="0" max="4" value="0" <?php $this->render_placeholder( 'child' ); ?>>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if ( !$disabled_message ): ?>
					<?php $this->render_label( 'message' ); ?>
					<div class="form-input pos-rel">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_message'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<textarea type="text" name="message" <?php $this->render_placeholder( 'message' ); ?>></textarea>
					</div>
					<?php endif; ?>
				</div>

				<input type="hidden" name="disabled_inputs" value="<?php echo esc_attr($disabled_inputs_serialize); ?>">
				<input type="hidden" name="booking_type" value="<?php echo esc_attr($this->get_settings_for_display('booking_type')); ?>">

				<?php wp_nonce_field( 'hub_booking_form_nonce', 'nonce' ); ?>

				<button type="submit" class="submit btn elementor-button ws-nowrap btn-solid btn-has-label w-100"><?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_button'), [ 'class' => 'form-btn-icon', 'aria-hidden' => 'true' ] ); echo $this->get_settings_for_display('button_text'); ?><span class="price"></span></button>

                <div class="hub-booking-form-message"></div>

			</form>

		<?php } else { ?>

			<form class="hub-booking-form form-init form-step-user d-flex" action="" method="post">

				<div class="form-field">
					<?php $this->render_label( 'datepicker' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_datepicker'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input
							class="h-100"
							<?php $this->render_placeholder( 'datepicker' ); ?>
							type="text"
							name="datepicker"
							data-options='<?php echo wp_json_encode( self::$datepicker_options ) ?>'
							readonly
						>
					</div>
				</div>

				<?php if ( $this->get_settings_for_display('booking_type') === 'slot' ): ?>
					<div class="form-field">
						<?php $this->render_label( 'time' ); ?>
						<div class="form-input pos-rel h-100">
							<div class="form-icon-wrapper">
								<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_time'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
							</div>
							<select name="time" class="h-100" required>
								<option value="" selected disabled><?php echo $this->get_settings_for_display('placeholder_time'); ?></option>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<span class="loader-clock"></span>

				<div class="form-field">
					<?php $this->render_label( 'first_name' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_first_name'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="text" name="first_name" required <?php $this->render_placeholder( 'first_name' ); ?>>
					</div>
				</div>

				<?php if ( !$disabled_last_name ): ?>
				<div class="form-field">
					<?php $this->render_label( 'last_name' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_last_name'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="text" name="last_name" required <?php $this->render_placeholder( 'last_name' ); ?>>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( !$disabled_phone ): ?>
				<div class="form-field">
					<?php $this->render_label( 'phone' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_phone'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="tel" name="phone" required <?php $this->render_placeholder( 'phone' ); ?>>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( !$disabled_email ): ?>
				<div class="form-field">
					<?php $this->render_label( 'email' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_email'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="email" name="email" required <?php $this->render_placeholder( 'email' ); ?>>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $this->get_settings_for_display( 'booking_type' ) === 'day' ): ?>
				<div class="form-field">
					<?php $this->render_label( 'adult' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_adult'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="number" name="adult" min="1" max="4" value="2" <?php $this->render_placeholder( 'adult' ); ?>>
					</div>
				</div>

				<?php if ( !$disabled_child ): ?>
				<div class="form-field">
					<?php $this->render_label( 'child' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_child'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<input class="h-100" type="number" name="child" min="0" max="4" value="0" <?php $this->render_placeholder( 'child' ); ?>>
					</div>
				</div>
				<?php endif; ?>
				<?php endif; ?>

				<?php if ( !$disabled_message ): ?>
				<div class="form-field">
					<?php $this->render_label( 'message' ); ?>
					<div class="form-input pos-rel h-100">
						<div class="form-icon-wrapper">
							<?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_message'), [ 'class' => 'form-icon', 'aria-hidden' => 'true' ] ); ?>
						</div>
						<textarea class="h-100" type="text" name="message" rows="1" <?php $this->render_placeholder( 'message' ); ?>></textarea>
					</div>
				</div>
				<?php endif; ?>

				<input type="hidden" name="disabled_inputs" value="<?php echo esc_attr($disabled_inputs_serialize); ?>">
				<input type="hidden" name="booking_type" value="<?php echo esc_attr($this->get_settings_for_display('booking_type')); ?>">

				<?php wp_nonce_field( 'hub_booking_form_nonce', 'nonce' ); ?>

				<div class="form-field">
					<button type="submit" class="submit btn elementor-button ws-nowrap btn-solid btn-has-label w-100"><?php Icons_Manager::render_icon( $this->get_settings_for_display('icon_button'), [ 'class' => 'form-btn-icon', 'aria-hidden' => 'true' ] ); echo $this->get_settings_for_display('button_text'); ?><span class="price"></span></button>
				</div>

                <div class="hub-booking-form-message"></div>

			</form>

		<?php }

	}

	protected function render() {

		self::$datepicker_options = array_merge( self::$datepicker_options, [
			'range' => $this->get_settings_for_display('booking_type') === 'day' ? true : false,
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
		] );

		$this->render_form();

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_BookingForm_Widget() );