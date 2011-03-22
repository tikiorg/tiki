var normalTWMatches = /[^\n\[{<'"(-_]/;
var TWParser = Editor.Parser = (function() {
	var tokenizeTW = (function() {
		function normal(source, setState) {
			var ch = source.next();
			var normalGo = false;
			switch (ch) {
				case "<": //comment
					if (source.lookAhead("!--", true)) {
						// Comment
						setState(inComment);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "_": //bold
					if (source.lookAhead("_", true)) {
						// Bold text
						setState(inBold);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "'": //italics
					if (source.lookAhead("'", true)) {
						// Italic text
						setState(inItalic);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "(":// Wiki Link
					if (source.lookAhead("(", true)) {
						setState(wikiLink);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "[":// Weblink
					setState(inWeblink);
					return null;
					break;
				case "|": //table
					if (source.lookAhead("|", true)) {
						setState(inTable);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "-": 
					if (source.lookAhead("=")) {//titleBar
						setState(inTitleBar);
						return null;
					}
					else if (source.lookAhead("-")) {//deleted
						setState(inDeleted);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "!": //header at start of line
					if (source.lookAhead('!!!!')) {
						setState(inHeader5);
						return null;
					} else if (source.lookAhead('!!!')) {
						setState(inHeader4);
						return null;
					} else if (source.lookAhead('!!')) {
						setState(inHeader3);
						return null;
					} else if (source.lookAhead('!')) {
						setState(inHeader2);
						return null;
					} else {
						setState(inHeader1);
						return null;
					}
					break;
				case "*": //unordered list line item, or <li /> at start of line
					setState(inUnorderedListItem);
					return null;
					break;
				case "#": //ordered list line item, or <li /> at start of line
					setState(inOrderedListItem);
					return null;
					break;
				case ":":
					if (source.lookAhead(":", true)) {
						setState(inCenter);
						return null;
					} else {
						normalGo = true;
					}
					break;
				case "{": //plugin
					setState(inPluginContainer);
					return null;
					break;
				case "^": //box
					setState(inBox);
					return null;
					break;
				case "=": //underline
					if (source.lookAhead("==", true)) {
						setState(inUnderline);
						return null;
					} else {
						normalGo = true;
					}
					break;
				default:
					// Normal wikitext
					normalGo = true;
			
			}
			
			if (normalGo) {
				// Normal wikitext
				source.nextWhileMatches(normalTWMatches);
				return "tw-text";
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
		
		function wikiLink(source, setState) {
			var closed = false;
			
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == ")" && source.lookAhead(")", true)) {
					closed = true;
					break;
				}
			}
			
			setState(normal);
			return (closed ? "tw-link" : "tw-syntaxerror");
		}
		
		function inWeblink(source, setState) {
			var closed = false;
			
			while (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead("]", true)) {
					closed = true;
					break;
				}
			}
			
			setState(normal);
			return (closed ? "tw-weblink" : "tw-syntaxerror");
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
				if ((ch == "-" && source.lookAhead("-", true)) || source.lookAhead("--", true)) {
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

		function inHeader4(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-header4";
		}

		function inHeader5(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "" || source.endOfLine()) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-header5";
		}
		
		function inUnorderedListItem(source, setState) {
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
		
		function inOrderedListItem(source, setState) {
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
			var endOfLine = false;
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
				if (ch == "(" && !source.lookAhead(')')) {
					setState(inPluginAttributes);
					break;
				} else if (ch == "}" || ch == '\n' || ch == '') {
					setState(normal);
					break;
				}
			}
			
			return "tw-plugin-container";
		}
		
		function inPluginComma(source, setState) {
			if (!source.endOfLine()) {
				var ch = source.next();
				if (ch == ',') {
					setState(inPluginAttributes);
				} else {
					setState(inPluginContainer);
				}
			}
			
			return "tw-plugin-container";
		}
		
		function inPluginAttributes(source, setState) {
			var endOfLine = false;
			while (!endOfLine) {
				var ch = source.next();
				if (source.lookAhead("=")) {
					setState(inPluginAttributeEquals);
					break;
				}
				endOfLine = source.endOfLine();
			}
			
			return "tw-plugin-attributes";
		}
		
		function inPluginAttributeEquals(source, setState) {
			if (!source.endOfLine()) {
				var ch = source.next();
				if (ch == '=') {
					setState(inPluginAttributeParenthesesLeft);
					return "tw-plugin-attribute-equals";
				}
			}
		}

		function inPluginAttributeParenthesesLeft(source, setState) {
			var closed = false;
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == '"' || ch == "'") {
					closed = true;
					break;
				} else if (ch == '\n') {
					break;
				}
			}
			
			if (closed) {
				setState(inPluginAttributeValue);
				return "tw-plugin-attribute-parentheses";
			} else {
				setState(normal);
				return "tw-syntaxerror";
			}
		}
		
		function inPluginAttributeValue(source, setState) {			
			var endOfLine = false;
			
			while (!endOfLine) {
				var ch = source.next();
				if (source.lookAhead("'") || source.lookAhead('"')) {
					setState(inPluginAttributeParenthesesRight);
					break;
				}
				endOfLine = source.endOfLine();
			}
			
			if (endOfLine) {
				setState(normal);
			}
			return "tw-plugin-attribute-value";
		}
		
		function inPluginAttributeParenthesesRight(source, setState) {
			if (!source.endOfLine()) {
				var ch = source.next();
				if (source.lookAhead(',')) {
					setState(inPluginComma);
				} else {
					setState(inPluginAttributes);
				}
				return "tw-plugin-attribute-parentheses";
			} else {
				setState(normal);
				return "tw-syntaxerror";
			}
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
		
		function inUnderline(source, setState) {
			while (!source.endOfLine()) {
				var ch = source.next();
				if (ch == "=" && source.lookAhead("==", true)) {
					setState(normal);
					break;
				}
			}
			
			setState(normal);
			return "tw-underline";
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
