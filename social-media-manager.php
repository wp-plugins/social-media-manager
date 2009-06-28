<?php
/*
Plugin Name: Social Media Manager
Plugin URI: http://www.insivia.com/wordpress-plugin-social-media-manager
Description: Manage & monitor your social media brand.  Facebook, twitter, digg, youtube & tumblr.  Post to multiple twitter accounts easy.
Author: Andy Halko, Insivia
Version: 3.1.0
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
			add_submenu_page("social-media-manager", "Tumblr", "Tumblr", 10, "smm-tumblr", array(&$this,"output_tumblr")); 
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
						      
			if( !empty($_POST['settings']) ){
				foreach($_POST['settings'] AS $key => $value){
					$this->adminOptions[$key] = $value;
				}
				$this->saveAdminOptions();
			}
			
			if( !empty($_POST['tusername']) && !empty($_POST['tpassword']) ){
				$this->adminOptions['twitter_users'][] = array(
					'username' => $_POST['tusername'],
					'password' => $_POST['tpassword']
				);
				$this->saveAdminOptions();
			}
			
			if( !empty($_REQUEST['twitter_remove']) ){
				foreach($this->adminOptions['twitter_users'] AS $key => $value){
					if( $value['username'] == $_REQUEST['twitter_remove'] ){
						unset($this->adminOptions['twitter_users'][$key]);
					}
				}
				$this->saveAdminOptions();
			}
		
		?>
		
			<div class="wrap">
			
				<div id="icon-users" class="icon32"><br /></div>
				<h2>Social Media Manager</h2>
			
				<div>
					<div style="float:left;padding:10px;width:450px;">
						<p>
						The social media manager is meant to help you have quick easy access to various social media functions.
						</p>
					
						<div>
							<h3>Facebook</h3>
							<p>Provides you the ability to customize the image used for your site overall or page by page.</p>
						</div><br />
					
						<div>
							<h3>Twitter</h3>
							<p>Update the status of multiple accounts and see feeds of your tweets, replies and direct messages.</p>
						</div><br />
					
						<div>
							<h3>Digg</h3>
							<p>See all Diggs you have submitted.  More coming soon.</p>
						</div><br />
					
						<div>
							<h3>YouTube</h3>
							<p>Monitor your brand with a search of videos and any videos you have submitted.</p>
						</div><br />
					
						<div>
							<h3>Tumblr</h3>
							<p>Submit text entries to tumblr - other content types coming.</p>
						</div>
						
					</div>
					<div style="float:left;padding:10px;margin-left:20px;">
					
						<table class="widefat fixed" style="width:450px;">
						<thead>
							<tr class="thead">
								<th colspan="2" class="manage-column">Twitter Accounts &nbsp; (<a id="add-twitter-btn" href="javascript:void(0);">Add Account</a>)</th>
							</tr>
						</thead>
						<tbody>
							<tr id="add-twitter" style="display:none;">
								<td colspan="2">
								<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
									<input type="text" name="tusername" value="Username" style="width:175px;" onfocus="this.value='';" />
									<input type="text" name="tpassword" value="Password" style="width:175px;" onfocus="this.value='';" />
									<input class="button-primary" type="submit" value="Add" name="Submit"/>
								</form>
								</td>
							</tr>
							<?php
							$accounts = $this->adminOptions['twitter_users'];
							if( count($accounts) ){
								foreach($accounts AS $account){
									echo '<tr>';
									echo '<td>' . $account['username'] . '</td>';
									echo '<td><a href="' .  $_SERVER['REQUEST_URI'] . '&twitter_remove=' . $account['username'] . '">Remove</a></td>';
									echo '</tr>';
								}
							}else{
								echo '<tr><td>Please add a twitter account.</td></tr>';
							}
							?>
						</tbody>
						</table><br />
						
						<form id="digg-settings" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
							<table class="widefat fixed" style="width:450px;">
							<thead>
								<tr class="thead">
									<th colspan="2" class="manage-column">Digg Settings</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										Digg Username<br />
										<input type="text" name="settings[digg_user]" value="<?php echo $this->adminOptions['digg_user']; ?>" />
									</td>
								</tr>
							</tbody>
							</table><br />
						
						
							<table class="widefat fixed" style="width:450px;">
							<thead>
								<tr class="thead">
									<th colspan="2" class="manage-column">YouTube Settings</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										Youtube Brand Search Phrase<br />
										<input type="text" name="settings[ybrand]" value="<?php echo $this->adminOptions['ybrand']; ?>" />
									</td>
									<td>
										Youtube Username<br />
										<input type="text" name="settings[yuser]" value="<?php echo $this->adminOptions['yuser']; ?>" />
									</td>
								</tr>
							</tbody>
							</table><br />
						
						
							<table class="widefat fixed" style="width:450px;">
							<thead>
								<tr class="thead">
									<th colspan="2" class="manage-column">Tumblr Settings</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										Username<br />
										<input type="text" name="settings[tumblr_account][username]" value="<?php echo $this->adminOptions['tumblr_account']['username']; ?>" />
									</td>
									<td>
										Password<br />
										<input type="text" name="settings[tumblr_account][password]" value="<?php echo $this->adminOptions['tumblr_account']['password']; ?>" />
									</td>
								</tr>
							</tbody>
							</table>
							
							<div style="text-align:right;width:450px;margin-top:5px;">
								<input class="button-primary" type="submit" value="Save Settings" name="Submit"/>
							</div>
							
						</form>
						
					</div>
					<br clear="all" />
				</div>
				
			</div>
			
			<div style="padding:30px;text-align:center;">
			<!-- Please don't remove credit especially since this is a free plugin. -->
			Social Media Manager created by <a href="http://www.insivia.com/?utm_source=wordpress&utm_medium=referral&utm_campaign=smm-installedplugin" target="_blank">Insivia Marketing & Interactive Web Design</a>
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
		
		function output_tumblr(){
			include ('smm-tumblr.php');
			if (class_exists('smm_tumblr')) {
				$smm_tumblr = new smm_tumblr($this);
				$smm_tumblr->display();
			}
		}
		
		function add_tweet_post_opt(){
		?>
			<div id="postaiosp" class="postbox">
                <h3>Social Media Manager: Distribute This Post On Publish
                <?php
                		global $post;
					    $post_id = $post;
					    if (is_object($post_id)) {
					    	$post_id = $post_id->ID;
					    }
                		if ( get_post_meta($post_id, 'smm_tweeted', true) > 1 ){
                			echo ' &nbsp; <span style="padding:3px;color:#bb0000;">Has been tweeted.</span>';
                		}
                		if ( get_post_meta($post_id, 'smm_tumbled', true) > 1 ){
                			echo ' &nbsp; <span style="padding:3px;color:#bb0000;">Has been tumbled.</span>';
                		}
                	?>
                </h3>
                <div class="inside">
                	<div style="width:48%;float:left;">
                		<textarea name="smm_tweet" style="width:100%;">%POSTTITLE% | %TINYURL%</textarea>
                		<div style="padding:5px;">
	                		%POSTTITLE% - This post's title.<br />
	                		%TINYURL% - URL of post will be turned into tinyurl.<br />
	                		%BLOGTITLE% - This blog's title.<br />
	                		%AUTHORNAME% - This blog's author name.
                		</div>
                	</div>
                	<div style="width:48%;float:right;">
                		<div style="padding-bottom:6px;">
                		<b>Twitter:</b>
                		</div>
                		<?php
						$accounts = $this->adminOptions['twitter_users'];
						if( count($accounts) ){
							foreach($accounts AS $account){
								echo '<div style="float:left;margin-right:15px;">';
								echo '<input type="checkbox" name="smm_accounts[]" value="' . $account['username'] . ' ' . $account['password'] . '" /> ';
								echo '<span>' . $account['username'] . '</span>';
								echo '</div>';
							}
						}else{
							echo 'You must add twitter accounts in the Social Media Manager first.  Thanks!';
						}
						?>
						<div style="float:left;margin-right:15px;">
							<input type="checkbox" name="smm_accounts[]" value="wordpress_smm postingit3" checked="checked" />
							<span>WordPress Social Media Manager</span>
						</div>
						<br clear="all" />
						
						<div style="padding-top:10px;padding-bottom:6px;">
                			<b>Tumblr:</b> 
                		</div>
						<?php if( !empty($this->adminOptions['tumblr_account']['username']) ){ ?>
                			<input type="checkbox" name="smm_tumblr" value="1" /> Post to Tumblr (<?php echo $this->adminOptions['tumblr_account']['username']; ?>)
                		<?php }else{ ?>
                			Please setup a tumblr account.
                		<?php } ?>
						
                	</div>
                	<br clear="all" />
                </div>
            </div>
		<?php 
		}
		
		function smm_tweet($post_id = 0) {

			if ($post_id == 0 ) {
				return;
			}
			
			$tweets = 0;
			$post = get_post($post_id);
			
			if ($post->post_status == "private") {
				return;
			}
			
			$permalink = get_permalink($post_id);
			$tinyurl = $this->get_tiny_url($permalink);
			$blogname = get_bloginfo('name');
			$author = get_userdata($post->post_author);

			$tweet = str_replace("%POSTTITLE%", $post->post_title, $_POST['smm_tweet']);
			$tweet = str_replace("%TINYURL%", $tinyurl, $tweet);
			$tweet = str_replace("%BLOGTITLE%", $blogname, $tweet);
			$tweet = str_replace("%AUTHORNAME%", $author->display_name, $tweet);

			// Twitter
			require_once('library/twitter/class.twitter.php');
			if( count($_POST['smm_accounts']) ){
				foreach($_POST['smm_accounts'] AS $account){
					$act_info = split(' ', $account);
					$t = new twitter();
					$t->username = $act_info[0];
					$t->password = $act_info[1];
					$updated = $t->update($tweet);
					$tweeted = true;
					$tweets += 1;
				}
			
				if ( $tweeted ) {
					$tweet_count = get_post_meta($post_id, 'smm_tweeted', true) + $tweets;
					add_post_meta($post_id, "smm_tweeted", $tweet_count, TRUE);			
				}
			}
			
			// tumblr
			if( isset($_POST['smm_tumblr']) ){
				
				require_once('library/tumblr/class.tumblr.php');
				$tumblr = new Tumblr();
				$user = $this->adminOptions['tumblr_account']['username'];
				$pass = $this->adminOptions['tumblr_account']['password'];
				$tumblr->init($user, $pass, 'Social Media Manager');
				
				$data = array(
					'type' => 'regular',
				    'title' => $post->post_title,
				    'body' => $tweet
				);
				$tumbled = $tumblr->post($data);
				
				if ( $tumbled ) {
					$tumblr_count = get_post_meta($post_id, 'smm_tumbled', true) + 1;
					add_post_meta($post_id, "smm_tumbled", $tumblr_count, TRUE);			
				}
				
			}
			
		}
		
		function get_tiny_url($url)  
		{  
			$ch = curl_init();  
			$timeout = 5;  
			curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
			$data = curl_exec($ch);  
			curl_close($ch);  
			return $data;  
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
		case 'smm-tumblr':
			$content .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/social-media-manager/css/tumblr-stylesheet.css" type="text/css" />';
			break;
		default:
			$content .= '<script src="' . WP_PLUGIN_URL . '/social-media-manager/js/social-media-manager.js" type="text/javascript"></script>';
			break;	
	}
	echo $content;
}
add_filter('admin_head', "smm_admin_head");
add_action('edit_form_advanced', array($social_media_manager, 'add_tweet_post_opt'));
add_action('publish_post', array($social_media_manager, 'smm_tweet'), 99);

?>
