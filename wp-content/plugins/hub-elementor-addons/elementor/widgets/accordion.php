<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Schemes\Color;
use Elementor\Schemes\Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class LD_Accordion extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ld_accordion';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Liquid Accordion', 'hub-elementor-addons' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-accordion lqd-element';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'hub-core' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'accordion', 'tab', 'toggle' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// Items Section
		$this->start_controls_section(
			'items_section',
			[
				'label' => __( 'Items', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'active_tab',
			[
				'label' => __( 'Active tab', 'hub-elementor-addons' ),
				'description' => __( 'Enter active tab. Set this to -1 if you want all the tabs closed.', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => '1',
			]
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Content type', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => esc_html__( 'Text Editor', 'hub-elementor-addons' ),
					'template' => esc_html__( 'Elementor Template', 'hub-elementor-addons' ),
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Accordion Title', 'hub-elementor-addons' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'hub-elementor-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Accordion Content', 'hub-elementor-addons' ),
				'show_label' => false,
				'condition' => [
					'content_type' => 'text'
				]
			]
		);

		$repeater->add_control(
			'template',
			[
				'label' => __( 'Select Template', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'label_block' => true,
				'options' => liquid_helper()->get_elementor_templates(),
				'description' => liquid_helper()->get_elementor_templates_edit(),
				'condition' => [
					'content_type' => 'template'
				]
			]
		);

		$this->add_control(
			'items',
			array(
				'label' => __( 'Accordion Items', 'hub-elementor-addons' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Accordion #1', 'hub-elementor-addons' ),
						'content' => __( '<p>Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>', 'hub-elementor-addons' ),
					],
					[
						'title' => __( 'Accordion #2', 'hub-elementor-addons' ),
						'content' => __( '<p>Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>', 'hub-elementor-addons' ),
					],
				],
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => esc_html__( 'Title Element Tag', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			)
		);

		$this->add_control(
			'use_inheritance',
			[
				'label' => __( 'Inherit font styles?', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'true',
			]
		);

		$this->add_control(
			'tag_to_inherite',
			array(
				'label' => esc_html__( 'Element Tag', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'p' => 'p',
				],
				'default' => 'h1',
				'condition' => array(
					'use_inheritance' => 'true',
				),

			)
		);

		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__( 'FAQ Schema', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// General Section
		$this->start_controls_section(
			'general_section',
			array(
				'label' => __( 'Accordion', 'hub-elementor-addons' ),
			)
		);

		$this->add_control(
			'accordion_deprecated_controls_warn',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( 'All these options are deprecated. Please use options in <b>Style</b> tab.', 'hub-elementor-addons' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Title height', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __( 'Shortest', 'hub-elementor-addons' ),
					'sm' => __( 'Short', 'hub-elementor-addons' ),
					'md' => __( 'Medium', 'hub-elementor-addons' ),
					'lg' => __( 'Tall', 'hub-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'borders',
			[
				'label' => __( 'Border style', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'accordion-title-bordered' => __( 'Title Bordered', 'hub-elementor-addons' ),
					'accordion-title-underlined' => __( 'Title Underlined', 'hub-elementor-addons' ),
					'accordion-body-underlined' => __( 'Content Underlined', 'hub-elementor-addons' ),
					'accordion-body-bordered' => __( 'Content Bordered', 'hub-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'border_round',
			[
				'label' => __( 'Title border round', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'accordion-title-round' => __( 'Round', 'hub-elementor-addons' ),
					'accordion-title-circle' => __( 'Circle', 'hub-elementor-addons' ),
				],
				'condition' => array(
					'borders' => 'accordion-title-bordered',
				)
			]
		);

		$this->add_control(
			'body_border_round',
			[
				'label' => __( 'Items border round', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'accordion-body-round' => __( 'Round', 'hub-elementor-addons' ),
				],
				'condition' => array(
					'borders' => 'accordion-body-bordered',
				)
			]
		);

		$this->add_control(
			'items_shadow',
			[
				'label' => __( 'Items Shadow', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'heading_shadow',
			[
				'label' => __( 'Headings shadow', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'active_style',
			[
				'label' => __( 'Active heading shadow', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => ''
			]
		);

		$this->end_controls_section();

		// Expander Section
		$this->start_controls_section(
			'expander_section',
			[
				'label' => __( 'Expander', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label' => __( 'Enable expander', 'hub-elementor-addons' ),
				'description' => __( 'If enabled will show icons in expander', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'hub-elementor-addons' ),
				'label_off' => __( 'Off', 'hub-elementor-addons' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'expander_position',
			[
				'label' => __( 'Expander position', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'hub-elementor-addons' ),
					'accordion-expander-left' => __( 'Left', 'hub-elementor-addons' ),
				],
				'condition' => array(
					'show_icon' => 'yes',
				)
			]
		);

		// $this->add_control(
		// 	'expander_size',
		// 	[
		// 		'label' => __( 'Expander size', 'hub-elementor-addons' ),
		// 		'type' => Controls_Manager::SELECT,
		// 		'default' => '',
		// 		'options' => [
		// 			'' => __( 'Normal', 'hub-elementor-addons' ),
		// 			'accordion-expander-lg' => __( 'Large ( 22px )', 'hub-elementor-addons' ),
		// 			'accordion-expander-xl' => __( 'xLarge ( 26px )', 'hub-elementor-addons' ),
		// 		],
		// 		'condition' => array(
		// 			'show_icon' => 'yes',
		// 		)
		// 	]
		// );

		$this->add_control(
			'i_type',
			[
				'label' => __( 'Icon library', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fontawesome',
				'options' => [
					'fontawesome'  => __( 'Icon Library', 'hub-elementor-addons' ),
					'image' => __( 'Image', 'hub-elementor-addons' ),
				],
				'condition' => array(
					'show_icon' => 'yes'
				)
			]
		);

		$this->add_control(
			'i_icon_fontawesome',
			[
				'label' => __( 'Icon', 'hub-elementor-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-chevron-down',
					'library' => 'solid',
				],
				'condition' => array(
					'i_type' => 'fontawesome',
					'show_icon' => 'yes'
				),
			]
		);

		$this->add_control(
			'i_icon_image',
			[
				'label' => __( 'Image', 'hub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'i_type' => 'image',
					'show_icon' => 'yes'
				),
			]
		);

		// active icon
		$this->add_control(
			'active_type',
			[
				'label' => __( 'Icon library', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fontawesome',
				'options' => [
					'fontawesome'  => __( 'Icon Library', 'hub-elementor-addons' ),
					'image' => __( 'Image', 'hub-elementor-addons' ),
				],
				'condition' => array(
					'show_icon' => 'yes'
				)
			]
		);

		$this->add_control(
			'active_icon_fontawesome',
			[
				'label' => __( 'Icon', 'hub-elementor-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fa fa-chevron-up',
					'library' => 'solid',
				],
				'condition' => array(
					'active_type' => 'fontawesome',
					'show_icon' => 'yes'
				),
			]
		);

		$this->add_control(
			'active_icon_image',
			[
				'label' => __( 'Image', 'hub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'active_type' => 'image',
					'show_icon' => 'yes'
					),
			]
		);

		$this->end_controls_section();

		// Style Tab
		$this->start_controls_section(
			'items_style_section',
			[
				'label' => __( 'Items', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'accordion_item_border',
				'selector' => '{{WRAPPER}} .accordion-item'
			]
		);

		$this->add_control(
			'accordion_item_border_radius',
			[
				'label' => __( 'Border radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'bottom_margin',
			[
				'label' => __( 'Bottom space', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label' => __( 'Box shadow', 'hub-elementor-addons' ),
				'name' => 'accordion_item_boxshadow',
				'selector' => '{{WRAPPER}} .accordion-item',
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'accordion_item_backdrop_filter',
				'selector' => '{{WRAPPER}} .accordion-item',
				'label' => esc_html__( 'CSS Backdrop Filters', 'hub-elementor-addons' ),
				'fields_options' => [
					'blur' => [
						'selectors' => [
							'{{SELECTOR}}' => '-webkit-backdrop-filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg );backdrop-filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg )',
						],
					]
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'heading_style_section',
			[
				'label' => __( 'Heading', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'label' => __( 'Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .accordion-title a',
			]
		);

		$this->start_controls_tabs(
			'accordion_heading_tabs'
		);

		// Headings normal state
		$this->start_controls_tab(
			'accordion_heading_normal_tab',
			[
				'label' => __( 'Normal', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'heading_bg_color',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .accordion-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'accordion_heading_border',
				'selector' => '{{WRAPPER}} .accordion-title a',
			]
		);

		$this->add_responsive_control(
			'accordion_heading_border_radius',
			[
				'label' => __( 'Border radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_heading_padding',
			[
				'label' => __( 'Padding', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label' => __( 'Box shadow', 'hub-elementor-addons' ),
				'name' => 'accordion_heading_boxshadow',
				'selector' => '{{WRAPPER}} .accordion-title a',
			]
		);

		$this->add_control(
			'accordion_heading_dep_warn',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( 'All below options are deprecated.', 'hub-elementor-addons' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-title a, {{WRAPPER}} .accordion-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		// Headings active state
		$this->start_controls_tab(
			'accordion_heading_active_tab',
			[
				'label' => __( 'Active', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'active_heading_color',
			[
				'label' => __( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-item.active .accordion-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'heading_active_bg_color',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .accordion-item.active .accordion-title a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'accordion_active_heading_border',
				'selector' => '{{WRAPPER}} .accordion-item.active .accordion-title a',
			]
		);

		$this->add_control(
			'accordion_active_heading_border_radius',
			[
				'label' => __( 'Border radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-item.active .accordion-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'label' => __( 'Box shadow', 'hub-elementor-addons' ),
				'name' => 'accordion_active_heading_boxshadow',
				'selector' => '{{WRAPPER}} .accordion-item.active .accordion-title a',
			]
		);

		$this->add_control(
			'accordion_active_heading_dep_warn',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( 'All below options are deprecated.', 'hub-elementor-addons' ) ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'active_border_color',
			[
				'label' => __( 'Border color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-item.active .accordion-title a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style_section',
			[
				'label' => __( 'Content', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => __( 'Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .accordion-content',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_bg',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .accordion-content'
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Expander color section
		$this->start_controls_section(
			'expander_style_section',
			[
				'label' => __( 'Expander', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_icon' => 'yes',
				)
			]
		);

		$this->add_responsive_control(
			'expander_size_slider',
			[
				'label' => __( 'Icon size', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-expander' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'expander_shape_size',
			[
				'label' => __( 'Shape size', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .accordion-expander' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; display: flex; flex-shrink: 0; align-items: center; justify-content: center; padding: 0;',
				]
			]
		);

		$this->start_controls_tabs(
			'accordion_expander_tabs'
		);

		// Expanders normal state
		$this->start_controls_tab(
			'accordion_expander_normal_tab',
			[
				'label' => __( 'Normal', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'exp_color',
			[
				'label' => __( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-expander' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'expander_bg',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .accordion-expander',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'expander_border',
				'label' => __( 'Border', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .accordion-expander',
			]
		);

		$this->add_responsive_control(
			'expander_border_radius',
			[
				'label' => __( 'Border radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .accordion-expander' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_tab();

		// Expanders active state
		$this->start_controls_tab(
			'accordion_expander_active_tab',
			[
				'label' => __( 'Active', 'hub-elementor-addons' ),
			]
		);

		$this->add_control(
			'active_exp_color',
			[
				'label' => __( 'Expander active color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .accordion-item.active .accordion-expander' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'expander_active_bg',
				'label' => __( 'Background', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .accordion-item.active .accordion-expander',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'expander_active_border',
				'label' => __( 'Border', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .accordion-item.active .accordion-expander',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$settings['size'] = 'accordion-'.$settings['size'];
		$side_space = '';
		$transparent_bg = '';
		$active_has_fill = '';

		if (
			(
				! empty( $settings['heading_bg_color_background'] ) ||
				! empty( $settings['heading_active_bg_color_background'] )
			) &&
			(
				(
					! empty( $settings['heading_bg_color_color'] ) ||
					! empty( $settings['heading_active_bg_color_color'] )
				) ||
				(
					isset( $settings['__globals__'] ) &&
					(
						! empty( $settings['__globals__']['heading_bg_color_color'] ) ||
						! empty( $settings['__globals__']['heading_active_bg_color_color'] )
					)
				)
			)
		) {
			$active_has_fill = 'accordion-active-has-fill';
		}

		if (
			$settings['items_shadow'] === 'yes' ||
			! empty( $active_has_fill )
		) {
			$transparent_bg = 'accordion-title-bg-transparent';
		}

		if (
			$settings['active_style'] === 'yes' ||
			$settings['items_shadow'] === 'yes' ||
			! empty( $active_has_fill )
		) {
			$side_space = 'accordion-side-spacing';
		}

		if($settings['items_shadow'] === 'yes') $settings['items_shadow'] = 'accordion-body-shadow';
		if($settings['heading_shadow'] === 'yes') $settings['heading_shadow'] = 'accordion-heading-has-shadow';
		if($settings['active_style'] === 'yes') $settings['active_style'] = 'accordion-active-has-shadow';


		$wrapper_class = array(
			'accordion',
			$settings['borders'],
			$settings['size'],
			$side_space,
			$transparent_bg,
			$settings['border_round'],
			$settings['body_border_round'],
			$settings['expander_position'],
			// $settings['expander_size'],
			$settings['items_shadow'],
			$settings['heading_shadow'],
			$settings['active_style'],
			(!empty($settings['content_bg'])) ? 'accordion-content-has-fill' : '',
			$active_has_fill,
		);

		$settings['active_tab'] = ! empty( $settings['active_tab'] ) ? intval( $settings['active_tab'] ) - 1 : 0;
		$parent = uniqid( 'accordion-' );

		?>

		<div class="<?php echo ld_helper()->sanitize_html_classes( $wrapper_class ); ?>" id="<?php echo esc_attr($parent) ?>" role="tablist" aria-multiselectable="true">
			<?php $id_int = substr( $this->get_id_int(), 0, 3 ); ?>
			<?php foreach (  $settings['items'] as $i => $item ) : ?>
			<?php
			$tab_count = $i + 1;
			$tab_content_setting_key = $this->get_repeater_setting_key( 'content', 'items', $i );
			$this->add_render_attribute( $tab_content_setting_key, [
				'class' => [ 'accordion-content' ],
			] );

			$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );

			$in = $i == $settings['active_tab'] ? ' show' : '';
			$active = $i == $settings['active_tab'] ? ' active' : '';
			$expanded = $i == $settings['active_tab'] ? 'true' : 'false';
			$body_roundness = '';
			$collapsed = $i == $settings['active_tab'] ? '' : 'collapsed';

			?>
			<div class="accordion-item panel <?php echo $active; ?>">

				<div class="accordion-heading" role="tab" id="<?php echo 'heading-'.$id_int . $tab_count ?>">
				<?php printf( '<%1$s class="accordion-title %2$s tabindex="-1">', $settings['title_tag'], $settings['use_inheritance'] === 'true' ? $settings['tag_to_inherite'] : '' ); ?>
					<a class="<?php echo $collapsed ?>" role="button" data-toggle="collapse" data-bs-toggle="collapse"
                    tabindex="0"
					data-parent="#<?php echo $parent ?>" data-bs-parent="#<?php echo $parent ?>" href="#<?php echo 'collapse-'.$id_int . $tab_count ?>" data-target="#<?php echo 'collapse-'.$id_int . $tab_count ?>" data-bs-target="#<?php echo 'collapse-'.$id_int . $tab_count ?>"
					aria-expanded="<?php echo $expanded ?>" aria-controls="<?php echo 'collapse-'.$id_int . $tab_count; ?>">
						<?php if ( $settings['show_icon'] === 'yes' && $settings['expander_position'] === 'accordion-expander-left' ) : ?>
							<?php echo $this->get_expander(); ?>
						<?php endif; ?>
						<span class="accordion-title-txt"><?php echo $item['title']; ?></span>
						<?php if ( $settings['show_icon'] === 'yes' && $settings['expander_position'] === '' ) : ?>
							<?php echo $this->get_expander(); ?>
						<?php endif; ?>
					</a>
				<?php printf( '</%s>', $settings['title_tag'] ); ?>
				</div>

				<div id="<?php echo 'collapse-'.$id_int . $tab_count ?>" class="accordion-collapse collapse <?php echo $in ?>" data-bs-parent="#<?php echo $parent ?>" role="tabpanel" aria-labelledby="<?php echo 'heading-'.$id_int . $tab_count; ?>">

				<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
					<?php echo $item['content_type'] === 'template' ? \Elementor\Plugin::instance()->frontend->get_builder_content( $item[ 'template' ], true ) : $item['content']; ?>
				</div>

				</div>

			</div>
			<?php endforeach; ?>
			<?php

			// FAQ Schema
			if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
				$json = [
					'@context' => 'https://schema.org',
					'@type' => 'FAQPage',
					'mainEntity' => [],
				];

				foreach ( $settings['items'] as $index => $item ) {
					$json['mainEntity'][] = [
						'@type' => 'Question',
						'name' => wp_strip_all_tags( $item['title'] ),
						'acceptedAnswer' => [
							'@type' => 'Answer',
							'text' => preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strip_tags( $item['content'] )),
						],
					];
				}
				?>
				<script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script>
			<?php } ?>

		</div>

		<?php

	}

	protected function content_template() {
		?>

		<#

		let side_space = '';
		let transparent_bg = '';
		let active_has_fill = '';

		if (
			(
				settings.heading_bg_color_background !== '' ||
				settings.heading_active_bg_color_background !== ''
			) &&
			(
				(
					settings.heading_bg_color_color !== '' ||
					settings.heading_active_bg_color_color !== ''
				) ||
				(
					settings.__globals__ &&
					(
						settings.__globals__.heading_bg_color_color !== '' ||
						settings.__globals__.heading_active_bg_color_color !== ''
					)
				)
			)
		) {
			active_has_fill = 'accordion-active-has-fill';
		}

		if (
			settings.items_shadow === 'yes' ||
			active_has_fill !== ''
		) {
			transparent_bg = 'accordion-title-bg-transparent';
		}

		if (
			settings.active_style === 'yes' ||
			settings.items_shadow === 'yes' ||
			active_has_fill !== ''
		) {
			side_space = 'accordion-side-spacing';
		}

		const wrapper_class = [
			'accordion',
			settings.borders,
			'accordion-'+settings.size,
			side_space,
			transparent_bg,
			settings.border_round,
			settings.body_border_round,
			settings.expander_position,
			// settings.expander_size,
			(settings.items_shadow === 'yes' ? 'accordion-body-shadow' : ''),
			(settings.heading_shadow === 'yes' ? 'accordion-heading-has-shadow' : ''),
			(settings.active_style === 'yes' ? 'accordion-active-has-shadow' : ''),
			(settings.content_bg ? 'accordion-content-has-fill' : ''),
			active_has_fill
		].filter(classname => classname !== '');

		const parent = 'accordion-' + Date.now();
		view.addRenderAttribute(
			'wrapper',
			{
				'class': [ wrapper_class.join(' ') ],
				'id': parent,
				'role': 'tablist',
				'aria-multiselectable': 'true',
			}
		);

		settings.active_tab = settings.active_tab ? settings.active_tab - 1 : 0;

		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
		<#
			if ( settings.items ) {

				var tabindex = view.getIDInt().toString().substr( 0, 3 );

				_.each( settings.items, function( item, i ) {

					function get_expander() {

						return `<span class="accordion-expander">
							<i class="${settings.i_icon_fontawesome.value}"></i>
							<i class="${settings.active_icon_fontawesome.value}"></i>
						</span>`;

					}

					var tabCount = i + 1,
						__in = i == settings.active_tab ? ' show' : '',
						active = i == settings.active_tab ? ' active' : '',
						expanded = i == settings.active_tab ? 'true' : 'false',
						collapsed = i == settings.active_tab ? '' : 'collapsed',
						tabTitleKey = view.getRepeaterSettingKey( 'title', 'items', i ),
						tabContentKey = view.getRepeaterSettingKey( 'content', 'items', i );

					view.addRenderAttribute( tabTitleKey, {
						'class': collapsed,
						'role': 'button',
						'data-toggle': 'collapse',
						'data-bs-toggle': 'collapse',
						'data-parent': '#' + parent,
						'data-bs-parent': '#' + parent,
						'href': '#collapse-' + tabindex + tabCount,
						'aria-expanded': expanded,
						'aria-controls': 'collapse-' + tabindex + tabCount
					} );

					view.addRenderAttribute( tabContentKey, {
						'class': [ 'accordion-content' ],
					} );

					view.addInlineEditingAttributes( tabContentKey, 'advanced' );

				#>

				<div class="accordion-item panel{{{ active }}}">
					<div class="accordion-heading" role="tab" id="heading-{{{ tabindex + tabCount }}}">
					<{{{settings.title_tag}}} class="accordion-title {{{settings.use_inheritance === 'true' ? settings.tag_to_inherite : ''}}}">
						<a {{{ view.getRenderAttributeString( tabTitleKey ) }}} >
							<# if ( settings.show_icon && settings.expander_position === 'accordion-expander-left' ) { #>
								{{{get_expander()}}}
							<# } #>
							<span class="accordion-title-txt">{{{ item.title }}}</span>
							<# if ( settings.show_icon && settings.expander_position === '' ) { #>
								{{{get_expander()}}}
							<# } #>
						</a>
					</{{{settings.title_tag}}}>
					</div>

					<div id="collapse-{{{ tabindex + tabCount }}}" class="accordion-collapse collapse {{{ __in }}}" data-bs-parent="#{{{ parent }}}" role="tabpanel" aria-labelledby="heading-{{{ tabindex + tabCount }}}">

					<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.content_type === 'text' ? item.content : "Template " + item.template + " will be display here!" }}}</div>

					</div>
				</div>

				<#
				} );
			}
		#>
		</div>
		<?php
	}

	protected function get_expander() {

		$settings = $this->get_settings_for_display();
		$normal_icon = $settings['i_icon_fontawesome']['value'];
		$active_icon = $settings['active_icon_fontawesome']['value'];

		return sprintf('<span class="accordion-expander"><i class="%1$s"></i><i class="%2$s"></i></span>', $normal_icon, $active_icon);

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Accordion() );