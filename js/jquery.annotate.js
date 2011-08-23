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
        this.pluginUrl = opts.pluginUrl;
        this.editable = opts.editable;
		this.addable = opts.addable;
        this.useAjax = opts.useAjax;
        this.notes = opts.notes;
		
		// Add the canvas
        this.canvas = $('<div class="image-annotate-canvas"><div class="image-annotate-view image-annotate-loading"></div><div class="image-annotate-edit"><div class="image-annotate-edit-area"></div></div></div>');
        this.canvas.children('.image-annotate-edit').hide();
        this.canvas.children('.image-annotate-view').hide();
        this.image.after(this.canvas);

        // Give the canvas and the container their size and background
		this.canvas.height(this.height());
        this.canvas.width(this.width());
        this.canvas.css('background-image', 'url("' + this.attr('src') + '")');
        this.canvas.children('.image-annotate-view, .image-annotate-edit').height(this.height());
        this.canvas.children('.image-annotate-view, .image-annotate-edit').width(this.width());
		
		
		// Add the behavior: hide/show the notes when hovering the picture
        this.canvas.hover(function() {
            if ($(this).children('.image-annotate-edit').css('display') == 'none') {
                $(this).children('.image-annotate-view').show();
            }
        }, function() {
            if($(this).children().hasClass('image-annotate-error') || $(this).children().hasClass('image-annotate-loading')) {
            	$(this).children('.image-annotate-view').show();
			} else {
				$(this).children('.image-annotate-view').hide();
			}
        });

        this.canvas.children('.image-annotate-view').hover(function() {
			if($(this).hasClass('image-annotate-error')) {
				$(this).show();	
				$(this).removeClass('image-annotate-error');
				$(this).addClass('image-annotate-loading');
				$.fn.annotateImage.ajaxLoad(this);
			} else {
				$(this).show();	
			}
        }, function() {
			if($(this).hasClass('image-annotate-error') || $(this).hasClass('image-annotate-loading')) {
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
        if (this.addable) {
			this.button = $('<div style="width:' + (this.width() + 10) + 'px"><a class="image-annotate-add" id="image-annotate-add" href="#' + this.getImgID.substring(4,this.getImgID.length) + '">Add Note</a></div>');
            this.button.click(function() {
                $.fn.annotateImage.add(image);
            });
            this.canvas.before(this.button);
        }

        // Hide the original
        this.hide();

        return this;
    };
	
	function ajaxTimeOut(image) {
		if(image.imageloaded == false) {
			image.canvas.children('.image-annotate-view').removeClass('image-annotate-loading');
			image.canvas.children('.image-annotate-view').addClass('image-annotate-error');
			image.canvas.children('.image-annotate-view').show();
		}
	}

    /**
    * Plugin Defaults
    **/
    $.fn.annotateImage.defaults = {
        pluginUrl: 'your-get.rails',
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
		
        $.getJSON(image.pluginUrl + '?action=get&imgid=' + image.getImgID + '&ticks=' + $.fn.annotateImage.getTicks(), function(data) {
			//if(image.notes.length != 0) {	
				//this.parents().removeClass('image-annotate-loading');
            	image.notes = data;
            	$.fn.annotateImage.load(image);
			//}
        });
		
    };

    $.fn.annotateImage.load = function(image) {
        ///	<summary>
        ///		Loads the annotations from the notes property passed in on the
        ///     options object.
        ///	</summary>
		
		image.canvas.children('.image-annotate-view').removeClass('image-annotate-loading');
		image.imageloaded = true;
		//image.canvas.children('.image-annotate-view').hide();
		
		if(image.notes.length != 0) {
			for (var i = 0; i < image.notes.length; i++) {
				image.notes[image.notes[i]] = new $.fn.annotateView(image, image.notes[i]);
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
        var ok = $('<a class="image-annotate-edit-ok">OK</a>');

        ok.click(function() {
            var form = $('#image-annotate-edit-form form');
            var text = $('#image-annotate-text').val();
			var author = $('#noteauthor').val();
			var email = $('#noteemail').val();
			
			author = author == undefined ? "" : author
			email = email == undefined ? "" : email
			
			var check = false;
			
			if(text != "") {
				if(image.editable == false) {
					if(author != "" && email !="") {
						AtPos = email.indexOf("@")
						StopPos = email.lastIndexOf(".")
						
						if (AtPos == -1 || StopPos == -1) {
							$("#errormsg").html('<span style="color:#C00">Please enter a valid email.</span>');	
						} else {
							check = true;
						}
					} else {
						$("#errormsg").html('<span style="color:#C00">Please fill the required fields (name, email).</span>');	
					}
				} else {
					check = true
				}
			} else {
				$("#errormsg").html('<span style="color:#C00">Please type a note.</span>');	
			}
			
			if(check == true) {
				$.fn.annotateImage.appendPosition(form, editable)
				image.mode = 'view';
			
				// Save via AJAX
				if (image.useAjax) {
					$.ajax({
						url: image.pluginUrl + "?action=save&imgid=" + image.getImgID + "&postid=" + image.getPostID,
						data: form.serialize(),
						error: function(xhr, ajaxOptions, thrownError) { /*alert("An error occured saving that note." + thrownError)*/ },
						success: function(data) {
					if (data.annotation_id != undefined) {
						editable.note.id = data.annotation_id;
					}
				},
						dataType: "json"
					});
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
			}
        });
        editable.form.append(ok);
    };

    $.fn.annotateImage.createCancelButton = function(editable, image) {
        ///	<summary>
        ///		Creates a Cancel button on the editable note.
        ///	</summary>
        var cancel = $('<a class="image-annotate-edit-close">Cancel</a>');
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
        var area = image.canvas.children('.image-annotate-edit').children('.image-annotate-edit-area');
        this.area = area;
        this.area.css('height', this.note.height + 'px');
        this.area.css('width', this.note.width + 'px');
        this.area.css('left', this.note.left + 'px');
        this.area.css('top', this.note.top + 'px');

        // Show the edition canvas and hide the view canvas
        image.canvas.children('.image-annotate-view').hide();
        image.canvas.children('.image-annotate-edit').show();
		
		//filter note
		for(var i = 0; i<this.note.text.length; i++) {
			var str = this.note.text
			if(str.substring(i,i+6) == "<br />") {
				this.note.text = str.substring(0,i);
			}
		}
		
        // Add the note (which we'll load with the form afterwards)
		if(image.editable) {
			var form = $('<div id="image-annotate-edit-form" style="height:100px;"><form><textarea id="image-annotate-text" name="text" rows="3" cols="30" maxlength="140">' + this.note.text + '</textarea></form><div id="errormsg">You can start edit the note here.</div></div>');
		} else {
        	var form = $('<div id="image-annotate-edit-form"><form><label for="author">Name : </label><input name="author" id="noteauthor" type="text" maxlength="100" /><br /><label for="email" >Email : </label><input name="email" id="noteemail" type="text" maxlength="100" /><textarea id="image-annotate-text" name="text" rows="3" cols="30" maxlength="140">' + this.note.text + '</textarea></form><div id="errormsg">Fill in the require fields to submit.</div></div>');
			
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
        this.image.canvas.children('.image-annotate-edit').hide();
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
        this.area = $('<div class="image-annotate-area' + (this.editable ? ' image-annotate-area-editable' : '') + '"><div></div></div>');
        image.canvas.children('.image-annotate-view').prepend(this.area);

        // Add the note
		this.form = $('<div class="image-annotate-note">' + note.author + note.text + '</div>');
        this.form.hide();
        image.canvas.children('.image-annotate-view').append(this.form);
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
            this.area.addClass('image-annotate-area-hover');
        } else {
            this.area.addClass('image-annotate-area-editable-hover');
        }
    };

    $.fn.annotateView.prototype.hide = function() {
        ///	<summary>
        ///		Removes the highlight from the annotation.
        ///	</summary>      
        this.form.fadeOut(250);
		this.form.css('z-index', this.form.oldindex);
        this.area.removeClass('image-annotate-area-hover');
        this.area.removeClass('image-annotate-area-editable-hover');
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
            var del = $('<a class="image-annotate-edit-delete">Delete</a>');
            del.click(function() {
                var form = $('#image-annotate-edit-form form');
				
				$.fn.annotateImage.appendPosition(form, editable)
                if (annotation.image.useAjax) {
                    $.ajax({
                        url: annotation.image.pluginUrl + "?action=delete&imgid=" + annotation.image.getImgID,
                        data: form.serialize(),
                        error: function(e) { alert("An error occured deleting that note.") }
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
        this.form.html(text);
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

})(jQuery);