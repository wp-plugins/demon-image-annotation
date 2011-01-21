<style>
	div.pagination {
		padding: 3px;
		margin: 3px;
		text-align:center;
		font-family:Tahoma,Helvetica,sans-serif;
		font-size:.85em;
	}
	
	div.pagination a {
		border: 1px solid #ccdbe4;
		margin-right:3px;
		padding:2px 8px;

		background-position:bottom;
		text-decoration: none;

		color: #0061de;		
	}
	div.pagination a:hover, div.pagination a:active {
		border: 1px solid #2b55af;
		background-image:none;
		background-color:#3666d4;
		color: #fff;
	}
	div.pagination span.current {
		margin-right:3px;
		padding:2px 6px;
		
		font-weight: bold;
		color: #000;
	}
	div.pagination span.disabled {
		display:none;
	}
	div.pagination a.next{
		border:2px solid #ccdbe4;
		margin:0 0 0 10px;
	}
	div.pagination a.next:hover{
		border:2px solid #2b55af;
	}
	div.pagination a.prev{
		border:2px solid #ccdbe4;
		margin:0 10px 0 0;
	}
	div.pagination a.prev:hover{
		border:2px solid #2b55af;
	}

</style>
<?php //tab settings 
	$tab = isset($_REQUEST['tab']) ? trim($_REQUEST['tab']) : 'settings';
	global $wpdb;
?>
 <div class="wrap">
<?php  echo "<h2>" . __( 'demon-Image-Annotation Settings', 'dia_trdom' ) . "</h2>"; ?>
 Finally got time to update this plugin! ENJOY!<br />Visit my site for more update. <a href="http://www.superwhite.cc" target="_blank">http://www.superwhite.cc</a><br />
