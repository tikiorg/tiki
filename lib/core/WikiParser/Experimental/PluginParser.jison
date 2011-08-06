%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+

%s bold italic

%%
\s								{return ' '}

"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		var pluginName = yytext.match(/^\{([a-z]+)/)[1];
		var pluginParams =  yytext.match(/[ ].*?[}]|[/}]/);
		yytext = {
			name: pluginName,
			params: pluginParams,
			body: ''
		};
		return 'INLINE_PLUGIN';
	%}

"{"{PLUGIN_ID}"(".*?")}"
	%{
		var pluginName = yytext.match(/^\{([A-Z]+)/)[1];
		var pluginParams =  yytext.match(/[(].*?[)]/);
		
		if (!yy.pluginStack) yy.pluginStack = [];
		yy.pluginStack.push({
			name: pluginName,
			params: pluginParams
		});
		
		return 'PLUGIN_START';
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (yy.pluginStack) {
			if (
				yy.pluginStack.length &&
				yytext.match(yy.pluginStack[yy.pluginStack.length - 1].name)
			) {
				var readyPlugin = yy.pluginStack.pop();
				yytext = readyPlugin;
				return 'PLUGIN_END';
			}
		}
		return 'CONTENT3';
	%}

(.|\n)+?/("{"{PLUGIN_ID}|"{"{INLINE_PLUGIN_ID}.*?"}"|[_'][_'])
	%{
		return 'CONTENT1';
	%}

<italic>("__") this.popState();      return 'ITALIC_END'
("__")         this.begin('italic'); return 'ITALIC_START'
<bold>[']['] this.popState();      return 'BOLD_END'
['][']      this.begin('bold');   return 'BOLD_START'

(.|\n)                         return 'CONTENT2'
<<EOF>>                         return 'EOF'

/lex

%%

wiki
 : wiki_contents EOF
	{return $1;}
 ;

wiki_contents
 :
 | contents
	{$$ = $1;}
 | wiki_contents plugin
	{$$ = ($1 ? $1 : '') + ($2 ? $2 : '');}
 | wiki_contents plugin contents
	{$$ = ($1 ? $1 : '') + ($2 ? $2 : '') + ($3 ? $3 : '');}
 ;

plugin
 : INLINE_PLUGIN
	{$$ = plugin($1);}
 | PLUGIN_START wiki_contents PLUGIN_END
	{
		$3.body = $2;
		$$ = plugin($3);
	}
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{$$ = $1 + $2;}
 ;

content
 : ITALIC_START wiki_contents ITALIC_END
	{$$ = "<i>" + $2 + "</i>";}
 | BOLD_START wiki_contents BOLD_END
	{$$ = "<b>" + $2 + "</b>";}
 | CONTENT1
	{$$ = '<u style="background-color: red;">' + $1 + '</u>';}
 | CONTENT2
	{$$ = '<u style="background-color: green;">' + $1 + '</u>';}
 | CONTENT3
	{$$ = '<u style="background-color: blue;">' + $1 + '</u>';}
 ;