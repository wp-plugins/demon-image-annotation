<?php
global $wpdb;
$table_note = $wpdb->prefix . "demon_imagenote";
$table_comment = $wpdb->prefix . "comments";

if( (get_option('demon_image_annotation_comments') == '0') ) {
	//Moderate
	$query = "SELECT ".$table_note.".note_ID, ".$table_note.".note_approved, ".$table_comment.".comment_approved
			FROM ".$table_note.", ".$table_comment."
			WHERE ".$table_note.".note_approved != ".$table_comment.".comment_approved COLLATE utf8_unicode_ci
			AND ".$table_note.".note_comment_ID = ".$table_comment.".comment_ID";
			
	$result = $wpdb->get_results($query);
	foreach ($result as $r) {
		$wpdb->query("UPDATE ".$table_note." SET note_approved = '".$r->comment_approved."' WHERE note_ID = ".$r->note_ID);
	}
	
	//Content
	$query = "SELECT ".$table_note.".note_ID, ".$table_note.".note_text, ".$table_comment.".comment_content
			FROM ".$table_note.", ".$table_comment."
			WHERE ".$table_note.".note_text != ".$table_comment.".comment_content
			AND ".$table_note.".note_comment_ID = ".$table_comment.".comment_ID";
	
	$result = $wpdb->get_results($query);
	foreach ($result as $r) {
		$wpdb->query("UPDATE ".$table_note." SET note_text = '".$r->comment_content."' WHERE note_ID = ".$r->note_ID);
	}
	
	//Author
	$query = "SELECT ".$table_note.".note_ID, ".$table_note.".note_author, ".$table_comment.".comment_author
			FROM ".$table_note.", ".$table_comment."
			WHERE ".$table_note.".note_author != ".$table_comment.".comment_author
			AND ".$table_note.".note_comment_ID = ".$table_comment.".comment_ID";
	
	$result = $wpdb->get_results($query);
	foreach ($result as $r) {
		$wpdb->query("UPDATE ".$table_note." SET note_author = '".$r->comment_author."' WHERE note_ID = ".$r->note_ID);
	}
	
	//Email
	$query = "SELECT ".$table_note.".note_ID, ".$table_note.".note_email, ".$table_comment.".comment_author_email
			FROM ".$table_note.", ".$table_comment."
			WHERE ".$table_note.".note_email != ".$table_comment.".comment_author_email
			AND ".$table_note.".note_comment_ID = ".$table_comment.".comment_ID";
	
	$result = $wpdb->get_results($query);
	foreach ($result as $r) {
		$wpdb->query("UPDATE ".$table_note." SET note_email = '".$r->comment_author_email."' WHERE note_ID = ".$r->note_ID);
	}
}
?>