rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    
    function log(msg) {
    	console.log(msg);
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
    
    var dom = api.dom;
    api = $.extend(api, {
    	htmlWords: {},
    	analyse: function(phrase, options) {
	    	phraser.lexer.wordHandler = null;
	    	phraser.lexer.tagHandler = null;
	    	phraser.lexer.charHandler = null;
	    	
		    phraser.lexer = $.extend(phraser.lexer, options);
		    
		    return phraser.parse(phrase);
	    },
	    phraseIndexes: function(phraseWords, parentWords) {
	    	var start = -1;
	    	var stop = false;
	        var y = phraseWords.length - 1;
	        for (var i = 0; i < parentWords.length && !stop; i++) {
	        	if (parentWords[i].match(phraseWords[0])) {
	        		var l = matchLength(parentWords, i, phraseWords, 0);
	        		if (l > 10) start = i;
	        	}
	        }
	        return {
	        	start: start,
	        	end: start + phraseWords.length
	        };
	    },
	    setPhraseSelection: function(phrase, rootNode, doc) {
	    	phrase = this.setPhrase(phrase, rootNode, doc);
	    	
	    	var range = api.createRange(doc);
			range.setStartBefore(phrase.start[0]);
			range.setEndAfter(phrase.end[0]);
			
			var sel = api.getSelection(window);
			var ranges = [range];
			sel.setRanges(ranges);
			phrase.range = range;
			return phrase;
	    },
	    setPhrase: function(phrase, rootNode, doc) {
			if (rootNode) {
            	doc = doc || dom.getDocument(rootNode);
			} else {
				doc = doc || document;
	            rootNode = doc.documentElement;
	        }
			rootNode = $(rootNode);
			var id = rootNode.attr('id');
			
       		var phraseWords = [];
       		api.analyse(phrase, {
       			wordHandler: function(word) {
	       			phraseWords.push(word);
	       			return word;
       			}
       		});
       		
       		//log(phraseWords);
       		//here we attempt to cache the rootNode as words
       		var htmlWords = [];
       		if (!api.htmlWords[id]) {
	       		api.analyse(rootNode.html(), {
	       			wordHandler: function(word) {
	       				htmlWords.push(word);
	       				return word;
	       			}
	       		});
	       		this.htmlWords[id] = htmlWords;
       		} else {
       			htmlWords = this.htmlWords[id];
       		}
       		
	        var indexes = api.phraseIndexes(phraseWords, htmlWords);
	        var wordI = 0;
	        
	        if (indexes.start > -1 && indexes.end > -1) {
	       		var html = api.analyse($(rootNode).html(), {
	       			wordHandler: function(word) {
	       				if (wordI >= indexes.start && wordI < indexes.end) {
	       					word = '<span class="rangyPhrase new" style="border: none;">' + word + '</span>';
	       				}
	       				
		       			if (wordI == indexes.start) {
		       				word = '<span class="rangyPhraseStart new" style="border: none;"/>' + word;
		       			} else if (wordI == indexes.end) {
		       				word = word + '<span class="rangyPhraseEnd new" style="border: none;"/>';	       						
		       			}
		       			
		       			wordI++;
		       			return word;
		       		},
		       		charHandler: function(ch) {
		       			if (wordI >= indexes.start && wordI < indexes.end) {
	       					ch = '<span class="rangyPhrase ui-state-highlight" style="border: none;">' + ch + '</span>';
	       				}
	       				return ch;
		       		}
	       		});
	        }
	        
	        rootNode.html(html);
	        
	        return {
	        	selection: $('.rangyPhrase.new').removeClass('new'),
	        	start: $('.rangyPhraseStart.new').removeClass('new'),
	        	end: $('.rangyPhraseEnd.new').removeClass('new'),
	        	phrase: phrase,
	        	phraseWords: phraseWords,
	        	htmlWords: htmlWords
	        };
	    }
    });
});