<?php
/**
 * Customizer settings for this theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

if ( ! class_exists( 'tsukiko_Customize' ) ) {
	/**
	 * CUSTOMIZER SETTINGS
	 */
	class tsukiko_Customize {

		/**
		 * Register customizer options.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public static function register( $wp_customize ) {





			/**
			 * Site Title & Description.
			 * */
			$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
			$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title a',
					'render_callback' => 'tsukiko_customize_partial_blogname',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => 'tsukiko_customize_partial_blogdescription',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'custom_logo',
				array(
					'selector'        => '.header-titles [class*=site-]:not(.site-description)',
					'render_callback' => 'tsukiko_customize_partial_site_logo',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'retina_logo',
				array(
					'selector'        => '.header-titles [class*=site-]:not(.site-description)',
					'render_callback' => 'tsukiko_customize_partial_site_logo',
				)
			);

			/**
			 * Site Identity
			 */

			/* 2X Header Logo ---------------- */
			$wp_customize->add_setting(
				'retina_logo',
				array(
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'retina_logo',
				array(
					'type'        => 'checkbox',
					'section'     => 'title_tagline',
					'priority'    => 10,
					'label'       => __( 'Retina logo', 'tsukiko' ),
					'description' => __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'tsukiko' ),
				)
			);

			// Header & Footer Background Color.
			$wp_customize->add_setting(
				'header_footer_background_color',
				array(
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'header_footer_background_color',
					array(
						'label'   => __( 'Header &amp; Footer Background Color', 'tsukiko' ),
						'section' => 'colors',
					)
				)
			);

			// Enable picking an accent color.
			$wp_customize->add_setting(
				'accent_hue_active',
				array(
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
					'transport'         => 'postMessage',
					'default'           => 'default',
				)
			);

			$wp_customize->add_control(
				'accent_hue_active',
				array(
					'type'    => 'radio',
					'section' => 'colors',
					'label'   => __( 'Primary Color', 'tsukiko' ),
					'choices' => array(
						'default' => __( 'Default', 'tsukiko' ),
						'custom'  => __( 'Custom', 'tsukiko' ),
					),
				)
			);

			/**
			 * Implementation for the accent color.
			 * This is different to all other color options because of the accessibility enhancements.
			 * The control is a hue-only colorpicker, and there is a separate setting that holds values
			 * for other colors calculated based on the selected hue and various background-colors on the page.
			 *
			 * @since Twenty Twenty 1.0
			 */

			// Add the setting for the hue colorpicker.
			$wp_customize->add_setting(
				'accent_hue',
				array(
					'default'           => 344,
					'type'              => 'theme_mod',
					'sanitize_callback' => 'absint',
					'transport'         => 'postMessage',
				)
			);

			// Add setting to hold colors derived from the accent hue.
			$wp_customize->add_setting(
				'accent_accessible_colors',
				array(
					'default'           => array(
						'content'       => array(
							'text'      => '#000000',
							'accent'    => '#cd2653',
							'secondary' => '#6d6d6d',
							'borders'   => '#dcd7ca',
						),
						'header-footer' => array(
							'text'      => '#000000',
							'accent'    => '#cd2653',
							'secondary' => '#6d6d6d',
							'borders'   => '#dcd7ca',
						),
					),
					'type'              => 'theme_mod',
					'transport'         => 'postMessage',
					'sanitize_callback' => array( __CLASS__, 'sanitize_accent_accessible_colors' ),
				)
			);

			// Add the hue-only colorpicker for the accent color.
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'accent_hue',
					array(
						'section'         => 'colors',
						'settings'        => 'accent_hue',
						'description'     => __( 'Apply a custom color for links, buttons, featured images.', 'tsukiko' ),
						'mode'            => 'hue',
						'active_callback' => function() use ( $wp_customize ) {
							return ( 'custom' === $wp_customize->get_setting( 'accent_hue_active' )->value() );
						},
					)
				)
			);

			// Update background color with postMessage, so inline CSS output is updated as well.
			$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';

			/**
			 * Theme Options
			 */

			$wp_customize->add_section(
				'options',
				array(
					'title'      => __( 'Theme Options', 'tsukiko' ),
					'priority'   => 40,
					'capability' => 'edit_theme_options',
				)
			);

			/* Enable Header Search ----------------------------------------------- */

			$wp_customize->add_setting(
				'enable_header_search',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'enable_header_search',
				array(
					'type'     => 'checkbox',
					'section'  => 'options',
					'priority' => 10,
					'label'    => __( 'Show search in header', 'tsukiko' ),
				)
			);

			/* Show author bio ---------------------------------------------------- */

			$wp_customize->add_setting(
				'show_author_bio',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
				)
			);

			$wp_customize->add_control(
				'show_author_bio',
				array(
					'type'     => 'checkbox',
					'section'  => 'options',
					'priority' => 10,
					'label'    => __( 'Show author bio', 'tsukiko' ),
				)
			);

			/* Display full content or excerpts on the blog and archives --------- */

			$wp_customize->add_setting(
				'blog_content',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => 'full',
					'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
				)
			);

			$wp_customize->add_control(
				'blog_content',
				array(
					'type'     => 'radio',
					'section'  => 'options',
					'priority' => 10,
					'label'    => __( 'On archive pages, posts show:', 'tsukiko' ),
					'choices'  => array(
						'full'    => __( 'Full text', 'tsukiko' ),
						'summary' => __( 'Summary', 'tsukiko' ),
					),
				)
			);

			/**
			 * Template: Cover Template.
			 */
			$wp_customize->add_section(
				'cover_template_options',
				array(
					'title'       => __( 'Cover Template', 'tsukiko' ),
					'capability'  => 'edit_theme_options',
					'description' => __( 'Settings for the "Cover Template" page template. Add a featured image to use as background.', 'tsukiko' ),
					'priority'    => 42,
				)
			);

			/* Overlay Fixed Background ------ */

			$wp_customize->add_setting(
				'cover_template_fixed_background',
				array(
					'capability'        => 'edit_theme_options',
					'default'           => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_checkbox' ),
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'cover_template_fixed_background',
				array(
					'type'        => 'checkbox',
					'section'     => 'cover_template_options',
					'label'       => __( 'Fixed Background Image', 'tsukiko' ),
					'description' => __( 'Creates a parallax effect when the visitor scrolls.', 'tsukiko' ),
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'cover_template_fixed_background',
				array(
					'selector' => '.cover-header',
					'type'     => 'cover_fixed',
				)
			);

			/* Separator --------------------- */

			$wp_customize->add_setting(
				'cover_template_separator_1',
				array(
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				)
			);

			$wp_customize->add_control(
				new tsukiko_Separator_Control(
					$wp_customize,
					'cover_template_separator_1',
					array(
						'section' => 'cover_template_options',
					)
				)
			);

			/* Overlay Background Color ------ */

			$wp_customize->add_setting(
				'cover_template_overlay_background_color',
				array(
					'default'           => tsukiko_get_color_for_area( 'content', 'accent' ),
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cover_template_overlay_background_color',
					array(
						'label'       => __( 'Overlay Background Color', 'tsukiko' ),
						'description' => __( 'The color used for the overlay. Defaults to the accent color.', 'tsukiko' ),
						'section'     => 'cover_template_options',
					)
				)
			);

			/* Overlay Text Color ------------ */

			$wp_customize->add_setting(
				'cover_template_overlay_text_color',
				array(
					'default'           => '#ffffff',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cover_template_overlay_text_color',
					array(
						'label'       => __( 'Overlay Text Color', 'tsukiko' ),
						'description' => __( 'The color used for the text in the overlay.', 'tsukiko' ),
						'section'     => 'cover_template_options',
					)
				)
			);

			/* Overlay Color Opacity --------- */

			$wp_customize->add_setting(
				'cover_template_overlay_opacity',
				array(
					'default'           => 80,
					'sanitize_callback' => 'absint',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'cover_template_overlay_opacity',
				array(
					'label'       => __( 'Overlay Opacity', 'tsukiko' ),
					'description' => __( 'Make sure that the contrast is high enough so that the text is readable.', 'tsukiko' ),
					'section'     => 'cover_template_options',
					'type'        => 'range',
					'input_attrs' => tsukiko_customize_opacity_range(),
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'cover_template_overlay_opacity',
				array(
					'selector' => '.cover-color-overlay',
					'type'     => 'cover_opacity',
				)
			);

			$wp_customize->add_setting( 'tsukiko_cover_page_height', array(
				'default' 			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback' => array( __CLASS__, 'sanitize_select' ),
			) );

			$wp_customize->add_control( 'tsukiko_cover_page_height', array(
				'label'			=> __( 'Page Cover Height', 'tsukiko' ),
				'section'		=> 'cover_template_options',
				'type'			=> 'select',
				'choices'		=> array(
					'medium'	=> __( 'Medium', 'tsukiko' ),
					''			=> __( 'Full', 'tsukiko' ),
				),
			) );

			$wp_customize->add_panel( 'tsukiko_theme_options_panel', array(
				'title' 	=> __( 'Tsukiko Theme Options', 'tsukiko' ),
				'priority'	=> 150,
			) );

			$wp_customize->add_section(
				'tsukiko_theme_options',
				array(
					'title'      => __( 'Blog', 'tsukiko' ),
					'panel'		 => 'tsukiko_theme_options_panel',
					'priority'   => 40,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting( 'tsukiko_post_excerpt', array(
				'default' 			=> 1,
				'sanitize_callback' => 'absint',
			) );

			$wp_customize->add_control( 'tsukiko_post_excerpt', array(
				'label'		=> __( 'Display manual excerpt below the title', 'tsukiko' ),
				'section'	=> 'tsukiko_theme_options',
				'type'		=> 'checkbox',
				'priority' 	=> 35,
			) );

			$wp_customize->add_section(
				'tsukiko_site_layout_options',
				array(
					'title'      => __( 'Site Layout', 'tsukiko' ),
					'panel'		 => 'tsukiko_theme_options_panel',
					'priority'   => 40,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting( 'tsukiko_text_width', array(
				'default'			=> '',
				'transport'			=> 'postMessage',
				'sanitize_callback'	=> array( __CLASS__, 'sanitize_select' ),
			) );

			$wp_customize->add_control( 'tsukiko_text_width', array(
				'label'			=> __( 'Text Width', 'tsukiko' ),
				'section'		=> 'tsukiko_site_layout_options',
				'type'			=> 'radio',
				'choices'		=> array(
					''			=> __( 'Narrow (Default)', 'tsukiko' ),
					'medium'	=> __( 'Medium', 'tsukiko' ),
					'wide'		=> __( 'Wide', 'tsukiko' ),
				),
			) );
		}


		/**
		 * Sanitization callback for the "accent_accessible_colors" setting.
		 *
		 * @static
		 * @access public
		 * @since Twenty Twenty 1.0
		 * @param array $value The value we want to sanitize.
		 * @return array       Returns sanitized value. Each item in the array gets sanitized separately.
		 */
		public static function sanitize_accent_accessible_colors( $value ) {

			// Make sure the value is an array. Do not typecast, use empty array as fallback.
			$value = is_array( $value ) ? $value : array();

			// Loop values.
			foreach ( $value as $area => $values ) {
				foreach ( $values as $context => $color_val ) {
					$value[ $area ][ $context ] = sanitize_hex_color( $color_val );
				}
			}

			return $value;
		}

		/**
		 * Sanitize select.
		 *
		 * @param string $input The input from the setting.
		 * @param object $setting The selected setting.
		 *
		 * @return string $input|$setting->default The input from the setting or the default setting.
		 */
		public static function sanitize_select( $input, $setting ) {
			$input   = sanitize_key( $input );
			$choices = $setting->manager->get_control( $setting->id )->choices;
			return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
		}

		/**
		 * Sanitize boolean for checkbox.
		 *
		 * @param bool $checked Whether or not a box is checked.
		 *
		 * @return bool
		 */
		public static function sanitize_checkbox( $checked ) {
			return ( ( isset( $checked ) && true === $checked ) ? true : false );
		}

		/**
		 * Sanitize fonts choices (allow uppercase and space chars).
		 */
		public static function tsukiko_sanitize_fonts( $choice, $setting ) {
			$choices = $setting->manager->get_control( $setting->id )->choices;
			return ( array_key_exists( $choice, $choices ) ? $choice : $setting->default );
		}

	}

	// Setup the Theme Customizer settings and controls.
	add_action( 'customize_register', array( 'tsukiko_Customize', 'register' ) );

}

/**
 * PARTIAL REFRESH FUNCTIONS
 * */
if ( ! function_exists( 'tsukiko_customize_partial_blogname' ) ) {
	/**
	 * Render the site title for the selective refresh partial.
	 */
	function tsukiko_customize_partial_blogname() {
		bloginfo( 'name' );
	}
}

if ( ! function_exists( 'tsukiko_customize_partial_blogdescription' ) ) {
	/**
	 * Render the site description for the selective refresh partial.
	 */
	function tsukiko_customize_partial_blogdescription() {
		bloginfo( 'description' );
	}
}

if ( ! function_exists( 'tsukiko_customize_partial_site_logo' ) ) {
	/**
	 * Render the site logo for the selective refresh partial.
	 *
	 * Doing it this way so we don't have issues with `render_callback`'s arguments.
	 */
	function tsukiko_customize_partial_site_logo() {
		tsukiko_site_logo();
	}
}


/**
 * Input attributes for cover overlay opacity option.
 *
 * @return array Array containing attribute names and their values.
 */
function tsukiko_customize_opacity_range() {
	/**
	 * Filter the input attributes for opacity
	 *
	 * @param array $attrs {
	 *     The attributes
	 *
	 *     @type int $min Minimum value
	 *     @type int $max Maximum value
	 *     @type int $step Interval between numbers
	 * }
	 */
	return apply_filters(
		'tsukiko_customize_opacity_range',
		array(
			'min'  => 0,
			'max'  => 90,
			'step' => 5,
		)
	);
}

function tsukiko_body_class(array $classes)
{
	if ( $text_width = get_theme_mod( 'tsukiko_text_width' ) ) {
		$classes[] = 'tsukiko-text-width-' . $text_width;
	}

	if (is_page() || is_single()) {
		if ( is_page_template( 'templates/template-cover.php' ) ) {
			if ( $cover_height = get_theme_mod( 'tsukiko_cover_page_height' ) ) {
				$classes[] = 'tsukiko-cover-' . $cover_height;
			}
		}
	}

	return $classes;
}

add_filter( 'body_class',  'tsukiko_body_class', 11 );

/**
 * Hide excerpt on single post.
 */
function tsukiko_remove_excerpt_single_post( $slug, $name = null ) {
	if ( is_single() && ! get_theme_mod( 'tsukiko_post_excerpt', true ) ) {
		add_filter( 'the_excerpt', '__return_empty_string' );
	}
}
add_action( 'get_template_part_template-parts/entry-header', 'tsukiko_remove_excerpt_single_post', 10, 2 );
add_action( 'get_template_part_template-parts/content-cover', 'tsukiko_remove_excerpt_single_post', 10, 2 );
