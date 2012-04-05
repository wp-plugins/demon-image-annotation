<?php 
/*
Plugin Name: Demon Image Annotations
Plugin URI: http://www.superwhite.cc/demon/image-annotation-plugin
Description: 'Allows you to add textual annotations to images by select a region of the image and then attach a textual description, the concept of annotating images with user comments.'
Author: Demon
Version: 2.4.7
Author URI: http://www.superwhite.cc
*/

//*************** Header function ***************
function load_jquery_js() {
	wp_deregister_script('jquery');
	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js');
    wp_enqueue_script( 'jquery' );
	
	wp_deregister_script('jquery-ui');
	wp_register_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.js');
	wp_enqueue_script('jquery-ui');
	
	$plugindir = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	wp_deregister_script('jquery-annotate');
	wp_register_script('jquery-annotate', $plugindir ."/js/jquery.annotate.js",array('jquery'));
	wp_enqueue_script('jquery-annotate');
	
	wp_deregister_script('jquery-md5');
	wp_register_script('jquery-md5', $plugindir ."/js/jquery.md5.js",array('jquery'));
	wp_enqueue_script('jquery-md5');	
}

function load_image_annotation_js() {
	$plugindir = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
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
		jQuery.noConflict();
		jQuery(document).ready(function(){
				//image annotaion
				jQuery("<?php echo get_option('demon_image_annotation_postcontainer'); ?> img").each(function() {						
						var idname = jQuery(this).attr("id")
						var source = jQuery(this).attr('src');
						
						if(idname.substring(4,idname.length) != 'exclude') {
							//check if image annotation addable attribute exist
							var addablecon = jQuery(this).attr("addable")
														
							//disable if image annotation addable for admin only
							<?php if (get_option('demon_image_annotation_admin') == '0') { ?>
							addablecon = false;
							<?php } else { ?>
							addablecon = addablecon == undefined ? "true" : addablecon;
							<?php } ?>
							
							//enable addable and editable only in single page						
							//disable addable button if not in single page
							<?php if ($plugin != 1) { ?>
							var addablepage = false;
							var editable = false
							addablecon = false;
							<?php  } else { ?>
							var addablepage = true;
							var editable = true;
							<?php  } ?>
							
							//find image link if exist
							var imagelink = jQuery(this).parent("a").attr('href');
							var imgid = ""
								
							//auto insert image id attribute
							<?php if( (get_option('demon_image_annotation_autoimageid') == '0') ) { ?>
								imgid = jQuery.md5(source);
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
								jQuery(this).parent("a").removeAttr("href");
								
								<?php if( (get_option('demon_image_annotation_dia_imgtag') == '0') ) { ?>
								jQuery(this).parent("a").removeAttr("title");
								<?php } ?>
								
								jQuery(this).attr("id", imgid);
								jQuery(this).wrap(jQuery('<div id=' + imgid.substring(4,imgid.length) + ' ></div>'));
								var imagenotedesc = "<?php echo get_option('demon_image_annotation_mouseoverdesc'); ?>";
								var imagelinkdesc = "<?php echo get_option('demon_image_annotation_linkdesc'); ?>";
								
								var imagenotetag = imagenotedesc != '' ? imagenotedesc : imagenotedesc;
								var imagelinktag = imagelink != undefined ? '<a href="' + imagelink + '" target="blank">' + imagelinkdesc + '</a>' : '';
								var divider;
								
								if(imagenotedesc != '') {
									divider = imagelink != undefined ? ' | ' : '';
								} else if (imagelink != undefined) {
									divider = imagenotetag == '' ? '' : ' | ';
								} else {
									divider = '';
								}
								
								jQuery(this).before('<div class="image-note-desc">'+ imagenotetag + divider + imagelinktag + '</div>');
							
								jQuery(this).mouseover(function() {
									jQuery(this).annotateImage({
										getPostID: <?php global $wp_query; $thePostID = $wp_query->post->ID; echo $thePostID; ?>,
										getImgID: imgid,
										pluginUrl: "<?php echo $plugindir; ?>/imageannotation-run.php",
										editable: <?php get_currentuserinfo(); global $user_level; if ($user_level > 0) { ?>editable<?php } else { ?> false <?php } ?>,
										addable: <?php get_currentuserinfo(); global $user_level; if ($user_level > 0) { ?>addablepage<?php } else { ?> addablecon == "true" ? true : false <?php } ?>
									});
								});
							}
						}
					
				});
				
				//comment thumbnails
				jQuery('div').each(function() {
					var divid = jQuery(this).attr("id");
					if(divid.substring(0,8) == "comment-") {
						var getimgsrc = imageSource(divid.substring(8,divid.length));
						if(getimgsrc != "") {
							jQuery(this).remove("noted");
							jQuery(this).html('<div class="image-note-thumbnail"><a href="#' + divid.substring(8,divid.length) + '"><img src="' + getimgsrc + '" /></a></div>');
						}
					}
				});
		});
		
		//get image source from post for thumbnail
		function imageSource(id) {
			var idreturn = "";
			jQuery('<?php echo get_option('demon_image_annotation_postcontainer'); ?> img').each(function() {
				var imgid = jQuery(this).attr("id");
				if(imgid == "img-" + id) {
					idreturn = jQuery(this).attr("src");
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
	$table_name = $wpdb->prefix . "demon_imagenote";
	$imgIDNow = $wpdb->get_var("SELECT note_img_ID FROM ".$table_name." WHERE note_comment_id = ".(int)$commentID);
	
	if($imgIDNow != "") {
		$str = substr($imgIDNow, 4, strlen($imgIDNow));
		echo "<div id=\"comment-".$str."\"><a href='#".$str."'>noted on #".$imgIDNow."</a></div>";
	} else {
		echo "&nbsp;";	
	}
}

function getImgID_inserter($comment_ID = 0){
	getImgID();
	$comment_content = get_comment_text();
	return $comment_content;
}

if( (get_option('demon_image_annotation_display') == '0') ) {
	if( (get_option('demon_image_annotation_thumbnail') == '0') ) {
		add_filter('comment_text', 'getImgID_inserter', 10, 4);
	}
}

//add_action('wp_head', 'load_image_annotation_js');
add_action('wp_enqueue_scripts', 'load_jquery_js');
add_action('wp_head', 'load_image_annotation_js');

//*************** Admin function ***************
function demonimageannotation_admin() {
	include('imageannotation-admin.php');
}

function demonimageannotation_admin_actions() {
	add_menu_page('demon-Image-Annotation', 'demon-Image-Annotation', 'manage_options', 'demon-Image-Annotation', 'demonimageannotation_admin', plugins_url('icon.png',__FILE__));
	changeTableName();
}

function changeTableName() {
	global $wpdb;
	$table_name = $wpdb->prefix . "demon_imagenote";

	//wp_demon_imagenote
    if($wpdb->get_var("show tables like '".$table_name."'") != $table_name) {
   		$sql = "Rename table `demon_imagenote` to `".$table_name."`;";
		$wpdb->query($sql);
		
		$sql = "Rename table `wp_imagenote` to `".$table_name."`;";
		$wpdb->query($sql);
    }
	
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
	  $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
	  `note_ID` int(11) NOT NULL AUTO_INCREMENT,
	  `note_img_ID` varchar(30) NOT NULL,
	  `note_comment_ID` int(11) NOT NULL,
	  `note_author` varchar(100) NOT NULL,
	  `note_email` varchar(100) NOT NULL,
	  `note_top` int(11) NOT NULL,
	  `note_left` int(11) NOT NULL,
	  `note_width` int(11) NOT NULL,
	  `note_height` int(11) NOT NULL,
	  `note_text` text NOT NULL,
	  `note_text_ID` varchar(100) NOT NULL,
	  `note_editable` tinyint(1) NOT NULL,
	  `note_date` datetime NOT NULL,
	  PRIMARY KEY (`note_ID`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;";

	  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	  dbDelta($sql);
   } else {
	  $sql = "ALTER TABLE `".$table_name."` modify `note_img_ID` VARCHAR(30);";
	  $wpdb->query($sql);
	  
	  if($wpdb->get_var("Show columns from ".$table_name." like 'note_approved'") != "note_approved") {
		echo "RUNNING";
   		$sql = "ALTER TABLE `".$table_name."` ADD `note_approved` VARCHAR(20) DEFAULT '1' AFTER `note_editable`;";
	    $wpdb->query($sql);
      }
   }
}


if (is_admin())
{
	add_action('admin_menu', 'demonimageannotation_admin_actions');
}
?>