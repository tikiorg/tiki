CodeMirror.defineMode('tikiwiki', function(config, options) {
	function setState(state, fn, ctx) {
        state.fn = fn;
        setCtx(state, ctx);
    }
	
	function setCtx(state, ctx) {
        state.ctx = ctx || {};
    }
	
	function setNormal(state, ch) {
        if (ch && (typeof ch !== 'string')) {
            var str = ch.current();
            ch = str[str.length-1];
        }

        setState(state, state, normal, {back: ch});
    }
	
	function hasMode(mode) {
        if (mode) {
            var modes = CodeMirror.listModes();

            for (var i in modes) {
                if (modes[i] == mode) {
                    return true;
                }
            }
        }

        return false;
    }

    function getMode(mode) {
        if (hasMode(mode)) {
            return CodeMirror.getMode(config, mode);
        } else {
            return null;
        }
    }
	
	var normalTWMatches = /[^\n\[{<'"(-_]/;
	function normal(source, state) {
		var ch = source.next();
		var normalGo = false;
		switch (ch) {
			case "<": //comment
				if (source.peek("!--", true)) {
					// Comment
					setState(state, inComment);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "_": //bold
				if (source.peek("_", true)) {
					// Bold text
					setState(state, inBold);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "'": //italics
				if (source.peek("'", true)) {
					// Italic text
					setState(state, inItalic);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "(":// Wiki Link
				if (source.peek("(", true)) {
					setState(state, wikiLink);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "[":// Weblink
				setState(state, inWeblink);
				return null;
				break;
			case "|": //table
				if (source.peek("|", true)) {
					setState(state, inTable);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "-": 
				if (source.peek("=")) {//titleBar
					setState(state, inTitleBar);
					return null;
				}
				else if (source.peek("-")) {//deleted
					setState(state, inDeleted);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "!": //header at start of line
				if (source.peek('!!!!')) {
					setState(state, inHeader5);
					return null;
				} else if (source.peek('!!!')) {
					setState(state, inHeader4);
					return null;
				} else if (source.peek('!!')) {
					setState(state, inHeader3);
					return null;
				} else if (source.peek('!')) {
					setState(state, inHeader2);
					return null;
				} else {
					setState(state, inHeader1);
					return null;
				}
				break;
			case "*": //unordered list line item, or <li /> at start of line
				setState(state, inUnorderedListItem);
				return null;
				break;
			case "#": //ordered list line item, or <li /> at start of line
				setState(state, inOrderedListItem);
				return null;
				break;
			case ":":
				if (source.peek(":", true)) {
					setState(state, inCenter);
					return null;
				} else {
					normalGo = true;
				}
				break;
			case "{": //plugin
				setState(state, inPluginContainer);
				return null;
				break;
			case "^": //box
				setState(state, inBox);
				return null;
				break;
			case "=": //underline
				if (source.peek("==", true)) {
					setState(state, inUnderline);
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
			source.eatWhile(normalTWMatches);
			return "tw-text";
		}
	}
	
	function inComment(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "-" && source.peek("->", true)) {
				setState(state, normal);
				break;
			}
		}
		return "tw-comment";
	}
	
	function inBold(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "_" && source.peek("_", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-bold";
	}
	
	function inItalic(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "'" && source.peek("'", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-italic";
	}
	
	function wikiLink(source, state) {
		var closed = false;
		
		while (!source.eol()) {
			var ch = source.next();
			if (ch == ")" && source.peek(")", true)) {
				closed = true;
				break;
			}
		}
		
		setState(state, normal);
		return (closed ? "tw-link" : "tw-syntaxerror");
	}
	
	function inWeblink(source, state) {
		var closed = false;
		
		while (!source.eol()) {
			var ch = source.next();
			if (source.peek("]", true)) {
				closed = true;
				break;
			}
		}
		
		setState(state, normal);
		return (closed ? "tw-weblink" : "tw-syntaxerror");
	}
	
	function inTable(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (source.peek("||", true)) {
				setState(state, normal);
				break;
			}
		}
		return "tw-table";
	}
	
	function inTitleBar(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (source.peek("=-", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-titlebar";
	}
	
	function inDeleted(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if ((ch == "-" && source.peek("-", true)) || source.peek("--", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-deleted";
	}
	
	function inHeader1(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-header1";
	}
	
	function inHeader2(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-header2";
	}
	
	function inHeader3(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-header3";
	}

	function inHeader4(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-header4";
	}

	function inHeader5(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-header5";
	}
	
	function inUnorderedListItem(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-listitem";
	}
	
	function inOrderedListItem(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "" || source.eol()) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-listitem";
	}
	
	function inCenter(source, state) {
		var eol = false;
		while (!source.eol()) {
			var ch = source.next();
			if (source.peek("::", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		
		return "tw-center";
	}
	
	function inPluginContainer(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "(" && !source.peek(')')) {
				setState(state, inPluginAttributes);
				break;
			} else if (ch == "}" || ch == '\n' || ch == '') {
				setState(state, normal);
				break;
			}
		}
		
		return "tw-plugin-container";
	}
	
	function inPluginComma(source, state) {
		if (!source.eol()) {
			var ch = source.next();
			if (ch == ',') {
				setState(state, inPluginAttributes);
			} else {
				setState(state, inPluginContainer);
			}
		}
		
		return "tw-plugin-container";
	}
	
	function inPluginAttributes(source, state) {
		var eol = false;
		while (!eol) {
			var ch = source.next();
			if (source.peek("=") || source.peek("->")) {
				setState(state, inPluginAttributeEquals);
				break;
			}
			eol = source.eol();
		}
		
		return "tw-plugin-attributes";
	}
	
	function inPluginAttributeEquals(source, state) {
		if (!source.eol()) {
			var ch = source.next();
			if (ch == '=') {
				setState(state, inPluginAttributeParenthesesLeft);
				return "tw-plugin-attribute-equals";
			}
		}
	}

	function inPluginAttributeParenthesesLeft(source, state) {
		var closed = false;
		while (!source.eol()) {
			var ch = source.next();
			if (ch == '"' || ch == "'") {
				closed = true;
				break;
			} else if (ch == '\n') {
				break;
			}
		}
		
		if (closed) {
			setState(state, inPluginAttributeValue);
			return "tw-plugin-attribute-parentheses";
		} else {
			setState(state, normal);
			return "tw-syntaxerror";
		}
	}
	
	function inPluginAttributeValue(source, state) {			
		var eol = false;
		
		while (!eol) {
			var ch = source.next();
			if (source.peek("'") || source.peek('"')) {
				setState(state, inPluginAttributeParenthesesRight);
				break;
			}
			eol = source.eol();
		}
		
		if (eol) {
			setState(state, normal);
		}
		return "tw-plugin-attribute-value";
	}
	
	function inPluginAttributeParenthesesRight(source, state) {
		if (!source.eol()) {
			var ch = source.next();
			if (source.peek(',')) {
				setState(state, inPluginComma);
			} else if (source.peek(')')) {
				setState(state, inPluginContainer);
			} else {
				setState(state, inPluginAttributes);
			}
			return "tw-plugin-attribute-parentheses";
		} else {
			setState(state, normal);
			return "tw-syntaxerror";
		}
	}
	
	function inBox(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "^") {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-box";
	}
	
	function inUnderline(source, state) {
		while (!source.eol()) {
			var ch = source.next();
			if (ch == "=" && source.peek("==", true)) {
				setState(state, normal);
				break;
			}
		}
		
		setState(state, normal);
		return "tw-underline";
	}
	
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
	
	return {
        startState: function() {
            return {fn: normal, ctx: {}};
        },

        copyState: function(state) {
            return {fn: state.fn, ctx: state.ctx};
        },

        token: function(stream, state) {
            var token = state.fn(stream, state);
            return token;
        }
    };
	//return {make: parseTW};
});

//I figure, why not
CodeMirror.defineMIME("text/tikiwiki", "tikiwiki");
