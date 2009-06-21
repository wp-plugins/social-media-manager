<?php
/*
Plugin Name: Social Media Manager
Plugin URI: http://www.insivia.com/wordpress-plugin-social-media-manager
Description: Manage your social media brand and presence.  Currently works for facebook, twitter, digg and youtube with much more coming soon.
Author: Andy Halko, Insivia
Version: 1.0
Author URI: http://www.insivia.com


Copyright 2009  Insivia  (email : info@insivia.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !class_exists('social_media_manager') ) {
	
	include ('library/Directory.php');
	
    class social_media_manager {
		
		/**
		* @var string   The name the options are saved under in the database.
		*/
		var $adminOptionsName = "social_media_manager_options";
		
		/**
		* PHP 4 Compatible Constructor
		*/
		function social_media_manager(){$this->__construct();}
		
		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
			add_action("admin_menu", array(&$this,"add_admin_pages"));
			add_action('wp_head', array(&$this,'wp_head_intercept'));
			add_shortcode('smm_twitter_timeline', array( &$this, 'smm_twitter_timeline_shortcode'));
			add_shortcode('smm_twitter_replies', array( &$this, 'smm_twitter_replies_shortcode'));
			add_shortcode('smm_twitter_messages', array( &$this, 'smm_twitter_messages_shortcode'));
			
			$this->adminOptions = $this->getAdminOptions();
			if ( ! defined( 'WP_CONTENT_URL' ) )
			      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
			if ( ! defined( 'WP_CONTENT_DIR' ) )
			      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
			if ( ! defined( 'WP_PLUGIN_URL' ) )
			      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
			if ( ! defined( 'WP_PLUGIN_DIR' ) )
			      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
			if ( ! defined( 'WP_UPLOAD_DIR' ) )
			      define( 'WP_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads' );
			if ( ! defined( 'WP_UPLOAD_URL' ) )
			      define( 'WP_UPLOAD_URL', WP_CONTENT_URL . '/uploads' );
			
		}
		
		/**
		* Retrieves the options from the database.
		* @return array
		*/
		function getAdminOptions() {
			$adminOptions = array(
				"facebook_thumb" => "<empty>"
			);
			$savedOptions = get_option($this->adminOptionsName);
			if (!empty($savedOptions)) {
				foreach ($savedOptions as $key => $option) {
					$adminOptions[$key] = $option;
				}
			}
			update_option($this->adminOptionsName, $adminOptions);
			return $adminOptions;
		}
		
		function saveAdminOptions(){
			update_option($this->adminOptionsName, $this->adminOptions);
		}
		
		/**
		* Creates the admin page.
		*/
		function add_admin_pages(){
			add_menu_page("Social Media", "Social Media", 10, "social-media-manager", array(&$this,"output_overview"));
			add_submenu_page("social-media-manager", "Facebook", "Facebook", 10, "smm-facebook", array(&$this,"output_facebook")); 
			add_submenu_page("social-media-manager", "Twitter", "Twitter", 10, "smm-twitter", array(&$this,"output_twitter")); 
			add_submenu_page("social-media-manager", "Digg", "Digg", 10, "smm-digg", array(&$this,"output_digg")); 
			add_submenu_page("social-media-manager", "YouTube", "YouTube", 10, "smm-youtube", array(&$this,"output_youtube")); 
		}
		
		/**
		* Called by the action wp_head
		*/
		function wp_head_intercept() {
			global $wp_query;
			$page_fthumb = get_post_meta($wp_query->post->ID, 'facebook_thumb', true);
			if( !empty($page_fthumb) ){
				$facebook_thumb = $page_fthumb;
			}else{
				$facebook_thumb = $this->adminOptions['facebook'];
			}
			echo '<link rel="image_src" href="'.WP_UPLOAD_URL.'/'. $facebook_thumb . '"  />';
			echo '<meta name="generator" content="Social Media Branding v0.1" />';
		}
		
		function output_overview(){
		?>
		
			<div class="wrap">
			<div id="icon-users" class="icon32"><br /></div>
			<h2>Social Media Manager</h2>
			
			<div style="padding:20px;">
			<p>
			The social media manager is meant to help you have quick easy access to various social media functions.
			</p><br />
			
			<div>
				<h3>Facebook</h3>
				<p>Provides you the ability to customize the image used for your site overall or page by page.</p>
			</div><br />
			
			<div>
				<h3>Twitter</h3>
				<p>Update the status of multiple accounts and see feeds of your tweets, replies and direct messages.</p>
			</div>
			</div>
			</div>
			<div style="padding:30px;text-align:center;">
			Social Media Manager created by <a href="http://www.insivia.com" target="_blank">Insivia Marketing & Interactive Web Design</a>
			</div>
			
		<?php
		}
		
		function output_facebook(){
			include ('smm-facebook.php');
			if (class_exists('smm_facebook')) {
				$smm_facebook = new smm_facebook($this);
				$smm_facebook->thumbnail();
			}
		}
		
		function output_twitter(){
			include ('smm-twitter.php');
			if (class_exists('smm_twitter')) {
				$smm_twitter = new smm_twitter($this);
				$smm_twitter->display();
			}
		}
		
		function output_digg(){
			include ('smm-digg.php');
			if (class_exists('smm_digg')) {
				$smm_digg = new smm_digg($this);
				$smm_digg->display();
			}
		}
		
		function output_youtube(){
			include ('smm-youtube.php');
			if (class_exists('smm_youtube')) {
				$smm_youtube = new smm_youtube($this);
				$smm_youtube->display();
			}
		}
		
		function smm_twitter_timeline_shortcode( $atts ) {
			
			$atts = shortcode_atts(
			array(
				'user' => '',
				'pass' => '',
			), 
			$atts);
		
			if( !empty($atts['user']) && !empty($atts['pass']) ){
				require_once('library/twitter/class.twitter.php');
				$t = new twitter();
				$t->username = $atts['user'];
				$t->password = $atts['pass'];
							
				$ret = '<div>' . $atts['user'] . ' Twitter Timeline:</div>';
				$ret .= '<ul>';
				$data = $t->userTimeline();
				foreach($data as $tweet) {
					$ret .= '<li class="twitter-line">' . $tweet->text . '</li>';
				}
				$ret .= '</ul>';
				
				return $ret;
			}else{
				return '';
			}
		}

		function smm_twitter_replies_shortcode($atts) {
		
			$atts = shortcode_atts(
			array(
				'user' => '',
				'pass' => '',
			), 
			$atts);
		
			if( !empty($atts['user']) && !empty($atts['pass']) ){
				require_once('library/twitter/class.twitter.php');
				$t = new twitter();
				$t->username = $atts['user'];
							
				$data = $t->getReplies(1);
				$ret = '<ul>';
				foreach($data as $tweet) {
					$ret .= '<li class="twitter-line">' . $tweet->text . '</li>';
				}
				$ret .= '</ul>';
				
				return $ret;
			}else{
				return '';
			}
			
		}
		
		function smm_twitter_messages_shortcode($atts) {
		
			$atts = shortcode_atts(
			array(
				'user' => '',
				'pass' => '',
			), 
			$atts);
		
			if( !empty($atts['user']) && !empty($atts['pass']) ){
				require_once('library/twitter/class.twitter.php');
				$t = new twitter();
				$t->username = $atts['user'];
							
				$data = $t->directMessages();
				$ret = '<ul>';
				foreach($data as $tweet) {
					$ret .= '<li class="twitter-line">' . $tweet->text . '</li>';
				}
				$ret .= '</ul>';
				
				return $ret;
			}else{
				return '';
			}
			
		}
		
    }
}

//instantiate the class
if ( class_exists('social_media_manager') ) {
	$social_media_manager = new social_media_manager();
}

		
function smm_admin_head($content){
	switch( $_REQUEST['page']){
		case 'smm-twitter':
			$content .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/social-media-manager/css/twitter-stylesheet.css" type="text/css" />';
			$content .= '<script src="' . WP_PLUGIN_URL . '/social-media-manager/js/tcounter.js" type="text/javascript"></script>';
			break;
		case 'smm-digg':
			$content .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/social-media-manager/css/digg-stylesheet.css" type="text/css" />';
			break;
		case 'smm-youtube':
			$content .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/social-media-manager/css/youtube-stylesheet.css" type="text/css" />';
			break;
	}
	echo $content;
}
add_filter('admin_head', "smm_admin_head");


?>