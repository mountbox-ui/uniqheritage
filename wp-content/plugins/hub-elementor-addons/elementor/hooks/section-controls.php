<?php

use Elementor\Element_Base;
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
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;

defined('ABSPATH') || die();

class Hub_Elementor_Custom_Controls {

    public static $shape_cutout_the_content = array();

	public static function init() {
        //add_action( 'elementor/element/common/_section_style/after_section_end', [ __CLASS__, 'add_controls_section' ], 1 );
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ __CLASS__, 'add_controls_section' ], 1 );
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ __CLASS__, 'add_controls_section' ], 1 );
        add_action( 'elementor/element/container/section_layout/after_section_end', [ __CLASS__, 'add_controls_section' ], 1 );
		add_action( 'elementor/frontend/before_render', [ __CLASS__, 'before_section_render' ], 1 );

        // Additional Shape Colors
        add_action( 'elementor/element/section/section_shape_divider/before_section_end', [ __CLASS__, 'additional_shape_colors' ], 1 );
        add_action( 'elementor/element/container/section_shape_divider/before_section_end', [ __CLASS__, 'additional_shape_colors' ], 1 );

        // Shape Cutout Render
        add_action( 'elementor/frontend/container/after_render', [ __CLASS__, 'shape_cutout_render' ] );
        add_action( 'elementor/frontend/widget/before_render', [ __CLASS__, 'shape_cutout_render' ] );
        add_action( 'the_content', [ __CLASS__, 'shape_cutout_the_content' ] );
        add_filter( 'elementor/container/print_template', [ __CLASS__, 'shape_cutout_editor_render' ], 10, 2 );
        add_filter( 'elementor/widget/print_template', [ __CLASS__, 'shape_cutout_editor_render' ], 10, 2 );

        // Liquid Animations
        add_action( 'elementor/element/after_section_end', function( $element, $section_id ) {

            if (
                ( $element->get_name() === 'container' && 'section_layout' === $section_id) ||
                'section_advanced' === $section_id ||
                '_section_style' === $section_id
            ) {

                $element->start_controls_section(
                    'lqd_custom_animations',
                    [
                        'label' => __( 'Animations & Parallax', 'Hub Elementor Addons' ),
                        'tab' => Controls_Manager::TAB_ADVANCED,
                    ]
                );

                ld_el_parallax( $element ); // call parallax options
                ld_el_content_animation( $element ); // call content animation options

                $element->end_controls_section();
            }
        }, 10, 2 );

        // Extra Styling
        add_action( 'elementor/element/after_section_end', function( $element, $section_id ) {

            if (
                ( $element->get_name() === 'container' && 'section_layout' === $section_id) ||
                'section_advanced' === $section_id ||
                '_section_style' === $section_id
            ) {

                $element->start_controls_section(
                    'lqd_extra_styling',
                    [
                        'label' => __( 'Extra Styling', 'hub elementor addons' ),
                        'tab' => Controls_Manager::TAB_ADVANCED,
                    ]
                );

                $element->add_group_control(
                    Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'lqd_extra_styling_css_filters',
                        'selector' => '{{WRAPPER}}',
                    ]
                );

                $element->add_group_control(
                    Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'lqd_extra_styling_css_backdrop_filters',
                        'selector' => '{{WRAPPER}}.e-con, {{WRAPPER}} > .elementor-widget-container',
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

                $element->add_control(
                    'lqd_extra_styling_opacity_toggle',
                    [
                        'label' => esc_html__( 'Opacity', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::POPOVER_TOGGLE,
                    ]
                );

                $element->start_popover();

                $element->add_responsive_control(
                    'lqd_extra_styling_opacity_normal',
                    [
                        'label' => esc_html__( 'Normal', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1,
                                'step' => 0.1,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}}.e-con, {{WRAPPER}} > .elementor-widget-container' => 'opacity: {{SIZE}};',
                        ],
                        'condition' => [
                            'lqd_extra_styling_opacity_toggle' => 'yes'
                        ]
                    ]
                );

                $element->add_responsive_control(
                    'lqd_extra_styling_opacity_hover',
                    [
                        'label' => esc_html__( 'Hover', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1,
                                'step' => 0.1,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}}.e-con:hover, {{WRAPPER}}:hover > .elementor-widget-container' => 'opacity: {{SIZE}};',
                        ],
                        'condition' => [
                            'lqd_extra_styling_opacity_toggle' => 'yes'
                        ]
                    ]
                );

                $element->end_popover();

                $element->add_control(
                    'blend_mode',
                    [
                        'label' => esc_html__( 'Blend mode', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            '' => esc_html__( 'Normal', 'hub-elementor-addons' ),
                            'multiply' => 'Multiply',
                            'screen' => 'Screen',
                            'overlay' => 'Overlay',
                            'darken' => 'Darken',
                            'lighten' => 'Lighten',
                            'color-dodge' => 'Color Dodge',
                            'hard-light' => 'Hard light',
                            'saturation' => 'Saturation',
                            'color' => 'Color',
                            'difference' => 'Difference',
                            'exclusion' => 'Exclusion',
                            'hue' => 'Hue',
                            'luminosity' => 'Luminosity',
                        ],
                        'selectors' => [
                            '{{WRAPPER}}' => 'mix-blend-mode: {{VALUE}}',
                        ],
                    ]
                );

                $element->add_control(
                    'lqd_sticky_header_extra_styling_heading',
                    [
                        'label' => esc_html__( 'Sticky Header Styling', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before'
                    ]
                );

                $element->add_group_control(
                    Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'lqd_sticky_header_extra_styling_css_filters',
                        'selector' => '.is-stuck {{WRAPPER}}',
                    ]
                );

                $element->add_group_control(
                    Group_Control_Css_Filter::get_type(),
                    [
                        'name' => 'lqd_sticky_header_extra_styling_css_backdrop_filters',
                        'selector' => '.is-stuck {{WRAPPER}}.e-con, .is-stuck {{WRAPPER}} > .elementor-widget-container',
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

                $element->add_control(
                    'lqd_sticky_header_extra_styling_opacity_toggle',
                    [
                        'label' => esc_html__( 'Opacity', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::POPOVER_TOGGLE,
                    ]
                );

                $element->start_popover();

                $element->add_responsive_control(
                    'lqd_sticky_header_extra_styling_opacity_normal',
                    [
                        'label' => esc_html__( 'Normal', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1,
                                'step' => 0.1,
                            ],
                        ],
                        'selectors' => [
                            '.is-stuck {{WRAPPER}}.e-con, .is-stuck {{WRAPPER}} > .elementor-widget-container' => 'opacity: {{SIZE}};',
                        ],
                        'condition' => [
                            'lqd_sticky_header_extra_styling_opacity_toggle' => 'yes'
                        ]
                    ]
                );

                $element->add_responsive_control(
                    'lqd_sticky_header_extra_styling_opacity_hover',
                    [
                        'label' => esc_html__( 'Hover', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1,
                                'step' => 0.1,
                            ],
                        ],
                        'selectors' => [
                            '.is-stuck {{WRAPPER}}.e-con:hover, .is-stuck {{WRAPPER}}:hover > .elementor-widget-container' => 'opacity: {{SIZE}};',
                        ],
                        'condition' => [
                            'lqd_extra_styling_opacity_toggle' => 'yes'
                        ]
                    ]
                );

                $element->end_popover();

                $element->add_control(
                    'lqd_sticky_header_blend_mode',
                    [
                        'label' => esc_html__( 'Blend mode', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            '' => esc_html__( 'Normal', 'hub-elementor-addons' ),
                            'multiply' => 'Multiply',
                            'screen' => 'Screen',
                            'overlay' => 'Overlay',
                            'darken' => 'Darken',
                            'lighten' => 'Lighten',
                            'color-dodge' => 'Color Dodge',
                            'hard-light' => 'Hard light',
                            'saturation' => 'Saturation',
                            'color' => 'Color',
                            'difference' => 'Difference',
                            'exclusion' => 'Exclusion',
                            'hue' => 'Hue',
                            'luminosity' => 'Luminosity',
                        ],
                        'selectors' => [
                            '.is-stuck {{WRAPPER}}' => 'mix-blend-mode: {{VALUE}}',
                        ],
                    ]
                );

                $element->end_controls_section();
            }
        }, 10, 2 );

		// Shape Cutout
        add_action( 'elementor/element/after_section_end', function( $element, $section_id ) {

			if (
                $element->get_name() === 'container' &&
				(
					'section_layout' === $section_id ||
					'section_advanced' === $section_id ||
					'_section_style' === $section_id
				)
            ) {

				$element->start_controls_section(
                    'lqd_shape_cutout',
                    [
                        'label' => __( 'Shape Cutout', 'hub elementor addons' ),
                        'tab' => Controls_Manager::TAB_ADVANCED,
                    ]
                );

				$element->add_control(
					'lqd_shape_cutout_style',
					[
						'label' => esc_html__( 'Style', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => esc_html__( 'None', 'hub-elementor-addons' ),
							'style-1' => esc_html__( 'Style 1', 'hub-elementor-addons' ),
						],
						'default' => '',
						'render_type' => 'template'
					]
				);

				$element->add_control(
					'lqd_shape_cutout_placement',
					[
						'label' => esc_html__( 'Placement', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'tl' => esc_html__( 'Top Left', 'hub-elementor-addons' ),
							'tr' => esc_html__( 'Top Right', 'hub-elementor-addons' ),
							'br' => esc_html__( 'Bottom Right', 'hub-elementor-addons' ),
							'bl' => esc_html__( 'Bottom Left', 'hub-elementor-addons' ),
						],
						'default' => 'br',
						'condition' => [
							'lqd_shape_cutout_style' => 'style-1'
						],
						'render_type' => 'template'
					]
				);

				$element->add_responsive_control(
					'lqd_shape_cutout_width',
					[
						'label' => esc_html__( 'Width', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'.lqd-cutout.lqd-cutout-{{ID}}' => '--shape-w: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'lqd_shape_cutout_style' => 'style-1'
						]
					]
				);

				$element->add_responsive_control(
					'lqd_shape_cutout_height',
					[
						'label' => esc_html__( 'Height', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'.lqd-cutout.lqd-cutout-{{ID}}' => '--shape-h: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'lqd_shape_cutout_style' => 'style-1'
						]
					]
				);

				$element->add_responsive_control(
					'lqd_shape_cutout_roundness',
					[
						'label' => esc_html__( 'Roundness', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1000,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'.lqd-cutout.lqd-cutout-{{ID}}' => '--shape-roundness: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'lqd_shape_cutout_style' => 'style-1'
						]
					]
				);

				$element->add_responsive_control(
					'lqd_shape_cutout_skew',
					[
						'label' => esc_html__( 'Skew', 'hub-elementor-addons' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => -20,
								'max' => 0,
								'step' => 1,
							],
						],
						'selectors' => [
							'.lqd-cutout.lqd-cutout-{{ID}}' => '--shape-skew: {{SIZE}}deg;',
						],
						'condition' => [
							'lqd_shape_cutout_style' => 'style-1'
						]
					]
				);

				$element->end_controls_section();

			}

		}, 10, 2);

        // Custom CSS
        add_action( 'elementor/element/parse_css', function( $post_css, $element ){

            if ( $post_css instanceof Dynamic_CSS ) {
                return;
            }

            $element_settings = $element->get_settings();

            if ( empty( $element_settings['lqd_custom_css'] ) ) {
                return;
            }

            $css = trim( $element_settings['lqd_custom_css'] );

            if ( empty( $css ) ) {
                return;
            }

            $css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );

            $post_css->get_stylesheet()->add_raw_css( $css );

        }, 10, 2 );

        add_action( 'elementor/element/after_section_end', function( $element, $section_id ) {

            if (
                ( $element->get_name() === 'container' && 'section_layout' === $section_id) ||
                'section_advanced' === $section_id ||
                '_section_style' === $section_id
            ) {

                $element->start_controls_section(
                    'lqd_custom_css_section',
                    [
                        'label' => __( 'Custom CSS', 'hub-elementor-addons' ),
                        'tab' => Controls_Manager::TAB_ADVANCED,
                    ]
                );

                $element->add_control(
                    'lqd_custom_css',
                    [
                        'type' => Controls_Manager::CODE,
                        'language' => 'css',
                        'render_type' => 'ui',
                        'label' => method_exists( liquid_helper(), 'hub_ai_btn' ) ? liquid_helper()->hub_ai_btn() : '',
                    ]
                );

                $element->add_control(
                    'lqd_custom_css_desc',
                    [
                        'raw' => sprintf(
                            esc_html__( 'Use "selector" to target wrapper element.%1$sselector {your css code}', 'hub-elementor-addons' ),
                            '<br><br>'
                        ),
                        'type' => Controls_Manager::RAW_HTML,
                        'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                    ]
                );

                $element->end_controls_section();
            }
        }, 10, 20 );

        // Iconbox Scale
        add_action( 'elementor/frontend/widget/before_render', function ( Element_Base $element ) {
            if ( ! $element->get_settings( 'enable_scale_animation' ) ) {
                return;
            }

            $element->add_render_attribute( '_wrapper', [
                'class' => 'lqd-iconbox-scale'
            ] );

        } );

        // Wrap Columns
        add_action( 'elementor/element/section/section_layout/before_section_end', function( $control_stack ) {

            $control_stack->start_injection([
                'at' => 'before',
                'of' => 'gap'
            ]);

            $control_stack->add_control(
                'liquid_columns_wrap',
                [
                    'label' => __( 'Wrap Columns', 'hub-elementor-addons' ),
                    'description' => __( 'Check this option if you want to wrap columns in multiple rows on desktop. Change column width to see the effect.', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'selectors' => [
                        '{{WRAPPER}} > .elementor-container' => 'flex-wrap: wrap;',
                    ],
                ]
            );

            $control_stack->end_injection();

        });

        // Reduce Liquid Text widget dom
        add_filter( 'elementor/widget/render_content', function( $widget_content, $widget ) {

            if ( 'hub_fancy_heading' === $widget->get_name() ) { // check the widget

                $settings = $widget->get_settings();
                $type = $settings['highlight_type'];

                // define all classnames for highlight_type
                $types = [
                    'lqd-highlight-custom-underline' => 'lqd-highlight-brush-svg lqd-highlight-brush-svg-1',
                    'lqd-highlight-custom-underline lqd-highlight-custom-underline-alt' => [ 'lqd-highlight-pen', 'lqd-highlight-brush-svg lqd-highlight-brush-svg-2' ],
                    'lqd-highlight-custom lqd-highlight-custom-3' => 'lqd-highlight-brush-svg lqd-highlight-brush-svg-3',
                    'lqd-highlight-custom lqd-highlight-custom-4' => 'lqd-highlight-brush-svg lqd-highlight-brush-svg-4',
                    'lqd-highlight-custom lqd-highlight-custom-5' => 'lqd-highlight-brush-svg lqd-highlight-brush-svg-5'
                ];

                if ( !empty( $type ) ){ // check underline style

                    unset($types[$type]); // remove current style in array
                    foreach ( $types as $key => $value ){
                        if ( is_array( $value ) ){
                            foreach( $value as $v ){
                                $widget_content = preg_replace( '#<svg class="'. $v .'"(.*?)</svg>#', '', $widget_content );
                            }
                        } else {
                            $widget_content = preg_replace( '#<svg class="'. $value .'"(.*?)</svg>#', '', $widget_content );
                        }
                    }

                }

            }

            return $widget_content;

        }, 10, 2 );

	}

    public static function additional_shape_colors( $control_stack ) {

        // Bottom
        $control_stack->start_injection([
            'at' => 'before',
            'of' => 'shape_divider_bottom_width'
        ]);

        $control_stack->add_control(
            'lqd_custom_shape_bottom_color2',
            [
                'label' => __( 'Color 2', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-bottom .elementor-shape-fill:nth-child(2)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_bottom!' => '',
                ],
            ]
        );

        $control_stack->add_control(
            'lqd_custom_shape_bottom_color3',
            [
                'label' => __( 'Color 3', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-bottom .elementor-shape-fill:nth-child(3)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_bottom!' => '',
                ],
            ]
        );

        $control_stack->add_control(
            'lqd_custom_shape_bottom_color4',
            [
                'label' => __( 'Color 4', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-bottom .elementor-shape-fill:nth-child(4)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_bottom!' => '',
                ],
            ]
        );

        $control_stack->end_injection();

        // Top
        $control_stack->start_injection([
            'at' => 'before',
            'of' => 'shape_divider_top_width'
        ]);

        $control_stack->add_control(
            'lqd_custom_shape_top_color2',
            [
                'label' => __( 'Color 2', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-top .elementor-shape-fill:nth-child(2)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_top!' => '',
                ],
            ]
        );

        $control_stack->add_control(
            'lqd_custom_shape_top_color3',
            [
                'label' => __( 'Color 3', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-top .elementor-shape-fill:nth-child(3)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_top!' => '',
                ],
            ]
        );

        $control_stack->add_control(
            'lqd_custom_shape_top_color4',
            [
                'label' => __( 'Color 4', 'hub-elementor-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-shape-top .elementor-shape-fill:nth-child(4)' => 'fill: {{VALUE}}; fill-opacity: 1 !important; opacity: 1 !important;',
                ],
                'condition' => [
                    'shape_divider_top!' => '',
                ],
            ]
        );

        $control_stack->end_injection();

        // Bottom shape animation
        $control_stack->start_injection([
            'at' => 'after',
            'of' => 'shape_divider_bottom_above_content'
        ]);

        $control_stack->end_injection();

        // Top shape animation
        $control_stack->start_injection([
            'at' => 'after',
            'of' => 'shape_divider_top_above_content'
        ]);

        $control_stack->end_injection();

    }

	public static function add_controls_section( Element_Base $element) {

        $is_container = 'container' === $element->get_name();
        $is_section = 'section' === $element->get_name();

        if ( $is_container || $is_section ) {

            $element->start_controls_section(
                'liquid_custom_row_heading',
                [
                    'label' => __( 'Section Options', 'hub-elementor-addons' ),
                    'tab'   => Controls_Manager::TAB_LAYOUT,
                ]
            );

            if ( get_post_type( get_the_ID()) !== 'liquid-header' ) {

                $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
			    $page_settings_model = $page_settings_manager->get_model( get_the_ID() );

                $element->add_control(
                    'liquid_luminosity_data_attr',
                    [
                        'label' => __( 'Luminosity', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'default-auto' => [
                                'title' => __( 'Automatic', 'hub-elementor-addons' ),
                                'icon' => 'fa fa-adjust',
                            ],
                            'dark' => [
                                'title' => __( 'Dark', 'hub-elementor-addons' ),
                                'icon' => 'fa fa-moon',
                            ],
                            'light' => [
                                'title' => __( 'Light', 'hub-elementor-addons' ),
                                'icon' => 'fa fa-sun',
                            ],
                        ],
                        'default' => 'default-auto',
                        'toggle' => false,
                    ]
                );

                $element->add_control(
                    'lqd_section_scroll',
                    [
                        'label' => __( 'Section Scroll?', 'hub-elementor-addons' ),
                        'description' => __( 'Enable this option to make the section scrollable.', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'yes',
                        'default' => '',
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'custom_cursor_on_hover',
                    [
                        'label' => __( 'Custom cursor on hover', 'hub-elementor-addons' ),
                        'description' => __( 'For it to work, enable the following from: Theme Options > Extras > Custom Cursor', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'yes',
                        'default' => '',
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'custom_cursor_color',
                    [
                        'label' => __( 'Custom cursor color', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} > .lqd-extra-cursor' => 'background: {{VALUE}};',
                        ],
                        'condition' => [
                            'custom_cursor_on_hover' => 'yes',
                        ],
                    ]
                );

                $page_enable_stack = $page_settings_model->get_settings( 'page_enable_stack' );
                $page_enable_stack = $page_enable_stack === 'on' ? '' : array( 'lqd_disable_option' => 'on' );

                $element->add_control(
                    'section_data_tooltip',
                    [
                        'label' => __( 'Section tooltip', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::TEXT,
                        'description' => __( 'Add title as tooltip on stack page', 'hub-elementor-addons' ),
                        'render_type' => 'none',
                        'condition' => $page_enable_stack
                    ]
                );

                $element->add_control(
                    'lqd_sticky_row',
                    [
                        'label' => __( 'Sticky', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'description' => __( 'Note: If Flexbox Container enabled, you need to set the Align Items option to Start from parent container options for this to work.', 'hub-elementor-addons' ),
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'lqd-css-sticky',
                        'render_type' => 'none',
                        'default' => '',
                        'separator' => 'before',
                    ]
                );

                $element->add_control(
                    'lqd_sticky_row_anchor',
                    [
                        'label' => __( 'Sticky anchor', 'hub-elementor-addons' ),
                        'description' => __( 'Choose row sticking from top or bottom. If you choose <b>bottom</b>, you may want to set a higher z-index for top sections.', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'top' => [
                                'title' => __( 'Top', 'hub-elementor-addons' ),
                                'icon' => 'eicon-v-align-top',
                            ],
                            'bottom' => [
                                'title' => __( 'Bottom', 'hub-elementor-addons' ),
                                'icon' => 'eicon-v-align-bottom',
                            ],
                        ],
                        'default' => 'top',
                        'toggle' => false,
                        'condition' => [
                            'lqd_sticky_row' => 'lqd-css-sticky'
                        ],
                    ]
                );

                $element->add_control(
                    'lqd_sticky_row_offset',
                    [
                        'label' => __( 'Sticky offset', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => '0px',
                        'placeholder' => __( 'ex. 10px', 'hub-elementor-addons' ),
                        'condition' => [
                            'lqd_sticky_row' => 'lqd-css-sticky'
                        ],
                    ]
                );

            }

            // Header section controls
            if ( get_post_type( get_the_ID()) === 'liquid-header' ) {

                $element->add_control(
                    'hide_on_sticky',
                    [
                        'label' => __( 'Hide On Sticky Header?', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'lqd-hide-onstuck',
                        'default' => '',
                        'condition' => array(
                            'show_on_sticky' => '',
                        ),
                    ]
                );

                $element->add_control(
                    'show_on_sticky',
                    [
                        'label' => __( 'Show Only On Sticky Header?', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'lqd-show-onstuck',
                        'default' => '',
                        'condition' => array(
                            'hide_on_sticky' => '',
                        ),
                    ]
                );

                $element->add_control(
                    'sticky_bar',
                    [
                        'label' => __( 'Vertical Bar?', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::SWITCHER,
                        'label_on' => __( 'On', 'hub-elementor-addons' ),
                        'label_off' => __( 'Off', 'hub-elementor-addons' ),
                        'return_value' => 'yes',
                        'default' => '',
                    ]
                );

                $element->add_control(
                    'stickybar_placement',
                    [
                        'label' => __( 'Vertical Bar position', 'hub-elementor-addons' ),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'lqd-stickybar-left' => [
                                'title' => __( 'Left', 'hub-elementor-addons' ),
                                'icon' => 'eicon-arrow-left',
                            ],
                            'lqd-stickybar-right' => [
                                'title' => __( 'Right', 'hub-elementor-addons' ),
                                'icon' => 'eicon-arrow-right',
                            ],
                        ],
                        'default' => 'lqd-stickybar-left',
                        'toggle' => false,
                        'condition' => [
                            'sticky_bar' => 'yes'
                        ],
                    ]
                );

            }

            $element->start_injection(
                array(
                    'type' => 'section',
                    'at' => 'end',
                    'of' => 'section_background',
                )
            );

            $element->add_control(
                'lqd_sticky_header_bg_heading',
                [
                    'label' => esc_html__( 'Background on sticky header', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $element->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'lqd_sticky_header_bg',
                    'label' => __( 'Background on sticky header', 'hub-elementor-addons' ),
                    'types' => [ 'classic', 'gradient' ],
                    'selector' => '.is-stuck {{WRAPPER}}',
                ]
            );

            $element->end_injection();

            $element->start_injection(
                array(
                    'type' => 'section',
                    'at' => 'end',
                    'of' => 'section_border',
                )
            );

            $element->add_control(
                'lqd_sticky_header_border_heading',
                [
                    'label' => esc_html__( 'Border on sticky header', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $element->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'lqd_sticky_header_border',
					'selector' => '.is-stuck {{WRAPPER}}',
				]
			);

            $element->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'lqd_sticky_header_box_shadow',
                    'label' => __( 'Box shadow on sticky header', 'hub-elementor-addons' ),
                    'selector' => '.is-stuck {{WRAPPER}}',
                    'separator' => 'after'
                ]
            );

            $element->end_injection();

            $element->start_injection(
                array(
                    'of' => 'padding',
                    'at' => 'after',
                )
            );

            $element->add_responsive_control(
                'lqd_sticky_section_margin',
                [
                    'label' => __( 'Margin on sticky header', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '.is-stuck {{WRAPPER}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
            );

            $element->add_responsive_control(
                'lqd_sticky_section_padding',
                [
                    'label' => __( 'Padding on sticky header', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '.is-stuck {{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'afterr',
                ]
            );

            $element->end_injection();

            $element->end_controls_section();
        }

        if ( 'column' === $element->get_name() ) {
            $element->start_controls_section(
                'liquid_custom_column_heading',
                [
                    'label' => __( 'Column Options', 'hub-elementor-addons' ),
                    'tab'   => Controls_Manager::TAB_LAYOUT,
                ]
            );

            $element->add_control(
                'enable_sticky_column',
                [
                    'label' => __( 'Sticky Column', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'hub-elementor-addons' ),
                    'label_off' => __( 'Off', 'hub-elementor-addons' ),
                    'return_value' => 'yes',
                    'default' => '',
                ]
            );

            $element->add_control(
                'sticky_column_offset',
                [
                    'label' => __( 'Sticky Offset', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::TEXT,
                    'default' => '30px',
                    'placeholder' => __( 'ex. 10px', 'hub-elementor-addons' ),
                    'condition' => [
                        'enable_sticky_column' => 'yes'
                    ],
                ]
            );

            $element->add_control(
                'enable_link',
                [
                    'label' => __( 'Enable Link', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __( 'On', 'hub-elementor-addons' ),
                    'label_off' => __( 'Off', 'hub-elementor-addons' ),
                    'return_value' => 'yes',
                    'default' => '',
                    'condition' => [
                        'lqd_disabled' => 'no',
                    ]
                ]
            );

            $element->add_control(
                'link',
                [
                    'label' => __( 'Link', 'hub-elementor-addons' ),
                    'type' => Controls_Manager::URL,
                    'placeholder' => __( 'https://your-link.com', 'hub-elementor-addons' ),
                    'show_external' => true,
                    'default' => [
                        'url' => '',
                        'is_external' => true,
                        'nofollow' => true,
                    ],
                    'condition' => [
                        'enable_link' => 'yes'
                    ]
                ]
            );
            $element->end_controls_section();
        }
	}

	public static function before_section_render( Element_Base $element ) {

        $container_selector = version_compare( ELEMENTOR_VERSION, '3.8', '>=' ) ? 'e-con' : 'e-container';
        $container_inner_selector = version_compare( ELEMENTOR_VERSION, '3.8', '>=' ) && $element->get_settings('content_width') === 'boxed' ? '.e-con-inner' : '';

        // Section
		if ( $element->get_settings( 'liquid_luminosity_data_attr' ) && 'default-auto' !== $element->get_settings( 'liquid_luminosity_data_attr' ) ) {
                $element->add_render_attribute( '_wrapper', [
                'data-section-luminosity' => $element->get_settings( 'liquid_luminosity_data_attr' ),
            ] );
        }

        if ( $element->get_settings( 'custom_cursor_on_hover' ) ) {
                $element->add_render_attribute( '_wrapper', [
                'data-lqd-custom-cursor' => 'true',
            ] );
        }

        if ( $element->get_settings( 'lqd_section_scroll' ) ) {
                $element->add_render_attribute( '_wrapper', [
                'data-lqd-section-scroll' => 'true',
            ] );
        }

        if ( $element->get_settings( 'hide_on_sticky' ) ) {
                $element->add_render_attribute( '_wrapper', [
                'class' => $element->get_settings( 'hide_on_sticky' ),
            ] );
        }

        if ( $element->get_settings( 'show_on_sticky' ) ) {
                $element->add_render_attribute( '_wrapper', [
                'class' => $element->get_settings( 'show_on_sticky' ),
            ] );
        }

        if ( $element->get_settings( 'sticky_bar' ) ) {
            $placement = $element->get_settings( 'stickybar_placement' );
            if ( empty( $placement ) ) {
                $placement = 'lqd-stickybar-left';
            }
            $element->add_render_attribute( '_wrapper', [
                'class' => 'lqd-stickybar-wrap '. $placement,
            ] );
        }

        if ( $element->get_settings( 'lqd_enable_bottom_shape_animation' ) ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => $element->get_settings( 'lqd_enable_bottom_shape_animation' ),
            ] );
        }

        if ( $element->get_settings( 'lqd_enable_top_shape_animation' ) ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => $element->get_settings( 'lqd_enable_top_shape_animation' ),
            ] );
        }

        if ( $element->get_settings( 'enable_sticky_column' ) ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => 'lqd-css-sticky-column',
                'style' => '--lqd-sticky-offset:'.$element->get_settings( 'sticky_column_offset' ) ,
            ] );
        }

        if ( $element->get_settings( 'section_data_tooltip' ) ) {
            $element->add_render_attribute( '_wrapper', [
                'data-tooltip' => $element->get_settings( 'section_data_tooltip' ),
            ] );
        }

        if ( $element->get_settings( 'lqd_sticky_row' ) ) {
            $element->add_render_attribute( '_wrapper', [
                'class' => $element->get_settings( 'lqd_sticky_row' ),
                'style' => 'top: auto; ' . $element->get_settings( 'lqd_sticky_row_anchor' ) . ': ' . $element->get_settings( 'lqd_sticky_row_offset' ) . ';',
            ] );
        }

        // Scale BG
        if ( $element->get_settings( 'row_scaleBg_onhover' ) ) {

                $image_uri = $element->get_settings( 'background_image' );

                $element->add_render_attribute( '_wrapper', [
                'class' => 'lqd-scale-bg-onhover',
                'data-row-bg' => $image_uri['url'],
            ] );
        }

        // Parallax
        if ( $element->get_settings( 'lqd_parallax' ) ) {

            $perspective = $element->get_settings( 'lqd_parallax_settings_perspective' );

            $from_x = $element->get_settings( 'lqd_parallax_from_x' );
            $from_y = $element->get_settings( 'lqd_parallax_from_y' );
            $from_z = $element->get_settings( 'lqd_parallax_from_z' );

            $from_scaleX = $element->get_settings( 'lqd_parallax_from_scaleX' );
            $from_scaleY = $element->get_settings( 'lqd_parallax_from_scaleY' );

            $from_rotationX = $element->get_settings( 'lqd_parallax_from_rotationX' );
            $from_rotationY = $element->get_settings( 'lqd_parallax_from_rotationY' );
            $from_rotationZ = $element->get_settings( 'lqd_parallax_from_rotationZ' );

            $from_opacity = $element->get_settings( 'lqd_parallax_from_opacity' );

            $from_transformOriginX = $element->get_settings( 'lqd_parallax_from_transformOriginX' );
            $from_transformOriginY = $element->get_settings( 'lqd_parallax_from_transformOriginY' );
            $from_transformOriginZ = $element->get_settings( 'lqd_parallax_from_transformOriginZ' );

            $to_x = $element->get_settings( 'lqd_parallax_to_x' );
            $to_y = $element->get_settings( 'lqd_parallax_to_y' );
            $to_z = $element->get_settings( 'lqd_parallax_to_z' );

            $to_scaleX = $element->get_settings( 'lqd_parallax_to_scaleX' );
            $to_scaleY = $element->get_settings( 'lqd_parallax_to_scaleY' );

            $to_rotationX = $element->get_settings( 'lqd_parallax_to_rotationX' );
            $to_rotationY = $element->get_settings( 'lqd_parallax_to_rotationY' );
            $to_rotationZ = $element->get_settings( 'lqd_parallax_to_rotationZ' );

            $to_opacity = $element->get_settings( 'lqd_parallax_to_opacity' );

            $to_transformOriginX = $element->get_settings( 'lqd_parallax_to_transformOriginX' );
            $to_transformOriginY = $element->get_settings( 'lqd_parallax_to_transformOriginY' );
            $to_transformOriginZ = $element->get_settings( 'lqd_parallax_to_transformOriginZ' );

            $parallax_ease = $element->get_settings( 'lqd_parallax_settings_ease' );
            $parallax_duration = $element->get_settings( 'lqd_parallax_settings_duration' );
            $parallax_trigger = $element->get_settings( 'lqd_parallax_settings_trigger' );
            $parallax_trigger_start = $element->get_settings( 'lqd_parallax_settings_trigger_start' );
            $parallax_trigger_end = $element->get_settings( 'lqd_parallax_settings_trigger_end' );

            $wrapper_attributes = $parallax_data = $parallax_data_from = $parallax_data_to = $parallax_opts = array();

            if ( !empty( $perspective ) && !empty( $perspective['size'] ) ) { $parallax_data_from['transformPerspective'] = $perspective['size'].$perspective['unit']; }

            if ( !empty( $from_x ) && !empty( $to_x ) && $from_x != $to_x ) {
                $parallax_data_from['x'] = $from_x['size'].$from_x['unit'];
                $parallax_data_to['x'] = $to_x['size'].$to_x['unit'];
            }
            if ( !empty( $from_y ) && !empty( $to_y ) && $from_y != $to_y ) {
                $parallax_data_from['y'] = $from_y['size'].$from_y['unit'];
                $parallax_data_to['y'] = $to_y['size'].$to_y['unit'];
            }
            if ( !empty( $from_z ) && !empty( $to_z ) && $from_z != $to_z ) {
                $parallax_data_from['z'] = $from_z['size'].$from_z['unit'];
                $parallax_data_to['z'] = $to_z['size'].$to_z['unit'];
            }

            if ( !empty( $from_scaleX ) && !empty( $to_scaleX ) && $from_scaleX != $to_scaleX ) {
                $parallax_data_from['scaleX'] = (float) $from_scaleX['size'];
                $parallax_data_to['scaleX'] = (float) $to_scaleX['size'];
            }
            if ( !empty( $from_scaleY ) && !empty( $to_scaleY ) && $from_scaleY != $to_scaleY ) {
                $parallax_data_from['scaleY'] = (float) $from_scaleY['size'];
                $parallax_data_to['scaleY'] = (float) $to_scaleY['size'];
            }

            if ( !empty( $from_rotationX ) && !empty( $to_rotationX ) && $from_rotationX != $to_rotationX ) {
                $parallax_data_from['rotationX'] = (int) $from_rotationX['size'];
                $parallax_data_to['rotationX'] = (int) $to_rotationX['size'];
            }
            if ( !empty( $from_rotationY ) && !empty( $to_rotationY ) && $from_rotationY != $to_rotationY ) {
                $parallax_data_from['rotationY'] = (int) $from_rotationY['size'];
                $parallax_data_to['rotationY'] = (int) $to_rotationY['size'];
            }
            if ( !empty( $from_rotationZ ) && !empty( $to_rotationZ ) && $from_rotationZ != $to_rotationZ ) {
                $parallax_data_from['rotationZ'] = (int) $from_rotationZ['size'];
                $parallax_data_to['rotationZ'] = (int) $to_rotationZ['size'];
            }

            if ( !empty( $from_opacity ) && !empty( $to_opacity ) && $from_opacity != $to_opacity ) {
                $parallax_data_from['opacity'] = (float) $from_opacity['size'];
                $parallax_data_to['opacity'] = (float) $to_opacity['size'];
            }

            $from_toriginX = $from_transformOriginX['size'].$from_transformOriginX['unit'];
            $from_toriginY = $from_transformOriginY['size'].$from_transformOriginY['unit'];
            $from_toriginZ = $from_transformOriginZ['size'].$from_transformOriginZ['unit'];

            $to_toriginX = $to_transformOriginX['size'].$to_transformOriginX['unit'];
            $to_toriginY = $to_transformOriginY['size'].$to_transformOriginY['unit'];
            $to_toriginZ = $to_transformOriginZ['size'].$to_transformOriginZ['unit'];

            $parallax_data_from['transformOrigin'] = $from_toriginX . ' ' . $from_toriginY . ' ' . $from_toriginZ;
            $parallax_data_to['transformOrigin'] = $to_toriginX . ' ' . $to_toriginY . ' ' . $to_toriginZ;

            //Parallax general options
            $parallax_data['from'] = $parallax_data_from;
            $parallax_data['to'] = $parallax_data_to;

            if( is_array( $parallax_data['from'] ) && ! empty( $parallax_data['from'] ) ) {
                $wrapper_attributes[] = 'data-parallax-from=\'' . wp_json_encode( $parallax_data['from'] ) . '\'';
            }
            if( is_array( $parallax_data['to'] ) && ! empty( $parallax_data['to'] ) ) {
                $wrapper_attributes[] = 'data-parallax-to=\'' . wp_json_encode( $parallax_data['to'] ) . '\'';
            }

            if ( isset( $parallax_ease ) ) { $parallax_opts['ease'] = $parallax_ease; }
            if( 'custom' !== $parallax_trigger ){
                $parallax_opts['start'] = esc_attr( $parallax_trigger );
                if ( isset($parallax_duration) && ! empty($parallax_duration) ) {
                    $parallax_duration_size = (float) $parallax_duration['size'];
                    $dur = $parallax_duration_size >= 0 ? '+='.abs($parallax_duration_size).$parallax_duration['unit'].'' : '-='.abs($parallax_duration_size).$parallax_duration['unit'].'';
                    $parallax_opts['end'] = esc_attr( 'bottom'  . $dur . ' top' );
                }
            } else {
                if ( ! empty( $parallax_trigger_start ) ) {
                    $parallax_opts['start'] = esc_attr( $parallax_trigger_start );
                }
                if ( ! empty( $parallax_trigger_end ) ) {
                    $parallax_opts['end'] = esc_attr( $parallax_trigger_end );
                }
            }
            if( ! empty( $parallax_opts ) ) {
                $wrapper_attributes[] = 'data-parallax-options=\'' . wp_json_encode( $parallax_opts ) .'\'';
            }

            $element->add_render_attribute( '_wrapper', [
                'data-parallax' => 'true',
                'data-parallax-options' => wp_json_encode( $parallax_opts ),
                'data-parallax-from' => wp_json_encode( $parallax_data['from'] ),
                'data-parallax-to' => wp_json_encode( $parallax_data['to'] ),
            ] );

        }

         // Animation
         if ( $element->get_settings( 'lqd_custom_animation' ) ) {

            $ca_preset_values = array();
            $ca_opts = $ca_from_values = $ca_to_values = array();
            $animation_targets = array();

            $animation_preset = $element->get_settings( 'lqd_ca_preset' );
            $ca_ease = $element->get_settings( 'lqd_ca_settings_ease' );
            $ca_direction = $element->get_settings( 'lqd_ca_settings_direction' );
            $ca_duration = $element->get_settings( 'lqd_ca_settings_duration' )['size'];
            $ca_stagger = $element->get_settings( 'lqd_ca_settings_stagger' )['size'];
            $ca_start_delay = $element->get_settings( 'lqd_ca_settings_start_delay' )['size'];

            $ca_opts['addChildTimelines'] = false;
            // $ca_opts['addPerspective'] = false;

            switch ( $element->get_name() ){
                case 'container':
                    if ( $element->get_settings('lqd_ca_targets') === 'contents' ) {
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-element:not(.lqd-exclude-parent-ca) > .elementor-widget-container');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-hub_fancy_heading .lqd-split-lines .lqd-lines .split-inner');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-hub_fancy_heading .lqd-split-words .lqd-words .split-inner');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-hub_fancy_heading .lqd-split-chars .lqd-chars .split-inner');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-hub_fancy_heading .lqd-adv-txt-fig');
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .elementor-widget-ld_custom_menu .lqd-fancy-menu > ul > li');
                        if ( $element->get_settings('lqd_ca_include_inner_content') === 'yes' ) {
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-element > .elementor-widget-container');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-hub_fancy_heading .lqd-split-lines .lqd-lines .split-inner');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-hub_fancy_heading .lqd-split-words .lqd-words .split-inner');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-hub_fancy_heading .lqd-split-chars .lqd-chars .split-inner');
                            array_push($animation_targets, ':scope .' . $container_selector . ':not([data-parallax]) .elementor-widget-ld_custom_menu .lqd-fancy-menu > ul > li');
                        }
                    } else {
                        array_push($animation_targets, ':scope ' . $container_inner_selector . ' > .' . $container_selector . '');
                    }
                break;
                case 'section':
                    array_push($animation_targets, ':scope > .elementor-container > .elementor-column');
                break;
                case 'column':
                    // $ca_opts['addChildTimelines'] = true;
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-element > .elementor-widget-container');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-section > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-element:not(.lqd-el-has-inner-anim) > .elementor-widget-container');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-hub_fancy_heading .lqd-split-lines .lqd-lines .split-inner');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-hub_fancy_heading .lqd-split-words .lqd-words .split-inner');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-hub_fancy_heading .lqd-split-chars .lqd-chars .split-inner');
                    array_push($animation_targets, ':scope > .elementor-widget-wrap > .elementor-widget-ld_custom_menu .lqd-fancy-menu > ul > li');
                break;
                case 'ld_carousel':
                case 'ld_testimonial_carousel':
                    array_push($animation_targets, '[data-lqd-flickity] > .flickity-viewport > .flickity-slider > .carousel-item > .carousel-item-inner');
                break;
                case 'ld_interactive_text_image':
                    array_push($animation_targets, '.lqd-iti-link-item');
                break;
                case 'ld_woo_products_list':
                    array_push($animation_targets, '.lqd-prod-item');
                break;
                case 'ld_portfolio':
                    array_push($animation_targets, '.lqd-pf-item');
                break;
                default:
                    if( $element->get_name() === 'hub_fancy_heading' && $element->get_settings( 'enable_split' ) ){

                        $split_type = $element->get_settings( 'split_type' );

                        if ( $split_type === 'lines' ){
                            array_push($animation_targets, '.lqd-split-lines .lqd-lines .split-inner');
                        } else if ( $split_type === 'words' ){
                            array_push($animation_targets, '.lqd-split-words .lqd-words .split-inner');
                        } else if ( $split_type === 'chars, words' ){
                            array_push($animation_targets, '.lqd-split-chars .lqd-chars .split-inner');
                        }
                        array_push($animation_targets, '.lqd-adv-txt-fig');
                    } else if ( $element->get_name() === 'ld_custom_menu' ) {
                        array_push($animation_targets, ':scope .lqd-fancy-menu > ul > li');
                    } else {
                        array_push($animation_targets, ':scope > .elementor-widget-container');
                    }

                break;
            }

            $ca_opts['trigger'] = 'firstChild';
            $ca_opts['animationTarget'] = implode(', ', $animation_targets);

            if ( !empty( $ca_duration ) && $ca_duration !== 1.6 ) {
                $ca_opts['duration'] = (float) ($ca_duration * 1000);
            }
            if( !empty( $ca_start_delay ) && $ca_start_delay !== 0 ) {
                $ca_opts['startDelay'] = (float) ($ca_start_delay * 1000);
            }
            if ( !empty( $ca_stagger ) && $ca_stagger !== 0.16 ) {
                $ca_opts['delay'] = (float) ($ca_stagger * 1000);
            }
            if ( $ca_ease !== 'power4.out' ) {
                $ca_opts['ease'] = $ca_ease;
            }
            if ( $ca_direction !== 'forward' ) {
                $ca_opts['direction'] = $ca_direction;
            }

            if( 'custom' !== $animation_preset ) {

                $defined_animations = array(

                    'Fade In' => array(
                        'from' => array( 'opacity' => 0 ),
                        'to'   => array( 'opacity' => 1 ),
                    ),
                    'Fade In Down' => array(
                        'from' => array( 'opacity' => 0, 'y' => -150 ),
                        'to'   => array( 'opacity' => 1, 'y' => 0 ),
                    ),
                    'Fade In Up' => array(
                        'from' => array( 'opacity' => 0, 'y' => 150 ),
                        'to'   => array( 'opacity' => 1, 'y' => 0 ),
                    ),
                    'Fade In Left' => array(
                        'from' => array( 'opacity' => 0, 'x' => -150 ),
                        'to'   => array( 'opacity' => 1, 'x' => 0 ),
                    ),
                    'Fade In Right' => array(
                        'from' => array( 'opacity' => 0, 'x' => 150 ),
                        'to'   => array( 'opacity' => 1, 'x' => 0 ),
                    ),
                    'Flip In Y' => array(
                        'from' => array( 'opacity' => 0, 'x' => 150, 'rotationY' => 30 ),
                        'to'   => array( 'opacity' => 1, 'x' => 0, 'rotationY' => 0 ),
                    ),
                    'Flip In X' => array(
                        'from' => array( 'opacity' => 0, 'y' => 150, 'rotationX' => -30 ),
                        'to'   => array( 'opacity' => 1, 'y' => 0, 'rotationX' => 0 ),
                    ),
                    'Scale Up' => array(
                        'from' => array( 'opacity' => 0, 'scale' => 0.75 ),
                        'to'   => array( 'opacity' => 1, 'scale' => 1 ),
                    ),
                    'Scale Down' => array(
                        'from' => array( 'opacity' => 0, 'scale' => 1.25 ),
                        'to'   => array( 'opacity' => 1, 'scale' => 1 ),
                    ),

                );

                $ca_preset_values = $defined_animations[ $animation_preset ];
                $ca_from_values = $ca_preset_values['from'];
                $ca_to_values = $ca_preset_values['to'];
            }
            else {

                // From values
                $ca_from_x = $element->get_settings( 'lqd_ca_from_x' );
                $ca_from_y = $element->get_settings( 'lqd_ca_from_y' );
                $ca_from_z = $element->get_settings( 'lqd_ca_from_z' );

                $ca_from_scaleX = $element->get_settings( 'lqd_ca_from_scaleX' );
                $ca_from_scaleY = $element->get_settings( 'lqd_ca_from_scaleY' );

                $ca_from_rotationX = $element->get_settings( 'lqd_ca_from_rotationX' );
                $ca_from_rotationY = $element->get_settings( 'lqd_ca_from_rotationY' );
                $ca_from_rotationZ = $element->get_settings( 'lqd_ca_from_rotationZ' );

                $ca_from_transformOriginX = $element->get_settings( 'lqd_ca_from_transformOriginX' );
                $ca_from_transformOriginY = $element->get_settings( 'lqd_ca_from_transformOriginY' );
                $ca_from_transformOriginZ = $element->get_settings( 'lqd_ca_from_transformOriginZ' );

                $ca_from_opacity = $element->get_settings( 'lqd_ca_from_opacity' );

                // To values
                $ca_to_x = $element->get_settings( 'lqd_ca_to_x' );
                $ca_to_y = $element->get_settings( 'lqd_ca_to_y' );
                $ca_to_z = $element->get_settings( 'lqd_ca_to_z' );

                $ca_to_scaleX = $element->get_settings( 'lqd_ca_to_scaleX' );
                $ca_to_scaleY = $element->get_settings( 'lqd_ca_to_scaleY' );

                $ca_to_rotationX = $element->get_settings( 'lqd_ca_to_rotationX' );
                $ca_to_rotationY = $element->get_settings( 'lqd_ca_to_rotationY' );
                $ca_to_rotationZ = $element->get_settings( 'lqd_ca_to_rotationZ' );

                $ca_to_transformOriginX = $element->get_settings( 'lqd_ca_to_transformOriginX' );
                $ca_to_transformOriginY = $element->get_settings( 'lqd_ca_to_transformOriginY' );
                $ca_to_transformOriginZ = $element->get_settings( 'lqd_ca_to_transformOriginZ' );

                $ca_to_opacity = $element->get_settings( 'lqd_ca_to_opacity' );

                if ( !empty( $ca_from_x ) && !empty( $ca_to_x ) && $ca_from_x != $ca_to_x ) {
                    $ca_from_values['x'] = $ca_from_x['size'].$ca_from_x['unit'];
                    $ca_to_values['x'] = $ca_to_x['size'].$ca_to_x['unit'];
                }
                if ( !empty( $ca_from_y ) && !empty( $ca_to_y ) && $ca_from_y != $ca_to_y ) {
                    $ca_from_values['y'] = $ca_from_y['size'].$ca_from_y['unit'];
                    $ca_to_values['y'] = $ca_to_y['size'].$ca_to_y['unit'];
                }
                if ( !empty( $ca_from_z ) && !empty( $ca_to_z ) && $ca_from_z != $ca_to_z ) {
                    $ca_from_values['z'] = $ca_from_z['size'].$ca_from_z['unit'];
                    $ca_to_values['z'] = $ca_to_z['size'].$ca_to_z['unit'];
                }

                if ( !empty( $ca_from_scaleX ) && !empty( $ca_to_scaleX ) && $ca_from_scaleX != $ca_to_scaleX ) {
                    $ca_from_values['scaleX'] = (float) $ca_from_scaleX['size'];
                    $ca_to_values['scaleX'] = (float) $ca_to_scaleX['size'];
                }
                if ( !empty( $ca_from_scaleY ) && !empty( $ca_to_scaleY ) && $ca_from_scaleY != $ca_to_scaleY ) {
                    $ca_from_values['scaleY'] = (float) $ca_from_scaleY['size'];
                    $ca_to_values['scaleY'] = (float) $ca_to_scaleY['size'];
                }

                if ( !empty( $ca_from_rotationX ) && !empty( $ca_to_rotationX ) && $ca_from_rotationX != $ca_to_rotationX ) {
                    $ca_from_values['rotationX'] = (int) $ca_from_rotationX['size'];
                    $ca_to_values['rotationX'] = (int) $ca_to_rotationX['size'];
                }
                if ( !empty( $ca_from_rotationY ) && !empty( $ca_to_rotationY ) && $ca_from_rotationY != $ca_to_rotationY ) {
                    $ca_from_values['rotationY'] = (int) $ca_from_rotationY['size'];
                    $ca_to_values['rotationY'] = (int) $ca_to_rotationY['size'];
                }
                if ( !empty( $ca_from_rotationZ ) && !empty( $ca_to_rotationZ ) && $ca_from_rotationZ != $ca_to_rotationZ ) {
                    $ca_from_values['rotationZ'] = (int) $ca_from_rotationZ['size'];
                    $ca_to_values['rotationZ'] = (int) $ca_to_rotationZ['size'];
                }

                if ( !empty( $ca_from_opacity ) && !empty( $ca_to_opacity ) && $ca_from_opacity != $ca_to_opacity ) {
                    $ca_from_values['opacity'] = (float) $ca_from_opacity['size'];
                    $ca_to_values['opacity'] = (float) $ca_to_opacity['size'];
                }

                $ca_from_toriginX = $ca_from_transformOriginX['size'].$ca_from_transformOriginX['unit'];
                $ca_from_toriginY = $ca_from_transformOriginY['size'].$ca_from_transformOriginY['unit'];
                $ca_from_toriginZ = $ca_from_transformOriginZ['size'].$ca_from_transformOriginZ['unit'];

                $ca_to_toriginX = $ca_to_transformOriginX['size'].$ca_to_transformOriginX['unit'];
                $ca_to_toriginY = $ca_to_transformOriginY['size'].$ca_to_transformOriginY['unit'];
                $ca_to_toriginZ = $ca_to_transformOriginZ['size'].$ca_to_transformOriginZ['unit'];

                $ca_from_values['transformOrigin'] = $ca_from_toriginX . ' ' . $ca_from_toriginY . ' ' . $ca_from_toriginZ;
                $ca_to_values['transformOrigin'] = $ca_to_toriginX . ' ' . $ca_to_toriginY . ' ' . $ca_to_toriginZ;

            }

            $ca_opts['initValues'] = !empty( $ca_from_values ) ? $ca_from_values : array();
            $ca_opts['animations'] = !empty( $ca_to_values ) ? $ca_to_values : array();

            $element->add_render_attribute( '_wrapper', [
                'data-custom-animations' => 'true',
                'data-ca-options' => stripslashes( wp_json_encode( $ca_opts ) ),
            ] );

        }
	}

    // Collect cutout datas before render the page
    public static function shape_cutout_render( Element_Base $element ) {

        if ( $style = $element->get_settings( 'lqd_shape_cutout_style' ) ) :

            $cache = get_option( 'shape_cutout_the_content', array() );
            $path = LD_ELEMENTOR_PATH . 'elementor/params/shape-cutout/'.$style.'.php';

            ob_start();
            include $path;
            $html = ob_get_contents();
            ob_end_clean();

            $cache[$element->get_id()] = $html;
            update_option( 'shape_cutout_the_content', $cache );
            self::$shape_cutout_the_content[$element->get_id()] = $html;

        endif;
    }

    // Print cutout datas in the_content
    public static function shape_cutout_the_content( $content ) {

        $ids = self::$shape_cutout_the_content;

        if ( empty( $ids ) ) {
            $ids = get_option( 'shape_cutout_the_content' );
        }

        if ( ! $ids ) {
            return $content; // Return if there is no data
        }

        return liquid_helper()->shape_cutout_the_content( $content, $ids );

    }

    public static function shape_cutout_editor_render( $template, $element ) {

        $old_template = $template;
        ob_start();
        if ( 'container' === $element->get_name() ) {
            $path = LD_ELEMENTOR_PATH . 'elementor/params/shape-cutout/templates.js';
            ?> <# <?php echo file_get_contents($path) ?> #>
            {{{render_lqd_cutout()}}} <?php
        }

        $new_template = ob_get_contents();
        ob_end_clean();

        $template = $new_template . $old_template;
        return $template;

    }
}

Hub_Elementor_Custom_Controls::init();
