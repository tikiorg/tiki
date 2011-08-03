
%lex

PLUGIN_ID   [A-Z]+

%%

"{"{PLUGIN_ID}"(".*?")}"
	%{
		if (!yy.pluginStack) yy.pluginStack = [];
		var pluginName = yytext.match(/^\{([A-Z]+)/)[1];
		yy.pluginStack.push({
			name: pluginName,
			permission: isPermissible(pluginName),
			body: []
		});
		return 'PLUGIN_START';
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (
			yy.pluginStack && yy.pluginStack.length &&
			yytext.match(yy.pluginStack[yy.pluginStack.length - 1].name)
		) {
			var returnPluginVal = plugin(yy.pluginStack.pop());
			
			if (yy.pluginStack.length) {
				yy.pluginStack[yy.pluginStack.length - 1].body.push(returnPluginVal);
			} else {
				yy.returnValue = returnPluginVal;
			}
			return 'PLUGIN_END';
		} else {
			return 'CONTENT';
		}
	%}

(.|\n)+?/("{"{PLUGIN_ID})
	%{
		if (yy.pluginStack[yy.pluginStack.length - 1]) {
			yy.pluginStack[yy.pluginStack.length - 1].body.push(yytext);
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