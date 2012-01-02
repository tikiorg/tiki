rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    
    function rePhrase(phrase) {
		var phrases = [phrase.substr(0, 200), phrase.substr(-200, 200)];
		console.log(phrases);
		
    	phrase = (phrase.length >= 200 ? phrases.join(' ') : phrase);
    	
    	phrase = phrase.replace('.', '[.]');
    	phrase = phrase.replace(/[ ]/g, '(.|\\n)+?');
    	phrase = phrase.replace('(.|\\n)+?(.|\\n)+?', '(.|\\n)+?');
    	phrase = phrase.replace('(.|\\n)+?(.|\\n)+?', '(.|\\n)+?');
    	phrase = phrase.replace('(.|\\n)+?(.|\\n)+?', '(.|\\n)+?');
    	console.log(phrase);
    	return new RegExp(phrase, "gm");
    }
    
    var dom = api.dom;
    api = $.extend(api, {
	    getPhrase: function() {
			return rangy.getSelection();
	    },
	    setPhraseSelection: function(phrase, rootNode, doc) {
	    	phrase = this.setPhrase(phrase, rootNode, doc);
	    	
	    	var range = api.createRange(doc);
			range.setStartBefore(phrase.start[0]);
			range.setEndAfter(phrase.end[0]);
			
			var sel = api.getSelection(window);
			var ranges = [range];
			sel.setRanges(ranges);
			return range;
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
	        	return '<span class="rangyPhraseStart new"/><span class="rangyPhrase new">' + found + '</span><span class="rangyPhraseEnd new"/>'
	        });
	        
	        $(rootNode).html(html);
	        
	        return {
	        	selection: $('span.rangyPhrase.new').removeClass('new'),
	        	start: $('span.rangyPhraseStart.new').removeClass('new'),
	        	end: $('span.rangyPhraseEnd.new').removeClass('new'),
	        	phrase: phrase
	        };
	    },
	    is: function(phrase, rootNode, doc) {
	       return $(rootNode).html().match(rePhrase(phrase));
	    }
    });
});
