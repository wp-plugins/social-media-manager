<?php

class smm_youtube {
		
		var $smm;
		
		function smm_youtube(&$social_media_manager){
			$this->__construct($social_media_manager);
		}
		
		function __construct(&$social_media_manager){
			$this->smm = $social_media_manager;
		}
		
		/**
		* Outputs the HTML for the admin sub page.
		*/
		function display() {
			
			$clientLibraryPath = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/social-media-manager/library/Zend';
			$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
			
			if( !empty($_POST['ybrand']) || !empty($_POST['yuser']) ){
				$this->smm->adminOptions['ybrand'] = $_POST['ybrand'];
				$this->smm->adminOptions['yuser'] = $_POST['yuser'];
				$this->smm->saveAdminOptions();
			}
			
			?>
			<div class="wrap">
			<div id="icon-users" class="icon32"><br /></div>
			
			<h2>YouTube Manager</h2>
			
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
			<div style="float:right;width:450px;">
				<span style="margin-right:60px;">Brand Search Term</span> <span>YouTube Username</span> <br />
				<input type="text" name="ybrand" style="width:175px;" value="<?php echo $this->smm->adminOptions['ybrand']; ?>" />
				<input type="text" name="yuser" style="width:175px;" value="<?php echo $this->smm->adminOptions['yuser']; ?>" />
				<input class="button-primary" type="submit" value="Update" name="Submit"/>
			</div>
			</form>
			
			<?php
			
			require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
			Zend_Loader::loadClass('Zend_Gdata_YouTube');
			
			?>
			<h3>Feeds from YoutTube</h3>
			<table class="widefat fixed" cellspacing="0">
			<thead>
				<tr class="thead">
					<th class="manage-column">YouTube Brand Mentions</th>
					<th class="manage-column">My Videos</th>
				</tr>
			</thead>
			<tfoot>
				<tr class="thead">
					<th class="manage-column"> 
						<input type="text" value="" style="width:90%;" onfocus="this.select();" />
					</th>
					<th class="manage-column"> 
						<input type="text" value="" style="width:90%;" onfocus="this.select();" />
					</th>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td>
						<?php 
							
							if( !empty($this->smm->adminOptions['ybrand']) ){
								$videos = $this->getVideoSearch($this->smm->adminOptions['ybrand']);
								if( count($videos) ){
									foreach ($videos as $video) {
										$this->printVideoEntry($video);
									}
								}else{
									echo 'No videos could be retrived.';
								}
							}else{
								echo 'Please enter a search terms above.';
							}
						?>
					</td>
					<td>
						<?php 
							
							if( !empty($this->smm->adminOptions['yuser']) ){
								$videos = $this->getTopRatedVideosByUser($this->smm->adminOptions['yuser']);
								if( count($videos) ){
									foreach ($videos as $video) {
										$this->printVideoEntry($video);
									}
								}else{
									echo 'No videos could be retrived.';
								}
							}else{
								echo 'Please enter a username above.';
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
		
		function getTopRatedVideosByUser($user) 
		{
		    $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' . $user . '/uploads';
		    $yt = new Zend_Gdata_YouTube();
		    $ytQuery = $yt->newVideoQuery($userVideosUrl);  
		    // order by the rating of the videos
		    $ytQuery->setOrderBy('rating');
		    // retrieve a maximum of 5 videos
		    $ytQuery->setMaxResults(5);
		    // retrieve only embeddable videos
		    $ytQuery->setFormat(5);
		    return $yt->getVideoFeed($ytQuery);
		}
		
		function getVideoSearch($searchTerms) 
		{
		    $yt = new Zend_Gdata_YouTube();
		    $query = $yt->newVideoQuery();
		    $query->setOrderBy('viewCount');
		    //$query->setSafeSearch('none');
		    $query->setVideoQuery($searchTerms);
		    
		    return $yt->getVideoFeed($query->getQueryUrl(2));
		    
		}
		
		function printVideoEntry($videoEntry) 
		{
		  
			$videoThumbnails = $videoEntry->getVideoThumbnails();
			
			$rating = $videoEntry->getVideoRatingInfo();
			
		  	echo '<div class="youtube-item">';
		  	echo '<div class="youtube-thumb">';
		  	echo '<a href="http://www.youtube.com/watch?v=' . $videoEntry->getVideoId() . '" target="_blank"><img src="' . $videoThumbnails[0]['url'] . '" width="' . $videoThumbnails[0]['width'] . '" height="' . $videoThumbnails[0]['height'] . '" /></a>';
		  	echo '<div class="youtube-stats">' . $videoEntry->getVideoViewCount() . ' views | rating: ' . $rating['average'] . '</div>';
		  	echo '</div>';
		  	echo '<div>';
		  	echo '<a href="http://www.youtube.com/watch?v=' . $videoEntry->getVideoId() . '" target="_blank"><b>' . $videoEntry->getVideoTitle() . '</b></a><br />';
		  	echo $videoEntry->getVideoDescription() . '<br /><i>' . implode(", ", $videoEntry->getVideoTags()) . '</i>';
		  	echo '</div>';
		  	echo '<br clear="all" /></div>';
		  
		  // the videoEntry object contains many helper functions
		  /* that access the underlying mediaGroup object
		  echo 'Video: ' . $videoEntry->getVideoTitle() . "\n";
		  echo 'Video ID: ' . $videoEntry->getVideoId() . "\n";
		  echo 'Updated: ' . $videoEntry->getUpdated() . "\n";
		  echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";
		  echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";
		  echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n";
		  echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";
		  echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";
		  echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";
		  echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";
		  echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n";
		  echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";
		  
		  // see the paragraph above this function for more information on the 
		  // 'mediaGroup' object. in the following code, we use the mediaGroup
		  // object directly to retrieve its 'Mobile RSTP link' child
		  foreach ($videoEntry->mediaGroup->content as $content) {
		    if ($content->type === "video/3gpp") {
		      echo 'Mobile RTSP link: ' . $content->url . "\n";
		    }
		  }
		  
		  echo "Thumbnails:\n";
		  $videoThumbnails = $videoEntry->getVideoThumbnails();
		
		  foreach($videoThumbnails as $videoThumbnail) {
		    echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
		    echo ' height=' . $videoThumbnail['height'];
		    echo ' width=' . $videoThumbnail['width'] . "\n";
		  }
		  */
		  
		}
		
}
	


?>