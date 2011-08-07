%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+

%s bold box italic titlebar

%%

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
		return 'CONTENT';
	%}


<bold>['][']		this.popState();		return 'BOLD_END'
['][']				this.begin('bold');		return 'BOLD_START'
<box>[\^]			this.popState();		return 'BOX_END'
[\^]				this.begin('box');		return 'BOX_START'
<italic>[_][_]		this.popState();		return 'ITALIC_END'
[_][_]				this.begin('italic');	return 'ITALIC_START'
<titlebar>[=][-]	this.popState();		return 'TITLEBAR_END'
[-][=]				this.begin('titlebar');	return 'TITLEBAR_START'


"<"(.|\n)*?">"								return 'HTML'
(.)											return 'CONTENT'
(\n)
	%{
		yytext = yytext.replace(/\n/g, '<br />');
		return 'CONTENT';
	%}

<<EOF>>                         			return 'EOF'

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
 : CONTENT
	{$$ = $1;}
 | HTML
	{$$ = $1;} 
 | BOLD_START wiki_contents BOLD_END
	{$$ = "<b>" + $2 + "</b>";}
 | BOX_START wiki_contents BOX_END
	{$$ = "<div style='border: solid 1px black;'>" + $2 + "</div>";}
 | ITALIC_START wiki_contents ITALIC_END
	{$$ = "<i>" + $2 + "</i>";}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{$$ = "<h1>" + $2 + "</h1>";}
 ;