/*
Script: Drag.Resize.js
	Mootools Drag extension class for creating elements resizable in 8 directions.
	Contains <Drag.Resize>, <Element::makeResizable>.
*/

Drag.Multi.$direction = {
	east: { 'x':1 },
	west: { 'x':-1 },
	north: { 'y':-1 },
	south: { 'y':1 },
	nw: { 'x':-1, 'y':-1 },
	ne: { 'x':1, 'y':-1 },
	sw: { 'x':-1, 'y':1 },
	se: { 'x':1, 'y':1 }
};

/*
Class: Drag.Resize
	Extends <Drag.Base>, has additional functionality for resizing an element into 8 direction.

Arguments:
	el - the $(element) to apply the resize to.
	options - the options object.

Options:
	zIndex - optional, resize shade z-index;
	moveLimit - object, limit for element moving (resize in negative directions), see Limit below;
	resizeLimit - object, limit for element resizing, see Limit below;
	grid - optional, distance in px for snap-to-grid dragging;
	modifiers - an object. see Modifiers below;
	container - an element, will fill automatically limiting options based on the $(element) size and position. if false no limiting is applied. defaults to null (parentNode);
	preserveRatio - boolean, preserve initial element aspect ratio during resize. defaults to false;
	ghost - optional, show wired ghpot element during resize and update the element size and position after resize is completed;
	snap - optional, the distance you have to drag before the element starts to respond to the drag. defaults to 6;
	direction - object, see Direction below;
	limiter - object, see Limiter below;
	moveLimiter - object, see Limiter below;
	ghostClass - optional, wired ghost element class name;
	classPrefix - optional, class name prefix to add to sizer elements;
	hoverClass - optional, class name added to element onmouserover;
	shadeBackground - optional, background CSS property value for resize shade element (contains path to 1x1 px transparent gif image file);

Direction:
	east - east direction: { 'x':1 },
	west - west direction: { 'x':-1 },
	north - north direction: { 'y':-1 },
	south - south direction: { 'y':1 },
	nw - north-west direction: { 'x':-1, 'y':-1 },
	ne - north-east direction: { 'x':1, 'y':-1 },
	sw - south-west direction: { 'x':-1, 'y':1 },
	se - south-east direction: { 'x':1, 'y':1 }

Limiter:
	x - internal; {'-1': ['left', 'right'], '1': ['right', 'left']},
	y - internal; {'-1': ['top', 'bottom'], '1': ['bottom', 'top']}

Events:
	onBuild - optional, function to execute when resize handle is built;
	onBeforeStart - optional, function to execute when the user starts resizing but before initial properties values are calculated;
	onStart - optional, function to execute when the user starts resizing;
	onResize - optional, function to execute at every resize step;
	onComplete - optional, function to execute when the user completes the resize;
*/

