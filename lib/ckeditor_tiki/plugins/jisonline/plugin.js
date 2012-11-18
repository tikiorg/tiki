CKEDITOR.plugins.add( 'jisonline', {
    init: function( editor ) {
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