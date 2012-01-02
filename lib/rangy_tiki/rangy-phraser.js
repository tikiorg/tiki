rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    var UNDEF = "undefined";

    // encodeURIComponent and decodeURIComponent are required for cookie handling
    if (typeof encodeURIComponent == UNDEF || typeof decodeURIComponent == UNDEF) {
        module.fail("Global object is missing encodeURIComponent and/or decodeURIComponent method");
    }
    
    function rePhrase(phrase) {
    	console.log(phrase);
    	phrase = phrase.split('.');
    	
    	var firstPhrase = phrase.shift();
    	var lastPhrase = phrase.pop();
    	
    	firstPhrase = (firstPhrase ? firstPhrase : phrase.shift());
    	lastPhrase = (lastPhrase ? lastPhrase : phrase.pop());
		
		var phrases = [$.trim(firstPhrase), $.trim(lastPhrase)];
		
    	console.log(phrases);
    	phrase = phrases.join('.')
    		.replace(/[,. ]/g, '(.+?)');
    	
    	console.log(phrase);
    	return new RegExp(phrase, "g");
    }
    
    var dom = api.dom;
    api = $.extend(api, {
	    getPhrase: function(node, offset, rootNode) {
	       
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
	        	alert(found);
	        	return '<span class="rangyPhraseStart new"/>' + found + '<span class="rangyPhraseEnd new"/>'
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
