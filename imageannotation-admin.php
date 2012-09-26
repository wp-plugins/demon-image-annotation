<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class My_Example_List_Table extends WP_List_Table {
    function __construct(){
		global $status, $page;
			parent::__construct( array(
				'singular'  => __( 'note', 'myannotatetable' ),     //singular name of the listed records
				'plural'    => __( 'notes', 'myannotatetable' ),   //plural name of the listed records
				'ajax'      => false        //does this table support ajax?
	
		) );
    }

  function no_items() {
    _e( 'No notes found.' );
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'note_img_ID':
        case 'note_comment_ID':
        case 'note_top':
        case 'note_left':
		case 'note_width':
		case 'note_height':
		case 'note_text':
            return $item->$column_name;
		case 'note_author':
			return $item->$column_name.'<br />'. $item->note_email;
		case 'note_response':
		if( (get_option('demon_image_annotation_comments') == '0') ) {
			global $wpdb;
			$list = $wpdb->get_results("select * from " . $wpdb->prefix . "comments where comment_ID ='".$item->note_comment_ID."' and comment_content = '".$item->note_text."'");
			$count;
			foreach ($list as $t) {
				$comment_approved = $t->comment_approved;
				$post = get_post($t->comment_post_ID);
				$posttitle = $post->post_title;
				return $posttitle;
				$count ++;
			}
			if($count == 0) {
				return 'No sync with wordpress comment';
			}
		} else {
			return 'No sync with wordpress comment';
		}
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function get_sortable_columns() {
	  $sortable_columns = array(
		'note_img_ID'  => array('note_img_ID',false),
		'note_author' => array('note_author',false),
		'note_text'   => array('note_text',false),
		'note_response'  => array('note_response',false)
	  );
	  return $sortable_columns;
	}

	function get_columns(){
			$columns = array(
				'cb'        => '<input type="checkbox" />',
				'note_img_ID' => __( 'IMG ID', 'myannotatetable' ),
				'note_author' => __( 'Author', 'myannotatetable' ),
				'note_text'    => __( 'Text', 'myannotatetable' ),
				'note_top'    => __( 'Top', 'myannotatetable' ),
				'note_left' => __( 'Left', 'myannotatetable' ),
				'note_width' => __( 'Width', 'myannotatetable' ),
				'note_height' => __( 'Height', 'myannotatetable' ),
				'note_response' => __( 'In Response to', 'myannotatetable' ),
				'note_action' => __( 'Action', 'myannotatetable' )
			);
			 return $columns;
		}
	
	function usort_reorder( $a, $b ) {
	  // If no sort, default to title
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'note_ID';
	  // If no order, default to asc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  // Determine sort order
	  $result = strcmp( $a[$orderby], $b[$orderby] );
	  // Send final sort direction to usort
	  return ( $order === 'asc' ) ? $result : -$result;
	}
	
	function column_note_action($item){
		if( (get_option('demon_image_annotation_comments') == '0') ) {
			//sync with wordpress comments
			if($item->comment_approved == 1) {
				$condition = 'Unapprove';
				$text = 'Approve';
			} else {
				$condition = 'Approve';
				$text = 'Unapprove';
			}
		} else {
			if($item->note_approved == 1) {
				$condition = 'Unapprove';
				$text = 'Approve';
			} else {
				$condition = 'Approve';
				$text = 'Unapprove';
			}
		}
	  $actions = array(
			$condition  => sprintf('<a href="?page=%s&action=%s&note=%s&tab=%s&paged=%s">'.$condition.'</a>',$_REQUEST['page'],strtolower($condition),$item->note_ID, $_REQUEST['tab'], $_REQUEST['paged']),
			'edit'      => sprintf('<a href="?page=%s&action=%s&note=%s&tab=%s&paged=%s">Edit</a>',$_REQUEST['page'],'edit',$item->note_ID, $_REQUEST['tab'], $_REQUEST['paged']),
			'delete'    => sprintf('<a href="?page=%s&action=%s&note=%s&tab=%s&paged=%s">Delete</a>',$_REQUEST['page'],'delete', $item->note_ID, $_REQUEST['tab'], $_REQUEST['paged']),
		);
	  return sprintf('%1$s %2$s', $text, $this->row_actions($actions) );
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete' , 'myannotatetable'),
			'approve' => __( 'Approve' , 'myannotatetable'),
			'unapprove' => __( 'Unapprove' , 'myannotatetable')
		);
	
		return $actions;
	}
	
	// Handle bulk actions
	function process_bulk_action() {
		$noteid = ( is_array( $_REQUEST['note'] ) ) ? $_REQUEST['note'] : array( $_REQUEST['note'] );
		global $wpdb;
		$table_note = $wpdb->prefix . "demon_imagenote";
		$table_comment = $wpdb->prefix . "comments";
		
		// Define our data source
		if ( 'delete' === $this->current_action() ) {
			foreach ( $noteid as $id ) {
				$id = absint( $id );
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					//find comment id and comment text
					$query = "SELECT * from ".$table_note." where note_ID =".$id;
					$result = $wpdb->get_results($query);
					foreach ($result as $r) {
						$comment_id = $r->note_comment_ID;
						$content = $r->note_text;
						//delete comment
						$wpdb->query("DELETE FROM ".$table_comment." WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'"); 
					}
				}
				//delete note
				$wpdb->query( "DELETE FROM ".$table_note." WHERE note_ID = $id");
			}
			echo '<div class="updated"><p><strong>Images Note Deleted.</strong></p></div>';
		} else if ( 'approve' === $this->current_action() ) {
			foreach ( $noteid as $id ) {
				$id = absint( $id );
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					//find comment id and comment text
					$query = "SELECT * from ".$table_note." where note_ID =".$id;
					$result = $wpdb->get_results($query);
					foreach ($result as $r) {
						$comment_id = $r->note_comment_ID;
						$content = $r->note_text;
						//approve comment
						$wpdb->query("UPDATE ".$table_comment." SET comment_approved = '1' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'");
					}
				}
				//approve note
				$wpdb->query( "UPDATE ".$table_note." SET `note_approved` = '1' where note_ID = $id");
			}
			echo '<div class="updated"><p><strong>Images Note Approved.</strong></p></div>';
		} else if ( 'unapprove' === $this->current_action() ) {
			foreach ( $noteid as $id ) {
				$id = absint( $id );
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					//find comment id and comment text
					$query = "SELECT * from ".$table_note." where note_ID =".$id;
					$result = $wpdb->get_results($query);
					foreach ($result as $r) {
						$comment_id = $r->note_comment_ID;
						$content = $r->note_text;
						//approve comment
						$wpdb->query("UPDATE ".$table_comment." SET comment_approved = '0' WHERE comment_ID = ".$comment_id." and comment_content = '".$content."'");
					}
				}
				//unapprove note
				$wpdb->query( "UPDATE ".$table_note." SET `note_approved` = '0' where note_ID = $id");
			}
			echo '<div class="updated"><p><strong>Images Note Unapproved.</strong></p></div>';
		} else if ( 'edit' === $this->current_action() ) {
			echo '<div class="updated"><p><strong>Images Note Edit.</strong></p></div>';
		} else if ( 'update' === $this->current_action() ) {
			echo '<div class="updated"><p><strong>Images Note Updated.</strong></p></div>';
		}
		
		if (isset($_POST['update_single_note'])) {
			if (!wp_verify_nonce($_POST['_wpnonce'], 'imagenotesactionupdate')) die('Update security violated');	
			if($_POST['update_single_note'] == "yes") {
				$imgid = $_POST['note_ID'];
				$commentid = $_POST['note_comment_ID'];
				$note_text_old = $_POST['note_text_old'];
				
				if( (get_option('demon_image_annotation_comments') == '0') ) {
					$wpdb->query("UPDATE ".$table_comment." SET comment_content = '".$_POST['note_text']."' WHERE comment_ID = ".$commentid." and comment_content = '".$note_text_old."'");
				}
				$query = "UPDATE `".$table_note."` SET
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
			}
		}
	}
	
	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="note[]" value="%s" />', $item->note_ID
		);
	}
	
	function editNote() {
		if ( 'edit' === $this->current_action() ) {
			$noteid = ( is_array( $_REQUEST['note'] ) ) ? $_REQUEST['note'] : array( $_REQUEST['note'] );
			global $wpdb;
			$table_note = $wpdb->prefix . "demon_imagenote";
			$table_comment = $wpdb->prefix . "comments";
			
			foreach ( $noteid as $id ) {
				$id = absint( $id );
				
				$query = "SELECT * from ".$table_note." where note_ID = $id";
				$result = $wpdb->get_results($query);
				?>
                <div class="wrap">
				<form name="dia_update_form" method="post" action="<?php echo sprintf('?page=%s&action=%s&tab=%s',$_REQUEST['page'],'update', $_REQUEST['tab']); ?>">
          
				<input type="hidden" name="update_single_note" value="yes">
				<?php wp_nonce_field('imagenotesactionupdate') ?>
                
				<?php
				foreach ($result as $r) {
					echo '<table class="widefat" width="100%">';
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
					echo '<td><input type="submit" name="update" value="update" class="button-secondary action" /><input type="button" name="cancel" value="cancel"  class="button-secondary action" onclick="window.location = \'?page='.$_REQUEST['page'].'&tab='.$_REQUEST['tab'].'\';" /></td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo "</table>";
				?></form></div><?php
			}
		}
	}
	
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$table_note = $wpdb->prefix . "demon_imagenote";
		$table_comment = $wpdb->prefix . "comments";
		$screen = get_current_screen();
		
		/* Handle our bulk actions */
			$this->process_bulk_action();
			
		/* -- Preparing your query -- */
			//check is sync with wordpress comment
			if( (get_option('demon_image_annotation_comments') == '0') ) {
				//with wordpress comment
				$query = "SELECT ".$table_note.".*, ".$table_comment.".comment_approved FROM ".$table_note." LEFT OUTER JOIN ".$table_comment." on ".$table_comment.".comment_ID = ".$table_note.".note_comment_ID";
			} else {
				$query = "SELECT * FROM ".$table_note;
			}
		/* -- Ordering parameters -- */
			//Parameters that are going to be used to order the result
			$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
			$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
			if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; } else { $query.= ' ORDER BY note_ID DESC';}
	
		/* -- Pagination parameters -- */
			//Number of elements in your table?
			$totalitems = $wpdb->query($query); //return the total number of affected rows
			//How many to display per page?
			$perpage = 10;
			//Which page is this?
			$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
			//Page Number
			if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
			//How many pages do we have in total?
			$totalpages = ceil($totalitems/$perpage);
			//adjust the query to take pagination into account
			if(!empty($paged) && !empty($perpage)){
				$offset=($paged-1)*$perpage;
				$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
			}
	
		/* -- Register the pagination -- */
			$this->set_pagination_args( array(
				"total_items" => $totalitems,
				"total_pages" => $totalpages,
				"per_page" => $perpage,
			) );
			//The pagination links are automatically built according to those parameters
	
		/* -- Register the Columns -- */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			
		/* -- Fetch the items -- */
			$this->items = $wpdb->get_results($query);
	}

} //class