<h2>
<a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&tab=settings" class="nav-tab<?php $tab == 'settings' ? print " nav-tab-active" : '' ?>">Settings</a>
<a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&tab=imagenotes" class="nav-tab<?php $tab == 'imagenotes' ? print " nav-tab-active" : ''; ?>">Image Notes</a>
</h2>
</div>
<?php if($tab == 'settings') {
		
		//admin settings
		if($_POST['dia_hidden'] == 'Y') {
			//show on home page
			$dia_homepage = $_POST['dia_homepage'];
			update_option('demon_image_annotation_homepage', $dia_homepage);
			
			//show on archive page
			$dia_archive = $_POST['dia_archive'];
			update_option('demon_image_annotation_archive', $dia_archive);
			
			//post content wrapper
			$dia_csscontainer = $_POST['dia_csscontainer'];
			update_option('demon_image_annotation_postcontainer', $dia_csscontainer);
			
			//plugin status
			$dia_display = $_POST['dia_display'];
			update_option('demon_image_annotation_display', $dia_display);
			
			//admin only
			$dia_admin = $_POST['dia_admin'];
			update_option('demon_image_annotation_admin', $dia_admin);
			
			//comments thumbnail
			$dia_thumbnail = $_POST['dia_thumbnail'];
			update_option('demon_image_annotation_thumbnail', $dia_thumbnail);
			
			//image note gravatar
			$dia_gravatar = $_POST['dia_gravatar'];
			update_option('demon_image_annotation_gravatar', $dia_gravatar);
			
			//image note gravatar
			$dia_gravatardefault = $_POST['dia_gravatardefault'];
			update_option('demon_image_annotation_gravatar_deafult', $dia_gravatardefault);
			
			//wordpress comment
			$dia_comments = $_POST['dia_comments'];
			update_option('demon_image_annotation_comments', $dia_comments);
			
			//auto insert image id
			$dia_autoimageid = $_POST['dia_autoimageid'];
			update_option('demon_image_annotation_autoimageid', $dia_autoimageid);
			
			//mouse over desc
			$dia_mouseoverdesc = $_POST['dia_mouseoverdesc'];
			update_option('demon_image_annotation_mouseoverdesc', $dia_mouseoverdesc);
			
			//link desc
			$dia_linkdesc = $_POST['dia_linkdesc'];
			update_option('demon_image_annotation_linkdesc', $dia_linkdesc);
			
			//post ID
			$dia_postid = $_POST['dia_postid'];
			update_option('demon_image_annotation_postid', $dia_postid);
			
			?>
			<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
			<?php
		} else {
			//Normal page display
			$dia_csscontainer = get_option('demon_image_annotation_postcontainer');
			$dia_display = get_option('demon_image_annotation_display');
			$dia_admin = get_option('demon_image_annotation_admin');
			$dia_thumbnail = get_option('demon_image_annotation_thumbnail');
			$dia_gravatar = get_option('demon_image_annotation_gravatar');
			$dia_gravatardefault = get_option('demon_image_annotation_gravatar_deafult');
			$dia_everypage = get_option('demon_image_annotation_everypage');
			$dia_comments = get_option('demon_image_annotation_comments');
			$dia_homepage = get_option('demon_image_annotation_homepage');
			$dia_archive = get_option('demon_image_annotation_archive');
			$dia_autoimageid = get_option('demon_image_annotation_autoimageid');
			$dia_mouseoverdesc = get_option('demon_image_annotation_mouseoverdesc');
			$dia_linkdesc = get_option('demon_image_annotation_linkdesc');
			$dia_postid = get_option('demon_image_annotation_postid');
		}
	?>
    
    <div class="wrap">
    <form name="dia_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="dia_hidden" value="Y">        
        <?php    echo "<h4>" . __( 'Image Annotation Settings', 'dia_trdom' ) . "</h4>"; ?>
        <table class="form-table" width="100%">
            <tr>
                <th>
                    <label><?php _e("demon-image-annotation plugin status : " ); ?></label>
                </th>
              <td>
                  <?php
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_display == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_display' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Enable or disable the demon-image-annotaion plugins although you want it to be Activate.</em>
              </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e("content wrapper : " ); ?></label>
                </th>
                <td>
                    <input type="text" name="dia_csscontainer" value="<?php echo ($dia_csscontainer == '') ? '#entrybody' : $dia_csscontainer; ?>" size="20"><?php _e(" ex: #entrybody, .entrybody" ); ?><br />
                    <span style="color:#C00">#IMPORTANT</span><br />
                    <em>This is where the image annotation check and load,<br />
                    put in the div wrapper id or class where your post content appear.<br />
                    (Leave it empty will treat all images as image annotation.)</em><br /><br />
                    <strong>Example (.entrybody)</strong><br />
                    <code>
                    &lt;div class="entrybody&gt;<br />
                    &nbsp;&nbsp;&nbsp; &lt;?php the_content(); ?&gt;<br />
                    &lt;/div&gt;</code><br />
                </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e("auto insert image id attribute : " ); ?></label>
                </th>
              <td>
                  <?php
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_autoimageid == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_autoimageid' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Enable jquery to auto insert images id attribute start with 'img-' for all the images,<br />
                    the uniqe id will be generate by img src, it will skip if id attribute is exist.</em><br /><br />
                    <em>Disable if you want to add id attribute manually like old version.</em><br />
                    <code>&lt;img id="img-4774005463" src="http://farm5.static.flickr.com/4121/4774005463_3837b6de44_o.jpg" /&gt;</code><br />
                    <em>Complete usage instructions are available here. (<a href="http://www.superwhite.cc/demon/image-annotation-plugin" target="_blank">http://www.superwhite.cc/demon/image-annotation-plugin</a>)</em>
              </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e("include post id in image id attribute : " ); ?></label>
                </th>
              <td>
                  <?php
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_postid == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_postid' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Include post id in every auto insert images id, this is to avoid duplicate comments when you had same images in different post.<br />
                    Enable this option will not load old images note since the images id is different.<br /><br /></em>
                    
                    <strong>Example (img-postid-4774005463)</strong><br />
                    <code>&lt;img id="img-12-4774005463" src="http://farm5.static.flickr.com/4121/4774005463_3837b6de44_o.jpg" /&gt;</code><br />
              </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e("admin only : " ); ?></label>
                </th>
              <td>
                  <?php 
                    $sndisplaymode = array( 0 => __( 'Yes' ), 1 => __( 'No' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_admin == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_admin' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Choose Yes will only allow admin to add image note, <br />or choose No for every user to add image note.</em>
              </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e("gravatar : " ); ?></label>
                </th>
              <td>
                  <?php 
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_gravatar == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_gravatar' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Enable or disable to show gravatar in image note.</em><br />
                    Default gravatar : <?php echo get_bloginfo('template_url'); ?><input type="text" name="dia_gravatardefault" value="<?php echo $dia_gravatardefault ?>" size="20"><?php _e(" ex: /images/default.png" ); ?><br />
              </td>
            </tr>
            <tr>
                <th>
                    <label><?php _e("other pages? : " ); ?></label>
                </th>
              <td>
                    <label>home : <input name="dia_homepage" type="checkbox" value="1" <?php ($dia_homepage == 1) ? print 'checked="checked"' :''; ?> /></label>
                    <label>archive : <input name="dia_archive" type="checkbox" value="1" <?php ($dia_homepage == 1) ? print 'checked="checked"' :''; ?> /></label>
                    <br />
                    <em>Show image note on others page instead of single page, but add note button will be disabled.</em>
              </td>
            </tr>
            <tr>
                <th>
                    <label><?php _e("mouseover description : " ); ?></label>
                </th>
              <td>
                    <input type="text" name="dia_mouseoverdesc" size="30" value="<?php echo ($dia_mouseoverdesc == '') ? '' : $dia_mouseoverdesc; ?>" size="20"><?php _e(" ex: Mouseover to load notes." ); ?>
                    <br />
                    <em>Show description on top of every image annotation, leave it empty to hide.</em>
              </td>
            </tr>
            <tr>
                <th>
                    <label><?php _e("image hyperlink name : " ); ?></label>
                </th>
              <td>
                    <input type="text" name="dia_linkdesc" size="30" value="<?php echo ($dia_linkdesc == '') ? 'link' : $dia_linkdesc; ?>" size="20"><?php _e(" ex: Link, Flickr" ); ?>
                    <br />
                    <em>Image hyperlink name after mouseover description.</em>
              </td>
            </tr>
        </table><br /><br />
        
        <?php    echo "<h4>" . __( 'Comment Settings', 'dia_trdom' ) . "</h4>"; ?>
        <table class="form-table" width="100%">
            <tr>
                <th>
                    <label><?php _e("Wordpress comments : " ); ?></label>
                </th>
              <td>
                  <?php
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_comments == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_comments' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>If enable all the image note will sync with wordpress commenting system, new image note will add into comment as waiting approval.<br />
                        If disable all the image note will publish without sync with wordpress comment.
                    </em>
              </td>
            </tr>
            <tr>
                <th>
                    <label><?php _e("Comments thumbnail : " ); ?></label>
                </th>
              <td>
                  <?php
                    $sndisplaymode = array( 0 => __( 'Enable' ), 1 => __( 'Disable' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_thumbnail == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_thumbnail' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Enable or disable to show image thumbnail in comment area.</em>
              </td>
            </tr>
        </table>
            
        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', 'dia_trdom' ) ?>" />
        </p>
        </form>
    </div>
    
<?php } else { 
	//image notes		
?>
    <div class="wrap">  
        <?php 
		//image notes selected remove
        if (isset($_POST['remove_selected_notes'])) {
            if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesaction')) die('Remove selected security violated');
			
			if($_POST['s'] != '') {
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					//find comment id and comment text
					$query = "SELECT * from wp_imagenote where note_ID in (" . implode(',', $_POST['s']) . ")";
					$result = $wpdb->get_results($query);
					foreach ($result as $r) {
						$comment_id = $r->note_comment_ID;
						$content = $r->note_text;
						
						//delete comment
						$wpdb->query("DELETE FROM wp_comments WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'"); 
					}
				}
				//delete note
				$query = "delete from wp_imagenote where note_ID in (" . implode(',', $_POST['s']) . ")";
				$wpdb->query($query); ?>
				<div class="updated"><p><strong><?php _e('Images Note Removed.' ); ?></strong></p></div>
            <?php }
        }?>
        
        <?php //image notes single remove
        if (isset($_POST['remove_single_note'])) {            
            if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesaction')) die('Remove single security violated');
			if($_POST['remove_single_note'] == "yes") {
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					//find comment id and comment text
					$query = "SELECT * from wp_imagenote where note_ID in (" .$_POST['note_id']. ")";
					$result = $wpdb->get_results($query);
					foreach ($result as $r) {
						$comment_id = $r->note_comment_ID;
						$content = $r->note_text;
						//delete comment
						$wpdb->query("DELETE FROM wp_comments WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'"); 
					}
				}
				//delete note
                $query = "delete from wp_imagenote where note_ID in (" .$_POST['note_id']. ")";
                $wpdb->query($query) ; ?>
                <div class="updated"><p><strong><?php _e('Images Note Removed.' ); ?></strong></p></div>
            <?php
            }
        }?>
        
         <?php //edit image note
        if (isset($_POST['edit_single_note'])) {
            if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesaction')) die('Edit security violated');
            if($_POST['edit_single_note'] == "yes") {
				$query = "SELECT * from wp_imagenote where note_ID in (" .$_POST['note_id']. ")";
                $result = $wpdb->get_results($query);
                echo "<h4>" . __( 'Edit Image Note', 'dia_trdom' ) . "</h4>";
                ?>
                <form name="dia_update_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="update_single_note" value="yes">
                <?php wp_nonce_field('imagenotesactionupdate') ?>
                
                <?php
                foreach ($result as $r) {
					echo '<table class="widefat" width="500px">';
					echo '<thead><tr>';
					echo '<th width="150">'.$r->note_img_ID.'<input type="hidden" name="note_text_old" value="'.$r->note_text.'"><input type="hidden" name="note_ID" value="'.$r->note_ID.'" /><input type="hidden" name="note_comment_ID" value="'.$r->note_comment_ID.'" /></th>';
					echo '<th></th>';
					echo '</tr></thead>';
					echo '<tbody>';
                    echo '<tr>';
                    echo '<td>Author</td>';
                    echo '<td><input name="note_author" type="text" size="40" value="'.$r->note_author.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Email</td>';
                    echo '<td><input name="note_email" type="text" size="40" value="'.$r->note_email.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Top</td>';
                    echo '<td><input name="note_top" type="text" size="5" value="'.$r->note_top.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Left</td>';
                    echo '<td><input name="note_left" type="text" size="5" value="'.$r->note_left.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Width</td>';
                    echo '<td><input name="note_width" type="text" size="5" value="'.$r->note_width.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Height</td>';
                    echo '<td><input name="note_height" type="text" size="5" value="'.$r->note_height.'" /></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Text</td>';
                    echo '<td><textarea name="note_text" cols="32" rows="5">'.$r->note_text.'</textarea></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td></td>';
                    echo '<td><input type="submit" name="update" value="update" /><input type="button" name="cancel" value="cancel" onClick="javascript: cancelUpdate();" /></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo "</table>";
                ?></form><?php
            ?>
            <?php
            }
        } ?>
        
        <?php //update image note
        if (isset($_POST['update_single_note'])) {
			if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesactionupdate')) die('Update security violated');	
            if($_POST['update_single_note'] == "yes") {
                $imgid = $_POST['note_ID'];
                $commentid = $_POST['note_comment_ID'];
				$note_text_old = $_POST['note_text_old'];
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					$wpdb->query("UPDATE wp_comments SET comment_content = '".$_POST['note_text']."' WHERE comment_ID = ".$commentid." and comment_content = '".$note_text_old."'");
				}
                $query = "UPDATE `wp_imagenote` SET
                                    `note_author` = '".$_POST['note_author']."',
                                    `note_email` = '".$_POST['note_email']."',
                                    `note_top` = '".$_POST['note_top']."',
                                    `note_left` = '".$_POST['note_left']."',
                                    `note_width` = '".$_POST['note_width']."',
                                    `note_height` = '".$_POST['note_height']."',
                                    `note_text` = '".$_POST['note_text']."'	
                                    where note_ID = '".$imgid."'		
                                ";
                $wpdb->query($query);
				echo "UPDATE wp_comments SET comment_content = '".$_POST['note_text']."' WHERE comment_ID = ".$commentid." and comment_content = '".$note_text_old."'";
            ?><div class="updated"><p><strong><?php _e('Image note saved.' ); ?></strong></p></div>
            <?php }
        } ?>
        
        <?php //update comment status
        if (isset($_POST['update_comment_status'])) {
			if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesaction')) die('Update comment security violated');	
            if($_POST['update_comment_status'] == "yes") {
					if( (get_option('demon_image_annotation_comments') == '0') ) {
						//find comment id and comment text
						$query = "SELECT * from wp_imagenote where note_ID in (" .$_POST['note_id']. ")";
						$result = $wpdb->get_results($query);
						foreach ($result as $r) {
							$comment_id = $r->note_comment_ID;
							$content = $r->note_text;
							
							if($_POST['note_comment_status'] == "1") {
								$query = "UPDATE `wp_imagenote` SET
										`note_approved` = '1'
										where note_ID = '".$_POST['note_id']."'		
									";
									$wpdb->query($query);
									echo $query;
								$wpdb->query("UPDATE wp_comments SET comment_approved = '1' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'");
								echo "UPDATE wp_comments SET comment_approved = '1' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'";
								?><div class="updated"><p><strong><?php _e('Comment approved.' ); ?></strong></p></div>
								<?php 
							} else {
								$query = "UPDATE `wp_imagenote` SET
										`note_approved` = '0'
										where note_ID = '".$_POST['note_id']."'		
									";
									$wpdb->query($query);
									echo $query;
								$wpdb->query("UPDATE wp_comments SET comment_approved = '0' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'");
								echo "UPDATE wp_comments SET comment_approved = '0' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'";
								?><div class="updated"><p><strong><?php _e('Comment unapprove.' ); ?></strong></p></div>
								<?php 
							}
						}
					} else {
						if($_POST['note_comment_status'] == "1") {
							$query = "UPDATE `wp_imagenote` SET
										`note_approved` = '1'
										where note_ID = '".$_POST['note_id']."'		
									";
									$wpdb->query($query);
							?><div class="updated"><p><strong><?php _e('Note Comment approved.' ); ?></strong></p></div>
							<?php 
						} else {
							$query = "UPDATE `wp_imagenote` SET
										`note_approved` = '0'
										where note_ID = '".$_POST['note_id']."'		
									";
									$wpdb->query($query);
							?><div class="updated"><p><strong><?php _e('Note Comment unapprove.' ); ?></strong></p></div>
							<?php 
						}
					}
           			?>
            <?php }
        } ?>
        
        <?php echo "<h4>" . __( 'Image Notes', 'dia_trdom' ) . "</h4>"; ?>
        <script language="javascript">
		<!--
		function deleteRecord(recID) {
			var docForm = document.imagenotes;
			document.getElementById("note_id").value = recID;
			document.getElementById("remove_single_note").value = "yes";		
			docForm.submit();
		}
		
		function editRecord(recID) {
			var docForm = document.imagenotes;
			document.getElementById("note_id").value = recID;
			document.getElementById("edit_single_note").value = "yes";		
			docForm.submit();
		}
		
		function cancelUpdate() {
			window.location.href=window.location.href;
		}
		
		function updateComment(recID, commentID, status) {
			var docForm = document.imagenotes;
			document.getElementById("note_id").value = recID;
			document.getElementById("note_comment_id").value = commentID;
			document.getElementById("note_comment_status").value = status;
			document.getElementById("update_comment_status").value = "yes";		
			docForm.submit();
		}
		//->
		</script>
		
        <form name="imagenotes" action="" method="post">
            <input type="hidden" name="remove_single_note" id="remove_single_note" value="" />
            <input type="hidden" name="edit_single_note" id="edit_single_note" value="" />
            <input type="hidden" name="update_comment_status" id="update_comment_status" value="" />
            <input type="hidden" name="note_comment_status" id="note_comment_status" value="" />
            <input type="hidden" name="note_id" id="note_id" value="" />
            <input type="hidden" name="note_comment_id" id="note_comment_id" value="" />
            
            <?php wp_nonce_field('imagenotesaction') ?>
            
            <?php
            require_once("pagination.class.php");
            $items = mysql_num_rows(mysql_query("SELECT * FROM wp_imagenote;")); // number of total rows in the database
            if($items > 0) {
                    $p = new pagination;
                    $p->items($items);
                    $p->limit(10); // Limit entries per page
                    $p->target("options-general.php?page=demon-Image-Annotation&tab=imagenotes");
                    $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
                    $p->calculate(); // Calculates what to show
                    $p->parameterName('paging');
                    $p->adjacents(1); //No. of page away from the current page
             
                    if(!isset($_GET['paging'])) {
                        $p->page = 1;
                    } else {
                        $p->page = $_GET['paging'];
                    }
             
                    //Query for limit paging
                    $limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;
             
            } else {
                //echo "No Record Found";
            } ?>
            
			<?php
                // sending query
                //$result = $wpdb->get_results("SELECT * FROM wp_imagenote");
                $sql = "SELECT * FROM wp_imagenote ORDER BY note_ID DESC ".$limit;
                $result = $wpdb->get_results($sql);
                
                echo '<table class="widefat">';
                echo '<thead><tr>';
                echo '<th>ID</th>';
                echo '<th>IMG ID</th>';
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					echo '<th>Response to</th>';
				}
                echo '<th>Comment ID</th>';
                echo '<th>Author</th>';
                echo '<th>Email</th>';
                echo '<th>Top</th>';
                echo '<th>Left</th>';
                echo '<th>Width</th>';
                echo '<th>Height</th>';
                echo '<th width="200">Text</th>';
                echo '<th>Date</th>';
                echo '<th>Action</th>';
                echo '</tr></thead>';
                
                echo '<tbody>';
                foreach ($result as $r) {
                    echo '<tr>';
                        echo '<td><input type="checkbox" name="s[]" value="'.$r->note_ID.'" /></td>';
                        echo '<td>'.$r->note_img_ID.'</td>';
						if( (get_option('demon_image_annotation_comments') == '0') ) {
							$list = $wpdb->get_results("select * from " . $wpdb->prefix . "comments where comment_ID ='".$r->note_comment_ID."' and comment_content = '".$r->note_text."'");
							$count;
							echo '<td>';
							foreach ($list as $t) {
								$comment_approved = $t->comment_approved;
								$post = get_post($t->comment_post_ID);
								$posttitle = $post->post_title;
								echo $posttitle;
								$count ++;
							}
							if($count == 0) {
								echo 'No sync with wordpress comment';
							}
							echo '</td>';
						}
                        echo '<td>'.$r->note_comment_ID.'</td>';
                        echo '<td>'.$r->note_author.'</td>';
                        echo '<td>'.$r->note_email.'</td>';
                        echo '<td>'.$r->note_top.'</td>';
                        echo '<td>'.$r->note_left.'</td>';
                        echo '<td>'.$r->note_width.'</td>';
                        echo '<td>'.$r->note_height.'</td>';
                        echo '<td>'.$r->note_text.'</td>';
                        echo '<td>'.$r->note_date.'</td>';
                        echo '<td>';
						if( (get_option('demon_image_annotation_comments') == '0') ) {
							if($comment_approved == 1) {
								echo '<input type="button" name="unapprove" value="unapprove" onClick="javascript: updateComment('.$r->note_ID.','. $r->note_comment_ID . ',0);" />';
							} else {
								echo '<input type="button" name="approve" value="approve" onClick="javascript: updateComment('.$r->note_ID.','. $r->note_comment_ID . ',1);" />';
							}
						} else {
							if($r->note_approved == 1) {
								echo '<input type="button" name="unapprove" value="unapprove" onClick="javascript: updateComment('.$r->note_ID.','. $r->note_comment_ID . ',0);" />';
							} else {
								echo '<input type="button" name="approve" value="approve" onClick="javascript: updateComment('.$r->note_ID.','. $r->note_comment_ID . ',1);" />';
							}
						}
						echo '<input type="button" name="edit" value="edit" onClick="javascript: editRecord(' . $r->note_ID . ');" /><input type="button" name="remove" value="Remove" onClick="javascript: deleteRecord(' . $r->note_ID . ');" /></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo "</table>";
				$items == 0 ? print "No Record Found" : '';
                $items > 0 ? print '<input type="submit" name="remove_selected_notes" value="Remove Selected"/>' : '';
                ?>
            <?php $items > 0 ? print $p->show() : '';  // Echo out the list of paging. ?>
        </form>
    </div>
<?php } ?>