Drag.Resize = new Class({

	options:{
		zIndex: 10000,
		moveLimit: false,
		resizeLimit: {'x': [0], 'y': [0]},
		grid: false,
		modifiers: {'x': 'left', 'y': 'top', 'width': 'width', 'height': 'height'},
		container: null, // false == no caintainer, null == container is parentNode
		preserveRatio: false,
		ghost: false,
		snap: 6,
		direction: Drag.Multi.$direction,
		limiter:{
			'x': {'-1': ['left', 'right'], '1': ['right', 'left']},
			'y': {'-1': ['top', 'bottom'], '1': ['bottom', 'top']}
		},
		moveLimiter:{
			'x': ['left', 'right'],
			'y': ['top', 'bottom']
		},
		ghostClass: 'ghost-sizer sizer-visible',
		classPrefix: 'sizer sizer-',
		hoverClass: 'sizer-visible',
		shadeBackground: 'transparent url(s.gif)',

		onBuild: Class.empty,
		onBeforeStart: Class.empty,
		onStart: Class.empty,
		onSnap: Class.empty,
		onResize: Class.empty,
		onComplete: Class.empty
	},

	initialize: function(el, options){
		var self = this;
		this.element = this.el = $(el);
		this.fx = {};
		this.binds = {};
		this.bound = {};
		this.setOptions(options);
		this.options.container = this.options.container === null ? this.el.getParent() : $(this.options.container);
		if ($type(this.options.direction) == 'string'){
			if (dir == 'all'){
				this.options.direction = Drag.Multi.$direction;
			} else {
				var dir = this.options.direction.split(/\s+/);
				this.options.direction = {};
				dir.each(function(d){ this[d] = Drag.Multi.$direction[d]; }, this.options.direction);
			}
		}
		var ce = this.el.getCoordinates(), positionStyle = this.el.getStyle('position');
		this.el.setStyles({'width': ce.width, 'height': ce.height});
		if (this.options.container){
			if (!(['relative', 'fixed'].contains(positionStyle))){
				var cc = this.options.container.getCoordinates();
				this.el.setStyles({'left': ce.left - cc.left, 'top': ce.top - cc.top});
			}
			this.options.moveLimit = $merge({'x': [0], 'y': [0]}, this.options.moveLimit);
		}
		if (this.options.preserveRatio){
			var R = ce.width / ce.height;
			// fix limits according to aspect ratio
			// FIXME how to process dynamic limits?
			// border limits do not work well too...
			var rlim = self.options.resizeLimit;
			var fix = function(z1, z2, op, no, coeff){
				if(rlim && rlim[z1] && rlim[z2] && rlim[z1][no] && rlim[z2][no])
					rlim[z1][no] = Math[op]( rlim[z1][no], coeff * rlim[z2][no] );
			};
			fix('x','y','max',0,R);
			fix('y','x','max',0,1/R);
			fix('x','y','min',1,R);
			fix('y','x','min',1,1/R);
			this.aspectStep = {
				x: {step: function(s, c, d){ return d * c / R - s; }},
				y: {step: function(s, c, d){ return d * c * R - s; }}
			};
			this.options.direction = $merge(this.options.direction);
			['nw','ne','sw','se'].each(function(z){ delete this[z]; }, this.options.direction);
		}
		if (this.options.ghost){
			this.ghost = new Element('div', {'class': this.options.ghostClass, 'styles': {'display': 'none'}}).injectAfter(this.el);
			for (var d in this.options.direction) this.ghost.adopt(new Element('div', {'class': this.options.classPrefix + d}));
		}
		var rOpts = {
			snap: this.options.snap,
			onBeforeStart: function(){
				self.fireEvent('onBeforeStart', this);
				self.started = true;
				this.shade = new Fx.Overlay(window, {'styles': {
					'position': positionStyle,
					'cursor': this.options.handle.getStyle('cursor'),
					'background': self.options.shadeBackground,
					'z-index': self.options.zIndex + 1
				}}).show();
				if (self.ghost){
					var ce = self.el.getCoordinates();
					self.ghost.setStyles({
						'display': 'block',
						'z-index': self.options.zIndex,
						'left': self.el.getStyle('left'),
						'top': self.el.getStyle('top'),
						'width': ce.width,
						'height': ce.height
					});
					for (var z in this.modifiers)
						this.modifiers[z].each(function(mod){
							if (mod.element === self.ghost)
								mod.element.setStyle(mod.style, self.el.getStyle(mod.style));
						});
					if (self.options.hoverClass) self.el.removeClass(self.options.hoverClass);
				}
			},
			onSnap: function(){
				self.fireEvent('onSnap', this);
			},
			onStart: function(){
				self.fireEvent('onStart', this);
			},
			onDrag: function(){
				self.fireEvent('onResize', this);
			},
			onComplete: function(){
				self.started = false;
				if (self.options.hoverClass) self.el.removeClass(self.options.hoverClass);
				this.shade.destroy();
				if (self.ghost){
					for (var z in this.modifiers){
						this.modifiers[z].each(function(mod){
							if (mod.element === self.ghost) self.el.setStyle(mod.style, mod.now + mod.unit);
						});
					}
					self.ghost.setStyle('display', 'none');
				}
				self.fireEvent('onComplete', this);
			}
		};
		var rlimitFcn = function(sign, props, limit){
			if (!self.options.container) return limit;
			if (!limit) limit = [0];
			var generator = function(lim){
				return function(mod){
					var cc = self.options.container.getCoordinates(),
						ec = mod.element.getCoordinates();
					var value = sign * (cc[props[0]] - ec[props[1]]);
					switch ($type(lim)){
						case 'number': return Math.min(value, lim);
						case 'function': return Math.min(value, lim(mod));
						default: return value;
					}
				};
			};
			return [limit[0], generator(limit[1])];
		};
		var mlimitFcn = function(props, limit, rlimit){
			var container = self.options.container;
			var generator = function(lim, rlim, op, rdef){
				if (!$type(rlim)) rlim = rdef;
				var lim_type = $type(lim);
				if (rlim === null) return lim_type == 'function' ? lim : function(){ return lim; };
				return function(mod){
					var cc = container.getCoordinates(),
						ec = mod.element.getCoordinates();
					var value = ec[props[1]] - cc[props[0]] - rlim;
					switch (lim_type){
						case 'number': return Math[op](value, lim);
						case 'function': return Math[op](value, lim(mod));
						default: return value;
					}
				};
			};
			if (!container){
				if (!limit) limit=false;
				container = self.el.getParent();
			} else if (!limit) limit=[0];
			return [generator(limit[0],rlimit[1],'max',null), generator(limit[1],rlimit[0],'min',limit[1])];
		};
		var opt = this.options, el = this.ghost ? this.ghost : this.el;
		if ($type(opt.grid) == 'number') opt.grid = {'x': opt.grid, 'y': opt.grid};
		for (var d in opt.direction){
			var mod = opt.direction[d];
			rOpts.handle = new Element('div', {'class': opt.classPrefix + d});
			var drag = this.fx[d] = new Drag.Multi(rOpts);
			var resizeLimit = {
				'x': rlimitFcn(mod.x, opt.limiter.x['' + mod.x], opt.resizeLimit.x),
				'y': rlimitFcn(mod.y, opt.limiter.y['' + mod.y], opt.resizeLimit.y)
			};
			var moveOpts = {};
			for (var z in mod){
				if (mod[z] < 0){
					moveOpts[z] = {
						limit: mlimitFcn(opt.moveLimiter[z], opt.moveLimit[z], opt.resizeLimit[z]),
						style: opt.modifiers[z],
						grid: opt.grid.x
					};
				}
			}
			var binds = {move: drag.add(el, moveOpts)}, resize = {opts: {}, bind: {}};
			this.binds[d] = binds;
			if ($defined(mod.x)){
				resize.opts.x = {
					limit: mod.x < 0 ? false : resizeLimit.x,
					grid: mod.x < 0 ? false : opt.grid.x,
					style: opt.modifiers.width,
					direction: mod.x
				};
				if (mod.x < 0) resize.bind.x = binds.move.x;
			}
			if ($defined(mod.y)){
				resize.opts.y = {
					limit: mod.y < 0 ? false : resizeLimit.y,
					grid: mod.y < 0 ? false : opt.grid.y,
					style: opt.modifiers.height,
					direction: mod.y
				};
				if (mod.y < 0) resize.bind.y = binds.move.y;
			}
			binds.resize = drag.add(el, resize.opts, resize.bind);
			if (opt.preserveRatio){
				var aspect = {
					'x': {
						fn: this.aspectStep.x,
						style: ($defined(mod.x)) ? opt.modifiers.height : null,
						direction: mod.x
					},
					'y': {
						fn: this.aspectStep.y,
						style: ($defined(mod.y)) ? opt.modifiers.width : null,
						direction: mod.y
					}
				};
				binds.aspect = drag.add(el, aspect, binds.resize);
			}
			this.fireEvent('onBuild', [d, binds]);
		}
		this.bound = (!this.options.hoverClass) ? {} : {
			'mouseenter': function(ev){
				this.addClass(self.options.hoverClass);
			},
			'mouseleave': function(ev){
				if(!self.started) this.removeClass(self.options.hoverClass);
			}
		};
		this.attach();
		if (this.options.initialize) this.options.initialize();
	},

	/*
	Property: add
		Call given function for each <Drag.Multi> instance created by <Drag.Resize>. Emulates onBuild event execution.

	Arguments:
		callback - the callback function called with arguments [direction, bind]
	*/

	add: function(callback){
		for (var d in this.options.direction)
			callback.call(this, d, this.binds[d]);
	},

	/*
	Property: attach
		Attach the effect to the element.
	*/

	attach: function(){
		$each(this.bound, function(fn, ev){ this.addEvent(ev, fn) }, this.el);
		for (var z in this.fx) this.element.adopt(this.fx[z].handle);
		return this;
	},

	/*
	Property: detach
		Detach the effect from the element.
	*/

	detach: function(){
		$each(this.bound, function(fn, ev){ this.removeEvent(ev, fn) }, this.el);
		for (var z in this.fx) this.fx[z].handle.remove();
		return this;
	},

	/*
	Property: stop
		Stop the effect and collect the garbage.
	*/

	stop: function(){
		this.detach();
		var garbage = [this.ghost];
		for (var z in this.fx) garbage.push(this.fx[z].handle);
		Garbage.trash(garbage);
		this.fx = this.bound = this.binds = {};
	}

});
Drag.Resize.implement(new Events, new Options);

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	/*
	Property: makeResizable
		Makes an element resizable (by dragging) with the supplied options.

	Arguments:
		options - see <Drag.Resize> and <Drag.Base> for acceptable options. Falls back to <Drag.Base> if handle options set.
	*/

	makeResizable: function(options){
		options = options || {};
		if (options.handle)
			return new Drag.Base(this, $merge({modifiers: {'x': 'width', 'y': 'height'}}, options));
		return new Drag.Resize(this, options);
	}

});
