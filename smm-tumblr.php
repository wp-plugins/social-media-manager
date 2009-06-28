<?php

class smm_tumblr {
		
		var $smm;
		
		function smm_tumblr(&$social_media_manager){
			$this->__construct($social_media_manager);
		}
		
		function __construct(&$social_media_manager){
			$this->smm = $social_media_manager;
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function display() {
			
			require_once('library/tumblr/class.tumblr.php');
			
			if( !empty($_POST['tusername']) && !empty($_POST['tpassword']) ){
				$this->smm->adminOptions['tumblr_account'] = array(
					'username' => $_POST['tusername'],
					'password' => $_POST['tpassword']
				);
				$this->smm->saveAdminOptions();
			}
			
			if( !empty($_POST['t_title']) && !empty($_POST['t_text']) ){
				
				$tumblr = new Tumblr();
				$user = $this->smm->adminOptions['tumblr_account']['username'];
				$pass = $this->smm->adminOptions['tumblr_account']['password'];
				$tumblr->init($user, $pass, 'Social Media Manager');
				
				$data = array(
					'type' => 'regular',
				    'title' => $_POST['t_title'],
				    'body' => $_POST['t_text']
				);
				$updated = $tumblr->post($data);
				
			}
			
			?>
			<div class="wrap">
			<div id="icon-users" class="icon32"><br /></div>
			<h2>Tumblr Manager</h2>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<div style="float:right;width:450px;padding-bottom:5px;">
				<span style="margin-right:65px;">Tumblr Username</span> <span>Tumblr Password</span> <br />
				<input type="text" name="tusername" value="<?php echo $this->smm->adminOptions['tumblr_account']['username']; ?>" style="width:175px;" onfocus="this.value='';" />
				<input type="text" name="tpassword" value="<?php echo $this->smm->adminOptions['tumblr_account']['password']; ?>" style="width:175px;" onfocus="this.value='';" />
				<input class="button-primary" type="submit" value="Save" name="Submit"/>
			</div>
			</form>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<?php if( $updated == true ){ ?>
				<span style="color:#aa0000;font-weight:bolder;">Your status has been updated! </span>
			<?php }else{ ?>
				<span style="color:#aa0000;font-weight:bolder;"><?php echo $updated; ?></span>
			<?php } ?>
			<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr class="thead">
					<th class="manage-column">Update Your Tumblr</th>
					<th class="manage-column">Your Tumblr</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:420px;">
						Title<br />
						<input type="text" id="t_title" name="t_title" style="width:400px;font-size:14px;" />
					</td>
					<td rowspan="3" class="alternate">
					
						<?php
							$tumblr = new Tumblr();
							$data = $tumblr->read('insivia', true);
							$data = str_replace('var tumblr_api_read = ', '', $data);
							$data = str_replace(';', '', $data);
							$data = json_decode($data, true);
							
							if( count($data['posts']) ){
								foreach($data['posts'] as $tumblr) {
							        echo '<div class="tumblr-line">';
							        echo '<div class="tumblr-title">' . $tumblr['regular-title'] . '</div>';
							        echo '<div class="tumblr-body">' . $tumblr['regular-body'] . '</div>'; 
							        echo '</div>';
							    }
						    }else{
						    	echo 'Feed data was not found.';
						    }
						    
						?>
					
					</td>
				</tr>
				<tr>
					<td style="width:420px;">
						Body<br />
						<textarea id="t_text" name="t_text" style="width:400px;height:200px;font-size:14px;"></textarea>
					</td>
				</tr>
				<tr>
					<td style="width:420px;">
						<input class="button-primary" type="submit" value="Update" name="Submit"/>
					</td>
				</tr>
			</tbody>
			</table>
			</form>
			
			</div>
			<div style="padding:30px;text-align:center;">
			<!-- Please don't remove credit especially since this is a free plugin. -->
			Social Media Manager created by <a href="http://www.insivia.com/?utm_source=wordpress&utm_medium=referral&utm_campaign=smm-installedplugin" target="_blank">Insivia Marketing & Interactive Web Design</a>
			</div>
			<?php
		
		}
		
}
	



?>