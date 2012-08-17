=== demon image annotation ===
Contributors: demonisblack
Tags: comment,comments,image,images,note,notes,annotation,image annotation,dannychoo,facebook,tag,flickr
Requires at least: 2.5
Tested up to: 3.4
Stable tag: 2.5

Allows you to add textual annotations to images by select a region of the image and then attach a textual description.

== Description ==

This plugin allows you to add textual annotations to images by select a region of the image and then attach a textual description, the concept of annotating images with user comments.
Integration with JQuery Image Annotation from Chris (http://www.flipbit.co.uk/jquery-image-annotation.html) with PHP support from GitHub (http://github.com/stas/jquery-image-annotate-php-fork).

Some features:


* Admin page
* Option to approve, edit amd remove image notes in admin page.
* Auto insert unique id attribute for all the images for image note.
* Show notes on other pages such as home and archive.
* Gravatar in the notes
* Option to sync with wordpress comments.
* Option to show thumbnail in comment list.
* Option to include post id in every auto insert images id.
* 'Mouseover to load notes' on top of every image note (editable).
* 'Link' on top of every image note if hyperlink image (editable).

== Installation ==

1. Put the plugin folder into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Choose the settings you want in demon-image-annotation settings.
4. Complete usage instructions are available here. (http://www.superwhite.cc/demon/image-annotation-plugin)

== Frequently Asked Questions ==

No questions have been asked.

== Screenshots ==

1. Demonstration of demon image annotation.
2. Demonstration of demon image annotation.
3. Image annotation settings.
4. Image annotation list.

== Changelog ==
= 2.5   =
* Fixed image annotation script.
* Fixed php JSON data.
* Fixed not workin in ie.
* New admin table list.

= 2.4.8   =
* Fixed MD5 not working.


= 2.4.7   =
* Fixed bugs.


= 2.4.6   =
* Fixed jquery conflict.


= 2.4.5   =
* Fixed missing add button.


= 2.4.4   =
* Added approve and unapprove button for selected image notes.
* Fixed table prefix issue

= 2.4.3   =
* Fixed on table name issue.
* Fixed pop up error while saving.
* Fixed image notes not loading (when comment or image note is not approve yet).
* Show error occured message when loading image notes timeout.
* Add option to remove HTML image tags.

= 2.4.1   =
* Fixed on Image Notes Tab not display in Safari browser.

= 2.4   =
* Fixed on Chrome and IE browsers.

= 2.3   =
* Fixed return and new line issue that cause image note stop loading.

= 2.2   =
* New image note as waiting approval even it is not sync with wordpress comment.
* Fixed image note not loading with special characters.
* Image note settings now is display for admin only.
* Customize default avatar for image note author gravatar.

= 2.1   =
* Rounded border.
* Add list of image notes in admin page.
* Add option to approve, edit and delete image notes.
* Add option to change mouseover description and image hyperlink name.
* Add option to lnclude post id in every auto insert images id.
* Fix issue of database prefix is not wp_.
* move author to top.

= 2.0   =
* Admin page
* Auto insert id attribute start with "img-".
* Add notes to your uploaded pictures and embed pictures.
* Add author gravatar on notes.
* Add option to show image notes not only in single page but other pages such as home and archives.
* Add option which enable user to disable or enable noted image for admin only or every user.
* Add option which enable user to disable or enable WordPress commenting system.
* Add option which enable user to disable or enable noted image thumbnail at comment list.
* Add description on top of every image note 'Mouseover to load notes'.
* Add link on top of every image note if hyperlink image.

= 1.2   =
* Delete comments
* Comment thumbnail hover

= 1.1   =
* Fix note overlap
* Image note user addable