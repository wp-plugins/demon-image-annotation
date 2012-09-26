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
					$(this).html('<div class="image-note-thumbnail"><a href="#' + divid.substring(8,divid.length) + '"><div class="mask"><img src="' + getimgsrc + '" /></div></a></div>');
					$.fn.initAnnotate.resizeImg($(this));
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
	
	$.fn.initAnnotate.resize = function(obj, input) {
		var ceil=Math.ceil;
		if(input==null)input=200;
		var w_n=input,h_n=input;
		$(obj).each(function(){
			var w=$(obj).width(),h=$(obj).height();
			if(h>w)h_n=ceil(w/h*input);
			else w_n=ceil(h/w*input);
			$(obj).css({width:h_n,height:w_n})
		})
	};
	
	
	$.fn.initAnnotate.resizeImg = function(obj) {
		var target = $(obj).find('.mask')
		var src = $(obj).find('img').attr('src');
		var maxWidth = 55;
		var maxHeight = 55;
		
		$(obj).find('.mask').empty();
		var img = new Image();
		img.onload = function() {
			var width = this.width
			var height = this.height
			
			var finalw = width;
			var finalh = height;
			var ratio = 0;
			
			if(width > height) {
				while(finalh > maxHeight){
					 finalh--
				 }
				 ratio = finalh / height;
			} else {
				while(finalw > maxWidth){
					 finalw--
				 }
				 ratio = finalw / width;
			}
			
			var imgWidth = width * ratio;
			var imgHeight = finalh;
			
			$(this).css('display','none');
			$.fn.initAnnotate.resize($(this),imgWidth);
			//$(this).resize(imgWidth);
			imgHeight = $(this).css('height');
			
			var marLeft = -((imgWidth - maxWidth) / 2);
			var marTop = -((imgHeight - maxHeight) / 2);
			
			$(this).css('margin-left',marLeft + 'px');
			$(this).css('margin-top', marTop + 'px');
			$(this).fadeTo('slow', 1, function() {
			});
			
			$(target).append(this);
		}
		img.src = src;
	};
	
})(jQuery);