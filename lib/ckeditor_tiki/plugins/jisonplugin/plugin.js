CKEDITOR.plugins.add( 'jisonplugin', {
    init: function( editor ) {
        editor.on('doubleclick', function(evt) {
            if (evt.data.element.data('t') == jisonSyntax.plugin) {
                var elem = evt.data.element,
                    name = elem.data('name'),
                    args = $.parseJSON(elem.data('args')),
                    body = decodeURIComponent(elem.data('body')),
                    index = decodeURIComponent(elem.data('i')),
                    form = $(build_plugin_form( name, index, '', args, body));

                var container = $('<div class="plugin"></div>')
                    .append(form)
                    .dialog();

                form.submit(function() {
                    //TODO: update element here and auto save
                    container.dialog('destroy');
                    return false;
                });
            }
        });
    }
});