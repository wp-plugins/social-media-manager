<?php

class smm_digg {
		
		var $smm;
		
		function smm_digg(&$social_media_manager){
			$this->__construct($social_media_manager);
		}
		
		function __construct(&$social_media_manager){
			$this->smm = $social_media_manager;
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function display() {
			
			if( !empty($_POST['duser']) ){
				$this->smm->adminOptions['digg_user'] = $_POST['duser'];
				$this->smm->saveAdminOptions();
			}
			
			?>
			<div class="wrap">
			<div id="icon-users" class="icon32"><br /></div>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<div style="float:right;width:400px;margin-top:40px;">
				Digg User: <br />
				<input type="text" name="duser" value="<?php echo $this->smm->adminOptions['digg_user']; ?>" style="width:175px;" onfocus="this.value='';" />
				<input class="button-primary" type="submit" value="Update" name="Submit"/>
			</div>
			</form>
			
			<h2>Digg Manager</h2>
			
			<h3>Diggs</h3>
			<?php 
				require_once('library/digg/diggclass.php');
				$diggobj = new diggclass();
			?>
			<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr class="thead">
					<th class="manage-column">My Recent Diggs</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="thead">
					<th class="manage-column"> 
						<input type="text" value="" style="width:90%;" onfocus="this.select();" />
					</th>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td><pre>
						<?php 
							
							if( !empty($this->smm->adminOptions['digg_user']) ){
								$data = $diggobj->getStories("","","","","", $this->smm->adminOptions['digg_user'],10);
								if( count($data) ){
									foreach($data AS $digg){
										echo '<div class="digg-item">';
										echo '<div class="digg-diggs">' . $digg['diggs'] . '</div>';
										echo '<a href="' . $digg['digg_link'] . '" target="_blank">' . $digg['title'] . '</a><br />';
										echo '<span>' . $digg['topic_name'] . ' | ' . $digg['container_name'] . ' | ' . $digg['comments'] . ' comments</span>';
										echo '<br clear="all" /></div>';
									}
								}else{
									echo 'No feed data available';
								}
							}else{
								echo 'Please enter a digg user above.';
							}
						?>
					</td>
				</tr>
			</tbody>
			</table>	
		
			</div>
			<div style="padding:30px;text-align:center;">
			<!-- Please don't remove credit especially since this is a free plugin. -->
			Social Media Manager created by <a href="http://www.insivia.com/?utm_source=wordpress&utm_medium=referral&utm_campaign=smm-installedplugin" target="_blank">Insivia Marketing & Interactive Web Design</a>
			</div>
			<?php
		
		}
		
		
}
	



?>