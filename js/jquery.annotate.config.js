(function($) {
    $.fn.initAnnotate = function(options) {
		var imgcount = 1
        var opts = $.extend({}, $.fn.initAnnotate.defaults, options);
		container = opts.container;
		admin = opts.admin;
		plugin = opts.plugin;
		pluginpath = opts.pluginpath;
		autoiimgd = opts.autoiimgd;
		postid = opts.postid;
		removeimgtag = opts.removeimgtag;
		mouseoverdesc = opts.mouseoverdesc;
		imagelinkdesc = opts.linkdesc;
		level = opts.level;
		
		$(container + ' img').each(function(index) {
			var idname = $(this).attr("id")
			var source = $(this).attr('src');
			idname = idname == undefined ? '' : idname
			if(idname.substring(4,idname.length) != 'exclude') {
				//check if image annotation addable attribute exist
				var addablecon = $(this).attr("addable");			
				//disable if image annotation addable for admin only
				if(admin==0){
					addablecon = false;
				}else{
					addablecon = addablecon == undefined ? true : addablecon;
				}
				//enable addable and editable only in single page						
				//disable addable button if not in single page
				if(plugin==0){
					var addablepage = false;
					var editable = false
					addablecon = false;
				}else{
					var addablepage = true;
					var editable = true;
				}
				//find image link if exist
				var imagelink = $(this).parent("a").attr('href');
				var imgid = "";
				//auto insert image id attribute
				if(autoiimgd==0){
					imgid = $(this).getMD5(source);
					if(autoiimgd==0){
						imgid = "img-" + postid + "-" + imgid.substring(0,10);
					}else{
						imgid = "img-" + imgid.substring(0,10);
					}
				}
				//replace if image id attribute exist
				if(idname.substring(0,4) == "img-") {
					imgid = idname;
				}
				if(imgid.substring(0,4) == "img-") {
					//deactive the link if exist
					$(this).parent("a").removeAttr("href");
					if(removeimgtag) {
						$(this).parent("a").removeAttr("title");
					}
					$(this).attr("id", imgid);
					$(this).wrap($('<div id=' + imgid.substring(4,imgid.length) + ' ></div>'));
					var imagenotetag = mouseoverdesc != '' ? mouseoverdesc : mouseoverdesc;
					var imagelinktag = imagelink != undefined ? '<a href="' + imagelink + '" target="blank">' + imagelinkdesc + '</a>' : '';
					
					var divider;
					if(mouseoverdesc != '') {
						divider = imagelink != undefined ? ' | ' : '';
					} else if (imagelink != undefined) {
						divider = imagenotetag == '' ? '' : ' | ';
					} else {
						divider = '';
					}
					if(level > 0) {
						editable = editable
						addablepage = addablepage
					} else {
						editable = false;
						addablepage = addablecon;
					}
					var newimgcount = imgcount < 10 ? "0" + imgcount : imgcount;
					$(this).before('<div class="image-note-desc">'+ newimgcount + " | " + imagenotetag + divider + imagelinktag + '</div>');
					imgcount++
								
					$(this).mouseover(function() {
						$(this).annotateImage({
							getPostID: postid,
							getImgID: imgid,
							pluginUrl: pluginpath,
							editable: editable,
							addable: addablepage
						});
					});
				}
			}
		});
		
		//comment thumbnails
		$.fn.initAnnotate.commentThumbnail();
    };
	
	$.fn.initAnnotate.commentThumbnail = function() {
		$('div').each(function() {
			var divid = $(this).attr("id");
			divid = divid == undefined ? '' : divid
			if(divid.substring(0,8) == "comment-") {
				var getimgsrc = $.fn.initAnnotate.imageSource(divid.substring(8,divid.length));
				if(getimgsrc != "") {
					$(this).remove("noted");
					$(this).html('<div class="image-note-thumbnail"><a href="#' + divid.substring(8,divid.length) + '"><img src="' + getimgsrc + '" /></a></div>');
				}
			}
		});
	}
	
	//get image source from post for thumbnail
	$.fn.initAnnotate.imageSource = function(id) {
		var idreturn = "";
		$(container + ' img').each(function(index) {
			var imgid = $(this).attr("id");
			if(imgid == "img-" + id) {
				idreturn = $(this).attr("src");
			}
		});
		return idreturn;
	}
})(jQuery);