
%lex

PLUGIN_ID   [A-Z]+

%%

"{"{PLUGIN_ID}"(".*?")}"
	%{
		if (!yy.pluginStack) yy.pluginStack = [];
		if (!yy.pluginBodyStack) yy.pluginBodyStack = [];
		yy.pluginStack.push(yytext.match(/^\{([A-Z]+)/)[1])
		return 'PLUGIN_START'
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (
			yy.pluginStack && yy.pluginStack.length &&
			yytext.match(yy.pluginStack[yy.pluginStack.length-1])
		) {
			var returnPluginVal = plugin(yy.pluginStack.pop(), yy.pluginBodyStack.pop(), yytext);
			
			if (yy.pluginStack.length) {
				yy.pluginBodyStack[yy.pluginBodyStack.length - 1].push(returnPluginVal);
			} else {
				sendcontent(returnPluginVal);
			}
			return 'PLUGIN_END';
		} else {
			return 'CONTENT';
		}
	%}

(.|\n)+?/("{"{PLUGIN_ID})
	%{
		if (!yy.pluginBodyStack[yy.pluginStack.length - 1]) {
			yy.pluginBodyStack[yy.pluginStack.length - 1] = [yytext];
		} else {
			yy.pluginBodyStack[yy.pluginStack.length - 1].push(yytext);
		}
		
		return 'CONTENT';
	%}

(.|\n)+                         return 'CONTENT'
<<EOF>>                         return 'EOF'

/lex

%%

wiki
 : wiki_contents EOF
 ;

wiki_contents
 :
 | content
 | wiki_contents PLUGIN_START wiki_contents PLUGIN_END
 | wiki_contents PLUGIN_START wiki_contents PLUGIN_END content
 ;

content
 : CONTENT
 | content CONTENT
 ;