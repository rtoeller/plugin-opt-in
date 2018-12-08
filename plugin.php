<?php
	/**
	 * Plugin Name:  OptIn Content
	 * Plugin URI:
	 * Description: This plugin sets overlays about content that should be shown after approval
	 * Version: 0.2.10
	 * Author: Rebecca Töller
	 * License: GPL2
	 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
	 * Text Domain:  hallo-welt-plugin
	 */


    $optin_content_pfad = plugin_dir_path( __FILE__ );

    require_once( $optin_content_pfad . 'include/optin_content_backend_menu.php' );
    require_once( $optin_content_pfad . 'include/optin_content_func_database.php' );

    /*
     * This function do plugin settings in the backend menu
     */
    function optin_content_add_menu() {
        add_menu_page( 'Add Content', 'OptIn Content', 'manage_options', 'optin_content', 'optin_content_backend_menu' );
        add_submenu_page( 'optin_content', 'Add Content', 'All Content Boxes', 'manage_options', 'optin_content' );
        add_submenu_page( 'optin_content', 'Overlays', 'Overlays', 'manage_options', 'optin_overlay', 'optin_overlay_backend_menu' );
    }

    add_action( 'admin_menu', 'optin_content_add_menu' );

    /*
     * This function create a shortcode for iframes
     */
    function optin_content_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'id' => '',
                'name' => ''
            ), $atts );

        $id = $atts['id'];
        $name = $atts['name'];

        $content = do_overlay($id, $name);

        return $content;
    }

    add_shortcode( 'optin_content', 'optin_content_shortcode' );

    /*
     * Bind jQuery from wp core
     */
    function wp_bind_jquery() {
        wp_enqueue_script('jquery');
    }
    add_action('wp_enqueue_scripts', 'wp_bind_jquery');


    /*
     * JS-data will load in backend and frontend
     */
    function wptuts_scripts_basic() {
        wp_register_script( 'custom-script', plugins_url( '/assets/js/loadContent.js', __FILE__ ) );
        wp_enqueue_script( 'custom-script' );
    }

    add_action( 'wp_enqueue_scripts', 'wptuts_scripts_basic' );
    add_action( 'admin_enqueue_scripts', 'wptuts_scripts_basic' );

    /*
     * CSS-data will load in backend and frontend
     */
    function register_plugin_styles() {
        wp_register_style( 'css', plugins_url( '/assets/css/style.css', __FILE__ ) );
        wp_enqueue_style( 'css' );
    }

    add_action( 'wp_enqueue_scripts', 'register_plugin_styles' );
    add_action( 'admin_enqueue_scripts', 'register_plugin_styles' );


    /*
     * Create colorpicker
    */
    add_action( 'admin_enqueue_scripts', 'mein_admin_print_scripts' );

    function mein_admin_print_scripts(){
        wp_enqueue_script( 'mm_editor_settings', plugins_url( 'assets/js/wp_color_picker.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ) );
    }

    /*
     * This function set image for overlay
     */
    function get_image_for_overlay( $attachment_id, $size) {
        $image = wp_get_attachment_image( $attachment_id, $size, 0, '');
        $src = wp_get_attachment_image_url( $attachment_id, $size, 0, '');

        if($size == 'full') {
            return $src;
        }
        if( $attachment_id == 0 ) {
            $image = '<img width="150" height="150" src="" class="attachment-thumbnail size-thumbnail" alt="" srcset="" sizes="(max-width: 150px) 85vw, 150px">';
            return $image;
        }
        return $image;
    }

	/*
     * In this function is the overlay. This is loading in the shortcode
     */
	function do_overlay( $id, $name ) {
		global $wpdb;
		if( $name ) {
            $result_content_box = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content where contentbox_name =' . $name );
            $get_data_overlay = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content_overlay where  =' . $id);
        }
        else {
            $result_content_box = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content where id =' . $id );
            $get_data_overlay = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content_overlay where id =' . $id);
        }


        $optin_content = $result_content_box[0]->contentbox_code;
		$attr_style = search_size ( $optin_content, '="', 'style' );
		//echo 'STYLE '. $attr_style;
		if( $get_data_overlay[0]->overlay_color != "" ) {
            $css_overlay =   'background-color: '.$get_data_overlay[0]->overlay_color.';';
        }

        // get height and width for overlay
        $overlay_height = "";
        $overlay_width = "";
		if ( $attr_style!= '' ){
            $args = explode(';', $attr_style);
            foreach ( $args as $arg ){
                $overlay_height = (!$overlay_height ? search_style_element($arg, 'height') : $overlay_height);
                $overlay_width = (!$overlay_width ? search_style_element($arg, 'width') : $overlay_width);
            }
		}

		$height_overlay_db = $get_data_overlay[0]->height;

		if($height_overlay_db) {
            $css_overlay .= 'height: '.$height_overlay_db.(is_numeric($height_overlay_db) ? 'px' : '').';';
        } else {
            if(!$overlay_height) {
                $overlay_height = searchSize ( $optin_content, '="', 'height' );
            }
            $css_overlay .= (!$overlay_height ? '' : 'height: '.$overlay_height.';');
        }

        $width_overlay = $get_data_overlay[0]->width;
        if($width_overlay) {
            $css_overlay .= 'width: '.$width_overlay.(is_numeric($width_overlay) ? 'px' : '').';';
        } else {
            if (!$overlay_width) {
                $overlay_width = searchSize($optin_content, '="', 'width');
            }
            $css_overlay .= (!$overlay_width ? '' : 'width: ' . $overlay_width . ';');
        }

        if($get_data_overlay[0]->image_id != 0){
            $image_src = get_image_for_overlay($get_data_overlay[0]->image_id, $get_data_overlay[0]->image_size);

            if( $get_data_overlay[0]->image_size == 'full' ) {
                $css_overlay .= 'background-image: url('.$image_src.');
                            background-repeat: no-repeat;
                            background-size: auto 100%;
                            background-position: center center;';
            }
        }

		if($get_data_overlay[0]->display_text == 1) {
			$overlay_text = $get_data_overlay[0]->overlay_text;
		}
		if($get_data_overlay[0]->datenschutz_on_or_off == 1) {
			$datenschutz_button = '<button><a href="'.get_the_permalink($get_data_overlay[0]->datenschutz).'">Datenschutzerklärung</a></button>';
		}

        $html_overlay = '
            <div><div id="overlay'.$id.'" class="my-overlay" style="'.$css_overlay.'">
                <div class="my-overlay-inner">
                    '.( $get_data_overlay[0]->image_size == 'full' ? '' : $image_src ).'
				    <input type="hidden" value="'.$id.'" name="overlay_content_id" />
				    <div class="overlayText">
				    '.$overlay_text.'
                    </div>
                <button class="button laden" value="'.$id.'">'.$get_data_overlay[0]->button_text.'</button>
                '.$datenschutz_button.'
				</div>
            </div></div>';
        return $html_overlay;
    }

    function search_style_element($arg, $element) {
        if ( strpos ( $arg, $element ) !== false ) {
            return search_size($arg, ':', $element);
        }
        return "";
    }

	/*
	 * This function find height and width from overlay
	 */
    function search_size($string, $searchChar, $searchArg){
        $string = str_replace('>', '', $string);
        $string = str_replace('<', '', $string);

        $pieces = explode($searchChar, $string);

	    $i = 0;

	    foreach ($pieces as $piece){
		    $searchString = strpos($piece, $searchArg);

		    if($searchString !== false){
			    $findSize = $pieces[$i+1];
			    $findSize = explode('"', $findSize);
			    $searchSize =  $findSize[0];
		    }

		    $i++;
	    }

	    if(is_numeric($searchSize)){
	        $searchSize .= 'px';
        }

	    return $searchSize;
	}



?>