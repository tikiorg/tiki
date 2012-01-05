rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    
    function log(msg) {
    	console.log(msg);
    }
    
    function analyse(phrase, options) {
    	phraser.lexer.wordHandler = null;
    	phraser.lexer.tagHandler = null;
    	phraser.lexer.charHandler = null;
    	
	    phraser.lexer = $.extend(phraser.lexer, options);
	    
	    return phraser.parse(phrase);
    }
    
    function matchLength(array1, i, array2, j) {
    	var matchLength = 0;
    	var stop = false;
    	for(; i < array1.length && !stop; i++){
			for(; j < array2.length && !stop; j++){
				if (array1[i] == array2[j] || array1[i].match(array2[j])) {
					matchLength++;
					i++;
				} else {
					stop = true;
				}
			}
    	}
    	
    	return matchLength;
    }
    
    function phraseIndex(phraseWords, parentWords) {
    	var start = -1;
    	var stop = false;
        var y = phraseWords.length - 1;
        for (var i = 0; i < parentWords.length && !stop; i++) {
        	if (parentWords[i].match(phraseWords[0])) {
        		var l = matchLength(parentWords, i, phraseWords, 0);
        		log(l);
        		if (l > 10) start = i;
        	}
        }
        return start;
    }
    
    var dom = api.dom;
    api = $.extend(api, {
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
			
       		var phraseWords = [];
       		analyse(phrase, {
       			wordHandler: function(word) {
	       			phraseWords.push(word);
	       			return word;
       			}
       		});
       		//log(phraseWords);
       		var htmlWords = [];
       		analyse($(rootNode).html(), {
       			wordHandler: function(word) {
       				htmlWords.push(word);
       				return word;
       			}
       		});
       		
	        var start = phraseIndex(phraseWords, htmlWords);
	        var end = start + phraseWords.length;
			//log([start, end, start + end]);
	       	//log(start);
	        var wordI = 0;
	        
	        if (start > -1 && end > -1) {
	       		var html = analyse($(rootNode).html(), {
	       			wordHandler: function(word) {
	       				if (wordI >= start && wordI < end) {
	       					word = '<span class="rangyPhrase new">' + word + '</span>';
	       				}
	       				
		       			if (wordI == start) {
		       				word = '<span class="rangyPhraseStart new"/>' + word;
		       			} else if (wordI == end) {
		       				word = word + '<span class="rangyPhraseEnd new"/>';	       						
		       			}
		       			
		       			//log([word, start, wordI, end]);
		       			wordI++;
		       			return word;
		       		}
	       		});
	        }
	        
	        $(rootNode).html(html);
	        
	        return {
	        	selection: $('.rangyPhrase.new').removeClass('new'),
	        	start: $('.rangyPhraseStart.new').removeClass('new'),
	        	end: $('.rangyPhraseEnd.new').removeClass('new'),
	        	phrase: phrase
	        };
	    }
    });
});
