
		function get_copyright($echo=false){
			$copyText = get_theme_mod('copyright_text');
			if($echo==true){
				echo $copyText;
				}else{
				return $copyText;
			}
		}
		function is_slider(){
			$slider = get_theme_mod('home_slider');
			if($slider=='true'){
				return true;
				}else{
				return false;
			}
		}
		
		class MyTheme_Customize {
			function __construct() {
				// Setup the Theme Customizer settings and controls...
				add_action( 'customize_register' , array($this, 'register' ) );
				
				// Output custom CSS to live site
				add_action( 'wp_head' , array($this , 'header_output' ) );
				
				// Enqueue live preview javascript in Theme Customizer admin screen
				add_action( 'customize_preview_init' , array($this, 'live_preview' ) );
			}
			
			public static function register ( $wp_customize ) {
				//1. Define a new section (if desired) to the Theme Customizer
				
				$wp_customize->add_section( 'mytheme_options', 
				array(
				'title'       => __( 'Theme Options', 'mytheme' ), //Visible title of section
				'priority'    => 100, //Determines what order this appears in
				'capability'  => 'edit_theme_options', //Capability needed to tweak
				'description' => __('To Customize some of  Theme settings.', 'mytheme'), //Descriptive tooltip
				) 
				);
				
				//2. Register new settings to the WP database...
				$wp_customize->add_setting( 'link_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
				'default'    => '#2BA6CB', //Default setting/value to save
				'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
				'transport'  => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				) 
				);      
				
				//2. Register new settings to the WP database...
				$wp_customize->add_setting( 'copyright_text', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
				'default'    => '', //Default setting/value to save
				'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				) 
				);      				
				//2. Register new settings to the WP database...
				$wp_customize->add_setting( 'home_slider', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
				'default'    => 'false', //Default setting/value to save
				'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				) 
				);    
				//2. Register Another settings to the WP database...
				$wp_customize->add_setting( 'container_width', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
				'default'    => '970px', //Default setting/value to save
				'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
				'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
				'transport'  => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				) 
				);      
				
				$wp_customize->add_control('mytheme_content_width', array(
				'label' => __( 'Width', 'mytheme' ),
				'type' => 'text',
				'settings'   => 'container_width',
				'section' => 'mytheme_options',
				'description' => __( 'Container width in Pixel', 'mytheme' ),
				));
				
				$wp_customize->add_control('mytheme_home_slider', array(
				'label' => __( 'Home Slider', 'mytheme' ),
				'type' => 'select',
				'settings'   => 'home_slider',
				'section' => 'mytheme_options',
				'choices'  => array(
				'true'  => 'True',
				'false' => 'False',
				),
				));
				
				$wp_customize->add_control('mytheme_copyright', array(
				'label' => __( 'Copyright', 'mytheme' ),
				'type' => 'text',
				'settings'   => 'copyright_text',
				'section' => 'title_tagline',
				'description' => __( 'Add Copyright Text', 'mytheme' ),
				));
				
				//3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
				$wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
				$wp_customize, //Pass the $wp_customize object (required)
				'mytheme_link_textcolor', //Set a unique ID for the control
				array(
				'label'      => __( 'Link Color', 'mytheme' ), //Admin-visible name of the control
				'settings'   => 'link_textcolor', //Which setting to load and manipulate (serialized is okay)
				'priority'   => 10, //Determines the order this control appears in for the specified section
				'section'    => 'mytheme_options', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
				) 
				) );
				
				//4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
				$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
				$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
				$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
				$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
			}
			
			public static function header_output() {
			?>
			<!--Customizer CSS--> 
			<style type="text/css">
				<?php self::generate_css('#site-title a', 'color', 'header_textcolor', '#'); ?> 
				<?php self::generate_css('body', 'background-color', 'background_color', '#'); ?> 
				<?php self::generate_css('a', 'color', 'link_textcolor'); ?>
				<?php self::generate_css('.wrapper', 'max-width', 'container_width'); ?>
			</style> 
			<!--/Customizer CSS-->
			<?php
			}
			public static function live_preview() {
				wp_enqueue_script('mytheme-themecustomizer', // Give the script a unique ID
				get_template_directory_uri() . '/js/theme-customizer.js', // Define the path to the JS file
				array(  'jquery', 'customize-preview' ), // Define dependencies
				'', // Define a version (optional) 
				true // Specify whether to put in footer (leave this true)
				);
			}
			public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
				$return = '';
				$mod = get_theme_mod($mod_name);
				if ( ! empty( $mod ) ) {
					$return = sprintf('%s { %s:%s; }',
					$selector,
					$style,
					$prefix.$mod.$postfix
					);
					if ( $echo ) {
						echo $return;
					}
				}
				return $return;
			}
		}
		$Mycustomize = new MyTheme_Customize();
