<?php
/**
 * The Helper
 * Contains all the helping functions
 *
 *
 * Table of Content
 *
 * 1. WordPress Helpers
 * 2. Markup Helpers
 * 3. Theme Options/Meta Helpers
 * 4. Array opperations
 */

/**
 * Main helper functions.
 *
 * @class Liquid_Helper
*/
class Liquid_Helper {

	/**
	 * Hold an instance of Liquid_Helper class.
	 * @var Liquid_Helper
	 */
	protected static $instance = null;

	/**
	 * Main Liquid_Helper instance.
	 *
	 * @return Liquid_Helper - Main instance.
	 */
	public static function instance() {

		if(null == self::$instance) {
			self::$instance = new Liquid_Helper();
		}

		return self::$instance;
	}

	// 1. WordPress Helpers -----------------------------------------------

	/**
	 * [ajax_url description]
	 * @method ajax_url
	 * @return [type]   [description]
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * [get_sidebars description]
	 * @method get_sidebars
	 * @param  array        $data [description]
	 * @return [type]             [description]
	 */
	public function get_sidebars( $data = array() ) {
		global $wp_registered_sidebars;

        foreach ( $wp_registered_sidebars as $key => $value ) {
            $data[ $key ] = $value['name'];
        }

		return $data;
	}

