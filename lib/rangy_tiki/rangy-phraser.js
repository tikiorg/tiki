rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    var UNDEF = "undefined";

    // encodeURIComponent and decodeURIComponent are required for cookie handling
    if (typeof encodeURIComponent == UNDEF || typeof decodeURIComponent == UNDEF) {
        module.fail("Global object is missing encodeURIComponent and/or decodeURIComponent method");
    }
    
    function rePhrase(phrase) {
		var phrases = [phrase.substr(0, 200), phrase.substr(-200, 200)];
		console.log(phrases);
    	
    	var phrase = (phrase.length >= 200 ? phrases.join(' ') : phrase);
    	
    	phrase = phrase.replace('.', '[.]');
    	phrase = phrase.replace(/[ ]/g, '(.|\\n)+?');
    	phrase = phrase.replace('(.|\\n)+(.|\\n)+', '(.|\\n)+');
    	phrase = phrase.replace('(.|\\n)+(.|\\n)+', '(.|\\n)+');
    	phrase = phrase.replace('(.|\\n)+(.|\\n)+', '(.|\\n)+');
    	console.log(phrase);
    	return new RegExp(phrase, "gm");
    }
    
    var dom = api.dom;
    api = $.extend(api, {
	    getPhrase: function() {
			return rangy.getSelection();
	    },
	    setPhrase: function(phrase, rootNode, doc) {
			if (rootNode) {
            	doc = doc || dom.getDocument(rootNode);
			} else {
				doc = doc || document;
	            rootNode = doc.documentElement;
	        }
	        
	        var html = $(rootNode).html();
	        
	        html = html.replace(rePhrase(phrase), function(found) {
	        	return '<span class="rangyPhraseStart new"/><span class="rangyPhrase">' + found + '</span><span class="rangyPhraseEnd new"/>'
	        });
	        
	        $(rootNode).html(html);
	        
			var range = api.createRange(doc);
			range.setStartBefore($('span.rangyPhraseStart.new').removeClass('new')[0]);
			range.setEndAfter($('span.rangyPhraseEnd.new').removeClass('new')[0]);
			
			var sel = api.getSelection(window);
			var ranges = [range];
			sel.setRanges(ranges);
			
			return range;
	    },
	    is: function(phrase, rootNode, doc) {
	       return $(rootNode).html().match(rePhrase(phrase));
	    }
    });
});
