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
    
    function getParts(val, id) {
    	var words = [];
    	var chs = [];
    	var i = 0;
    	
    	id = (!id ? val : id);
    	
   		if (!api.words[id]) {
       		api.analyse(val, {
       			wordHandler: function(word) {
       				i++;
       				words.push(word);
       				return word;
       			},
       			charHandler: function(ch) {
       				if (!chs[i]) chs[i] = '';
	       				chs[i] += ch;
	       			
       				return ch;
       			}
       		});
       		
       		api.words[id] = words;
       		api.chs[id] = chs;
   		} else {
   			words = api.words[id];
   			chs = api.chs[id];
   		}
   		
   		return {
   			words: words,
   			chs: chs
   		};
    }
    
    function expandIndexesToCh(htmlParts, indexes, ch) {
        for(var i = indexes.start; i > 0; i--) {
        	if (htmlParts.chs[i])
        		if (htmlParts.chs[i].match(ch)) {
        			indexes.start = i;
        			i = -1;
        		}
        }
        
        for(var i = indexes.end; i < htmlParts.words.length; i++) {
        	if (htmlParts.chs[i])
        		if (htmlParts.chs[i].match(ch)) {
	        		indexes.end = i - 1;
	        		i = htmlParts.words.length;
	        	}
        }
        
        return indexes;
    }
    
    function getWrappedHtml(html, indexes) {
    	var i = 0;
	    return api.analyse(html, {
   			wordHandler: function(word) {
   				if (i >= indexes.start && i <= indexes.end) {
   					word = '<span class="rangyPhrase new" style="border: none;">' + word + '</span>';
   				}
   				
       			if (i == indexes.start) {
       				word = '<span class="rangyPhraseStart new" style="border: none;"/>' + word;
       			}
       			
       			//it is possible that word's start and end index are the same'
       			if (i == indexes.end) {
       				word = word + '<span class="rangyPhraseEnd new" style="border: none;"/>';	       						
       			}
       			
       			i++;
       			return word;
       		},
       		charHandler: function(ch) {
       			if (i > indexes.start && i < indexes.end) {
   					ch = '<span class="rangyPhrase new" style="border: none;">' + ch + '</span>';
   				}
   				return ch;
       		}
   		});
    }
    
    var dom = api.dom;
    api = $.extend(api, {
    	words: {},
    	chs: {},
    	tags: {},
    	analyse: function(phrase, options) {
		    options = $.extend({
		    	wordHandler: function(word) { return word; },
	    		tagHandler: function(tag) { return tag; },
	    		charHandler: function(ch) { return ch; }
		    }, options);
		    
		    Parser.lexer = $.extend(Parser.lexer, options);
		    
		    return Parser.parse(phrase);
	    },
	    phraseIndexes: function(phraseWords, parentWords, allMatches) {
	    	var start = -1;
	    	var stop = false;
	        var y = phraseWords.length - 1;
	        var matches = [];
	        
	        for (var i = 0; i < parentWords.length && !stop; i++) {
	        	if (parentWords[i].match(phraseWords[0])) {
	        		var l = matchLength(parentWords, i, phraseWords, 0);
	        		if (l > 10 || l == phraseWords.length) {
	        			matches.push({
	        				start: i,
	        				end: i + y
	        			});
	        		}
	        	}
	        }
	        
	        if (!allMatches) {
	        	return matches[0];
	        } else {
	        	return matches;
	        }
	    },
	    setPhraseSelection: function(phrase, rootNode, doc) {
	    	phrase = api.setPhrase(phrase, rootNode, doc);
	    	
	    	var range = api.createRange(doc);
			range.setStartBefore(phrase.start[0]);
			range.setEndAfter(phrase.end[0]);
			
			var sel = api.getSelection(window);
			var ranges = [range];
			sel.setRanges(ranges);
			phrase.range = range;
			return phrase;
	    },
	    expandPhrase: function(phrase, ch, rootNode, doc) {
	    	if (rootNode) {
            	doc = doc || dom.getDocument(rootNode);
			} else {
				doc = doc || document;
	            rootNode = doc.documentElement;
	        }
			rootNode = $(rootNode);
			
			var html = rootNode.html();
       		var htmlParts = getParts(html, rootNode.attr('id'));
       		var phraseParts = getParts(phrase);
	    	
	    	var indexes = api.phraseIndexes(phraseParts.words, htmlParts.words);
	    	indexes = expandIndexesToCh(htmlParts, indexes, ch);
	    	
	    	var newPhrase = '';
	    	if (indexes.start > -1 && indexes.end > -1) {
	    		for(var i = indexes.start; i <= indexes.end + 1; i++) {
	    			if (htmlParts.chs[i] && i != indexes.start) newPhrase += htmlParts.chs[i];
	    			if (htmlParts.words[i] && i != indexes.end + 1) newPhrase += htmlParts.words[i];
	    		}
	    		
	    		return newPhrase
			}
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
			
			var html = rootNode.html();
       		var htmlParts = getParts(html, rootNode.attr('id'));
       		var phraseParts = getParts(phrase);
       		
	        var indexes = api.phraseIndexes(phraseParts.words, htmlParts.words);
	        
	        if (indexes.start > -1 && indexes.end > -1) {
				rootNode.html(getWrappedHtml(html, indexes));
			}
	        
	        return {
	        	selection: $('.rangyPhrase.new').removeClass('new'),
	        	start: $('.rangyPhraseStart.new').removeClass('new'),
	        	end: $('.rangyPhraseEnd.new').removeClass('new'),
	        	phrase: phrase,
	        	phraseParts: phraseParts,
	        	htmlParts: htmlParts
	        };
	    },
	    isUnique: function(phrase, rootNode, doc) {
	    	if (rootNode) {
            	doc = doc || dom.getDocument(rootNode);
			} else {
				doc = doc || document;
	            rootNode = doc.documentElement;
	        }
	        
	        rootNode = $(rootNode);
			
			var htmlParts = getParts(rootNode.html(), rootNode.attr('id'));
	        var phraseParts = getParts(phrase);
       		
       		var indexes = api.phraseIndexes(phraseParts.words, htmlParts.words, true);
	        
	        if (indexes.length > 1) {
	        	return false;
	        } else {
	        	return true;
	        }
	    }
    });
});