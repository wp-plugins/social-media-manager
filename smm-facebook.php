<?php

class smm_facebook {
		
		var $smm;
		
		function smm_facebook(&$social_media_manager){
			$this->__construct($social_media_manager);
		}
		
		function __construct(&$social_media_manager){
			$this->smm = $social_media_manager;
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function thumbnail() {
		
			// PATHS
			$uploaddir = WP_UPLOAD_DIR.'/social_media_manager/';
			if( !is_dir($uploaddir) ) { 
				mkdir($uploaddir, 0777, true); 
				if( is_dir($uploaddir) ) { 
					echo 'Directory Created!';
				}
			}
			$uploadurl = WP_UPLOAD_URL.'/social_media_manager/';
			$smbdir = WP_UPLOAD_DIR.'/social_media_manager/';
			$smburl = WP_UPLOAD_URL.'/social_media_manager/';
			$submiturl = preg_replace('/&[du]=[a-z0-9.%()_-]*\.(jpg|jpeg|gif|png)/is', '', $_SERVER['REQUEST_URI']);
			
			$msg = "";

			// USER UPLOADED A NEW IMAGE
			if (!empty($_FILES)) {
				$userfile = preg_replace('/\\\\\'/', '', $_FILES['facebook']['name']);
				$file_size = $_FILES['facebook']['size'];
				$file_temp = $_FILES['facebook']['tmp_name'];
				$file_err = $_FILES['facebook']['error'];
				$file_name = explode('.', $userfile);
				$file_type = strtolower($file_name[count($file_name) - 1]);
				$uploadedfile = $uploaddir.$userfile;
				
				if(!empty($userfile)) {
					$file_type = strtolower($file_type);
					$files = array('jpeg', 'jpg', 'gif', 'png');
					$key = array_search($file_type, $files);
				
					if(!$key) {
						$msg .= "ILLEGAL FILE TYPE. Only JPEG, JPG, GIF or PNG files are allowed.<br />";
					}
				
					// ERROR CHECKING
					$error_count = count($file_error);
					if($error_count > 0) {
						for($i = 0; $i <= $error_count; ++$i) {
							$msg .= $_FILES['facebook']['error'][$i]."<br />";
						}
					} else {
					
						if(!move_uploaded_file($file_temp, $uploadedfile)) {
							$msg .= "There was an error when uploading your file.<br />";
						}
						if (!chmod($uploadedfile, 0777)) {
							$msg .= "There was an error when changing your favicon's permissions.<br />";
						}
					
						$this->smm->adminOptions['facebook'] = $userfile;
						$this->smm->saveAdminOptions();
						$msg .= "Your facebook has been updated.";
					}

				}
			}

			// USER HAS CHOSEN TO DELETE AN UPLOADED IMAGE
			if (!empty($_GET['d']) && is_file($uploaddir.$_GET['d'])) {
				if (!unlink ($uploaddir.$_GET['d'])) {
					$msg .= "There was a problem deleting the selected image.";
				} else {
					$msg .= "The selected image has been deleted.";
				}
			}
			
			// USER HAS CHOSEN TO CHANGE HIS FAVICON TO A PREVIOUSLY UPLOADED IMAGE
			if (!empty($_GET['u'])) {
				$this->smm->adminOptions['facebook'] = $_GET['u'];
				$this->smm->saveAdminOptions();
				$msg .= "Your facebook thumbnail has been updated.";
			}
			?>
<div class="wrap">
	<div id="icon-users" class="icon32"><br /></div>
	
	<form method="post" action="<?php echo $submiturl; ?>" enctype="multipart/form-data">
    
    <div style="float:left; width:475px;">
	<h2>Facebook Sharing Image</h2>

		<?php wp_nonce_field('update-options'); ?>
		
		<h3>Upload a New Image</h3>
		<p>Acceptable file types are JPG, JPEG, GIF and PNG.</p>
			
		<div>
			Add an Image &nbsp; 
			<input type="file" name="facebook" id="facebook" />
			<input type="submit" class="button" name="html-upload" value="Upload" />
		</div>
		<br />
		
		<p><b>Customize Per Post/Page</b>:  Use a custom field with the name 'facebook_thumb' that's value is the <i>File Name</i> below to customize per post or page.</p>
		
	</div>
    <img src="<?php echo WP_PLUGIN_URL ?>/social-media-manager/img/facebook-sample.png" style="float:left;margin:15px 0 0 25px;" />
	<div class="clear"></div><br />
	
		<h3>Select a Previously Uploaded File</h3>
		<p>Since this plugin stores every image you upload, you can upload as many images as you like.
			You can then come back from time to time and change your facebook thumbnail. Select from the
			choices below.</p>
			
		<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr class="thead">
				<th class="manage-column">Image</th>
				<th class="manage-column">File Name</th>
				<th class="manage-column">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$files = dirList($uploaddir);
			for ($i = 0; $i < count($files); $i++) :
				$active = ($files[$i] == $this->smm->adminOptions['facebook']) ? true : false;
				echo '<tr class="'.(($active) ? 'alternate' : '').'">';
				echo '<td>';
				echo '<img src="'.$uploadurl.$files[$i].'" title="'.$files[$i].'" alt="'.$files[$i].'" style="" />';
				echo '</td>';
				echo '<td>' . $files[$i] . '</td>';
				echo '<td>';
				echo ($active) ? 'Active' : '		<a href="'.$submiturl.'&d='.$files[$i].'">Delete</a> &nbsp; | &nbsp; ';
				echo ($active) ? '' : '		<a href="'.$submiturl.'&u='.$files[$i].'">Use</a>';
				echo '</td>';
				echo '</tr>';
				
			endfor;
		?>
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