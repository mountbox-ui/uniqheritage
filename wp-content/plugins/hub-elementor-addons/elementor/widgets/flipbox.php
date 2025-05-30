<?php
namespace LiquidElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
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
class LD_Flipbox extends Widget_Base {

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
		return 'ld_flipbox';
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
		return __( 'Liquid Flip Box', 'hub-elementor-addons' );
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
		return 'eicon-flip-box lqd-element';
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
		return [ 'flip', 'box', 'rotate' ];
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

		// General Section
		$this->start_controls_section(
			'general_section',
			[
				'label' => __( 'General', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'direction',
			[
				'label' => __( 'Flip Direction', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Left to Right', 'hub-elementor-addons' ),
					'ld-flipbox-rl' => __( 'Right to Left', 'hub-elementor-addons' ),
					'ld-flipbox-tb' => __( 'Top to Bottom', 'hub-elementor-addons' ),
					'ld-flipbox-bt' => __( 'Bottom to Top', 'hub-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'shadow',
			[
				'label' => __( 'Shadow', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'ld-flipbox-shadow' => __( 'Default', 'hub-elementor-addons' ),
					'ld-flipbox-shadow-onhover' => __( 'On Hover', 'hub-elementor-addons' ),
				],
			]
		);
        
		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hub-elementor-addons' ),
					'semi-round' => __( 'Semi Round', 'hub-elementor-addons' ),
					'round' => __( 'Round', 'hub-elementor-addons' ),
					'circle' => __( 'Circle', 'hub-elementor-addons' ),
				],
			]
		);
		$this->end_controls_section();

        // Front Section
		$this->start_controls_section(
			'front_side_section',
			[
				'label' => __( 'Front Side', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'front_content_type',
			[
				'label' => __( 'Content Type', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'front_tinymce',
				'label_block' => true,
				'options' => [
					'front_tinymce' => __( 'TinyMCE', 'hub-elementor-addons' ),
					'front_el_template' => __( 'Elementor Template', 'hub-elementor-addons' ),
				],
				'separator' => 'before',
			]
		);

        $this->add_control(
			'front_content', [
				'label' => __( 'Content', 'hub-elementor-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Content' , 'hub-elementor-addons' ),
				'condition' => [
					'front_content_type' => 'front_tinymce'
				],
			]
		);

		$this->add_control(
			'front_templates', [
				'label' => __( 'Select Template', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => liquid_helper()->get_elementor_templates(),
				'description' => liquid_helper()->get_elementor_templates_edit(),
				'default' => '0',
				'condition' => [
					'front_content_type' => 'front_el_template'
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_bg',
				'label' => __( 'Navigation Background Color', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient', 'image' ],
				'selector' => '{{WRAPPER}} .ld-flipbox-front',
                'separator' => 'before',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
                    'image' => [
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
					],
				],
			]
		);

		$this->add_control(
			'front_o_bg_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Overlay Backgorund' , 'hub-elementor-addons'),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_o_bg',
				'label' => __( 'Background Overlay Color', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ld-flipbox-front .ld-flipbox-overlay',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
                    'image' => [
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
					],
				],
			]
		);

		$this->add_control(
			'front_o_padding_h',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Padding' , 'hub-elementor-addons'),
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'front_o_padding',
			[
				'label' => __( 'Padding', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'vw', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} .ld-flipbox-front .ld-flipbox-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => "40",
					'right' => "50",
					'bottom' => "40",
					'left' => "50",
					'unit' => 'px',
					'isLinked' =>  false,
				]
			]
		);

		$this->end_controls_section();

         // Back Section
		$this->start_controls_section(
			'back_side_section',
			[
				'label' => __( 'Back Side', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'back_content_type',
			[
				'label' => __( 'Content Type', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'back_tinymce',
				'label_block' => true,
				'options' => [
					'back_tinymce' => __( 'TinyMCE', 'hub-elementor-addons' ),
					'back_el_template' => __( 'Elementor Template', 'hub-elementor-addons' ),
				],
				'separator' => 'before',
			]
		);

        $this->add_control(
			'back_content', [
				'label' => __( 'Content', 'hub-elementor-addons' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Content' , 'hub-elementor-addons' ),
				'condition' => [
					'back_content_type' => 'back_tinymce'
				],
			]
		);

		$this->add_control(
			'back_templates', [
				'label' => __( 'Select Template', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => liquid_helper()->get_elementor_templates(),
				'description' => liquid_helper()->get_elementor_templates_edit(),
				'default' => '0',
				'condition' => [
					'back_content_type' => 'back_el_template'
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'back_bg',
				'label' => __( 'Background Color', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient', 'image' ],
				'selector' => '{{WRAPPER}} .ld-flipbox-back',
                'separator' => 'before',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
                    'image' => [
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
					],
				],
			]
		);

		$this->add_control(
			'back_o_bg_heading',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Overlay Backgorund' , 'hub-elementor-addons'),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'back_o_bg',
				'label' => __( 'Background Overlay Color', 'hub-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ld-flipbox-back .ld-flipbox-overlay',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
                    'image' => [
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
					],
				],
			]
		);

		$this->add_control(
			'back_o_padding_h',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Padding' , 'hub-elementor-addons'),
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'back_o_padding',
			[
				'label' => __( 'Padding', 'hub-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'vw', 'vh' ],
				'selectors' => [
					'{{WRAPPER}} .ld-flipbox-back .ld-flipbox-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => "40",
					'right' => "50",
					'bottom' => "40",
					'left' => "50",
					'unit' => 'px',
					'isLinked' => false,
				]
			]
		);

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
		extract( $settings );

		$roundness = '';

		if ( $border_radius === 'semi-round' ) {
			$roundness = 'border-radius-2';
		} else if ( $border_radius === 'round' ) {
			$roundness = 'border-radius-4';
		} else if ( $border_radius === 'circle' ) {
			$roundness = 'border-radius-circle';
		}

		$this->add_render_attribute(
			'wrapper',
			[
				'class' => [
					'ld-flipbox',
					'd-flex',
					'flex-column',
					'justify-content-center',
					'align-items-center',
					'pos-rel',
					'perspective',
					$direction,
					$shadow,
					$roundness,
				]
			]
		);
		
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="ld-flipbox-wrap w-100 pos-rel transform-style-3d">
				<?php 

					printf(
						'<div id="%1$s" class="ld-flipbox-face ld-flipbox-front d-flex flex-column w-100 backface-hidden transform-style-3d">
						<span class="ld-flipbox-overlay lqd-overlay">
						</span><div class="ld-flipbox-inner w-100 flex-grow-1 align-items-center justify-content-center backface-hidden">%2$s</div></div>',
						'front-' . $this->get_id(), 
						($front_content_type === 'front_tinymce' ? $front_content : \Elementor\Plugin::instance()->frontend->get_builder_content( $front_templates, true ))
					);

					printf(
						'<div id="%1$s" class="ld-flipbox-face ld-flipbox-back d-flex flex-column lqd-overlay backface-hidden transform-style-3d">
						<span class="ld-flipbox-overlay lqd-overlay">
						</span><div class="ld-flipbox-inner d-flex flex-column flex-grow-1 align-items-center justify-content-center w-100 backface-hidden">%2$s</div></div>',
						'back-' . $this->get_id(), 
						($back_content_type === 'back_tinymce' ? $back_content : \Elementor\Plugin::instance()->frontend->get_builder_content( $back_templates, true ))
					);
						
				?>
			</div>
		</div>
		<?php
	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Flipbox() );