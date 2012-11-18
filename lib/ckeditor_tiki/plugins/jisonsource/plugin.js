CKEDITOR.plugins.add( 'jisonsource', {
    init: function( editor ) {
        editor.on( 'mode', function(evt) {
            if (evt.editor.mode == "source") {
                $.modal(tr('Loading...'));
                //TODO: Create services for converting to source
            } else if (evt.editor.mode == "wysiwyg") {
                //TODO: Create service for converting back to wysiwyg
            }
        });
    }
});
