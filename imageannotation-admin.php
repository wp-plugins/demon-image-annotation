<?php 
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
		
		//wordpress comment
		$dia_comments = $_POST['dia_comments'];
		update_option('demon_image_annotation_comments', $dia_comments);
		
		//auto insert image id
		$dia_autoimageid = $_POST['dia_autoimageid'];
		update_option('demon_image_annotation_autoimageid', $dia_autoimageid);
		
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
		$dia_everypage = get_option('demon_image_annotation_everypage');
		$dia_comments = get_option('demon_image_annotation_comments');
		$dia_homepage = get_option('demon_image_annotation_homepage');
		$dia_archive = get_option('demon_image_annotation_archive');
		$dia_autoimageid = get_option('demon_image_annotation_autoimageid');
	}
	
	
?>

<div class="wrap">
<?php    echo "<h2>" . __( 'demon-Image-Annotation Settings', 'dia_trdom' ) . "</h2>"; ?>

<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="dia_hidden" value="Y">
    Finally got time to update this plugin! ENJOY!<br />
    
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
                <em>This is where the image annotation check and load, <br />
                put in the div wrapper id or class where your post content appear.</em><br /><br />
                <strong>Example (.entrybody)</strong><br />
                <code>
                &lt;div class="entrybody&gt;<br />
                &nbsp;&nbsp;&nbsp; &lt;?php the_content(); ?&gt;<br />
                &lt;/div&gt;</code><br />
            </td>
        </tr>
        
        <tr>
            <th>
                <label><?php _e("auto insert images id : " ); ?></label>
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
                <code>&lt;img id="img-4774005463" src="http://farm5.static.flickr.com/4121/4774005463_3837b6de44_o.jpg" /&gt;</code>
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
            	<em>Enable or disable to show gravatar in image note.</em>
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