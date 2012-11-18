CKEDITOR.plugins.add( 'jisonplugin', {
    init: function( editor ) {

        var pluginHandler = {
            getFromElement: function(element) {
                return plugin = {
                    name: element.data('name'),
                    args: $.parseJSON(element.data('args')),
                    body: decodeURIComponent(element.data('body')),
                    index: element.data('i')
                };
            },
            setElementFromJson: function(element, plugin) {
                var syntax = '',
                    args = [];
                for(arg in plugin.args) {
                    args.push(arg + '="' + plugin.args + '"');
                }

                if ($.trim(plugin.body)) { //State-able plugin
                    syntax = "{" + plugin.name.toUpperCase() + ' (' +
                    args.join(" ") +
                    ")}" + plugin.body + "{" + plugin.name.toUpperCase() + "}";
                } else { //inline plugin
                    syntax = "{" + plugin.name.toLowerCase() + ' ' + args.join(" ") + "}";

                }
                element.data('name', plugin.name);
                element.data('args', JSON.stringify(plugin.args));
                element.data('body', encodeURIComponent(plugin.body));
                element.data('i', plugin.index)
                element.data('syntax', encodeURIComponent(syntax));
            }
        };

        editor.on('doubleclick', function(evt) {
            if (evt.data.element.data('t') == jisonSyntax.plugin) {
                var element = evt.data.element,
                    plugin = pluginHandler.getFromElement(element),
                    form = $(build_plugin_form( plugin.name, plugin.index, '', plugin.args, plugin.body));

                var container = $('<div class="plugin"></div>')
                    .append(form)
                    .dialog();

                form.submit(function() {
                    //TODO: update element here and auto save
                    var newPlugin = {
                        name: plugin.name,
                        args: {},
                        body: form.find('textarea[name="content"]').val(),
                        index: plugin.index
                    };

                    form.find('input[name^="params"]').each(function() {
                        var arg = $(this).attr('name');
                        arg = arg.replace('params[', '');
                        arg = arg.replace(']', '');

                        newPlugin.args[arg] = $(this).val();
                    });

                    pluginHandler.setElementFromJson(element, newPlugin);

                    container.dialog('destroy');
                    return false;
                });
            }
        });
    }
});