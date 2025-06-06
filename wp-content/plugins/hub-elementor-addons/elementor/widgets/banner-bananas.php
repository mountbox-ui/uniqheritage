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
class LD_Bananas_Banner extends Widget_Base {

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
		return 'ld_bananas_banner';
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
		return __( 'Liquid Banner Bananas', 'hub-elementor-addons' );
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
		return 'eicon-slider-vertical lqd-element';
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
		return [ 'banner', 'image' ];
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
				'label' => __( 'Banner Bananas', 'hub-elementor-addons' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'hub-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'heading',
			[
				'label' => __( 'Heading', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Heading', 'hub-elementor-addons' ),
				'placeholder' => __( 'Type your title here', 'hub-elementor-addons' ),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'label' => __( 'Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .lqd-bnr-bnns-h',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-bnr-bnns-h' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading2',
			[
				'label' => __( 'Heading 2', 'hub-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Heading Two', 'hub-elementor-addons' ),
				'placeholder' => __( 'Type your title here', 'hub-elementor-addons' ),
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading2_typography',
				'label' => __( 'Typography', 'hub-elementor-addons' ),
				'selector' => '{{WRAPPER}} .lqd-bnr-bnns-h-inner',
			]
		);

		$this->add_control(
			'color2',
			[
				'label' => __( 'Color', 'hub-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .lqd-bnr-bnns-h-inner' => 'color: {{VALUE}}',
				],
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

		?>
		<div class="lqd-bnr-bnns text-center">
			<div class="lqd-bnr-bnns-inner lqd-overlay d-flex flex-wrap">

				<?php if( !empty( $heading ) ) { ?>
				<div
				class="lqd-bnr-bnns-h-wrap lqd-bnr-bnns-h-wrap-first w-100"
				data-parallax="true"
				data-parallax-from='{"opacity": 0, "y": 100}'
				data-parallax-to='{"opacity": 1, "y": 0}'
				data-parallax-options='{"addWillChange": true, "start": "top top", "end": "+=60%"}'>
					<h2 class="lqd-bnr-bnns-h">
						<?php echo $heading; ?>
					</h2>

				</div>
				<?php } ?>

				<div class="fullwidth pos-abs h-100">
					<div class="lqd-bnr-bnns-media h-vh-100 pos-sticky pos-tl">
						<div
						class="lqd-bnr-media-inner"
						data-parallax="true"
						data-parallax-from='{"scale": 1, "y": 0}'
						data-parallax-to='{"scale": 0.75, "y": 100}'
						data-parallax-options='{"addWillChange": true, "start": "top top", "end": "+=50%"}'>
						<figure>
							<?php echo wp_get_attachment_image( $image['id'], 'full', false, array( 'class' => 'w-100' ) ); ?>
						</figure>
						</div>

					</div>
				</div>

				<?php if( !empty( $heading2 ) ) { ?>
				<div class="lqd-bnr-bnns-h-wrap lqd-bnr-bnns-h-wrap-last d-flex align-items-center justify-content-center h-vh-100 pos-sticky pos-tl text-center w-100"
				data-parallax="true"
				data-parallax-from='{"opacity": 1, "y": 0}'
				data-parallax-to='{"opacity": 0, "y": 75}'
				data-parallax-options='{"addWillChange": true, "start": "top top", "end": "+=50%"}'>

					<h2 class="lqd-bnr-bnns-h-inner m-0"><?php echo $heading2; ?></h2>

				</div>
				<?php } ?>

			</div>

		</div>
		<?php

	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new LD_Bananas_Banner() );