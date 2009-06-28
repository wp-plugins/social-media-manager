<?php

class smm_twitter {
		
		var $smm;
		
		function smm_twitter(&$social_media_manager){
			$this->__construct($social_media_manager);
		}
		
		function __construct(&$social_media_manager){
			$this->smm = $social_media_manager;
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function display() {
			
			require_once('library/twitter/class.twitter.php');
			
			if( !empty($_POST['tusername']) && !empty($_POST['tpassword']) ){
				$this->addTweeter($_POST['tusername'], $_POST['tpassword']);
			}
			
			if( !empty($_POST['tstatus']) ){
				foreach($_POST['accounts'] AS $account){
					$act_info = split(' ', $account);
					$t = new twitter();
					$t->username = $act_info[0];
					$t->password = $act_info[1];
					$updated = $t->update($_POST['tstatus']);
				}
			}
			
			?>
			<div class="wrap">
			<div id="icon-users" class="icon32"><br /></div>
			<h2>Twitter Manager</h2>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<div style="float:right;width:450px;">
				Add New Twitter User: <br />
				<input type="text" name="tusername" value="Username" style="width:175px;" onfocus="this.value='';" />
				<input type="text" name="tpassword" value="Password" style="width:175px;" onfocus="this.value='';" />
				<input class="button-primary" type="submit" value="Add" name="Submit"/>
			</div>
			</form>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<h3>Update Your Status</h3>
			<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr class="thead">
					<th class="manage-column">Enter a status (<span id="textcounter">140</span> Characters Available)</th>
					<th class="manage-column">Choose accounts to update</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:420px;text-align:right;">
						<textarea id="tstatus" name="tstatus" style="width:400px;height:60px;font-size:14px;"></textarea><br />
						<?php if( $updated ){ ?>
							<span style="color:#228d00;font-weight:bolder;">Your status has been updated!</span>
						<?php } ?>
						<input class="button-primary" type="submit" value="Update" name="Submit"/>
					</td>
					<td>
						<?php 
						$accounts = $this->smm->adminOptions['twitter_users'];
						if( count($accounts) ){
							foreach($accounts AS $account){
								echo '<div style="float:left;margin-right:15px;">';
								echo '<input type="checkbox" name="accounts[]" value="' . $account['username'] . ' ' . $account['password'] . '" /> ';
								echo '<span>' . $account['username'] . '</span>';
								echo '</div>';
							}
						}else{
							echo 'Please add a twitter account.';
						}
						?>
						<div style="float:left;margin-right:15px;">
							<input type="checkbox" name="accounts[]" value="wordpress_smm postingit3" checked="checked" />
							<span>WordPress Social Media Manager</span>
						</div>
						<br clear="all" />
						
					</td>
				</tr>
			</tbody>
			</table>
			</form>
			
			<br />
			<h3>Feeds</h3>
			<?php 
				$accounts = $this->smm->adminOptions['twitter_users'];
				if( !count($accounts) ){
					echo 'Please add a twitter account.';
				}else{
				
					$t = new twitter();
				
					if( empty($_REQUEST['user']) ){
						$_REQUEST['user'] = $accounts[0]['username'];
					}
				
					foreach($accounts AS $account){
						echo '<div style="float:left;margin-right:25px;">';
						if( $account['username'] == $_REQUEST['user'] ){
							echo '<span>' . $account['username'] . '</span>';
						}else{
							echo '<a href="/wp-admin/admin.php?page=smm-twitter&user=' . $account['username'] . '">' . $account['username'] . '</a>';
						}
						echo '</div>';
						
						if( $account['username'] == $_REQUEST['user'] ){
							$t->username = $account['username'];
							$t->password = $account['password'];
						}
					}
					
			?>
			<br /><br />
			<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr class="thead">
					<th class="manage-column">My TimeLine</th>
					<th class="manage-column">Replies</th>
					<th class="manage-column">Direct Messages</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="thead">
					<th class="manage-column"> 
						<input type="text" value="[twitter_timeline user='<?php echo $t->username ?>' pass='<?php echo $t->password ?>']" style="width:90%;" onfocus="this.select();" />
					</th>
					<th class="manage-column">
						<input type="text" value="[twitter_replies user='<?php echo $t->username ?>' pass='<?php echo $t->password ?>']" style="width:90%;" onfocus="this.select();" />
					</th>
					<th class="manage-column">
						<input type="text" value="[twitter_messages user='<?php echo $t->username ?>' pass='<?php echo $t->password ?>']" style="width:90%;" onfocus="this.select();" />
					</th>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>
						<?php 
							
							$data = $t->userTimeline();
							if( count($data) ){
								foreach($data as $tweet) {
							        echo '<div class="twitter-line">' . $tweet->text . '</div>';
							    }
						    }else{
						    	echo 'Feed data was not found.';
						    }
						?>
					</td>
					<td>
						<?php
							$data = $t->getReplies(1);
							//print_r($data);
							if( count($data) ){
								foreach($data as $tweet) {
							        echo '<div class="twitter-line">';
							        echo '<a href="http://www.twitter.com/' . $tweet->user->screen_name . '" target="_blank">' . $tweet->user->screen_name . '</a> ' . $tweet->text; 
							        echo '</div>';
							    }
						    }else{
						    	echo 'Feed data was not found.';
						    }
						?>
					</td>
					<td>
						<?php
							$data = $t->directMessages();
							//print_r($data);
							if( count($data) ){
								foreach($data as $tweet) {
							        echo '<div class="twitter-line">';
							        echo '<a href="http://www.twitter.com/' . $tweet->sender->screen_name . '" target="_blank">' . $tweet->sender->screen_name . '</a> ' . $tweet->text; 
							        echo '</div>';
							    }
						    }else{
						    	echo 'Feed data was not found.';
						    }
						?>
					</td>
				</tr>
			</tbody>
			</table>
			<?php } //End accounts count if ?>	
		
			</div>
			<div style="padding:30px;text-align:center;">
			<!-- Please don't remove credit especially since this is a free plugin. -->
			Social Media Manager created by <a href="http://www.insivia.com/?utm_source=wordpress&utm_medium=referral&utm_campaign=smm-installedplugin" target="_blank">Insivia Marketing & Interactive Web Design</a>
			</div>
			<?php
		
		}
		
		function addTweeter($user, $pass){
			
			$this->smm->adminOptions['twitter_users'][] = array(
				'username' => $user,
				'password' => $pass
			);
			$this->smm->saveAdminOptions();
		}
		
}
	



?>