/*
Script: Windoo.Panel.js
	Windoo extension for creating border panels (toolbars).
	Contains <Windoo::addPanel>, <Windoo::removePanel>.
*/

Windoo.implement({

	/*
	Property: addPanel
		Add border panel to the window at specified position and recalculate window paddings.

	Arguments:
		element - the panel content $(element)
		position - one of 'bottom', 'top', 'left', 'right'

	Returns:
		The Windoo.
	*/

	addPanel: function(element, position){
		position = $pick(position, 'bottom');
		var dim, ndim,
			size = this.el.getSize().size,
			styles = {'position': 'absolute'},
			panel = {'element': $(element), 'position': position, 'fx': []};
		switch (position){
			case 'top':
			case 'bottom': dim = 'x'; ndim = 'y'; break;
			case 'left':
			case 'right': dim = 'y'; ndim = 'x'; break;
			default: return this;
		}
		var options = Windoo.panelOptions[dim];
		styles[position] = this.padding[position];
		styles[options.deltaP] = this.padding[options.deltaP];
		element = panel.element.addClass(this.classPrefix('pane')).setStyles(styles).inject(this.el);
		panel.padding = element.getSize().size[ndim];
		this.padding[position] += panel.padding;
		if (this.options.resizable && !this.options.ghost.resize){
			this.fx.resize.add(function(dir, binds){
				if (binds.resize[dim]){
					var fx = this.fx[dir], mod = {};
					mod[dim] = $merge(binds.resize[dim]);
					mod[dim].limit = null;
					panel.fx.push({
						'fx': fx,
						'bind': fx.add(panel.element, mod, binds.resize)
					});
				}
			});
		}
		this.addEvent('onResizeComplete', function(){
			panel.element.setStyle(options.style, this.el.getSize().size[dim] - this.padding[options.deltaM] - this.padding[options.deltaP] - 1);
		});
		this.panels.push(panel);
		return this.setSize(size.x, size.y);
	},

	/*
	Property: removePanel
		Remove window border panel.

	Arguments:
		element - the panel content $(element)

	Returns:
		The Windoo.
	*/

	removePanel: function(element){
		var panel, size;
		element = $(element);
		for (var i = 0, len = this.panels.length; i < len; i++){
			panel = this.panels[i];
			if (panel.element === element){
				this.padding[panel.position] -= panel.padding;
				panel.element.remove();
				panel.fx.each(function(pfx){ pfx.fx.detach(pfx.bind); }, this);
				this.panels.splice(i, 1);
				size = this.el.getSize().size;
				this.setSize(size.x, size.y);
				break;
			}
		}
		return this;
	}

});

Windoo.panelOptions = {
	'x': {'style': 'width', 'deltaP': 'left', 'deltaM': 'right'},
	'y': {'style': 'height', 'deltaP': 'top', 'deltaM': 'bottom'}
};
