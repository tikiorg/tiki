rangy.createModule("Phraser", ["WrappedSelection", "WrappedRange"], function(api, module) {

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
    
    function expandIndexesToCh(parentParts, indexes, ch) {
        for(var start = indexes.start; start > 0; start--) {
        	if (parentParts.chs[start])
        		if (parentParts.chs[start].match(ch)) {
        			indexes.start = start;
        			break;
        		}
        }
        
        for(var end = indexes.end; end < parentParts.words.length; end++) {
        	if (parentParts.chs[end])
        		if (parentParts.chs[end].match(ch)) {
	        		indexes.end = end - 1;
					end = parentParts.words.length;
					break;
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
       			if (i > indexes.start && i <= indexes.end) {
   					ch = '<span class="rangyPhrase new" style="border: none;">' + ch + '</span>';
   				}
   				return ch;
       		}
   		});
    }
    
    function o(rootNode, doc) {
    	if (rootNode) {
        	doc = doc || dom.getDocument(rootNode);
		} else {
			doc = doc || document;
            rootNode = doc.documentElement;
        }
		return (rootNode = $(rootNode));
    }

	var dom = api.dom, Phraser;
    api = $.extend(api, {
    	words: {},
    	chs: {},
    	tags: {},
    	analyse: function(phrase, options) {
		    options = $.extend({
		    	wordHandler: 	function(word) { return word; },
	    		tagHandler: 	function(tag) { return tag; },
	    		charHandler: 	function(ch) { return ch; }
		    }, options);
		    
		    Phraser = $.extend(Phraser, options);
		    
		    return Phraser.parse(phrase);
	    },
	    phraseIndexes: function(parentWords, phraseWords, allMatches) {
	    	var phraseLength = phraseWords.length - 1,
			phraseConcat = phraseWords.join('|'),
			parentConcat = parentWords.join('|'),
			boundaries = parentConcat.split(phraseConcat),
			indexes = [];

			var boundaryLength;
			for (var i = 0; i < boundaries.length; i++) {
				boundaryLength = boundaries[i].split('|').length - 1;

				indexes.push({
					start: Math.min(parentWords.length - phraseWords.length, boundaryLength),
					end: Math.min(parentWords.length, boundaryLength + phraseLength)
				});

				i++;
			}
			
			if (allMatches)
				return indexes;
			
			return indexes[0];
	    },
	    setPhraseSelection: function(rootNode, phrase, doc) {
	    	phrase = this.setPhrase(rootNode, phrase, doc);
	    	
	    	var range = api.createRange(doc);

			if (!phrase.start[0] || !phrase.end[0]) {
				return {
					selection: $('<span class="rangyPhrase" />'),
					start: 		$('<span class="rangyPhraseStart" />'),
					end: 		$('<span class="rangyPhraseEnd" />'),
					phrase: 	phrase,
					indexes: 	[],
					rootNode:	rootNode
				};
			}

			range.setStartBefore(phrase.start[0]);
			range.setEndAfter(phrase.end[0]);
			
			var sel = api.getSelection(window);
			var ranges = [range];
			sel.setRanges(ranges);
			phrase.range = range;
			return phrase;
	    },
	    setPhraseBetweenNodes: function(node1, node2, doc) {
		    var range = api.createRange(doc);
		    range.setStartBefore(node1[0]);
		    range.setEndAfter(node2[0]);
		    var sel = api.getSelection(window);
		    var ranges = [range];
		    sel.setRanges(ranges);
	    },
	    expandPhrase: function(phrase, ch, rootNode, doc) {
	    	rootNode = o(rootNode, doc);
			
			var parent = rootNode.html();
       		var parentParts = getParts(parent, rootNode.attr('id'));
       		var phraseParts = getParts(phrase);
	    	
	    	var indexes = this.phraseIndexes(parentParts.words, phraseParts.words);
	    	indexes = expandIndexesToCh(parentParts, indexes, ch);
	    	
	    	var newPhrase = '';
	    	if (indexes.start > -1 && indexes.end > -1) {
	    		for(var i = indexes.start; i <= indexes.end + 1; i++) {
	    			if (parentParts.chs[i] && i != indexes.start) newPhrase += parentParts.chs[i];
	    			if (parentParts.words[i] && i != indexes.end + 1) newPhrase += parentParts.words[i];
	    		}
	    		
	    		return newPhrase
			}
			return phrase;
	    },
	    setPhrase: function(rootNode, phrase, doc) {
			rootNode = o(rootNode, doc);
			
			var parent = rootNode.html();
       		var parentWords = this.sanitizeToWords(parent);
       		var phraseWords = this.sanitizeToWords(phrase);
       		
	        var indexes = this.phraseIndexes(parentWords, phraseWords);
	        
	        if (indexes.start > -1 && indexes.end > -1) {
				rootNode.html(getWrappedHtml(parent, indexes));
			}
	        
	        return {
	        	selection: 	rootNode.find('.rangyPhrase.new').removeClass('new'),
	        	start: 		rootNode.find('.rangyPhraseStart.new').removeClass('new'),
	        	end: 		rootNode.find('.rangyPhraseEnd.new').removeClass('new'),
	        	phrase: 	phrase,
	        	indexes: 	indexes,
	        	parentWords: parentWords,
	        	phraseWords: phraseWords,
	        	rootNode:	rootNode
	        };
	    },
	    isUnique: function(rootNode, phrase, doc) {
	    	rootNode = o(rootNode, doc);
	    	
			var parentWords = this.sanitizeToWords(rootNode.html());
	        var phraseWords = this.sanitizeToWords(phrase);
       		
       		var indexes = this.phraseIndexes(parentWords, phraseWords, true);
	        
	        return indexes.length <= 1;
	    },
		hasPhrase: function (parent, phrase) {
			parent = this.sanitizeToWords(parent);
			phrase = this.sanitizeToWords(phrase);
	
			parent = parent.join('|');
			phrase = phrase.join('|');
	
			return (parent.indexOf(phrase) > -1);
		},
		sanitizeToWords: function (html) {
			var sanitized = html.replace(/<(.|\n)*?>/g, ' ');
			
			sanitized = sanitized.replace(/\W/g, ' ');
			sanitized = sanitized.split(' ');

			var sanitizedFiltered = [], i;
			for(i in sanitized) {
				if (sanitized[i])
					sanitizedFiltered.push(sanitized[i]);
			}
			
			return sanitizedFiltered;
		},
	    superSanitize: function(html) {
		    return this.sanitizeToWords(html).join('');
	    }
    });
});