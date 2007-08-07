/*
Script: Windoo.Themes.js
	Windoo additional themes.
	Contains <Windoo.Themes.aero>.
*/

/*
Property: Windoo.Themes.aero
	Modified 'aero' theme from YUI-Ext library <http://extjs.com/>
*/

Windoo.Themes.aero = {
	'name': 'aero',
	'padding': {'top': 28, 'right': 10, 'bottom': 15, 'left': 10},
	'resizeLimit': {'x': [175], 'y': [58]},
	'className': 'windoo windoo-aero',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-aero-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadow': 'simple window-shadow-aero-simple',
	'shadeBackground': 'transparent url(windoo/s.gif)',
	'shadowDisplace': {'left': 3, 'top': 3, 'width': 0, 'height': 0}
};

/*
Property: Windoo.Themes.aqua
	MacOS X aqua theme
*/

Windoo.Themes.aqua = {
	'name': 'aqua',
	'padding': {'top': 23, 'right': 0, 'bottom': 15, 'left': 0},
	'resizeLimit': {'x': [275], 'y': [37]},
	'className': 'windoo windoo-aqua',
	'sizerClass': 'sizer',
	'classPrefix': 'windoo',
	'ghostClass': 'windoo-ghost windoo-aqua-ghost windoo-hover',
	'hoverClass': 'windoo-hover',
	'shadeBackground': 'transparent url(themes/windoo/s.gif)',
	'shadow': 'aqua',
	'complexShadow': true,
	'shadowDisplace': {'left': -13, 'top': -8, 'width': 26, 'height': 31, 'delta': 23}
};
