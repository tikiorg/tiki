/*
jQuery.s5() - Simple Standards Slide Show System
$Id$
http://code.google.com/p/jquerys5/
		
Copyright (C) 2010 Robert Plummer
Dual licensed under the LGPL v3 and GPL v3 licenses.
http://www.gnu.org/licenses/
*/

var $window = jQuery(window);
var $body = jQuery('body');

/*
jQuery().s5 rendering
Used with header (h1-h6) jQuery dom objects 
*/
jQuery.fn.extend({
	s5: function (settings){
		settings = jQuery.s5.s = jQuery.extend({
			menu: function() { //navigation menu for presenter
				return jQuery(
					'<a href="#" onclick="jQuery.s5.first(); return false;" title="First"><img src="images/resultset_first.png" alt="First" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.prev(); return false;" title="Prev"><img src="images/resultset_previous.png" alt="Prev" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.next(); return false;" title="Next"><img src="images/resultset_next.png" alt="Next" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.last(); return false;" title="Last"><img src="images/resultset_last.png" alt="Last" /></a> ' +
					'<a href="#" onclick="jQuery.s5.listSlideTitles(this); return false;" title="Jump To Slide"><img src="images/layers.png" alt="Jump To Slide" /></a> ' +
					'<a href="#" onclick="jQuery.s5.play(); return false;" title="Play"><img src="images/control_play_blue.png" alt="Play" /></a> ' +
					'<a href="#" onclick="jQuery.s5.pause(); return false;" title="Pause"><img src="images/control_pause_blue.png" alt="Pause" /></a> ' +
					'<a href="#" onclick="jQuery.s5.stop(); return false;" title="Stop"><img src="images/control_stop_blue.png" alt="Stop" /></a> ' +
					'<a href="#" onclick="jQuery.s5.getNote(); return false;" title="Notes"><img src="images/note.png" alt="Notes" /></a> ' +
					'<a href="#" onclick="jQuery.s5.toggleLoop(); return false;" title="Toggle Loop"><img src="images/arrow_rotate_clockwise.png" alt="Toggle Loop" /></a>'
				);
			},
			noteMenu: function() {
				return jQuery(
					'<a href="#" onclick="jQuery.s5.first(); return false;" title="First"><img src="images/resultset_first.png" alt="First" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.prev(); return false;" title="Prev"><img src="images/resultset_previous.png" alt="Prev" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.next(); return false;" title="Next"><img src="images/resultset_next.png" alt="Next" /></a> ' + 
					'<a href="#" onclick="jQuery.s5.last(); return false;" title="Last"><img src="images/resultset_last.png" alt="Last" /></a> ' +
					'<a href="#" onclick="jQuery.s5.listSlideTitles(this, window.opener.$body, true); return false;" title="Jump To Slide"><img src="images/layers.png" alt="Jump To Slide" /></a> ' +
					'<a href="#" onclick="jQuery.s5.play(); return false;" title="Play"><img src="images/control_play_blue.png" alt="Play" /></a> ' +
					'<a href="#" onclick="jQuery.s5.pause(); return false;" title="Pause"><img src="images/control_pause_blue.png" alt="Pause" /></a> ' +
					'<a href="#" onclick="jQuery.s5.stop(); return false;" title="Stop"><img src="images/control_stop_blue.png" alt="Stop" /></a> ' +
					'<a href="#" onclick="jQuery.s5.toggleLoop(); return false;" title="Toggle Loop"><img src="images/arrow_rotate_clockwise.png" alt="Toggle Loop" /></a>'
				);
			},
			slideNum: jQuery('<span id="slideNum"></span>'),
			noteTemplate: function() { //here we will help the presenter by giving him/her more info so they know what to present
				return jQuery('<div />');
			},
			parent: jQuery(this),
			slideDuration: 15000,
			pause: false,
			play: false,
			loop: false,
			imageSizeAdjustment: function(slide, sizeLimits, imgs) {
				imgs
					.each(function() {
						
						var img = jQuery(this);
												
						img
							.css('height', '')
							.css('width', '')
							.removeAttr('height')
							.removeAttr('width');
						
						var w = img.width();
						var h = img.height();
						
						if (w > jQuery.s5.s.imageSizeToAdjust.width) {
							img.addClass('s5-image');
							
							slide.find('td').width(sizeLimits.width);
							
							//It is needed to check both height and width after resize
							if (w > sizeLimits.width) {
								img
									.css('width',  sizeLimits.width + 'px')
									.css('height', 'auto');
							}
						}
						
						if (h > jQuery.s5.s.imageSizeToAdjust.height) {
							img.addClass('s5-image');
							//It is needed to check both height and width after resize
							if (h > (sizeLimits.height / imgs.length)) {
								img
									.css('height', (sizeLimits.height / imgs.length) + 'px')
									.css('width', 'auto');
							}
						}
						
						if (img.hasClass('s5-image')) {
							if (settings.textSide == "left") {
								slide.find(".s5-slide-right").append(img);
							}
							else {
								slide.find(".s5-slide-left").append(img);
							}
						}
				});
				
				return imgs;
			},
			maxFontSize: 30,
			imageSizeToAdjust: {height: 100, width: 100},
			textSide: "left",
			slideHeaders: '',
			notes: [],
			slideClass: '',
			backgroundImage: '',
			backgroundColor: '',
			headerFontColor: '',
			slideFontColor: '',
			listItemHighlightColor: ''
		}, settings);
		
		jQuery.s5.safeImg();
		
		/* private functions */
		var fn = {
			// Key trap fix, new function body for trap()
			trap: function (e) {
				if (!e) {
					e = event;
					e.which = e.keyCode;
				}
				try {
					modifierKey = e.ctrlKey || e.altKey || e.metaKey;
				}
				catch(e) {
					modifierKey = false;
				}
				return modifierKey || e.which == 0;
			}
		};
		
		jQuery.s5.makeSizeDetector();
		
		/* prep notes */
		jQuery('.s5-note').hide();
		$window.unload(function() {
			if (jQuery.s5.note) {
				jQuery.s5.note.close();
			}
		});
		
		/* inject some elements to stylize each slide */
		var footer = jQuery('<div class="s5-footer" />').appendTo($body);

		var isFirst = true;
		jQuery(this)
			.children(settings.slideHeaders)
			.addClass('s5-header')
			.each(function(){
				var slide = jQuery(
					'<table class="s5-slide" style="width: 100%;">' +
						'<tr>' +
							'<td class="s5-slide-top" colspan="2"></td>' +
						'</tr>' +
						'<tr>' +
							'<td class="s5-slide-left"></td>' +
							'<td class="s5-slide-right"></td>' +
						'</tr>' +
						'<tr>' +
							'<td class="s5-slide-bottom" colspan="2"></td>' +
						'</tr>' +
					'</table>'
				)
				.appendTo($body)
				.addClass('s5-hide');
					
				var slideChild = jQuery(this)
					.add(jQuery(this).siblingsUntil(settings.slideHeaders));
				
				slide.find('td.s5-slide-top').hide();
				slide.find('td.s5-slide-bottom').hide();
				
				if (settings.textSide == "left") {
					slide.find(".s5-slide-left").html(slideChild);
				} else {
					slide.find(".s5-slide-right").html(slideChild);
				}
			});

		// initialize 
		var slides = jQuery('.s5-slide');
		
		//add line item styling
		slides.each(function() {
			jQuery(this).find('li').each(function(i) {
				jQuery(this).click(function() {
					jQuery.s5.goLI(i);
				});
			});
		});
		
		var slideFirst = slides.first().addClass('s5-first');
		
		jQuery.s5.slideCount = slides.length;
		
		var footerChild = slideFirst.clone();
		footer.html((footerChild.html() + '').replace(/<(?!\/(?=>|\s.*>))\/?.*?>/g, ' '));

		// load the key/mouse bindings
		jQuery(document)
			.keyup(jQuery.s5.keys)
			.keypress(fn.trap);

		$window
			.resize(function() {
				jQuery.s5.scale();
				
				//We need to update the theme because of a few sizes that are dependant on em
				jQuery.s5.makeTheme(settings);
			})
			.resize();
		
		var menu = jQuery.s5.menu = jQuery('<div class="s5-menu" />')
			.append(settings.menu())
			.appendTo($body)
			.fadeTo(0, .01)
			.hover(function() {
				menu.stop().fadeTo(100, .9);
			}, function() {
				menu.stop().fadeTo(100, .01);
			});
		
		$body
			.children()
			.not(menu)
			.click(function(e) {
				if (jQuery(e.target).attr('href'))
					return true; 
				
				jQuery.s5.next();
			});
			
		var first_slide = Number(document.location.hash.substring(2));
		
		footer.append('<span id="s5-slide-num" />');
						
		jQuery.s5.timeManager();
		
		// start the presentation
		jQuery.s5.go(first_slide);
		
		return settings.parent
			.bind('slideChange', function(e, i, I) {
				jQuery.s5.updateSlideNumber();
				document.location.hash = (i ? "#s" + i : '');
				$window.resize();
			})
			.trigger('slideChange', first_slide, jQuery.s5.slideCount);
	},
	siblingsUntil: function( match ){
	    var r = [];
	    $(this).each(function(i){
	        for(var n = this.nextSibling; n; n = n.nextSibling){
	            if($(n).is(match)){
	                break;
	            }
	            r.push(n);
	        }
	    });
	    return this.pushStack( jQuery.unique( r ) );
	}
});

