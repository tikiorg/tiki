/*
Script: Windoo.Manager.js
	Windoo window manager.
	Contains <Windoo.Manager>, <Windoo.$wm>.
*/

/*
Class: Windoo.Manager
	Window manager class.

Options:
	zIndex - Starting window z-index value;
	onRegister - optional, function to execute when window is registered;
	onUnregister - optional, function to execute when window is unregistered;
	onFocus - optional, function to execute when window is focused;
	onBlur - optional, function to execute when window loses focus;
*/

Windoo.Manager = new Class({
	focused: false,
	options: {
		zIndex: 100,
		onRegister: Class.empty,
		onUnregister: Class.empty,
		onFocus: Class.empty,
		onBlur: Class.empty
	},

	initialize: function(options){
		this.hash = [];
		this.setOptions(options);
	},

	/*
	Property: maxZIndex
		Returns maximal z-index value of all windows.
	*/

	maxZIndex: function(){
		var windows = this.hash;
		if (!windows.length) return this.options.zIndex;
		var zindex = [];
		windows.each(function(item){ this.push(item.zIndex);}, zindex);
		zindex.sort(function(a, b){ return a - b; });
		return zindex.getLast() + 3;
	},

	/*
	Property: register
		internal, register new window in the manager.
	*/

	register: function(win){
		win.setZIndex(this.maxZIndex());
		this.hash.push(win);
		return this.fireEvent('onRegister', win);
	},

	/*
	Property: unregister
		internal, unregister window.
	*/

	unregister: function(win){
		this.hash.remove(win);
		if (this.focused === win) this.focused = false;
		return this.fireEvent('onUnregister', win);
	},

	/*
	Property: focus
		internal, set focus to the window.

	Arguments:
		win - window to set as focused
	*/

	focus: function(win){
		if (win === this.focused) return false;
		if (this.focused) this.focused.blur();
		this.focused = win;
		win.bringTop(this.maxZIndex());
		return this.fireEvent('onFocus', win);
	},

	/*
	Property: blur
		internal, remove focus from the window if focused. Returns true if focus is removed.

	Arguments:
		win - window to remove focus from
	*/

	blur: function(win){
		if (this.focused === win){
			this.focused = false;
			this.fireEvent('onBlur', win);
			return true;
		}
		return false;
	}

});
Windoo.Manager.implement(new Events, new Options);

/*
Property: Windoo.$wm
	Default window manager object.
*/

Windoo.$wm = new Windoo.Manager();
