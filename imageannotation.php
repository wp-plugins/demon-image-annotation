<?php 
/*
Plugin Name: Demon Image Annotations
Plugin URI: http://www.superwhite.cc/demon/image-annotation-plugin
Description: 'Allows you to add textual annotations to images by select a region of the image and then attach a textual description, the concept of annotating images with user comments.'
Author: Demon
Version: 2.5.4
Author URI: http://www.superwhite.cc
*/

//*************** Header function ***************
function load_jquery_js() {
	wp_register_style( 'annotate-style', plugins_url( '/css/annotation.css', __FILE__ ));
    wp_enqueue_style( 'annotate-style' ); 

	wp_deregister_script('jquery');
	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js');
    wp_enqueue_script( 'jquery' );
	
	wp_deregister_script('jquery-ui');
	wp_register_script('jquery-ui', 'http://code.jquery.com/ui/1.8.21/jquery-ui.min.js');
	wp_enqueue_script('jquery-ui');
	
	wp_deregister_script('jquery-annotate');
	wp_register_script('jquery-annotate', plugins_url( 'js/jquery.annotate.js' , __FILE__ ),array('jquery'));
	wp_enqueue_script('jquery-annotate');
	
	wp_deregister_script('jquery-annotate-config');
	wp_register_script('jquery-annotate-config', plugins_url( 'js/jquery.annotate.config.js' , __FILE__ ),array('jquery'));
	wp_enqueue_script('jquery-annotate-config');
}

function load_image_annotation_js() {
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
	} else if(is_page()){
		if( (get_option('demon_image_annotation_pages') == '1') ) {
			$plugin = 2;
		} else {
			$plugin = 0;
		}	
	}
	
	?>
    <script language="javascript">
	<?php if( (get_option('demon_image_annotation_display') == '0' && $plugin != 0) ) { ?>
	//jQuery.noConflict();
	jQuery(document).ready(function(){
		jQuery(this).initAnnotate({
				container:'<?php echo get_option('demon_image_annotation_postcontainer'); ?>',
				admin:<?php echo get_option('demon_image_annotation_admin'); ?>,
				plugin:<?php echo $plugin; ?>,
				pluginpath:'<?php echo plugins_url( 'imageannotation-run' , __FILE__ ); ?>',
				autoiimgd:<?php echo get_option('demon_image_annotation_autoimageid'); ?>,
				postid:<?php global $wp_query; $thePostID = $wp_query->post->ID; echo $thePostID; ?>,
				removeimgtag:<?php echo get_option('demon_image_annotation_autoimageid'); ?>,
				mouseoverdesc:'<?php echo get_option('demon_image_annotation_mouseoverdesc'); ?>',
				linkdesc:'<?php echo get_option('demon_image_annotation_linkdesc'); ?>',
				level:<?php get_currentuserinfo(); global $user_level; echo $user_level;?>
		});
	});
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

function demonimageannotation_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('css/admin.css', __FILE__). '">';
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
	add_action('admin_head', 'demonimageannotation_admin_head');
	add_action('admin_menu', 'demonimageannotation_admin_actions');
}
?>