/*jQuery.s5() rendering, provides external functionality*/
jQuery.extend({
	s5: {
		i: -1, //current integer of the active slide
		liI: -1,
		slideCount: -1, //total number of slides
		slides: function() { return jQuery('.s5-slide'); },
		slide: function() { return this.slides().eq(this.i); },
		slideHeaders: function() { return jQuery(this.s.slideHeaders); },
		loadedBackgroundImage: '',
		menu: {},
		status: '',
		start: function(s) {
			s = $.extend({
				slideHeaders:'h1,h2,h3,h5,h6,.titlebar'
			},s);
			
			return jQuery(s.slideHeaders).first().parent().s5(s);
		},
		getNote: function() { //displays notes for the presenter
			if (!this.note) {
				this.note = window.open('about:blank', 'jQuery_s5_note', 'top=0,left=0');
				this.note.document.write(
					'<html>' + 
						'<body class="s5-note-body">' +
							'<table style="width: 100%;">' +
								'<tr>' +
									'<th>Elapsed</th>' +
									'<th>Remaining Overall</th>' +
									'<th>Remaining on Slide</th>' +
									'<th>Slide#</th>' +
									'<th>Status</th>' +
								'</tr>' +
								'<tr>' +
									'<td id="s5-note-time-elapsed" style="text-align: center;"></td>' +
									'<td id="s5-note-timeoverall-remaining" style="text-align: center;"></td>' +
									'<td id="s5-note-time-slide-remaining" style="text-align: center;"></td>' +
									'<td id="s5-note-slide" style="text-align: center;"></td>' +
									'<td id="s5-note-status" style="text-align: center;"></td>' +
								'</tr>' +
							'</table>' +
							'<div id="s5-note-menu" colspan="5" style="text-align: center; padding: 5px;">' +
								
							'</div>' +
							'Title: <span id="s5-note-title"></span><br />' +
							'Notes: <div id="s5-active-note"></div>' +
						'</body>' + 
					'</html>'
				);
				
				this.makeTheme(null, jQuery(this.note.document).find('body'), true);
				
				this.updateNote();
				
				this.noteSet("s5-note-menu", this.s.noteMenu());
				
				if (this.note) {
					jQuery(this.note.document).keyup(jQuery.s5.keys);
				}
				
			} else {
				this.note.close();
				this.note = '';
			}
		},
		play: function() {	//Plays the slide show, changing at intervals based on the setting slideDuration
			this.s.play = true;
			this.s.pause = false;
		},
		stop: function() {
			this.s.play = false;
			this.s.pause = false;
			this.time.elapsed = 0;
			this.go('first');
		},
		pause: function() {
			this.s.play = true;
			this.s.pause = !this.s.pause;
		},
		time: {
			elapsed: 0,
			slideRemaining: 0,
			overallRemaining: 0
		},
		findoverallRemaining: function() {
			return (this.slideCount - (this.i + 1)) * (this.s.slideDuration / 1000);
		},
		findSlideRemaining: function() {
			if (this.i >= this.slideCount) {
				return 0;
			} else {
				return this.time.slideRemaining;
			}
		},
		reset: function() {
			this.time.overallRemaining = 0;
			this.time.slideRemaining = 0;
			this.s.play = false;
			this.updateSlideNumber();
		},
		timeManager: function(type) {
			type = (type ? type : '');
			if (this.s.play && !this.s.pause) {
				this.time.overallRemaining -= 1;
				this.time.slideRemaining -= 1;
				this.time.elapsed += 1;
				
				if (this.time.overallRemaining < 0) {
					this.time.overallRemaining = 0;
				}
				
				if (this.time.slideRemaining < 0) {
					this.time.slideRemaining = 0;
				}
				
				if (this.time.slideRemaining <= 0) {
					if (this.i < this.slideCount - 1) {
						this.go('next');
					} else if (this.s.loop) {
						this.go('first');
					}
				}
			}
			
			this.updateNote();
			setTimeout('jQuery.s5.timeManager();', 1000);
		},
		updateNote: function() {
			this.status = "Stopped";
			if (this.s.play) this.status = "Playing";
			if (this.s.pause) this.status = "Paused";
			this.status += (this.s.loop ? ",Looped" : "");
			
			this.noteSet("s5-note-title", this.slide().find(this.s.slideHeaders).first().text())
			this.noteSet("s5-note-status", this.status);
			this.noteSet("s5-note-slide", this.i + 1);
			this.noteSet("s5-note-time-elapsed", this.formatTime(this.time.elapsed));
			this.noteSet("s5-note-timeoverall-remaining", this.formatTime(this.time.overallRemaining));
			this.noteSet("s5-note-time-slide-remaining", this.formatTime(this.time.slideRemaining));
			var note = jQuery('.s5-note').eq(this.i).html();
			this.noteSet('s5-active-note', note ? note : "No notes for this slide.");
			
			this.noteTitleUpdate();
		},
		formatTime: function(secs, format) { //secs is 1 based not 1000
			format = (format ? format : function(o) {
				d = Number(secs);
				var h = Math.floor(d / 3600);
				var m = Math.floor(d % 3600 / 60);
				var s = Math.floor(d % 3600 % 60);
				return ((h > 0 ? h + ":" : "") + (m > 0 ? (h > 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s);
			});
			return format();
		},
		listSlideTitles: function(parent, body, removeX) { //Lists slide titles so that user can click and change the active slide
			// create an outline container
			body = (body ? body : $body);
			jQuery('.s5-outline').remove();
			
			var outline = jQuery('<div class="s5-outline" />')
				.appendTo(body)
				.width('auto');
			
			var outlineList = jQuery('<ul />')
				.appendTo(outline);
			jQuery(this.s.slideHeaders).each(function(i){ 
				if (jQuery.s5.slideCount > i) {
					var li = jQuery("<li/>").appendTo(outlineList);
					var a = jQuery("<a>(" + (i + 1) + '/' + jQuery.s5.slideCount + ') - ' +  jQuery(this).text() + "</a>")
						.attr('href', '#s' + i)
						.click(function(e){
							jQuery.s5.go(i);
							return false;
						})
						.prependTo(li);
				}
			});
			
			if (!removeX) {
				$('<div onclick="jQuery.s5.listSlideTitles(); return false;" class="s5-outlook-close">X</div>')
					.click(function(){
						jQuery('.s5-outline').remove();
					})
					.prependTo(outline);
			}
			
			if (parent) {
				var offset = jQuery(parent).offset();
				
				outline
					.show()
					.css('left', offset.left + 'px')
					.css('bottom', '0px');
			}
			
			this.scale();
			this.updateSlideNumber();
		},
		updateSlideNumber: function() { //Displays the slide count on the slide show in the 
			this.time.slideRemaining = (this.s.slideDuration / 1000);
			this.time.overallRemaining = (this.slideCount - this.i) * (this.s.slideDuration / 1000);
			
			this.updateNote();

			var outlineList = $('.s5-outline ul');
			outlineList.find('li.s5-active').removeClass('s5-active');
			var activeLi = outlineList.find('li').eq(this.i).addClass('s5-active');
			var scrollTop = activeLi.height() * (this.i);

			outlineList
				.stop()
				.animate({
					scrollTop: scrollTop
				}, 500);
			
			if (this.i) {
				jQuery('#s5-slide-num').html('Slide: ' + (this.i + 1) + '/' + (this.slideCount));
			} else {
				jQuery('#s5-slide-num').html('');
			}
		},
		toggleLoop: function() { //Toggles looping in the slideshow playback
			jQuery.s5.s.loop = !jQuery.s5.s.loop;
		},
		first: function() { this.go('first'); },
		last: function() { this.go('last'); },
		next: function() { this.go('next'); },
		prev: function() { this.go('prev'); },
		go: function(n){ //Navigates to a specific slide
			if ( typeof n == 'string' ){
				switch(n) {
				case 'next': n = (this.i < (this.slideCount-1) ? this.i + 1 : this.i);
					break;
				case 'prev': n = (this.i > 0 ? this.i - 1 : this.i);
					break;
				case 'last': n = this.slideCount - 1;
					break;		
				case 'first': n = 0;
					break;
				}
			}
			n = parseInt(n >= this.slideCount - 1 ? this.slideCount - 1 : n);
			if(n == this.i) return this.slide();
			this.prevViewed = this.i;
			this.i = n;
			
			//clean up last selected line item
			$('li.s5-slide-li-highlighted').removeClass('s5-slide-li-highlighted');
			this.liI = -1;
			
			this.slides().addClass('s5-hide');
			this.slide()
				.fadeTo(1, 0.01, function() {
					jQuery(this).removeClass('s5-hide');
				});
			
			jQuery.s5.scale();

			this.slide().fadeTo('slow', 1); //now we fade in so that it looks good
			
			if(this.i == 0){
				jQuery('.s5-footer').slideUp();
			} else {
				jQuery('.s5-footer').slideDown();
			}
			
			jQuery.s5.s.parent.trigger('slideChange', n, this.slideCount); 
			
			return this.slide();
		},
		goLI: function(n) {
			$('li.s5-slide-li-highlighted').removeClass('s5-slide-li-highlighted');
			
			if (isNaN(n + '')) {
				switch (n) {
					case "prev":
						this.liI--;
						break;
					case "next":
					default:
						this.liI++;
						break;
				}
			} else {
				this.liI = n;
			}
			
			var slide = this.slide();
			var li = slide.find('li');
			
			if (this.liI > -1 && this.liI < li.length) {
				li.eq(this.liI).addClass('s5-slide-li-highlighted');
				return slide;
			} else {
				slide = this.go(n);
				li = slide.find('li');
			}
			
			//we must check the new slide and reset the line item index once more so that it knows what line item we are on in the new slide
			switch (n) {
				case "prev":
					this.liI = li.length - 1;
					li.eq(this.liI).addClass('s5-slide-li-highlighted');
					break;
				case "next":
				default:
					this.liI = -1; break;
			}
		},
		noteSet: function(id, html) {
			if (this.note) {
				jQuery(this.note.document).find('#' + id).html(html);
			}
		},
		noteTitleUpdate: function() {
			if (this.note) {
				this.note.document.title = "(" + (this.i + 1) + '/' + jQuery.s5.slideCount + ') - ' +  jQuery(this.s.slideHeaders).eq(this.i).text();
			}
		},
		imgs: function(o) {
			//resize images
			o = (o ? o : jQuery.s5.slide());	
			
			//first check if they are within the size that need corrected
			var imgs;
			o
				.find('img')
				.not('.fixedSize') //adding fixedSize class to object omits it from being auto sized for slide
					.each(function() {
						var img = jQuery(this);
						if (
							img.hasClass('s5-image') || 
							img.width() >= jQuery.s5.s.imageSizeToAdjust.width || 
							img.height() >= jQuery.s5.s.imageSizeToAdjust.height
						) {
							if (imgs) {
								imgs.push(img);
							} else {
								imgs = jQuery(img);
							}
						}
					});
			return imgs
		},
		scale: function () {  // causes layout problems in FireFox that get fixed if browser's Reload is used; same may be true of other Gecko-based browsers
			var vScale = 20;  // both yield 32 (after rounding) at 1024x768
			var hScale = 30;  // perhaps should auto-calculate based on theme's declared value?
			var vSize = ($window.height() - jQuery('div.s5-menu').height()) - (this.emSize() * 2);
			var hSize = $window.width() - this.emSize();
			
			var newSize = Math.min(Math.round(vSize / vScale), Math.round(hSize / hScale));
			//resize fonts
			var newFontSize = (newSize <  this.s.maxFontSize ? newSize : this.s.maxFontSize);
			$body
				.css('font-size', newFontSize + 'px')
				.height($window.height());
			
			$body.find('div.s5-outline').height($body.height() / 2);
			
			//resize images
			var slide = jQuery.s5.slide();			
			var imgs = this.imgs(slide);
			
			// correct the image sizes
			if (imgs) {
				jQuery.s5.s.imageSizeAdjustment(slide, {
					height: vSize,
					width: hSize / 2
				}, imgs);
			}
			
			//resize font if it is too big
			slide.css('font-size', '');
			if (slide.find('td.s5-slide-left').height() > $window.height()) { 
				slide.css('font-size', '0.8em');
			}
			
			return newSize;
		},
		makeSizeDetector: function() {
			jQuery('#s5-size-detector').remove();
			this.sizeDetector = jQuery('<div id="s5-size-detector" />').appendTo($body);
		},
		sizeDetector: jQuery('<div />'),
		emSize: function() {
			var height = this.sizeDetector.height();  
			return (height > 1 ? height : 1);
		},
		safeImg: function() {
			$body.find('img').each(function() {
				var img = jQuery(this);
				var src = img.attr('src');
				img.removeAttr('src');
				
				jQuery(this)
					.load(function() {
						jQuery(this).fadeTo(1, 0.01);
						jQuery.s5.scale();
						jQuery(this)
							.hide()
							.fadeTo(1, 1)
							.fadeIn();
					})
					.attr('src', src);
			});
		},
		makeTheme: function(theme, parent, isNotes) {
			if (theme) {
				if (
					theme.slideFontColor || 
					theme.headerFontColor ||
					theme.backgroundColor ||
					theme.backgroundImage ||
					theme.listItemHighlightColor
				) {
					this.s.slideFontColor = theme.slideFontColor = (theme.slideFontColor ? theme.slideFontColor : '');
					this.s.headerFontColor = theme.headerFontColor = (theme.headerFontColor ? theme.headerFontColor : '');
					this.s.backgroundColor = theme.backgroundColor = (theme.backgroundColor ? theme.backgroundColor : '');
					this.s.backgroundImage = theme.backgroundImage = (theme.backgroundImage ? theme.backgroundImage : '');
					this.s.listItemHighlightColor = theme.listItemHighlightColor = (theme.listItemHighlightColor ? theme.listItemHighlightColor : '');
				}
			}
			
			var cl = '';
			if (isNotes) {
				cl = 's5-style-note';
			} else {
				cl = 's5-style';
				if (this.note) {
					this.makeTheme(null, jQuery(this.note.document).find('body'), true);
				}
			}
			
			jQuery('style.' + cl).remove();
			
			parent = (parent ? parent : $body);
			
			parent.append(
				'<style class="' + cl + '">' +
					'.s5-slide,.s5-note-body,.s5-note-body * {' +
						'color:' + this.s.slideFontColor + ';' +
					'} ' +
					'.s5-header {' + 
						'color:' + this.s.headerFontColor + ';' +
					'} ' +
					'body {' + 
						'background-color: ' + this.s.backgroundColor + ';' +
					'}' +
					'.s5-footer,.s5-menu,.s5-menu *,#tiki_slideshow_buttons,.s5-outlook-close,#s5-note-menu,#s5-note-menu * {' +
						'background: ' + this.s.headerFontColor + ' ! important;' +
						'color: ' + this.s.backgroundColor + ' ! important;' +
					'}' +
					'.s5-outline {' +
						'color: ' + this.s.headerFontColor + ' ! important;' +
						'background-color: ' + this.s.backgroundColor + ' ! important;' +
						'border: solid 1px ' + this.s.headerFontColor + ' ! important;' +
					'}' +
					'.s5-slide a,.s5-outline a {' +
						'color: ' + this.s.headerFontColor + ' ! important;' +
					'}' +
					'.s5-slide-li-highlighted {' +
						 'color: ' + this.s.listItemHighlightColor + ';' +
						 'font-size: ' + (this.emSize() * 1.2) + 'px;' +
					'}' +
					'.s5-slide-li-highlighted ul li {' +
						 'color: ' + this.s.slideFontColor + ' ! important;' +
						 'font-size: ' + this.emSize() + 'px ! important;' +
					'}' +
				'</style>'
			);
			
			jQuery.s5.scale();
			
			if (isNotes) return false;
			if (!this.s.backgroundImage) return false;
			if (!this.loadedBackgroundImage == this.s.backgroundImage) return false;
			
			jQuery('img.s5-background').remove();
			
			var background = jQuery('<img class="s5-background" />')
				.load(function() {
					jQuery.s5.scale();
				})
				.attr('src', this.s.backgroundImage)
				.prependTo(parent);
			
			this.s.backgroundImage = this.loadedBackgroundImage;
		},
		keys: function (key) {
			if (!key) {
				key = event;
				key.which = key.keyCode;
			}
			switch (key.which) {
				case 10: // return
				case 13: // enter
				case 32: // spacebar
				case 34: // page down
				case 40: // downkey
					jQuery.s5.go('next');
					break;
				case 33: // page up
				case 38: // upkey
				case  8: // backspace
					jQuery.s5.go('prev');
					break;
				case 39: // rightkey
					jQuery.s5.goLI('next');
					break;
				case 37: // leftkey
					jQuery.s5.goLI('prev');
					break;
				case 36: // home
					jQuery.s5.go(0);
					break;
				case 35: // end
					jQuery.s5.go(this.slideCount - 1);
					break;
				case 67: // c
					break;
				case 79: // o
					jQuery.s5.listSlideTitles($body);
			}
			return false;
		}
	}
});
