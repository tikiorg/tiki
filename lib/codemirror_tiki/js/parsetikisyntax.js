var TWParser = Editor.Parser = (function() {
	var normalMatches = /[^\n\[{<']/;
	var tokenizeTW = (function() {
		function normal(source, setState, otherClass) {
			var ch = source.next();
			switch (ch) {
				case "<": //comment
					if (source.lookAhead("!--", true)) {
						// Comment
						setState(inComment);
						return null;
					}
					break;
				case "_": //bold
					if (source.lookAhead("_", true)) {
						// Bold text
						setState(inBold);
					}
					return null;
					break;
				case "'": //italics
					if (source.lookAhead("'", true)) {
						// Italic text
						setState(inItalic);
						return null;
					}
					else {
						// Normal wikitext
						source.nextWhileMatches(normalMatches);
						return "tw-text";
					}
					break;
				case "[":
					if (source.lookAhead("[", true)) {
						// Interwiki link
							setState(inLink);
							return null;
						}
						else {
							// Weblink
							setState(inWeblink);
							return null;
						} 
					break;
				case "|": //table
					if (source.lookAhead("|", true)) {
						setState(inTable);
						//return null;
					}
					return null;
					break;
				case "-": 
					if (source.lookAhead("=")) {//titleBar
						setState(inTitleBar);
					}
					else if (source.lookAhead("-")) {//deleted
						setState(inDeleted);
					}
					return null;
					break;
				case "!": //header
					if (source.lookAhead('!!')) {
						setState(inHeader3);
					}
					else if (source.lookAhead('!')) {
						setState(inHeader2);
					}
					else {
						setState(inHeader1);
					}
					
					return null;
					break;
				case "*": //line item, or <li />
					setState(inListItem);
					return null;
					break;
				case ":":
					if (source.lookAhead(":", true)) {
						setState(inCenter);
						return null;
					}
				case "{": //plugin
					setState(inPluginContainer);
					return null;
					break;
				case "^": //box
					setState(inBox);
					return null;
					break;
				default:
					// Normal wikitext
					source.nextWhileMatches(normalMatches);
					return "tw-text";
					break;
			}
		}
		
		function inComment(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "-" && source.lookAhead("->", true)) {
					setState(normal);
					break;
				}
			}
			return "tw-comment";
		}
		
		function inBold(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "_" && source.lookAhead("_", true)) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-bold";
		}
		
		function inItalic(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "'" && source.lookAhead("'", true)) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-italic";
		}
		
		function inLink(source, setState) {
			var closed = false;
			
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "]" && source.lookAhead("]", true)) {
					closed = true;
					break;
				}
			}
			
			setState(normal);
			if (closed) return "tw-link"; else return "tw-syntaxerror";
		}
		
		function inWeblink(source, setState) {
			var closed = false;
			
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("]")) {
					closed = true;
					break;
				}
			}
			
			setState(normal);
			if (closed) return "tw-weblink"; else return "tw-syntaxerror";
		}
		
		function inTable(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("||", true)) {
					setState(normal);
					break;
				}
			}
			return "tw-table";
		}
		
		function inTitleBar(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("=-", true)) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-titlebar";
		}
		
		function inDeleted(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("--", true)) {
					if (source.endOfLine()) {
						setState(normal);
						break;
					}
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-deleted";
		}
		
		function inHeader1(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-header1";
		}
		
		function inHeader2(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-header2";
		}
		
		function inHeader3(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-header3";
		}
		
		function inListItem(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-listitem";
		}
		
		function inCenter(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("::", true)) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-center";
		}
		
		function inPluginContainer(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead('\n')) {
					setState(normal);
					break;
				} else if (ch == "}") {
					setState(normal);
					break;
				} else if (ch == "(" && !source.lookAhead(')')) {
					setState(inPluginAttributes);
					break;
				}
			}
		
			return "tw-plugin-container";
		}
		
		function inPluginAttributes(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead('\n')) {
					setState(normal);
					break;
				} else if (source.lookAhead("=")) {
					setState(inPluginAttributeEquals);
					break;
				}
			}
			
			return "tw-plugin-attributes";
		}
		
		function inPluginAttributeEquals(source, setState) {
			if (!source.endOfLine()) {
				var ch = source.next();
				setState(inPluginAttributeValue);
			}
			return "tw-plugin-attribute-equals";
		}
		
		function inPluginAttributeValue(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead(",")) {
					setState(inPluginAttributes);
					break;
				} else if (source.lookAhead(")")) {
					setState(inPluginContainer);
					break;
				} else if (source.lookAhead("\n")) {
					setState(normal);
					break;
				}
			}
			
			return "tw-plugin-attribute-value";
		}
		
		function inBox(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "^") {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-box";
		}
		
		return function(source, startState) {
			return tokenizer(source, startState || normal);
		};
	})();
	
	function parseTW(source, space) {
		function indentTo(n) {return function() {return n;}}
		
		var tokens = tokenizeTW(source);		
		var space = space || 0;
		
		var iter = {
			next: function() {
		        var token = tokens.next(), style = token.style, content = token.content;
				if (content == "\n") {
					token.indentation = indentTo(space);
				}
				return token;
			},
			copy: function() {
				var _tokenState = tokens.state;
				return function(source) {
					tokens = tokenizeTW(source, _tokenState);
					return iter;
				};
			}
		};
		return iter;
	}
	return {make: parseTW};
})();
