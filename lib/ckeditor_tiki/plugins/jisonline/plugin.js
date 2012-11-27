CKEDITOR.plugins.add( 'jisonline', {
    init: function( editor ) {

	    CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
	    CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_BR;
	    editor.dataProcessor.writer.setRules('br',{
		    indent: false,
		    breakBeforeOpen: false,
		    breakAfterOpen: false,
		    breakBeforeClose: false,
		    breakAfterClose: false
	    });
        editor.on( "key", function( evt ) {
            if (
                evt.data.keyCode == 13 //enter
                    ||
                    evt.data.keyCode == 2228237 //shift + enter
                ) {
                if (evt.editor.getSelection().getStartElement().$.localName == "body") {
                    evt.editor.insertElement(CKEDITOR.dom.element.createFromHtml("<br data-t=\'ln\' />"));
                    evt.cancel();
                    evt.stop();
                }
            }
        });
    }
});