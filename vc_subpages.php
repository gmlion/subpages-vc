<?php
/*
Plugin Name: Subpages List (vc)
Plugin URI: http://wpbakery.com/vc
Description: Show a list of subpages
Version: 0.1
Author: Gianmarco Leone
Author URI: http://wpbakery.com
License: GPLv2 or later
Icons made by Stephen Hutchings from www.flaticon.com is licensed by CC BY 3.0
*/

/*

*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCSubpagesAddonClass {
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
        // Use this when creating a shortcode addon
        add_shortcode( 'bartag', array( $this, 'renderMyBartag' ) );

        // Register CSS and JS
        add_action( 'wp_enqueue_scripts', array( $this, 'loadCssAndJs' ) );
    }
 
    public function integrateWithVC() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }
 
        /*
        Add your Visual Composer logic here.
        Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */
        /*vc_map( array(
            "name" => __("My Bar Shortcode", 'vc_subpages'),
            "description" => __("Bar tag description text", 'vc_subpages'),
            "base" => "bartag",
            "class" => "",
            "controls" => "full",
            "icon" => plugins_url('assets/asterisk_yellow.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_subpages_my_class"
            "category" => __('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_subpages.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_subpages_admin.css', __FILE__)), // This will load css file in the VC backend editor
            "params" => array(
                array(
                  "type" => "textfield",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Text", 'vc_subpages'),
                  "param_name" => "foo",
                  "value" => __("Default params value", 'vc_subpages'),
                  "description" => __("Description for foo param.", 'vc_subpages')
              ),
              array(
                  "type" => "colorpicker",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Text color", 'vc_subpages'),
                  "param_name" => "color",
                  "value" => '#FF0000', //Default Red color
                  "description" => __("Choose text color", 'vc_subpages')
              ),
              array(
                  "type" => "textarea_html",
                  "holder" => "div",
                  "class" => "",
                  "heading" => __("Content", 'vc_subpages'),
                  "param_name" => "content",
                  "value" => __("<p>I am test text block. Click edit button to change this text.</p>", 'vc_subpages'),
                  "description" => __("Enter your content.", 'vc_subpages')
              ),
            )
        ) );*/
        vc_map( array(
            "name" => __("Subpages List", 'vc_subpages'),
            "description" => __("Show a list of all subpages", 'vc_subpages'),
            "base" => "bartag",
            "class" => "vc_subpages",
            "controls" => "full",
            "icon" => plugins_url('assets/th.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "vc_subpages_my_class"
            "category" => __('Content', 'js_composer'),
            //'admin_enqueue_js' => array(plugins_url('assets/vc_subpages.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('assets/vc_subpages_admin.css', __FILE__)), // This will load css file in the VC backend editor
            ) );
    }
    
    /*
    Shortcode logic how it should be rendered
    */
    public function renderMyBartag( $atts, $content = null ) {
      /*extract( shortcode_atts( array(
        'foo' => 'something',
        'color' => '#FF0000'
      ), $atts ) );
      $content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content
     
      $output = "<div style='color:{$color};' data-foo='${foo}'>{$content}</div>";
      return $output;*/
      
      /*query for child pages*/
      global $post;
        
      query_posts(array(
          'post_parent' => $post->ID,
          'post_type' => 'page',
          'posts_per_page' => -1 
      ));
      
      if ( have_posts() ) {
          
          $output = '<ul class="vc_subpages">';
          
          while( have_posts() ) {
              
              the_post();
              
              $output .=  '<li><a href="' . get_page_link() . '">' . get_the_title() . '</a></li>';
              
              
              
          }
          
          $output .= '</ul>';
          
          wp_reset_query();
          
      }
      
      
      
      return $output;
    }

    /*
    Load plugin css and javascript files which you may need on front end of your site
    */
    public function loadCssAndJs() {
      wp_register_style( 'vc_subpages_style', plugins_url('assets/vc_subpages.css', __FILE__) );
      wp_enqueue_style( 'vc_subpages_style' );

      // If you need any javascript files on front end, here is how you can load them.
      //wp_enqueue_script( 'vc_subpages_js', plugins_url('assets/vc_subpages.js', __FILE__), array('jquery') );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
          <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_subpages'), $plugin_data['Name']).'</p>
        </div>';
    }
}
// Finally initialize code
new VCSubpagesAddonClass();