<?php 
/*
Plugin Name: Demon Image Annotations
Plugin URI: http://www.superwhite.cc/demon/image-annotation-plugin
Description: 'Allows you to add textual annotations to images by select a region of the image and then attach a textual description, the concept of annotating images with user comments.'
Author: Demon
Version: 2.1
Author URI: http://www.superwhite.cc
*/

//*************** Header function ***************
function load_image_annotation_js() {
	$plugindir = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js' type='text/javascript'></script>\n";
	echo "<script type='text/javascript' src='". $plugindir ."/js/jquery.annotate.js'></script>\n";
	echo "<script type='text/javascript' src='". $plugindir ."/js/jquery-ui-1.7.1.js'></script>\n";
	echo "<script type='text/javascript' src='". $plugindir ."/js/jquery.md5.js'></script>\n";
	echo "<link rel='stylesheet' href='$plugindir/css/annotation.css' type='text/css' />\n";
	
	function ae_detect_ie()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']) && 
		(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
			return true;
		else
			return false;
	}
	
	if (is_single()) {
		$plugin = 1;
	} else if(is_archive()){
		if( (get_option('demon_image_annotation_archive') == '1') ) {
			$plugin = 2;
		} else {
			$plugin = 0;
		}
	} else if(is_home()){
		if( (get_option('demon_image_annotation_homepage') == '1') ) {
			$plugin = 2;
		} else {
			$plugin = 0;
		}	
	}
	
	?>
    <script language="javascript">
	<?php if( (get_option('demon_image_annotation_display') == '0' && $plugin != 0) ) { ?>
		$(document).ready(function(){
				//image annotaion
				$("<?php echo get_option('demon_image_annotation_postcontainer'); ?> img").each(function() {						
						var idname = $(this).attr("id")
						var source = $(this).attr('src');
						
						if(idname.substring(4,idname.length) != 'exclude') {
							//check if image annotation addable attribute exist
							var addablecon = $(this).attr("addable")
							addablecon = addablecon == undefined ? "true" : addablecon;
							
							//disable if image annotation addable for admin only
							<?php if (get_option('demon_image_annotation_admin') == '0') { ?>
								addablecon = false;
							<?php } ?>
							
							//enable addable and editable only in single page
							var addablepage = true;
							var editable = true;
							
							//disable addable button if not in single page
							<?php if ($plugin != 1) { ?>
								addablepage = false;
								addablecon = false;
								editable = false
							<?php  } ?>
							
							//find image link if exist
							var imagelink = $(this).parent("a").attr('href');
							var imgid = ""
								
							//auto insert image id attribute
							<?php if( (get_option('demon_image_annotation_autoimageid') == '0') ) { ?>
								imgid = $.md5(source);
								<?php if( (get_option('demon_image_annotation_autoimageid') == '0') ) { ?>
									var postid = <?php global $wp_query; $thePostID = $wp_query->post->ID; echo $thePostID; ?>;
									imgid = "img-" + postid + "-" + imgid.substring(0,10);
								<?php } else { ?>
									imgid = "img-" + imgid.substring(0,10);
								<?php }; ?>
							<?php }; ?>
							
							//replace if image id attribute exist
							if(idname.substring(0,4) == "img-") {
								imgid = idname;
							}
							
							if(imgid.substring(0,4) == "img-") {
								//deactive the lnik if exist
								$(this).parent("a").removeAttr("href");
								
								$(this).attr("id", imgid);
								$(this).wrap($('<div id=' + imgid.substring(4,imgid.length) + ' ></div>'));
								var imagenotedesc = "<?php echo get_option('demon_image_annotation_mouseoverdesc'); ?>";
								var imagelinkdesc = "<?php echo get_option('demon_image_annotation_linkdesc'); ?>";
								
								var imagenotetag = imagenotedesc != '' ? imagenotedesc : imagenotedesc;
								var imagelinktag = imagelink != undefined ? '<a href="' + imagelink + '" target="blank">' + imagelinkdesc + '</a>' : '';
								var divider = imagelink != undefined ? ' | ' : '';
								var divider = imagenotetag == '' ? '' : ' | ';
								$(this).before('<div class="image-note-desc">'+ imagenotetag + divider + imagelinktag + '</div>');
							
								$(this).mouseover(function() {
									$(this).annotateImage({
										getPostID: <?php global $wp_query; $thePostID = $wp_query->post->ID; echo $thePostID; ?>,
										getImgID: imgid,
										getUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
										saveUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
										deleteUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
										editable: <?php get_currentuserinfo(); global $user_level; if ($user_level > 0) { ?>editable<?php } else { ?> false <?php } ?>,
										addable: <?php get_currentuserinfo(); global $user_level; if ($user_level > 0) { ?>addablepage<?php } else { ?> addablecon == "true" ? true : false <?php } ?>
									});
								});
							}
						}
					
				});
				
				//comment thumbnails
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
		
		//get image source from post for thumbnail
		function imageSource(id) {
			var idreturn = "";
			$('<?php echo get_option('demon_image_annotation_postcontainer'); ?> img').each(function() {
				var imgid = $(this).attr("id");
				if(imgid == "img-" + id) {
					idreturn = $(this).attr("src");
				}
			});
			
			return idreturn;
		}
	<?php } ?>
	
	</script>
    <?php
}

//*************** Comment function ***************
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

function guan_getImgID_inserter($comment_ID = 0){
	getImgID();
	$guan_comment_content = get_comment_text();
	return $guan_comment_content;
}

if( (get_option('demon_image_annotation_display') == '0') ) {
	if( (get_option('demon_image_annotation_thumbnail') == '0') ) {
		add_filter('comment_text', 'guan_getImgID_inserter', 10, 4);
	}
}

add_action('wp_head', 'load_image_annotation_js');

//*************** Admin function ***************
function demonimageannotation_admin() {
	include('imageannotation-admin.php');
}

function demonimageannotation_admin_actions() {
    add_options_page("demon-Image-Annotation", "demon-Image-Annotation", 1, "demon-Image-Annotation", "demonimageannotation_admin");
}

if ( is_admin() ){ // admin actions
  	add_action('admin_menu', 'demonimageannotation_admin_actions');
}
?>