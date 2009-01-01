/*
Script: Windoo.Drag.js
	Draggable and resizable effects for Windoo class.
	Contains <Windoo::makeDraggable>, <Windoo::makeResizable>.
*/

/*
Class: Windoo
	Draggable and resizable window class.
*/

Windoo.implement({

	/*
	Property: makeResizable
		internal, add resizable effect to the window (see: <Drag.Resize>)
	*/

	makeResizable: function(){
		var self = this, theme = this.theme, opt = this.options, inbody = opt.container === $(document.body);
		this.fx.resize = this.el.makeResizable({
			ghostClass: theme.ghostClass,
			hoverClass: theme.hoverClass,
			classPrefix: theme.classPrefix + '-sizer ' + theme.classPrefix + '-',
			shadeBackground: theme.shadeBackground,

			container: (opt.restrict && !inbody) ? opt.container : false,
			resizeLimit: opt.resizeLimit,
			ghost: opt.ghost.resize,
			snap: opt.snap.resize,

			onBeforeStart: function(){
				self.fireEvent('onBeforeResize', this).focus();
			},
			onStart: function(fx){
				if (self.maximized){
					fx.stop();
				} else {
					if (!this.ghost && window.gecko) Element.$overlay.call(fx.shade.overlay);
					self.fireEvent('onStartResize', this);
				}
			},
			onResize: function(){
				self.fireEvent('onResize', this);
			},
			onComplete: function(){
				if (this.ghost){
					var size = self.getState().outer;
					self.setSize(size.width, size.height);
				} else {
					self.fix().fireEvent('onResizeComplete', this);
				}
			},
			onBuild: function(dir, binds){
				if (!this.ghost){
					var fx = this.fx[dir], nolimit = {'x':{'limit': false}, 'y':{'limit': false}};
					if (binds.resize.y) ['strut', 'body', 'shm'].each(function(name){
						if (this[name]) fx.add(this[name], {'y': {direction: binds.resize.y.direction, style: 'height'}}, binds.resize);
					}, self.dom);
					[self.shadow, self.el.fixOverlayElement].each(function(el){
						if (el){
							fx.add(el, $merge(binds.resize, nolimit), binds.resize);
							if (binds.move) fx.add(el, $merge(binds.move, nolimit), binds.move);
						}
					}, self);
				}
			}
		});
	},

	/*
	Property: makeDraggable
		internal, add drag effect to the window (see: <Drag.Move>)
	*/

	makeDraggable: function(){
		var self = this, fx = this.fx.drag = [], inbody = this.options.container === $(document.body);
		var xLimit = function(){ return 2 - self.el.offsetWidth; };
		var opts = {
			container: (this.options.restrict && !inbody ? this.options.container : null),
			limit: (inbody ? {'x': [xLimit], 'y': [0]} : {}),
			snap: this.options.snap.move,
			onBeforeStart: function(){
				self.focus();
				this.shade = new Fx.Overlay(window, {'styles': {
					'cursor': this.options.handle.getStyle('cursor'),
					'background': self.theme.shadeBackground,
					'zIndex': self.zIndex + 3
				}}).show();
				if (self.ghost){
					var ce = self.el.getSize().size;
					this.element.setStyles({
						'zIndex': self.zIndex + 3,
						'left': self.el.getStyle('left'),
						'top': self.el.getStyle('top'),
						'width': ce.x,
						'height': ce.y
					});
				} else if (window.gecko){
					Element.$overlay.call(this.shade.overlay, false, 2);
				}
				self.fireEvent('onBeforeDrag', this);
			},
			onStart: function(){
				if (self.maximized && !self.minimized) this.stop();
				else self.fireEvent('onStartDrag', this);
			},
			onSnap: function(){
				if (self.ghost) this.element.setStyle('display', 'block');
			},
			onDrag: function(){
				self.fix().fireEvent('onDrag', this);
			},
			onComplete: function(){
				this.shade.destroy();
				if (self.ghost){
					for (var z in this.options.modifiers){
						var style = this.options.modifiers[z];
						self.el.setStyle(style, this.element.getStyle(style));
					}
					this.element.setStyle('display', 'none');
				}
				self.fix().fireEvent('onDragComplete', this);
			}
		};
		if (this.options.ghost.move) this.ghost = new Element('div', {'class': this.theme.ghostClass, 'styles': {'display': 'none'}}).injectAfter(this.el);
		this.el.getElements('.' + this.theme.classPrefix + '-drag').each(function(d){
			opts.handle = d;
			d.setStyle('cursor', 'move');
			fx.push((this.ghost || this.el).makeDraggable(opts));
		}, this);
	}

});
