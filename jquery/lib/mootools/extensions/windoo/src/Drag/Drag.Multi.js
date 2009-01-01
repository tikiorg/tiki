/*
Script: Drag.Multi.js
	Mootools Drag.Base class extension which adds support for modifying multiple css properties of different elements simultaneously.
	Contains <Drag.Multi>.

License:
	MIT-style license.

Copyright:
	copyright (c) 2007 Yevgen Gorshkov
*/

// internal, the default Drag.Transition linear function and it's inverse

Drag.Transition = {
	linear:{
		step: function(start, current, direction){
			return direction * current - start;
		},
		inverse: function(start, current, direction){
			return (start + current) / direction;
		}
	}
};

/*
Class: Drag.Multi
	Modify multiple css properties of multiple elements based on the position of the mouse.

Arguments:
	options - The options object.

Options:
	handle - required, the $(element) to act as the handle for the draggable elements.
	onStart - optional, function to execute when the user starts to drag (on mousedown);
	onBeforeStart - optional, function to execute when the user starts to drag (on mousedown) but before initial properties values are calculated;
	onComplete - optional, function to execute when the user completes the drag.
	onSnap - optional, function to execute when the distance between staring point and current mouse position exceeds snap option value
	onDrag - optional, function to execute at every step of the drag
	snap - optional, the distance you have to drag before the element starts to respond to the drag. defaults to false

Example:
	(start code)
	var drag = new Drag.Multi({
		handle: $('handle'),
		
		onBeforeStart: function(){
			var size = $(document.body).getSize().scrollSize;
			this.shade = new Element('div', {
				styles: {
					position: 'absolute',
					top: 0,
					left: 0,
					width: size.x,
					height: size.y,
					cursor: 'move',
					'z-index': 100
				}
			}).inject(document.body);
		},

		onStart: function(){
			$each(arguments, function(el){
				el.addClass('ondrag');
			});
		},

		onComplete: function(){
			this.shade.remove();
			$each(arguments, function(el){
				el.removeClass('ondrag');
			});
		}

	});

	drag.add($('object'), {
		'x': {
			limit: [0,440],
			style: 'margin-left'
		},
		'y': {
			limit: [0, 198],
			style: 'margin-top'
		}
	});
	(end)
*/

Drag.Multi = Drag.Base.extend({

	options: {
		handle: false,
		onStart: Class.empty,
		onBeforeStart: Class.empty,
		onComplete: Class.empty,
		onDrag: Class.empty,
		snap: 6
	},

	elementOptions: {
		unit: 'px',
		direction: 1,
		limit: false,
		grid: false,
		bind: false,
		fn: Drag.Transition.linear
	},

	initialize: function(options){
		this.setOptions(options);
		this.handle = $(this.options.handle);
		this.element = [];
		this.mouse = {'start': {}, 'now': {}};
		this.modifiers = {};
		this.bound = {
			'start': this.start.bindWithEvent(this),
			'check': this.check.bindWithEvent(this),
			'drag': this.drag.bindWithEvent(this),
			'stop': this.stop.bind(this)
		};
		this.attach();
		if (this.options.initialize) this.options.initialize.call(this);
	},

	/*
	Property: add
		Add element to modify its css properties based on the position of the mouse.

	Returns:
		Bind object.

	Arguments:
		el - the $(element) to apply the transformations to.
		options - The options object.
		bind - The Bind object (see <Bind> below).

	Options:
		x - optional, the Modifier object (see below).
		y - optional, the Modifier object (see below).

	Modifier:
		style - required, the style you want to modify when the mouse moves in an horizontal direction.
		direction - optional, 1 corresponds to positive direction (style change according to move movement), -1 inverse direction. defaults to 1.
		limit - optional, array with start and end limit for style value.
		grid - optional, distance in px for snap-to-grid dragging.
		fn - optional, object with two properties - direct and inverse functions
				(start code)
				{
					step: function(start, current, direction){ return direction * current - start; },
					inverse: function(start, current, direction){ return (start + current) / direction; }
				}
				(end code)

	Bind:
		x - optional, Bind object; change $(element) modifier value according to changes in Bind object.
		y - optional, Bind object; change $(element) modifier value according to changes in Bind object.
	*/

	add: function(el, options, bind){
		el = $(el);
		if (!$defined(bind)) bind = {};
		var result = {};
		for (var z in options){
			if ($type(options[z]) != 'object' || !$defined(options[z].style)) continue;
			if (!$defined(this.modifiers[z])) this.modifiers[z] = [];
			var mod = $merge(this.elementOptions, options[z], {modifier: z, element: el, bind: false, binded: false});
			if (bind[z]){ mod.bind = bind[z]; mod.bind.binded = true; }
			var sign = mod.style.slice(0, 1);
			if (sign == '-' || sign == '+'){
				mod.direction = (sign + 1).toInt();
				mod.style = mod.style.slice(1);
			}
			this.modifiers[z].push(mod);
			result[z] = mod;
		}
		if (!this.element.contains(el)) this.element.push(el);
		return result;
	},

	/*
	Property: remove
		Stop all transformations for the passed element.

	Arguments:
		el - the $(element) to stop transformations for.
	*/

	remove: function(el){
		el = $(el);
		for (var z in this.modifiers) this.modifiers[z] = this.modifiers[z].filter(function(e){ return el != e.element; });
		this.element.remove(el);
		return this;
	},

	/*
	Property: detach
		Stop transformations described by the argument.

	Arguments:
		mod - Bind object returned by <Drag.Multi::add>
	*/

	detach: function(mod){
		for (var z in mod) if ($type(mod[z]) == 'object' && !mod[z].binded) this.modifiers[z].remove(mod[z]);
		return this;
	},

	start: function(event){
		this.fireEvent('onBeforeStart', this.element);
		this.mouse.start = event.page;
		for (var z in this.modifiers){
			var mouse = this.mouse.start[z];
			this.modifiers[z].each(function(mod){
				mod.now = mod.element.getStyle(mod.style).toInt();
				mod.start = mod.fn.step(mod.now, mouse, mod.direction, true);
				mod.$limit = [];
				var limit = mod.limit;
				if (limit) for (var i = 0; i < 2; i++){
					if ($chk(limit[i])) mod.$limit[i] = ($type(limit[i]) == 'function') ? limit[i](mod) : limit[i];
				}
			}, this);
		}
		document.addListener('mousemove', this.bound.check);
		document.addListener('mouseup', this.bound.stop);
		this.fireEvent('onStart', this.element);
		event.stop();
	},

	modifierUpdate: function(mod){
		var z = mod.modifier, mouse = this.mouse.now[z];
		mod.out = false;
		mod.now = mod.fn.step(mod.start, mod.bind ? mod.bind.inverse : mouse, mod.direction);
		if (mod.$limit && $chk(mod.$limit[1]) && (mod.now > mod.$limit[1])){
			mod.now = mod.$limit[1];
			mod.out = true;
		} else if (mod.$limit && $chk(mod.$limit[0]) && (mod.now < mod.$limit[0])){
			mod.now = mod.$limit[0];
			mod.out = true;
		}
		if (mod.grid) mod.now -= ((mod.now + mod.grid/2) % mod.grid) - mod.grid/2;
		if (mod.binded) mod.inverse = mod.fn.inverse(mod.start, mod.now, mod.direction);
		mod.element.setStyle(mod.style, mod.now + mod.unit);
	},

	drag: function(event){
		this.mouse.now = event.page;
		for (var z in this.modifiers) this.modifiers[z].each(this.modifierUpdate, this);
		this.fireEvent('onDrag', this.element);
		event.stop();
	}

});