	public function get_elementor_templates() {
		$posts = get_posts( array(
			'post_type' => 'elementor_library',
			'posts_per_page' => -1,
			'meta_query'  => array(
                array(
                    'key' => '_elementor_template_type',
                    'value' => 'kit',
                    'compare' => '!=',
                ),
            ),
		) );

		$options = [ '0' => 'Select Template' ];

		foreach ( $posts as $post ) {
		  $options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	public function get_elementor_templates_edit() {

		$out = '
		<div class="lqd-tmpl-edit-editor-buttons">
			<button
				class="elementor-button"
				type="button"
				onClick="lqd_edit_tmpl(event)"
			><i class="eicon-edit"></i>
			</button>

			<button
				class="elementor-button"
				type="button"
				onClick="lqd_add_tmpl(event)"
			><i class="eicon-plus"></i> Add template
			</button>
		</div>
		';

		return $out;

	}

	public function get_elementor_edit_cpt( $post_id, $post_type = '' ) {

		if ( defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->preview->is_preview_mode() && !empty( $post_id ) ){
			$out = '
				<style>
				.lqd-site-header:not(.absolute), .lqd-site-footer{
					position: relative;
				}
				.lqd-tmpl-edit-cpt{
                    width: 100%;
                    height:100%;
					position:absolute;
					z-index:2;
                    top: 0;
                    left: 0;
					display:flex;
					justify-content:end;
                    align-items:start;
					transition: opacity 300ms;
                    pointer-events: none;
                    opacity: 0;
                    border:1px solid var(--e-a-btn-bg-primary);
				}
				.lqd-tmpl-edit-cpt--btn{
                    display:flex;
					align-items:center;
                    gap: 8px;
                    padding: 2px 8px;
					font-size: 14px;
					background: var(--e-a-btn-bg-primary)!important;
					color: #000 !important;
					border-radius: 0 0 0 4px;
					cursor: pointer;
                    pointer-events: auto;
                    white-space: nowrap;
				}
				.lqd-tmpl-edit-cpt--btn i {
                    font-size: 12px;
                }
                .lqd-site-header:hover .lqd-tmpl-edit-cpt,
                .lqd-site-footer:hover .lqd-tmpl-edit-cpt{
                    opacity:1;
                }
                .elementor-editor-preview .lqd-tmpl-edit-cpt {
                    display: none;
                }
				</style>


				<div class="lqd-tmpl-edit-cpt">
				<button
					class="lqd-tmpl-edit-cpt--btn"
					type="button"
					data-post-id="'.$post_id.'"
				>Edit ' . esc_html__( $post_type, 'hub' ) . ' <i class="eicon-edit"></i>
				</button>
				</div>
			';

			echo $out;
		}

	}

	/**
	 * Instantiates the WordPress filesystem for use with Hub.
	 *
	 * @static
	 * @access public
	 * @return object
	 */
	public function init_filesystem() {

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		// The WordPress filesystem.
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * [get_template_part description]
	 * @method get_template_part
	 * @param  [type]            $template [description]
	 * @param  [type]            $args     [description]
	 * @return [type]                      [description]
	 */
	public function get_template_part( $template, $args = null ) {

		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = locate_template( $template . '.php' );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( wp_kses_post( __( '<code>%s</code> does not exist.', 'hub' ) ), $located ), null );
			return;
		}

		include $located;
	}

	/**
	 * [get_theme_name description]
	 * @method get_theme_name
	 * @return [type]         [description]
	 */
	public function get_current_theme() {
		$current_theme = wp_get_theme();
		if( $current_theme->parent_theme ) {
			$template_dir  = basename( get_template_directory() );
			$current_theme = wp_get_theme( $template_dir );
		}

		return $current_theme;
	}

	/**
	 * Generate plugin action link
	 * @return html
	 */
	public function tgmpa_plugin_action( $plugin, $status ) {

		$btn_class = $btn_text = $nonce_url = '';
		$page = admin_url( 'admin.php?page=' . $_GET['page'] );

		switch( $status ) {
			case 'not-installed':
				$btn_class = 'white';
				$btn_text = esc_html_x( 'Install', 'Liquid plugin installation page.', 'hub' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'tgmpa-install' => 'install-plugin',
							'return_url' => $_GET['page']
						),
						TGM_Plugin_Activation::$instance->get_tgmpa_url()
					),
					'tgmpa-install',
					'tgmpa-nonce'
				);
				break;

			case 'installed':
				$btn_class = 'success';
				$btn_text = esc_html_x( 'Activate', 'Liquid plugin installation page.', 'hub' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'liquid-activate' => 'activate-plugin'
						),
						$page
					),
					'liquid-activate',
					'liquid-activate-nonce'
				);
				break;

			case 'active':
				$btn_class = 'danger';
				$btn_text = esc_html_x( 'Deactivate', 'Liquid plugin installation page.', 'hub' );

				$nonce_url = wp_nonce_url(
					add_query_arg(
						array(
							'plugin' => urlencode( $plugin['slug'] ),
							'liquid-deactivate' => 'deactivate-plugin'
						),
						$page
					),
					'liquid-deactivate',
					'liquid-deactivate-nonce'
				);
				break;
		}

		printf(
			'<a class="liquid-button" href="%4$s" title="%2$s %1$s"><span>%2$s</span> <i class="fa fa-angle-right"></i></a>',
			$plugin['name'], $btn_text, $btn_class, esc_url( $nonce_url )
		);
	}

	/**
	 * [sanitize_html_classes description]
	 * @method sanitize_html_classes
	 * @return (mixed: string / $fallback ) [description]
	 */
	public function sanitize_html_classes( $class, $fallback = null ) {

		// Explode it, if it's a string
		if ( is_string( $class ) ) {
			$class = explode( ' ', $class );
		}

		if ( is_array( $class ) && !empty( $class ) ) {
			$class = array_map( 'sanitize_html_class', $class );
			return join( ' ', $class );
		}
		else {
			return sanitize_html_class( $class, $fallback );
		}

	}

	/**
	 * Adds all variables from $_GET array to given URL and returns this URL
	 * @param type $url url
	 * @param type $skip array of variables to skip
	 * @return type
	 */
	public function add_to_url_from_get( $url, $skip = array() ) {

		if ( isset( $_GET ) && is_array( $_GET ) ) {
			foreach ( $_GET as $key => $val ) {
				if ( in_array( $key, $skip ) ) {
					continue;
				}
				$url = add_query_arg( $key . '=' . $val, '', $url );
			}
		}
		return $url;
	}

	/**
	 * [has_seo_plugins description]
	 * @method has_seo_plugins
	 * @return boolean         [description]
	 */
	public function has_seo_plugins() {

		$plugins = array(
			'yoast' => defined( 'WPSEO_VERSION' ),
			'ainseop' => defined( 'AIOSEOP_VERSION' )
		);

		foreach( $plugins as $item ) {
			if( $item ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * [get_menu_location_name description]
	 * @method get_menu_location_name
	 * @param  [type]                 $location [description]
	 * @return [type]                           [description]
	 */
	public function get_menu_location_name( $location ) {

		$locations = get_registered_nav_menus();

		return isset( $locations[ $location ] ) ? $locations[ $location ] : '';
	}

	/**
	 * [get_attachment_types description]
	 * @method get_attachment_types
	 * @param  integer              $post_id [description]
	 * @return [type]                        [description]
	 */
	public function get_attachment_types( $post_id = 0 ) {

		$post_id   = empty( $post_id ) ? get_the_ID() : $post_id;
		$mime_type = get_post_mime_type( $post_id );

		list( $type, $subtype ) = false !== strpos( $mime_type, '/' ) ? explode( '/', $mime_type ) : array( $mime_type, '' );

		return (object) array( 'type' => $type, 'subtype' => $subtype );
	}

	/**
	 * [get_attachment_type description]
	 * @method get_attachment_type
	 * @param  integer             $post_id [description]
	 * @return [type]                       [description]
	 */
	public function get_attachment_type( $post_id = 0 ) {
		return $this->get_attachment_types( $post_id )->type;
	}

	/**
	 * [get_attachment_subtype description]
	 * @method get_attachment_subtype
	 * @param  integer                $post_id [description]
	 * @return [type]                          [description]
	 */
	public function get_attachment_subtype( $post_id = 0 ) {
		return $this->get_attachment_types( $post_id )->subtype;
	}

	/**
	 * [is_attachment_audio description]
	 * @method is_attachment_audio
	 * @param  integer             $post_id [description]
	 * @return boolean                      [description]
	 */
	public function is_attachment_audio( $post_id = 0 ) {
		return 'audio' === $this->get_attachment_type( $post_id );
	}

	/**
	 * [is_attachment_video description]
	 * @method is_attachment_video
	 * @param  integer             $post_id [description]
	 * @return boolean                      [description]
	 */
	public function is_attachment_video( $post_id = 0 ) {
		return 'video' === $this->get_attachment_type( $post_id );
	}

	/**
	 * Function for figuring out if we're viewing a "plural" page.  In WP, these pages after_header
	 * archives, search results, and the home/blog posts index.
	 * @method is_plural
	 * @return boolean          [description]
	 */
	public function is_plural() {
		return ( is_home() || is_archive() || is_search() );
	}

	public function get_vc_custom_css( $id ) {

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			return;
		}

		$out = '';

		if ( ! $id ) {
			return;
		}

		$post_custom_css = get_post_meta( $id, '_wpb_post_custom_css', true );
		if ( ! empty( $post_custom_css ) ) {
			$post_custom_css = strip_tags( $post_custom_css );
			$out .= '<style type="text/css" data-type="vc_custom-css">';
			$out .= $post_custom_css;
			$out .= '</style>';
		}

		$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
			$out .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			$out .= $shortcodes_custom_css;
			$out .= '</style>';
		}

		return $out;
	}

	// 2. Markup Helpers -----------------------------------------------


	public function output_css( $styles = array() ) {

		// If empty return false
		if ( empty( $styles ) ) {
			return false;
		}

		$out = '';
		foreach ( $styles as $key => $value ) {

			if( ! $value ) {
				continue;
			}

			if( is_array( $value ) ) {

				switch( $key ) {

					case 'padding':
					case 'margin':
						$new_value = '';
						foreach( $value as $k => $v ) {

							if( '' != $v ) {
								$out .= sprintf( '%s: %s;', esc_html( $k ), $this->sanitize_unit($v) );
							}
						}
						break;

					default:
						$value = join( ';', $value );
				}
			}
			else {
				$out .= sprintf( '%s: %s;', esc_html( $key ), $value );
			}
		}

		return rtrim( $out, ';' );
	}

	public function sanitize_unit( $value ) {

		if( $this->str_contains( 'em', $value ) || $this->str_contains( 'rem', $value ) || $this->str_contains( '%', $value ) || $this->str_contains( 'px', $value ) ) {
			return $value;
		}

		return $value.'px';
	}

	/**
	 * Check if the string contains the given value.
	 *
	 * @param  string	$needle   The sub-string to search for
	 * @param  string	$haystack The string to search
	 *
	 * @return bool
	 */
    public function str_contains( $needle, $haystack ) {
        return strpos( $haystack, $needle ) !== false;
    }

	/**
	 * [str_starts_with description]
	 * @method str_starts_with
	 * @param  [type]          $needle   [description]
	 * @param  [type]          $haystack [description]
	 * @return [type]                    [description]
	 */
	public function str_starts_with( $needle, $haystack ) {
		return substr( $haystack, 0, strlen( $needle ) ) === $needle;
	}

	/**
	 * [html_attributes description]
	 *
	 * @method html_attributes
	 * @param  array           $attributes [description]
	 *
	 * @return [type]                [description]
	 */
	public function html_attributes( $attributes = array(), $prefix = '' ) {

		// If empty return false
		if ( empty( $attributes ) ) {
			return false;
		}

		$options = false;
		if( isset( $attributes['data-options'] ) ) {
			$options = $attributes['data-options'];
			unset( $attributes['data-options'] );
		}

		$out = '';
		foreach ( $attributes as $key => $value ) {

			if( ! $value ) {
				continue;
			}

			$key = $prefix . $key;
			if( true === $value ) {
				$value = 'true';
			}

			if( false === $value ) {
				$value = 'false';
			}

			if( is_array( $value ) ) {
				$out .= sprintf( ' %s=\'%s\'', esc_html( $key ), json_encode( $value ) );
			}
			else {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
			}
		}

		if( $options ) {
			$out .= sprintf( ' data-options=\'%s\'', $options );
		}

		return $out;
	}

	public function attr( $context, $attributes = array() ) {
		$atts = $this->get_attr( $context, $attributes );
		echo apply_filters( 'liquid_attributes', $atts );
	}

	/**
	 * [get_attr description]
	 * @method get_attr
	 * @param  [type] $context    [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function get_attr( $context, $attributes = array() ) {

		$defaults = array(
			'class' => sanitize_html_class( $context )
		);

		$attributes = wp_parse_args( $attributes, $defaults );
		$attributes = apply_filters( "liquid_attr_{$context}", $attributes, $context );

		$output = $this->html_attributes( $attributes );
	    $output = apply_filters( "liquid_attr_{$context}_output", $output, $attributes, $context );

	    return trim( $output );
	}

	// 3. Option Helpers -----------------------------------------------

	/**
	 * [get_option description]
	 * @method get_option
	 * @param  [type]     $id      [description]
	 * @param  boolean    $default [description]
	 * @param  string     $context [description]
	 * @param  string     $esc     [description]
	 */
    public function get_option_echo( $id, $esc = 'raw', $default = false, $context = 'all' ) {
		$option_value = $this->get_option( $id, $esc, $default, $context );
		echo apply_filters( 'liquid_get_option_echo', $option_value );
    }

	/**
	 * [get_option description]
	 * @method get_option
	 * @param  [type]     $id      [description]
	 * @param  boolean    $default [description]
	 * @param  string     $context [description]
	 * @param  string     $esc     [description]
	 * @return [type]              [description]
	 */
	public function get_option( $id, $esc = 'raw', $default = '', $context = 'all' ) {

		$value = false;
		$keys = explode( '.', $id );
		$id = array_shift( $keys );

		// Get first value from context
		switch( $context ) {

			case 'options':
				$value = $this->get_theme_option( $id );
				break;

			case 'post':
				$value = $this->get_post_meta( $id );
				break;

			default:
				$value = $this->get_post_meta( $id );
				$value = '' != $value ? $value : $this->get_theme_option( $id );
				break;
		}

		// parsing dot notation
		if( ! empty( $keys ) ) {
			foreach( $keys as $inner_key ) {

				if( isset( $value[$inner_key] ) ) {
					$value = $value[$inner_key];
				}
				else {
					break;
				}
			}
		}

		// Set default value if no value
		$value = ! empty( $value ) ? $value : $default;

		// Escape the value
		switch( $esc ) {

			case 'attr':
				$value = esc_attr( $value );
				break;

			case 'url':
				$value = esc_url( $value );
				break;

			case 'html':
				$value = esc_html( $value );
				break;

			case 'post':
				$value = wp_kses_post( $value );
				break;
		}

		// Return default
		return $value;
	}

	/**
	 * [get_post_meta description]
	 * @method get_post_meta
	 * @param  [type]        $id [description]
	 * @return [type]            [description]
	 */
	public function get_post_meta( $id, $post_id = null ) {

		if ( is_null( $post_id ) ) {
			$post_id = $this->get_current_page_id();
		}

		if ( ! $post_id ) {
			return;
		}

		$value = get_post_meta( $post_id, $id, true );
		if( is_array( $value ) ) {
			$value = array_filter($value);

			if( empty( $value ) ) {
				return '';
			}
		}
		return $value ? $value : '';
	}

	public function get_current_page_id() {

		global $post;
		$page_id = false;
		$object_id = is_null($post) ? get_queried_object_id() : $post->ID;

		// If we're on search page, set to false
		if( is_search() ) {
			$page_id = false;
		}
		// If we're not on a singular post, set to false
		if( ! is_singular() ) {
			$page_id = false;
		}
		// Use the $object_id if available
		if( ! is_home() && ! is_front_page() && ! is_archive() && isset( $object_id ) ) {
			$page_id = $object_id;
		}
		// if we're on front-page
		if( ! is_home() && is_front_page() && isset( $object_id ) ) {
			$page_id = $object_id;
		}
		// if we're on posts-page
		if( is_home() && ! is_front_page() ) {
			$page_id = get_option( 'page_for_posts' );
		}
		// The woocommerce shop page
		if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
			if( $shop_page = wc_get_page_id( 'shop' ) ) {
				$page_id = $shop_page;
			}
		}
		// if in the loop
		if( in_the_loop() ) {
			$page_id = get_the_ID();
		}

		return $page_id;
	}
	
	/**
	 * [get_typography_option description]
	 * @method get_typography_option
	 * @param  [type]        $global_opt [description]
	 * @param  [type]        $local_opt [description]
	 * @return [type]            [description]
	 */
	public function get_typography_option( $global_opt = '', $local_opt = '', $key = '' ) {
		
		$default_opt = '';
		if( isset( $local_opt[ $key ] ) && ! empty( $local_opt[ $key ] ) ) {
			
			if( in_array( $key, array( 'line-height', 'letter-spacing' ) ) ) {
				return $local_opt[ $key ];
			}
			elseif( 'color' === $key ) {
				return $local_opt[ $key ] . ' !important';
			}
			else
			{
				return $local_opt[ $key ];	
			}
		}
		elseif( isset( $global_opt[ $key ] ) && ! empty( $global_opt[ $key ] ) ) {

			if( in_array( $key, array( 'line-height', 'letter-spacing' ) ) ) {
				return $global_opt[ $key ];
			}
			elseif( 'color' === $key ) {
				return $global_opt[ $key ] . ' !important';
			}
			else {
				return $global_opt[ $key ];
			}
		}

		return $default_opt;

	}
	
	/**
	 * [get_shadow_css description]
	 * @method get_shadow_css
	 * @return [type]   [description]
	 */
	public function get_shadow_css( $atts = array() ) {
		
		if( empty( $atts ) ){
			return;
		}
		
		$css_arr = array();
		$res_css = $shadow_css = '';
		
		
		foreach( $atts as $att ) {
			$css_arr[] = $this->create_box_shadow_property( $att );
		}
		$shadow_css = join( ', ', $css_arr );
	
		return $shadow_css;
	
	}
	
	/**
	 * [create_box_shadow_property description]
	 * @method create_box_shadow_property
	 * @return [type]   [description]
	 */
	
	public function create_box_shadow_property( $param = array() ) {
		
		$param = array_filter( $param );
		if( empty( $param ) ) {
			return;
		}
		
		$res = '';
	
		$res .= ! empty( $param['inset'] ) ? $param['inset'] . ' ' : '';
		$res .= isset( $param['x_offset'] ) ? $param['x_offset'] . ' ' : '0px ';
		$res .= isset( $param['y_offset'] ) ? $param['y_offset'] . ' ' : '0px ';
		$res .= isset( $param['blur_radius'] ) ? $param['blur_radius'] . ' ' : '0px ';		
		$res .= isset( $param['spread_radius'] ) ? $param['spread_radius'] . ' ' : '0px ';
		$res .= ! empty( $param['shadow_color'] ) ? $param['shadow_color'] : '#000';
		
		return $res;
	}

	/**
	 * Check if woocommerce class exists
	 * @return boolean
	 */

	public function is_woocommerce_active() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}

	/**
	 * [get_theme_option description]
	 * @method get_theme_option
	 * @param  [type]           $id [description]
	 * @return [type]               [description]
	 */
	public function get_theme_option( $id ) {
		
		global $wp_customize;
		global $liquid_options;
		
		if ( $wp_customize ) {
			$options = $liquid_options;
		}
		else {
			$options = $GLOBALS[liquid()->get_option_name()];	
		}

		if( empty( $options ) || ! isset( $options[$id] ) ) {
			return '';
		}

		return $options[$id];
	}

	/**
	 * [dashboard_page_url description]
	 * @method dashboard_page_url
	 * @return [type]             [description]
	 */
	public function dashboard_page_url() {

		if( isset( $_GET['page'] ) && 'liquid' === $_GET['page'] ) {
			return '';
		}
		return admin_url( 'admin.php?page=liquid' );
	}

	/**
	 * [plugin_page_url description]
	 * @method plugin_page_url
	 * @return [type]          [description]
	 */
	public function plugin_page_url() {
		return admin_url( 'admin.php?page=liquid-plugins' );
	}

	/**
	 * [import_demo_url description]
	 * @method import_demos_page_url
	 * @return [type]          [description]
	 */
	public function import_demos_page_url() {
		return admin_url( 'admin.php?page=liquid-import-demos' );
	}

	/**
	 * [active_tab description]
	 * @method active_tab
	 * @return [type]          [description]
	 */	
	public function active_tab( $page ) {

		if( isset( $_GET['page'] ) && $page === $_GET['page'] ) {
			echo 'is-active';
		}
		
	}

	public function liquid_post_date() {

		if ( liquid_helper()->get_theme_option( 'blog-date-format' ) === 'ago' ) {
			return sprintf( esc_html__( '%s ago', 'hub' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
		} else{
			return get_the_date();
		}

	}

	public function liquid_elementor_script_depends() {
		if ( 
			liquid_helper()->get_theme_option( 'enable_optimized_files' ) === 'off' || 
			liquid_helper()->get_theme_option( 'combine_js' ) === 'off' ||
			\Elementor\Plugin::$instance->preview->is_preview_mode() || 
			(liquid_helper()->get_theme_option( 'enable_optimized_files' ) == 'on' && !liquid_helper()->get_assets_cache(liquid_helper()->get_page_id_by_url()) )
		) { return true; } else { return false; }
	}

	public function get_page_id_by_url() {
		
		global $wp;

		$url = add_query_arg( $wp->request, home_url() );
		if ( !empty( site_url( '', 'relative' ) ) ) {
			$url_parts = parse_url( home_url() );
			$url = $url_parts['scheme'] . "://" . $url_parts['host'] . add_query_arg( NULL, NULL );
		}
		if ( '/?' == substr( $url, 0, 2) ) {
			$url = home_url();
		}
		$post_id = url_to_postid( $url );

		return $post_id;
		
	}
	
	public function is_page_elementor(){

		global $wp;

		if ( class_exists( 'Liquid_Elementor_Addons' ) && defined( 'ELEMENTOR_VERSION' ) ){
			
			// check archive and woocommerce pages
			if ( is_archive() || is_search() || is_404() || get_post_type( $this->get_page_id_by_url() ) == 'product' ||
			class_exists('WooCommerce') && is_product() || class_exists('WooCommerce') && is_shop() ){
				return false;
			}

			// check preview mode
			if ( substr( home_url( add_query_arg( $_GET, $wp->request ) ), -12 ) === 'preview=true' ){
				return false;
			}

			// check blog posts page
			if ( get_option('page_for_posts') == $this->get_page_id_by_url() || $this->get_page_id_by_url() == 0 ){
				return false;
			}

			// check css file
			/*
			if ( $this->get_page_id_by_url() > 0 ){
				$path = wp_upload_dir()['basedir'] . '/liquid-styles/liquid-merged-styles-' . $this->get_page_id_by_url() . '.css';
				
				if ( file_exists($path) && filesize( $path ) < 1 ){

					if ( $this->get_assets_cache( $this->get_page_id_by_url() ) ) {
						return false;
					}
				}
			}
			*/
	
			// check elementor
			$document = \Elementor\Plugin::$instance->documents->get( $this->get_page_id_by_url() );

			if ( ! $document ) {
				return false;
			}
			
			if ( $document->is_built_with_elementor() ) {

				if ( ! $this->get_assets_cache( $this->get_page_id_by_url() ) ) {
					return false;
				}

				return true;
			}
			
		}


		return false;
	}

	public function get_assets_cache( $post_id ) {

		$get_cache = get_option( 'liquid_assets_cache' );

		if ( is_array( $get_cache ) ){
			if ( in_array( $post_id, $get_cache ) ){     
				return true;
			}
		}

		return false;

	}

	public function purge_assets_cache( $post_id ) {

		if ( $post_id === true ){ // if post_id is true, purge all cache
			update_option( 'liquid_assets_cache', array() );
			if ( is_array( scandir( wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . 'liquid-styles' ) ) ){
				foreach ( array_diff(scandir( wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . 'liquid-styles' ), array('.', '..')) as $file ){ // find all files in uploads/liquid-styles
					wp_delete_file( wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . 'liquid-styles' . DIRECTORY_SEPARATOR . $file ); // delete all files
				}
			}
		} else { // purge cache by post_id
			$get_cache = get_option( 'liquid_assets_cache' );
			if ( is_array( $get_cache ) ){
				if (($key = array_search($post_id, $get_cache)) !== false) {
					unset($get_cache[$key]);
					update_option( 'liquid_assets_cache', $get_cache, 'yes' );
					wp_delete_file( wp_upload_dir()['basedir'] . '/liquid-styles/liquid-merged-styles-' . $post_id . '.css' ); // delete css file
					wp_delete_file( wp_upload_dir()['basedir'] . '/liquid-styles/liquid-merged-scripts-' . $post_id . '.js' ); // delete js file
				}
			}
		}

	}

	public function hub_ai_btn() {
		return '<div class="hub-ai-el-action" data-action="code">Hub AI</div>';
	}

	public function shape_cutout_the_content( $content, $ids ){

		if ( empty( $content ) || count( $ids ) == 0 ) {
			return $content;
		}

		libxml_use_internal_errors( true ); // Hide errors for HTML5
		$doc = new DOMDocument();
		$fix_chartset = mb_convert_encoding($content, 'HTML-ENTITIES', "UTF-8");
		@$doc->loadHTML($fix_chartset);

		$xpath = new DOMXPath( $doc ); // Create a new xpath object

		// Loop through each ID and HTML to be added
		foreach( $ids as $id => $html ) {
			$element = $xpath->query('//div[@data-id="' . $id . '"]')->item(0);
			if ($element) {
				// Find the inner div element to append to
				// TODO: Check .e-con-inner reason.
				$innerElement = $xpath->query('.//div[@class="e-con-inner"]', $element)->item(0);
				if (!$innerElement) {
					// If there is no inner div with class "e-con-inner", use the outer div
					$innerElement = $element;
				}
				// Create a new document fragment for the HTML to be added
				$newElement = $doc->createDocumentFragment();
				$newElement->appendXML($html);
				// Insert the new element before the first child of the inner div element
				$innerElement->insertBefore($newElement, $innerElement->firstChild);
			}
		}

		$content = $doc->saveHTML();
		return $content;

	}

}

/**
 * Main instance of Liquid_Helper.
 *
 * Returns the main instance of Liquid_Helper to prevent the need to use globals.
 *
 * @return Liquid_Helper
 */
function liquid_helper() {
	return Liquid_Helper::instance();
}
