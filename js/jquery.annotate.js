/// <reference path="jquery-1.2.6-vsdoc.js" />
(function($) {
    $.fn.annotateImage = function(options) {
        ///	<summary>
        ///		Creates annotations on the given image.
        ///     Images are loaded from the "getUrl" propety passed into the options.
        ///	</summary>
        var opts = $.extend({}, $.fn.annotateImage.defaults, options);
        var image = this;
		
		this.imageloaded = false;
        this.image = this;
        this.mode = 'view';

        // Assign defaults
		this.getPostID = opts.getPostID;
		this.getImgID = opts.getImgID;
        this.pluginPath = opts.pluginPath;
        this.editable = opts.editable;
		this.addable = opts.addable;
        this.useAjax = opts.useAjax;
        this.notes = opts.notes;
		this.maxLength = opts.maxLength;
		this.previewOnly = opts.previewOnly;
		
		// Add the canvas
        this.canvas = $('<div class="dia-canvas"><div class="dia-view dia-loading"></div><div class="dia-edit"><div class="dia-edit-area"></div></div></div>');
        this.canvas.children('.dia-edit').hide();
        this.canvas.children('.dia-view').hide();
        this.image.after(this.canvas);

        // Give the canvas and the container their size and background
		this.canvas.height(this.height());
        this.canvas.width(this.width());
		this.canvas.closest('.dia-holder').width(this.width());
        this.canvas.css('background-image', 'url("' + this.attr('src') + '")');
        this.canvas.children('.dia-view, .dia-edit').height(this.height());
        this.canvas.children('.dia-view, .dia-edit').width(this.width());
		
		
		// Add the behavior: hide/show the notes when hovering the picture
        this.canvas.hover(function() {
            if ($(this).children('.dia-edit').css('display') == 'none') {
                $(this).children('.dia-view').show();
            }
        }, function() {
            if($(this).children().hasClass('dia-error') || $(this).children().hasClass('dia-loading')) {
            	$(this).children('.dia-view').show();
			} else {
				$(this).children('.dia-view').hide();
			}
        });

        this.canvas.children('.dia-view').hover(function() {
			if($(this).hasClass('dia-error')) {
				$(this).show();	
				$(this).removeClass('dia-error');
				$(this).addClass('dia-loading');
				$.fn.annotateImage.ajaxLoad(this);
			} else {
				$(this).show();	
			}
        }, function() {
			if($(this).hasClass('dia-error') || $(this).hasClass('dia-loading')) {
            	$(this).show();
			} else {
				$(this).hide();
			}
        });

        // load the notes		
        if (this.useAjax) {
            $.fn.annotateImage.ajaxLoad(this);
        } else {
            $.fn.annotateImage.load(this);
        }

        // Add the "Add a note" button
        if (this.addable==true) {
			this.button = $('<a class="dia-add" id="dia-add" href="#' + this.getImgID.substring(4,this.getImgID.length) + '">Add Note</a>');
            this.button.click(function() {
                $.fn.annotateImage.add(image);
            });
			this.canvas.closest('.dia-holder').find('.dia-desc-holder').append(this.button);
        }

        // Hide the original
        this.hide();

        return this;
    };
	
	function ajaxTimeOut(image) {
		if(image.imageloaded == false) {
			image.canvas.children('.dia-view').removeClass('dia-loading');
			image.canvas.children('.dia-view').addClass('dia-error');
			image.canvas.children('.dia-view').show();
		}
	}

    /**
    * Plugin Defaults
    **/
    $.fn.annotateImage.defaults = {
        pluginPath: 'your-get.rails',
        editable: true,
        useAjax: true,
        notes: new Array()
    };

    $.fn.annotateImage.clear = function(image) {
        ///	<summary>
        ///		Clears all existing annotations from the image.
        ///	</summary>    
        for (var i = 0; i < image.notes.length; i++) {
            image.notes[image.notes[i]].destroy();
        }
        image.notes = new Array();
    };

    $.fn.annotateImage.ajaxLoad = function(image) {
        ///	<summary>
        ///		Loads the annotations from the "getUrl" property passed in on the
        ///     options object.
        ///	</summary>
		image.ajaxLoadTime = setTimeout(ajaxTimeOut, 15000, image);
		
        $.getJSON(image.pluginPath + '.php?action=get&imgid=' + image.getImgID + '&preview=' + image.previewOnly + '&ticks=' + $.fn.annotateImage.getTicks(), function(data) {
			image.canvas.children('.dia-view').removeClass('dia-loading');
			image.imageloaded = true;
			if(data.note != null){	
				//this.parents().removeClass('dia-loading');
            	image.notes = data.note;
            	$.fn.annotateImage.load(image);
			}
        });
		
    };

    $.fn.annotateImage.load = function(image) {
        ///	<summary>
        ///		Loads the annotations from the notes property passed in on the
        ///     options object.
        ///	</summary>
		
		var targetNoteID = image.closest('#dia-admin-holder').attr('data-note-ID');
		targetNoteID = targetNoteID == undefined ? '' : targetNoteID;
		//image.canvas.children('.dia-view').hide();
		
		if(image.notes.length != 0) {
			for (var i = 0; i < image.notes.length; i++) {
				if(targetNoteID!=''){
					//target single note
					if(targetNoteID == image.notes[i].id){
						image.notes[image.notes[i]] = new $.fn.annotateView(image, image.notes[i]);	
					}
				}else{
					image.notes[image.notes[i]] = new $.fn.annotateView(image, image.notes[i]);	
				}
			}
		}
    };

    $.fn.annotateImage.getTicks = function() {
        ///	<summary>
        ///		Gets a count og the ticks for the current date.
        ///     This is used to ensure that URLs are always unique and not cached by the browser.
        ///	</summary>        
        var now = new Date();
        return now.getTime();
    };

    $.fn.annotateImage.add = function(image) {
        ///	<summary>
        ///		Adds a note to the image.
        ///	</summary>        
        if (image.mode == 'view') {
            image.mode = 'edit';

            // Create/prepare the editable note elements
            var editable = new $.fn.annotateEdit(image);

            $.fn.annotateImage.createSaveButton(editable, image);
            $.fn.annotateImage.createCancelButton(editable, image);
        }
    };

    $.fn.annotateImage.createSaveButton = function(editable, image, note) {
        ///	<summary>
        ///		Creates a Save button on the editable note.
        ///	</summary>
        var ok = $('<a class="dia-edit-ok">OK</a>');

        ok.click(function() {
            var form = $('#dia-edit-form form');
            var text = $('#dia-text').val();
			var author = $('#noteauthor').val();
			var email = $('#noteemail').val();
			
			author = author == undefined ? "" : author
			email = email == undefined ? "" : email
			
			var check = false;
			var errorMsg = '';
			if(text != "") {
				if(image.editable == false) {
					if(author != "" && email !="") {
						AtPos = email.indexOf("@")
						StopPos = email.lastIndexOf(".")
						
						if (AtPos == -1 || StopPos == -1) {
							errorMsg = 'Please enter a valid email.';	
						} else {
							check = true;
						}
					} else {
						errorMsg = 'Please fill the required fields (name, email).';	
					}
				} else {
					check = true
				}
			} else {
				errorMsg = 'Please type a note';
			}
			$("#errormsg").html('<span style="color:#C00">'+errorMsg+'</span>');
			
			if(check == true) {
				$.fn.annotateImage.appendPosition(form, editable)
				image.mode = 'view';
			
				// Save via AJAX
				if (image.useAjax) {
					$.ajax({
						url: image.pluginPath + ".php?action=save&imgid=" + image.getImgID + "&postid=" + image.getPostID,
						data: form.serialize(),
						error: function(xhr, ajaxOptions, thrownError) { /*alert("An error occured saving that note." + thrownError)*/ },
						success: function(data) {
							if(data.status == true){
								var redictLink = $('#dia-admin-holder').attr('date-note-link');
								if(redictLink != undefined){
									window.location = redictLink+'&jsupdate=update';	
								}
								
								// Add to canvas
								if (note) {
									note.resetPosition(editable, text);
								} else {
									editable.note.editable = true;
									note = new $.fn.annotateView(image, editable.note)
									note.resetPosition(editable, text);
									image.notes.push(editable.note);
								}
								editable.destroy();
							}else{
								$("#errormsg").html('<span style="color:#C00">Error, please try again.</span>');	
							}
				},
						dataType: "json"
					});
				}
			}
        });
        editable.form.append(ok);
    };

    $.fn.annotateImage.createCancelButton = function(editable, image) {
        ///	<summary>
        ///		Creates a Cancel button on the editable note.
        ///	</summary>
        var cancel = $('<a class="dia-edit-close">Cancel</a>');
        cancel.click(function() {
            editable.destroy();
            image.mode = 'view';
        });
        editable.form.append(cancel);
    };

    $.fn.annotateImage.saveAsHtml = function(image, target) {
        var element = $(target);
        var html = "";
        for (var i = 0; i < image.notes.length; i++) {
            html += $.fn.annotateImage.createHiddenField("text_" + i, image.notes[i].text);
            html += $.fn.annotateImage.createHiddenField("top_" + i, image.notes[i].top);
            html += $.fn.annotateImage.createHiddenField("left_" + i, image.notes[i].left);
            html += $.fn.annotateImage.createHiddenField("height_" + i, image.notes[i].height);
            html += $.fn.annotateImage.createHiddenField("width_" + i, image.notes[i].width);
        }
        element.html(html);
    };

    $.fn.annotateImage.createHiddenField = function(name, value) {
        return '&lt;input type="hidden" name="' + name + '" value="' + value + '" /&gt;<br />';
    };

    $.fn.annotateEdit = function(image, note) {
        ///	<summary>
        ///		Defines an editable annotation area.
        ///	</summary>
        this.image = image;
		if (note) {
            this.note = note;
        } else {
			var newNote = new Object();
            newNote.id = 'new';
            newNote.top = 30;
            newNote.left = 30;
            newNote.width = 30;
            newNote.height = 30;
            newNote.text = "";
            this.note = newNote;
        }

        // Set area
        var area = image.canvas.children('.dia-edit').children('.dia-edit-area');
        this.area = area;
        this.area.css('height', this.note.height + 'px');
        this.area.css('width', this.note.width + 'px');
        this.area.css('left', this.note.left + 'px');
        this.area.css('top', this.note.top + 'px');
		
        // Show the edition canvas and hide the view canvas
        image.canvas.children('.dia-view').hide();
        image.canvas.children('.dia-edit').show();
		
		//filter note
		var notetext = this.note.text;
		/*for(var i = 0; i<this.note.text.length; i++) {
			var str = this.note.text
			if(str.substring(i,i+6) == "<br />") {
				this.note.text = str.substring(0,i);
			}
		}*/
		
        // Add the note (which we'll load with the form afterwards)
		var inputMax = Number(image.maxLength);
		inputMax = isNaN(inputMax) ? 140 : inputMax;
		
		var errorMsg = '';
		if(image.editable) {
			errorMsg = 'You can start edit the note here.';
			var form = $('<div id="dia-edit-form" style="height:auto;"><form><input type="hidden" id="noteID" name="noteID" value="' + this.note.id + '"><textarea id="dia-text" name="text" rows="3" cols="30" maxlength="'+inputMax+'">' + notetext + '</textarea></form><div id="errormsg">'+errorMsg+'</div></div>');
		} else {
			errorMsg = 'Fill in the require fields to submit.';
        	var form = $('<div id="dia-edit-form"><form><input type="hidden" id="noteID" name="noteID" value="' + this.note.id + '"><label for="author">Name : </label><input name="author" id="noteauthor" type="text" maxlength="100" /><br /><label for="email" >Email : </label><input name="email" id="noteemail" type="text" maxlength="100" /><textarea id="dia-text" name="text" rows="3" cols="30" maxlength="'+inputMax+'">' + notetext + '</textarea></form><div id="errormsg">'+errorMsg+'</div></div>');
			
		}
        this.form = form;
		
        $('body').append(this.form);
        this.form.css('left', this.area.offset().left + 'px');
        this.form.css('top', (parseInt(this.area.offset().top) + parseInt(this.area.height()) + 7) + 'px');
		
		$('textarea[maxlength]').keyup(function(){
			var max = parseInt($(this).attr('maxlength'));
			if($(this).val().length > max){
				$(this).val($(this).val().substr(0, $(this).attr('maxlength')));
				$("#errormsg").html('<span style="color:#C00">You have ' + (max - $(this).val().length) + ' characters remaining</span>');
			} else {
				$("#errormsg").html('You have ' + (max - $(this).val().length) + ' characters remaining');	
			}
		});
		
        // Set the area as a draggable/resizable element contained in the image canvas.
        // Would be better to use the containment option for resizable but buggy
        area.resizable({
            handles: 'all',

            stop: function(e, ui) {
                form.css('left', area.offset().left + 'px');
                form.css('top', (parseInt(area.offset().top) + parseInt(area.height()) + 2) + 'px');
            }
        })
        .draggable({
            containment: image.canvas,
            drag: function(e, ui) {
                form.css('left', area.offset().left + 'px');
                form.css('top', (parseInt(area.offset().top) + parseInt(area.height()) + 2) + 'px');
            },
            stop: function(e, ui) {
                form.css('left', area.offset().left + 'px');
                form.css('top', (parseInt(area.offset().top) + parseInt(area.height()) + 2) + 'px');
            }
        });
        return this;
    };

    $.fn.annotateEdit.prototype.destroy = function() {
        ///	<summary>
        ///		Destroys an editable annotation area.
        ///	</summary>        
        this.image.canvas.children('.dia-edit').hide();
        this.area.resizable('destroy');
        this.area.draggable('destroy');
        this.area.css('height', '');
        this.area.css('width', '');
        this.area.css('left', '');
        this.area.css('top', '');
        this.form.remove();
    }

    $.fn.annotateView = function(image, note) {
        ///	<summary>
        ///		Defines a annotation area.
        ///	</summary>
        this.image = image;

        this.note = note;

        this.editable = (note.editable && image.editable);
		
        // Add the area
        this.area = $('<div data-note-id="'+this.note.id+'" class="dia-area' + (this.editable ? ' dia-area-editable' : '') + '"><div></div></div>');
        image.canvas.children('.dia-view').prepend(this.area);

        // Add the note
		note.author = note.author == undefined ? '' : note.author;
		this.form = $('<div class="dia-note">' + note.author + '<div class="dia-note-text">'+note.text.replace(/\n/g, "<br />")+'</div></div>');
        this.form.hide();
        image.canvas.children('.dia-view').append(this.form);
        this.form.children('span.actions').hide();
		
		if(this.note.height > this.note.width) {
			this.area.css('z-index', 20 - (Math.round(this.note.height/100 * 1)))
			this.form.css('z-index', 20 - (Math.round(this.note.height/100 * 1)))
		} else {
			this.area.css('z-index', 20 - (Math.round(this.note.width/100 * 1)))
			this.form.css('z-index', 20 - (Math.round(this.note.height/100 * 1)))
		}
		
        // Set the position and size of the note
        this.setPosition();

        // Add the behavior: hide/display the note when hovering the area
        var annotation = this;
        this.area.hover(function() {
            annotation.show();
        }, function() {
            annotation.hide();
        });

        // Edit a note feature
        if (this.editable) {
            var form = this;
            this.area.click(function() {
                form.edit();
            });
        } else {
			this.area.click(function() {
				window.location.hash = "#comment-" + note.commentid;
			});
		}
    };

    $.fn.annotateView.prototype.setPosition = function() {
        ///	<summary>
        ///		Sets the position of an annotation.
        ///	</summary>
        this.area.children('div').height((parseInt(this.note.height) - 2) + 'px');
        this.area.children('div').width((parseInt(this.note.width) - 2) + 'px');
        this.area.css('left', (this.note.left) + 'px');
        this.area.css('top', (this.note.top) + 'px');
        this.form.css('left', (this.note.left) + 'px');
        this.form.css('top', (parseInt(this.note.top) + parseInt(this.note.height) + 7) + 'px');
    };

    $.fn.annotateView.prototype.show = function() {
        ///	<summary>
        ///		Highlights the annotation
        ///	</summary>
        if(this.form.oldindex == undefined) {
			this.form.oldindex = this.form.css("z-index");
		}
		this.form.css('z-index', 100);
        this.form.fadeIn(250);
        if (!this.editable) {
            this.area.addClass('dia-area-hover');
        } else {
            this.area.addClass('dia-area-editable-hover');
        }
    };

    $.fn.annotateView.prototype.hide = function() {
        ///	<summary>
        ///		Removes the highlight from the annotation.
        ///	</summary>      
        this.form.fadeOut(250);
		this.form.css('z-index', this.form.oldindex);
        this.area.removeClass('dia-area-hover');
        this.area.removeClass('dia-area-editable-hover');
    };

    $.fn.annotateView.prototype.destroy = function() {
        ///	<summary>
        ///		Destroys the annotation.
        ///	</summary>      
        this.area.remove();
        this.form.remove();
    }

    $.fn.annotateView.prototype.edit = function() {
        ///	<summary>
        ///		Edits the annotation.
        ///	</summary>      
        if (this.image.mode == 'view') {
            this.image.mode = 'edit';
            var annotation = this;

            // Create/prepare the editable note elements
            var editable = new $.fn.annotateEdit(this.image, this.note);

            $.fn.annotateImage.createSaveButton(editable, this.image, annotation);

            // Add the delete button
            var del = $('<a class="dia-edit-delete">Delete</a>');
            del.click(function() {
                var form = $('#dia-edit-form form');
				
				$.fn.annotateImage.appendPosition(form, editable)
                if (annotation.image.useAjax) {
                    $.ajax({
                        url: annotation.image.pluginPath + ".php?action=delete&imgid=" + annotation.image.getImgID,
                        data: form.serialize(),
                        error: function(e) { alert("An error occured deleting that note.")},
						success: function(data) {
							var redictLink = $('#dia-admin-holder').attr('date-note-link');
							if(redictLink != undefined){
								window.location = redictLink+'&jsupdate=delete';	
							}
						}
                    });
                }

                annotation.image.mode = 'view';
                editable.destroy();
                annotation.destroy();
            });
            editable.form.append(del);

            $.fn.annotateImage.createCancelButton(editable, this.image);
        }
    };

    $.fn.annotateImage.appendPosition = function(form, editable) {
        ///	<summary>
        ///		Appends the annotations coordinates to the given form that is posted to the server.
        ///	</summary>
        var areaFields = $('<input type="hidden" value="' + editable.area.height() + '" name="height"/>' +
                           '<input type="hidden" value="' + editable.area.width() + '" name="width"/>' +
                           '<input type="hidden" value="' + editable.area.position().top + '" name="top"/>' +
                           '<input type="hidden" value="' + editable.area.position().left + '" name="left"/>' +
                           '<input type="hidden" value="' + editable.note.id + '" name="id"/>');
        form.append(areaFields);
    }

    $.fn.annotateView.prototype.resetPosition = function(editable, text) {
        ///	<summary>
        ///		Sets the position of an annotation.
        ///	</summary>
        this.form.find('div.dia-note-text').html(text.replace(/\n/g, "<br />"));
        this.form.hide();

        // Resize
        this.area.children('div').height(editable.area.height() + 'px');
        this.area.children('div').width((editable.area.width() - 2) + 'px');
        this.area.css('left', (editable.area.position().left) + 'px');
        this.area.css('top', (editable.area.position().top) + 'px');
        this.form.css('left', (editable.area.position().left) + 'px');
        this.form.css('top', (parseInt(editable.area.position().top) + parseInt(editable.area.height()) + 7) + 'px');

        // Save new position to note
        this.note.top = editable.area.position().top;
        this.note.left = editable.area.position().left;
        this.note.height = editable.area.height();
        this.note.width = editable.area.width();
        this.note.text = text;
        this.note.id = editable.note.id;
        this.editable = true;
    };
	
	
	
	
		/*
		* jQuery MD5 Plugin 1.0
		*
		* Copyright 2010, Sebastian Tschan, AQUANTUM
		* Licensed under the MIT license:
		* http://creativecommons.org/licenses/MIT/
		*
		* https://blueimp.net
		* http://www.aquantum.de
		*
		* Based on
		* A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
		* Digest Algorithm, as defined in RFC 1321.
		* Version 2.2 Copyright (C) Paul Johnston 1999 - 2009
		* Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
		* Distributed under the BSD License
		* See http://pajhome.org.uk/crypt/md5 for more info.
		*/
		
		/*jslint bitwise: false */
		/*global unescape, jQuery */
	
	
		/*
		* Add integers, wrapping at 2^32. This uses 16-bit operations internally
		* to work around bugs in some JS interpreters.
		*/
		function safe_add(x, y) {
			var lsw = (x & 0xFFFF) + (y & 0xFFFF),
				msw = (x >> 16) + (y >> 16) + (lsw >> 16);
			return (msw << 16) | (lsw & 0xFFFF);
		}
	
		/*
		* Bitwise rotate a 32-bit number to the left.
		*/
		function bit_rol(num, cnt) {
			return (num << cnt) | (num >>> (32 - cnt));
		}
	
		/*
		* These functions implement the four basic operations the algorithm uses.
		*/
		function md5_cmn(q, a, b, x, s, t) {
			return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s), b);
		}
		function md5_ff(a, b, c, d, x, s, t) {
			return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
		}
		function md5_gg(a, b, c, d, x, s, t) {
			return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
		}
		function md5_hh(a, b, c, d, x, s, t) {
			return md5_cmn(b ^ c ^ d, a, b, x, s, t);
		}
		function md5_ii(a, b, c, d, x, s, t) {
			return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
		}
	
		/*
		* Calculate the MD5 of an array of little-endian words, and a bit length.
		*/
		function binl_md5(x, len) {
			/* append padding */
			x[len >> 5] |= 0x80 << ((len) % 32);
			x[(((len + 64) >>> 9) << 4) + 14] = len;
	
			var i, olda, oldb, oldc, oldd,
				a = 1732584193,
				b = -271733879,
				c = -1732584194,
				d = 271733878;
	
			for (i = 0; i < x.length; i += 16) {
				olda = a;
				oldb = b;
				oldc = c;
				oldd = d;
	
				a = md5_ff(a, b, c, d, x[i + 0], 7, -680876936);
				d = md5_ff(d, a, b, c, x[i + 1], 12, -389564586);
				c = md5_ff(c, d, a, b, x[i + 2], 17, 606105819);
				b = md5_ff(b, c, d, a, x[i + 3], 22, -1044525330);
				a = md5_ff(a, b, c, d, x[i + 4], 7, -176418897);
				d = md5_ff(d, a, b, c, x[i + 5], 12, 1200080426);
				c = md5_ff(c, d, a, b, x[i + 6], 17, -1473231341);
				b = md5_ff(b, c, d, a, x[i + 7], 22, -45705983);
				a = md5_ff(a, b, c, d, x[i + 8], 7, 1770035416);
				d = md5_ff(d, a, b, c, x[i + 9], 12, -1958414417);
				c = md5_ff(c, d, a, b, x[i + 10], 17, -42063);
				b = md5_ff(b, c, d, a, x[i + 11], 22, -1990404162);
				a = md5_ff(a, b, c, d, x[i + 12], 7, 1804603682);
				d = md5_ff(d, a, b, c, x[i + 13], 12, -40341101);
				c = md5_ff(c, d, a, b, x[i + 14], 17, -1502002290);
				b = md5_ff(b, c, d, a, x[i + 15], 22, 1236535329);
	
				a = md5_gg(a, b, c, d, x[i + 1], 5, -165796510);
				d = md5_gg(d, a, b, c, x[i + 6], 9, -1069501632);
				c = md5_gg(c, d, a, b, x[i + 11], 14, 643717713);
				b = md5_gg(b, c, d, a, x[i + 0], 20, -373897302);
				a = md5_gg(a, b, c, d, x[i + 5], 5, -701558691);
				d = md5_gg(d, a, b, c, x[i + 10], 9, 38016083);
				c = md5_gg(c, d, a, b, x[i + 15], 14, -660478335);
				b = md5_gg(b, c, d, a, x[i + 4], 20, -405537848);
				a = md5_gg(a, b, c, d, x[i + 9], 5, 568446438);
				d = md5_gg(d, a, b, c, x[i + 14], 9, -1019803690);
				c = md5_gg(c, d, a, b, x[i + 3], 14, -187363961);
				b = md5_gg(b, c, d, a, x[i + 8], 20, 1163531501);
				a = md5_gg(a, b, c, d, x[i + 13], 5, -1444681467);
				d = md5_gg(d, a, b, c, x[i + 2], 9, -51403784);
				c = md5_gg(c, d, a, b, x[i + 7], 14, 1735328473);
				b = md5_gg(b, c, d, a, x[i + 12], 20, -1926607734);
	
				a = md5_hh(a, b, c, d, x[i + 5], 4, -378558);
				d = md5_hh(d, a, b, c, x[i + 8], 11, -2022574463);
				c = md5_hh(c, d, a, b, x[i + 11], 16, 1839030562);
				b = md5_hh(b, c, d, a, x[i + 14], 23, -35309556);
				a = md5_hh(a, b, c, d, x[i + 1], 4, -1530992060);
				d = md5_hh(d, a, b, c, x[i + 4], 11, 1272893353);
				c = md5_hh(c, d, a, b, x[i + 7], 16, -155497632);
				b = md5_hh(b, c, d, a, x[i + 10], 23, -1094730640);
				a = md5_hh(a, b, c, d, x[i + 13], 4, 681279174);
				d = md5_hh(d, a, b, c, x[i + 0], 11, -358537222);
				c = md5_hh(c, d, a, b, x[i + 3], 16, -722521979);
				b = md5_hh(b, c, d, a, x[i + 6], 23, 76029189);
				a = md5_hh(a, b, c, d, x[i + 9], 4, -640364487);
				d = md5_hh(d, a, b, c, x[i + 12], 11, -421815835);
				c = md5_hh(c, d, a, b, x[i + 15], 16, 530742520);
				b = md5_hh(b, c, d, a, x[i + 2], 23, -995338651);
	
				a = md5_ii(a, b, c, d, x[i + 0], 6, -198630844);
				d = md5_ii(d, a, b, c, x[i + 7], 10, 1126891415);
				c = md5_ii(c, d, a, b, x[i + 14], 15, -1416354905);
				b = md5_ii(b, c, d, a, x[i + 5], 21, -57434055);
				a = md5_ii(a, b, c, d, x[i + 12], 6, 1700485571);
				d = md5_ii(d, a, b, c, x[i + 3], 10, -1894986606);
				c = md5_ii(c, d, a, b, x[i + 10], 15, -1051523);
				b = md5_ii(b, c, d, a, x[i + 1], 21, -2054922799);
				a = md5_ii(a, b, c, d, x[i + 8], 6, 1873313359);
				d = md5_ii(d, a, b, c, x[i + 15], 10, -30611744);
				c = md5_ii(c, d, a, b, x[i + 6], 15, -1560198380);
				b = md5_ii(b, c, d, a, x[i + 13], 21, 1309151649);
				a = md5_ii(a, b, c, d, x[i + 4], 6, -145523070);
				d = md5_ii(d, a, b, c, x[i + 11], 10, -1120210379);
				c = md5_ii(c, d, a, b, x[i + 2], 15, 718787259);
				b = md5_ii(b, c, d, a, x[i + 9], 21, -343485551);
	
				a = safe_add(a, olda);
				b = safe_add(b, oldb);
				c = safe_add(c, oldc);
				d = safe_add(d, oldd);
			}
			return [a, b, c, d];
		}
	
		/*
		* Convert an array of little-endian words to a string
		*/
		function binl2rstr(input) {
			var i,
				output = '';
			for (i = 0; i < input.length * 32; i += 8) {
				output += String.fromCharCode((input[i>>5] >>> (i % 32)) & 0xFF);
			}
			return output;
		}
	
		/*
		* Convert a raw string to an array of little-endian words
		* Characters >255 have their high-byte silently ignored.
		*/
		function rstr2binl(input) {
			var i,
				output = [];
			output[(input.length >> 2) - 1] = undefined;
			for (i = 0; i < output.length; i += 1) {
				output[i] = 0;
			}
			for (i = 0; i < input.length * 8; i += 8) {
				output[i>>5] |= (input.charCodeAt(i / 8) & 0xFF) << (i % 32);
			}
			return output;
		}
	
		/*
		* Calculate the MD5 of a raw string
		*/
		function rstr_md5(s) {
			return binl2rstr(binl_md5(rstr2binl(s), s.length * 8));
		}
	
		/*
		* Calculate the HMAC-MD5, of a key and some data (raw strings)
		*/
		function rstr_hmac_md5(key, data) {
			var i,
				bkey = rstr2binl(key),
				ipad = [],
				opad = [],
				hash;
			ipad[15] = opad[15] = undefined;
			if (bkey.length > 16) {
				bkey = binl_md5(bkey, key.length * 8);
			}
			for (i = 0; i < 16; i += 1) {
				ipad[i] = bkey[i] ^ 0x36363636;
				opad[i] = bkey[i] ^ 0x5C5C5C5C;
			}
			hash = binl_md5(ipad.concat(rstr2binl(data)), 512 + data.length * 8);
			return binl2rstr(binl_md5(opad.concat(hash), 512 + 128));
		}
	
		/*
		* Convert a raw string to a hex string
		*/
		function rstr2hex(input) {
			var hex_tab = '0123456789abcdef',
				output = '',
				x, i;
			for (i = 0; i < input.length; i += 1) {
				x = input.charCodeAt(i);
				output += hex_tab.charAt((x >>> 4) & 0x0F) +
				hex_tab.charAt(x & 0x0F);
			}
			return output;
		}
	
		/*
		* Encode a string as utf-8
		*/
		function str2rstr_utf8(input) {
			return unescape(encodeURIComponent(input));
		}
	
		/*
		* Take string arguments and return either raw or hex encoded strings
		*/
		function raw_md5(s) {
			return rstr_md5(str2rstr_utf8(s));
		}
		function hex_md5(s) {
			return rstr2hex(raw_md5(s));
		}
		function raw_hmac_md5(k, d) {
			return rstr_hmac_md5(str2rstr_utf8(k), str2rstr_utf8(d));
		}
		function hex_hmac_md5(k, d) {
			return rstr2hex(raw_hmac_md5(k, d));
		}
		
		
		$.fn.getMD5 = function(string, key, raw) { {
			if (!key) {
				if (!raw) {
					return hex_md5(string);
				} else {
					return raw_md5(string);
				}
			}
			if (!raw) {
				return hex_hmac_md5(key, string);
			} else {
				return raw_hmac_md5(key, string);
			}
		}};
})(jQuery);