/*
Script: Windoo.Dialog.js
	Windoo standard modal dialog utility classes.
	Contains <Windoo.Alert>, <Windoo.Confirm>.
*/

/*
Property: Windoo.Dialog
	Abstract inline modal Dialog class.

Arguments:
	message - message text.
	options - Options object.

Options:
	window - custom Windoo window options. see Windoo options.
	buttons - Buttons object.
	panel - custom Panel element options. see Element options.
	message - custom message element options. see Element options.

Buttons:
	ok - custom OK button options. see Element options.
	cancel - custom Cancel button options. see Element options.

Events:
	onConfirm - optional, function to execute when dialog is confirmed.
	onCancel - optional, function to execute when dialog is rejected.

*/

Windoo.Dialog = Windoo.extend({

	initialize: function(message, options){
		var self = this, dialog = this.dialog = {
			dom: {},
			buttons: {},
			options: $merge(Windoo.Dialog.options, options),
			message: message
		};
		this.parent($merge({
			'onShow': function(){
				if (dialog.buttons.ok) dialog.buttons.ok.focus();
			}
		}, dialog.options.window));
		dialog.bound = function(ev){
			ev = new Event(ev);
			if (['enter', 'esc'].contains(ev.key)){
				dialog.result = (ev.key == 'enter') ? !dialog.cancelFocused : false;
				self.close();
				ev.stop();
			}
		};
		document.addEvent('keydown', dialog.bound);
		this.addEvent('onClose', function(){
			document.removeEvent('keydown', dialog.bound);
			dialog.options[(dialog.result) ? 'onConfirm' : 'onCancel'].call(this);
		});
	},

	buildDialog: function(klass, buttons){
		var self = this, dialog = this.dialog;
		if ('ok' in buttons) dialog.buttons.ok =  new Element('input', $merge({
			'events': {
				'click': function(){
					dialog.result = true;
					self.close();
				}
			}
		}, dialog.options.buttons.ok));
		if ('cancel' in buttons) dialog.buttons.cancel = new Element('input', $merge({
			'events': {
				'click': function(){
					dialog.result = false;
					self.close();
				}
			}
		}, dialog.options.buttons.cancel)).addEvents({
			'focus': function(){
				dialog.cancelFocused = true;
			},
			'blur': function(){
				dialog.cancelFocused = false;
			}
		});
		dialog.dom.panel = new Element('div', $merge({'class': this.classPrefix(klass + '-pane')}, dialog.options.panel));
		for (var btn in buttons) if (buttons[btn]) dialog.dom.panel.adopt(dialog.buttons[btn]);
		dialog.dom.message = new Element('div', $merge({'class': this.classPrefix(klass + '-message')}, dialog.options.message));
		return this.addPanel(dialog.dom.panel).adopt(dialog.dom.message.setHTML(dialog.message));
	}

});

Windoo.Dialog.options = {
	'window': {
		'modal': true,
		'resizable': false,
		'buttons': {
			'minimize': false,
			'maximize': false
		}
	},
	'buttons': {
		'ok': {
			'properties': {
				'type': 'button',
				'value': 'OK'
			}
		},
		'cancel': {
			'properties': {
				'type': 'button',
				'value': 'Cancel'
			}
		}
	},
	'panel': null,
	'message': null,
	'onConfirm': Class.empty,
	'onCancel': Class.empty
};

/*
Property: Windoo.Alert
	Alert inline dialog class.
	Inherits properties, methods, events, and options from <Windoo.Dialog>.

Arguments:
	message - message text.
	options - Windoo.Dialog options object.
*/

Windoo.Alert = Windoo.Dialog.extend({

	initialize: function(message,  options){
		this.parent(message,  options);
		this.buildDialog('alert', {'ok': true}).show();
	}

});

/*
Property: Windoo.Confirm
	Confirm inline dialog class.
	Inherits properties, methods, events, and options from <Windoo.Dialog>.

Arguments:
	message - message text.
	options - Windoo.Dialog options object.

*/

Windoo.Confirm = Windoo.Dialog.extend({

	initialize: function(message,  options){
		this.parent(message,  options);
		this.buildDialog('confirm', {'ok': true, 'cancel': true}).show();
	}

});
