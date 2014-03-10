var WikiLingoEdit = (function(document, $, Medium) {
    var Construct = function(el, input) {
	    var
		    bubble = new WLBubble(window.expressionSyntaxes, el),
            WLPlugin = function(el) {
                if (el.getAttribute('data-draggable') == 'true') {
                    new WLPluginAssistant(el, 'vendor/wikilingo/wikilingo/');
                }
            },
            color = function(element) {
                var newColor = prompt(tr('What color?'), element.style['color']);
                if (newColor) {
                    element.style['color'] = newColor
                }
            },
		    table = function(element) {

		    },
            medium = (Medium ? el.medium = new Medium({
                element: el,
                mode: 'rich',
                placeholder: tr('Your Article'),
                autoHR: false,
                cssClasses: [],
                attributes: {
                    remove: []
                },
                tags: {
                    paragraph: 'p',
                    outerLevel: ['pre','blockquote', 'figure', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'ul', 'strong', 'code', 'br', 'b', 'span'],
                    innerLevel: ['a', 'b', 'u', 'i', 'img', 'div', 'strong', 'li', 'span', 'code', 'br']
                },
                modifiers: [],
                beforeAddTag: function(tag, shouldFocus, isEditable, afterElement) {
                    var newEl;
                    switch (tag) {
                        case 'br':
                        case 'p':
                            newEl = document.createElement('br');
                            newEl.setAttribute('class', 'element');
                            newEl.setAttribute('data-element', 'true');
                            newEl.setAttribute('data-type', 'WikiLingo\\\\Expression\\\\Line');

                            this.element.medium.insertHtml(newEl)
                            return true;
                    }

                    return newEl;
                }
            }) : null);

        $('body')
            .on('resetWLPlugins', function() {
                for(var i = 0; i < window.wLPlugins.length; i++) {
                    new WLPlugin(document.getElementById(window.wLPlugins[i]));
                }
            })
            .trigger('resetWLPlugins');

	    $(el)
		    .on('mouseup', function(event) {
			    if (document.activeElement === this) {
				    bubble.goToSelection();
			    }
		    })
		    .on('focus', function() {
			    this.before = this.innerHTML;
			    return this;
		    })
		    .on('blur keyup paste input', function() {
			    var $this = $(this);
			    if (this.before !== this.innerHTML) {
				    this.before = this.innerHTML;
				    setTimeout(function() {
					    $this.trigger('change');
				    }, 10);
			    }
			    return this;
		    });

	    bubble.staticToTop();

        el.onchange = function() {
            input.value = el.innerHTML;
        };

        this.el = el;
        this.input = input;
    };


    return Construct;
})(document, jQuery, window.Medium);

$(document)
    //wysiwyg events
    .bind('previewWikiLingo', function(e, wysiwyg, data, form, previewWindow) {
        $.post($.service("edit", "wikiLingo"), {
            wysiwyg: (wysiwyg ? 1 : 0),
            data: data,
            autoSaveId: autoSaveId,
            page: autoSaveId.split(':').pop(),
            preview: true
        }, function(result){
            result = $.parseJSON(result);
            previewWindow.html(result.parsed);

            $('body')
                .append(result.css)
                .append(result.script);
        });
    })
    .bind('saveWikiLingo', function(e, wysiwyg, data, form) {
        var page;
        $.post($.service("edit", "wikiLingo"), {
            wysiwyg: (wysiwyg ? 1 : 0),
            data: data,
            autoSaveId: autoSaveId,
            page: page = autoSaveId.split(':').pop(),
            save: 1
        }, function(result) {
	        var json = $.parseJSON(result);
	        if (json) {
                document.location = 'tiki-index.php?page=' + page
	        } else {
		        $.notify(result);
	        }
        });
    });

window.update_output_type = function(select) {
    $.get($.service('edit', 'update_output_type'), {
        page: autoSaveId.split(':').pop(),
        output_type: $(select).val()
    },function(result){
        result = $.parseJSON(result);
        if (result.value) {
            $.notify(tr('Changed to:') + result.value);
        } else {
            $.notify(tr('Changed to default'));
        }
    });
};