?>

<?php //tab settings 
	$tab = isset($_REQUEST['tab']) ? trim($_REQUEST['tab']) : 'settings';
	global $wpdb;
	$table_name = $wpdb->prefix . "demon_imagenote";
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
			
			//show on pages
			$dia_pages = $_POST['dia_pages'];
			update_option('demon_image_annotation_pages', $dia_pages);
			
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
			
			//admin only
			$dia_imgtag = $_POST['dia_imgtag'];
			update_option('demon_image_annotation_dia_imgtag', $dia_imgtag);
			
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
			$dia_pages = get_option('demon_image_annotation_pages');
			$dia_autoimageid = get_option('demon_image_annotation_autoimageid');
			$dia_mouseoverdesc = get_option('demon_image_annotation_mouseoverdesc');
			$dia_linkdesc = get_option('demon_image_annotation_linkdesc');
			$dia_postid = get_option('demon_image_annotation_postid');
			$dia_imgtag = get_option('demon_image_annotation_dia_imgtag');
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
                    <label>Pages : <input name="dia_pages" type="checkbox" value="1" <?php ($dia_pages == 1) ? print 'checked="checked"' :''; ?> /></label>
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
            <tr>
                <th>
                    <label><?php _e("Remove Image Tag : " ); ?></label>
                </th>
              <td>
                  <?php 
                    $sndisplaymode = array( 0 => __( 'Yes' ), 1 => __( 'No' ) );	
                    foreach ( $sndisplaymode as $key => $value) {
                        $selected = $dia_imgtag == $key ? 'checked="checked"' : '';
                        echo "<label><input type='radio' name='dia_imgtag' value='" . esc_attr($key) . "' $selected/> $value</label>";
                    } ?>
                    <br />
                    <em>Choose Yes to remove HTML Image tag.</em>
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
        <!--new-->
        <?php
		 global $myListTable;
		  $myListTable = new My_Example_List_Table();
		  echo '</pre><div class="wrap"><h2>Image Notes</h2>';
		  $myListTable->editNote();
		  $myListTable->prepare_items();
		?>
		  <form method="post">
			<input type="hidden" name="page" value="myannotatetable">
			<?php
		  	$myListTable->display(); 
		  	echo '</form></div>'; 
		?>
    </div>
<?php } ?>