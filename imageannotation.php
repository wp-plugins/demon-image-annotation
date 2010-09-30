<?php 
/*
Plugin Name: Demon Image Annotations
Plugin URI: http://www.superwhite.cc/demon/image-annotation-plugin
Description: 'Allows you to add textual annotations to images by select a region of the image and then attach a textual description, the concept of annotating images with user comments.'
Author: Demon
Version: 1.0
Author URI: http://www.superwhite.cc
*/

//header function
function load_image_annotation_js() {
	$plugindir = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js' type='text/javascript'></script>\n";
	echo "<script type='text/javascript' src='". $plugindir ."/js/jquery.annotate.js'></script>\n";
	echo "<script type='text/javascript' src='". $plugindir ."/js/jquery-ui-1.7.1.js'></script>\n";
	echo "<link rel='stylesheet' href='$plugindir/css/annotation.css' type='text/css' />\n";
	
	function ae_detect_ie()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']) && 
		(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
			return true;
		else
			return false;
	}  
	
	if (is_single()) { ?>
		
    <script language="javascript">
	$(document).ready(function(){
			$("img").each(function() {
				var idname = $(this).attr("id")
				if(idname.substring(0,4) == "img-") {
					source = $(this).attr('src');					
					$(this).wrap($('<div id=' + idname.substring(4,idname.length) + ' ></div>'));
					
					$('#' + idname).mouseover(function() {
						$(this).annotateImage({
							getPostID: <?php global $wp_query; $thePostID = $wp_query->post->ID; echo $thePostID; ?>,
							getImgID: idname,
							getUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
							saveUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
							deleteUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
							editable: <?php get_currentuserinfo(); global $user_level; if ($user_level > 0) { ?>true<?php } else { ?>false<?php } ?>,
							addable: true
						});
					});
				}
			});
			
			$('div').each(function() {
				var divid = $(this).attr("id");
				if(divid.substring(0,8) == "comment-") {
					var getimgsrc = imageSource(divid.substring(8,divid.length));
					if(getimgsrc != "") {
						$(this).remove("noted");
						$(this).html('<div class="image-note-thumbnail"><a href="#' + divid.substring(8,divid.length) + '"><img src="' + getimgsrc + '" /></a></div>');
					}
				}
			});
	});
	
	function imageSource(id) {
		var idreturn = "";
		$('img').each(function() {
			var imgid = $(this).attr("id");
			if(imgid == "img-" + id) {
				idreturn = $(this).attr("src");
			}
		});
		
		return idreturn;
	}
	
	</script>
	<?php }
}

//comment function
function getImgID() {
	global $comment;
	$commentID = $comment->comment_ID;
	
	global $wpdb;
	$imgIDNow = $wpdb->get_var("SELECT note_img_ID FROM wp_imagenote WHERE note_comment_id = ".(int)$commentID);
	
	if($imgIDNow != "") {
		$str = substr($imgIDNow, 4, strlen($imgIDNow));
		echo "<div id=\"comment-".$str."\"><a href='#".$str."'>noted on #".$imgIDNow."</a></div>";
	} else {
		echo "&nbsp;";	
	}
}

add_action('wp_head', 'load_image_annotation_js');
add_filter('Comments', 'getImgID');
?>