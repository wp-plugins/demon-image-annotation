(function($) {
    $.fn.initAnnotate = function(options) {
		var imgcount = 1
        var opts = $.extend({}, $.fn.initAnnotate.defaults, options);
		container = opts.container;
		adminOnly = opts.adminOnly;
		pageOnly = opts.pageOnly;
		pluginPath = opts.pluginPath;
		autoImgID = opts.autoImgID;
		postID = opts.postID;
		removeImgTag = opts.removeImgTag;
		mouseoverDesc = opts.mouseoverDesc;
		maxLength = opts.maxLength;
		imgLinkOption = opts.imgLinkOption;
		imgLinkDesc = opts.imgLinkDesc;
		userLevel = opts.userLevel;
		previewOnly = opts.previewOnly;
		
		$(container + ' img').each(function(index) {
			var idname = $(this).attr("id")
			var source = $(this).attr('src');
			idname = idname == undefined ? '' : idname;
			
			var exclude = false;
			if($(this).attr('exclude') != undefined || idname.substring(4,idname.length) == 'exclude'){
				exclude = true;
			}
			if(!exclude){
				var editable=false;
				//check if image annotation addable attribute exist
				var addablecon = $(this).attr("addable");
				
				//disable if image annotation addable for admin only
				if(adminOnly==0){
					//admin
					addablecon = false;
				}else{
					//not admin
					addablecon = addablecon == undefined ? true : addablecon;
				}
									
				//disable addable button if not in single page
				if(pageOnly==1||pageOnly==2){
					//addablecon = false;
				}
				
				//find image link if exist
				var imagelink = $(this).parent("a").attr('href');
				var imagetitle = $(this).parent("a").attr('title');
				imagetitle = imagetitle == undefined ? '' : imagetitle
				
				var imgid = "";
				if(autoImgID==0){
					//auto insert image id attribute
					imgid = $(this).getMD5(source);
					imgid = "img-" + postID + "-" + imgid.substring(0,10);
				}
				
				//replace if image id attribute exist
				if(idname.substring(0,4) == "img-") {
					imgid = idname;
				}
				if(imgid.substring(0,4) == "img-") {
					//deactive the link if exist
					$(this).parent("a").removeAttr("href");
					if(removeImgTag==0) {
						//remove the link title attribute
						$(this).parent("a").removeAttr("title");
					}
					$(this).attr("id", imgid);
					$(this).wrap($('<div id=' + imgid.substring(4,imgid.length) + ' class="dia-holder" ></div>'));
					var imagenotetag = mouseoverDesc != '' ? ' | '+mouseoverDesc : mouseoverDesc;
					
					var divider;
					if(mouseoverDesc != '') {
						if(imgLinkOption==0){
							imgLinkDesc = imgLinkDesc == '%NONE%' ? imagetitle : imgLinkDesc;
							divider = imagelink != undefined ? ' | ' : '';
						}else{
							imgLinkDesc=''
							divider = '';	
						}
					} else {
						if(imgLinkOption==0){
							imgLinkDesc = imgLinkDesc == '%NONE%' ? imagetitle : imgLinkDesc;
							divider = imgLinkDesc != '' ? ' | ' : '';
						}else{
							imgLinkDesc=''
							divider = '';
						}
					}
					
					if(userLevel > 3) {
						//admin
						editable = true;
						addablecon = true;
					} else {
						//annyoumous
						editable = false;
					}
					
					var imagelinktag = imagelink != undefined ? '<a href="' + imagelink + '" target="blank">' + imgLinkDesc + '</a>' : '';
					var newimgcount = imgcount < 10 ? "0" + imgcount : imgcount;
					$(this).before('<div class="dia-desc-holder"><span class="dia-desc">'+ newimgcount + imagenotetag + divider + imagelinktag + '</span></div>');
					imgcount++
								
					$(this).mouseover(function() {
						$(this).annotateImage({
							getPostID: postID,
							getImgID: imgid,
							pluginPath: pluginPath,
							editable: editable,
							addable: addablecon,
							maxLength: maxLength,
							previewOnly: previewOnly
						});
					});
				}
			}
		});
		
		$.fn.initAnnotate.commentThumbnail();
    };
	
	//inject thumbnails
	$.fn.initAnnotate.commentThumbnail = function() {
		$('div').each(function() {
			var divid = $(this).attr("id");
			divid = divid == undefined ? '' : divid;
			if(divid.substring(0,8) == "comment-") {
				var getimgsrc = $.fn.initAnnotate.imageSource(divid.substring(8,divid.length));
				if(getimgsrc != "") {
					$(this).remove("noted");
					$(this).html('<a href="#' + divid.substring(8,divid.length) + '"><div class="dia-thumbnail"><div class="dia-thumbnail-src" style="background:url('+getimgsrc+') no-repeat; background-size:cover;"></div></div></a>');
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