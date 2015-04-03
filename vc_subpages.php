<?php
/*
Plugin Name: Subpages List (vc)
Plugin URI: http://wpbakery.com/vc
Description: Show a list of subpages
Version: 0.1
Author: Gianmarco Leone
Author URI: http://wpbakery.com
License: GPLv2 or later
Icons made by Stephen Hutchings from www.flaticon.com and licensed by CC BY 3.0
*/

/*

*/

// don't load directly
if (!defined('ABSPATH')) die('-1');

class VCSubpagesAddonClass {
    function __construct() {
        // VC integration
        add_action( 'init', array( $this, 'integrateWithVC' ) );
 
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
 
        
        vc_map( array(
            "name" => __("Subpages List", 'vc_subpages'),
            "description" => __("Show a list of all subpages", 'vc_subpages'),
            "base" => "bartag",
            "class" => "vc_subpages",
            "controls" => "full",
            "icon" => plugins_url('assets/th.png', __FILE__),
            "category" => __('Content', 'js_composer'),
            ) );
    }
    
    /*
    Shortcode logic
    */
    public function renderMyBartag( $atts, $content = null ) {
      /*Query for child pages*/
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
    Load plugin css and javascript files
    */
    public function loadCssAndJs() {
      wp_register_style( 'vc_subpages_style', plugins_url('assets/vc_subpages.css', __FILE__) );
      wp_enqueue_style( 'vc_subpages_style' );
    }

    /*
    Show notice if plugin is activated but Visual Composer is not
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