rangy.createModule("Phraser", function(api, module) {
    api.requireModules( ["WrappedSelection", "WrappedRange"] );
    
    function rePhrase(phrase) {
		var phrases = [phrase.substr(0, Math.min(phrase.length, 200)), phrase.substr(-200, Math.min(phrase.length, 200))];
		console.log(phrases);
		
		var re = '(.|\\n)+?';
    	phrase = (phrase.length >= 200 ? phrases.join(' ') : phrase);
    	
    	phrase = phrase.replace(/[.,\n]/g, ' ');
    	phrase = phrase.replace(/[ ]/g, re);
    	phrase = phrase.replace(re + re, re);
    	phrase = phrase.replace(re + re, re);
    	phrase = phrase.replace(re + re, re);
    	
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
			
        	//ready the sheet's parser
	        var Phraser = {};
			Phraser.lexer = function() {};
			Phraser.lexer.prototype = phraser.lexer;
			Phraser.phraser = function() {
				this.lexer = new Phraser.lexer();
				this.yy = {};
			};
			Phraser.phraser.prototype = phraser;
			
			//first lets establish a base to find the phrase in
       		var phraseBase = new Phraser.phraser;
       		phraseBase.words = [];
       		phraseBase.lexer.wordHandler = function(word) {
       			phraseBase.words.push(word);
       			return word;
       		};
       		phraseBase.parse(html);
	       	console.log(phraseBase.words);
	        
	        //now lets analyze the phrase itself so we can match the phrase to the base
	        var phraseFind = new Phraser.phraser;
	        phraseFind.words = [];
       		phraseFind.lexer.wordHandler = function(word) {
       			phraseFind.words.push(word);
       			return word;
       		};
       		phraseFind.parse(phrase);
	       	console.log(phraseFind.words);
	        
	        
	        //now that we have both a phrase and a base, LETS FIND IT!
	        var found = {
	        	at: -1
	        };
	        
	        for ( var i = 0; i < phraseBase.words.length; i++ ) {
	        	if (phraseBase.words[i] == phraseFind.words[0] && phraseBase.words[i + phraseFind.words.length - 1] == phraseFind.words[phraseFind.words.length - 1]) {
	        		found.at = i;
	        		break;
	        	}
	        }
	        
	        console.log(found.at);
	       	
	       	
	       	var phraseApply = new Phraser.phraser;
	        phraseApply.i = 0;
	        
       		phraseApply.lexer.wordHandler = function(word) {
       			if (phraseApply.i == found.at) {
       				word = '<span class="rangyPhraseStart new"/><span class="rangyPhrase new">' + word;
       			}
       			
       			if (phraseApply.i == phraseFind.words.length - 1) {
       				console.log([phraseFind.words[phraseFind.words.length - 1], word])
       				word = word + '</span><span class="rangyPhraseEnd new"/>';
       			}
       			
       			phraseApply.i++;
       			return word;
       		};
       		
       		html = phraseApply.parse(html);
	        
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
