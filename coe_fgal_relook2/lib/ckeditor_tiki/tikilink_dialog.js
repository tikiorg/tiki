/* 
* (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
* 
* All Rights Reserved. See copyright.txt for details and a complete list of authors.
* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
* 
* $Id$
* 
* Actually not a real plugin but a modfier for the standard ckeditor link dialog
* Initially taken from http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Dialog_Customization
*/

 
CKEDITOR.on('dialogDefinition', function(ev) {
	// Take the dialog name and its definition from the event data.
	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;

 
	// Check if the definition is from the dialog we're
	// interested on (the Link dialog).
	if (dialogName == 'link') {
		
		var infoPanel = dialogDefinition.getContents('info');
		var tikilinkOptions = {
			type :  'vbox',
			id : 'tikilinkOptions',
			padding : 1,
			children :
			[
				{
					type : 'text',
					id : 'tikilinkPage',
					label : 'Wiki Page',
					required : true,
					validate : function() {
						var dialog = this.getDialog();



						if ( !dialog.getContentElement( 'info', 'linkType' ) ||
								dialog.getValueOf( 'info', 'linkType' ) != 'tikilink' ) {
							return true;
						}
							var func = CKEDITOR.dialog.validate.notEmpty( 'No page specified' );
						return func.apply( this );
					},
					setup : function( data ) {
						if (data.tikilink) {
							this.setValue(data.tikilink.page);
						}

						var linkType = this.getDialog().getContentElement( 'info', 'linkType' );
						if (linkType && linkType.getValue() == 'tikilink') {
							this.select();
						}
						// set autocomplete here
						if (jqueryTiki.autocomplete) {
							$("#" + this._.inputId).tiki('autocomplete', 'pagename');
						}
					},
					commit : function( data ) {
						if (!data.tikilink) {
							data.tikilink = {};
						}
						data.tikilink.page = this.getValue();
					}
				},
				{
					type : 'text',
					id : 'tikilinkLabel',
					label : "Link Text",
					setup : function( data ) {
						if (data.tikilink) {
							this.setValue(data.tikilink.label);
						}
					},
					commit : function( data ) {
						if (!data.tikilink) {
							data.tikilink = {};
						}
						data.tikilink.label = this.getValue();
					}
				}
			],
			setup : function( data ) {
				if (!this.getDialog().getContentElement('info', 'linkType')) {
					this.getElement().hide();
				}
			}
		};

		infoPanel.add(tikilinkOptions);
		
		var typeSelector = infoPanel.get('linkType');
		typeSelector.items.push([ "Wiki Page", 'tikilink' ]);
		
		var originalLinkTypeChanged = typeSelector.onChange;
		typeSelector.onChange = function() {
			var dialog = this.getDialog(),
				typeValue = this.getValue();

			var element = dialog.getContentElement( 'info', 'tikilinkOptions' );
			if ( element ) {
				element = element.getElement().getParent().getParent();
			}
			
			if ( typeValue == 'tikilink' ) {
				dialog.hidePage( 'target' );
				dialog.hidePage( 'advanced' );

				if (element) {
					element.show();
				}
			} else {
				dialog.showPage( 'advanced' );
				if (element) {
					element.hide();
				}
			}
			originalLinkTypeChanged.call(this);
		};
		
		var originalOnOkFunction = dialogDefinition.onOk;
		
		dialogDefinition.onOk = function() {	// switch the link type to normal url, no protocol and class wiki
			
			if (this.getContentElement('info','linkType').getValue() == "tikilink") {
				this.getContentElement('info','linkType').setValue('url');
				var page = escape(this.getContentElement('info','tikilinkPage').getValue().replace(/ /g, '+'));
				if (!jqueryTiki.sefurl) {
					page = 'tiki-index.php?page=' + page;
				}
				this.getContentElement('info','url').setValue(page);
				this.getContentElement('info','protocol').setValue('');
				this.getContentElement('advanced','advCSSClasses').setValue('wiki');
				if (this._.selectedElement && this._.selectedElement.setText) {
					this._.selectedElement.setText( this.getContentElement('info','tikilinkLabel').getValue() );
				}
			}

			originalOnOkFunction.call(this);
		};
		
		var originalOnShowFunction = dialogDefinition.onShow;
		
		dialogDefinition.onShow = function() {	// check for class=wiki and fill tikilink form if so
			
			originalOnShowFunction.call(this);
			
			if (this.getContentElement('advanced','advCSSClasses').getValue() === 'wiki') {
				this.getContentElement('info','linkType').setValue('tikilink');
				var page = this.getContentElement('info','url').getValue(page);
				if (!jqueryTiki.sefurl) {
					page = page.replace('tiki-index.php?page=', '');
				}
				page = unescape(page).replace(/\+/g, ' ' );
				this.getContentElement('info','tikilinkPage').setValue(page);
				this.getContentElement('info','tikilinkLabel').setValue(this._.selectedElement.getText());
			} else {
				var editor = this.getParentEditor(),
					selection = editor.getSelection(),
					r = selection.getRanges(),
					seltext = "";
				
				if (selection.getType() === CKEDITOR.SELECTION_TEXT && !r[0].collapsed) {
					seltext = getTASelection("#" + editor.name);
					this.getContentElement('info','tikilinkLabel').setValue(seltext);
				}
			}
		};
	}
 });

