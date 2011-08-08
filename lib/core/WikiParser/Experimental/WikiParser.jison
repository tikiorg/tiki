%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SMILE							[a-z]+

%s bold box center colortext italic link strikethrough table titlebar underscore wikilink

%%

"~np~"(.|\n)*?"~/np~"
	%{
		yytext = yytext.substring(4, yytext.length - 5);
		return 'NP_CONTENT';
	%}

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

"---" 
	%{
		yytext = "<hr />";
		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		var smile = yytext.substring(2, yytext.length - 2);
		yytext = "<img src='img/smiles/icon_" + smile + ".gif' alt='" + smile + "' />";
		return 'SMILE';
	%}

"[[".*?
	%{
		var smile = yytext.substring(2, yytext.length - 2);
		yytext = "<img src='img/smiles/icon_" + smile + ".gif' alt='" + smile + "' />";
		return 'CONTENT';
	%}

<bold>[_][_]				this.popState();				return 'BOLD_END'
[_][_]						this.begin('bold');				return 'BOLD_START'
<box>[\^]					this.popState();				return 'BOX_END'
[\^]						this.begin('box');				return 'BOX_START'
<center>[:][:]				this.popState();				return 'CENTER_END'
[:][:]						this.begin('center');			return 'CENTER_START'
<colortext>[\~][\~]			this.popState();				return 'COLORTEXT_END'
[\~][\~][#]					this.begin('colortext');		return 'COLORTEXT_START'
<italic>['][']				this.popState();				return 'ITALIC_END'
['][']						this.begin('italic');			return 'ITALIC_START'
<link>("]")					this.popState();				return 'LINK_END'
("[")						this.begin('link');				return 'LINK_START'
<strikethrough>[-][-]		this.popState();				return 'STRIKETHROUGH_END'
[-][-]						this.begin('strikethrough');	return 'STRIKETHROUGH_START'
<table>[|][|]				this.popState();				return 'TABLE_END'
[|][|]						this.begin('table');			return 'TABLE_START'
<titlebar>[=][-]			this.popState();				return 'TITLEBAR_END'
[-][=]						this.begin('titlebar');			return 'TITLEBAR_START'
<underscore>[=][=][=]		this.popState();				return 'UNDERSCORE_END'
[=][=][=]					this.begin('underscore');		return 'UNDERSCORE_START'
<wikilink>[)][)]			this.popState();				return 'WIKILINK_END'
[(][(]						this.begin('wikilink');			return 'WIKILINK_START'

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
 | np_content
	{$$ = $1;}
 | contents content
	{$$ = $1 + $2;}
 | contents np_content
	{$$ = $1 + $2;}
 ;

content
 : CONTENT
	{$$ = $1;}
 | HTML
	{$$ = isHtmlPermissible($1);}
 | LINK
	{$$ = $1;}
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
 | BOLD_START wiki_contents BOLD_END
	{$$ = "<b>" + $2 + "</b>";}
 | BOX_START wiki_contents BOX_END
	{$$ = "<div style='border: solid 1px black;'>" + $2 + "</div>";}
 | CENTER_START wiki_contents CENTER_END
	{$$ = "<center>" + $2 + "</center>";}
 | COLORTEXT_START wiki_contents COLORTEXT_END
	{
		var text = $2.split(':');
		$$ = "<span style='color: #" + text[0] + ";'>" +text[1] + "</span>";
	}
 | ITALIC_START wiki_contents ITALIC_END
	{$$ = "<i>" + $2 + "</i>";}
 | LINK_START wiki_contents LINK_END
	{
		var link = $2.split('|');
		var href = $2;
		var text = $2;
		
		if ($2.match(/\|/)) {
			href = link[0];
			text = link[1];
		}
		
		$$ = "<a href='" + href + "'>" + text  + "</a>";
	}
 | STRIKETHROUGH_START wiki_contents STRIKETHROUGH_END
	{$$ = "<span style='text-decoration: line-through;'>" + $2 + "</span>";}
 | TABLE_START wiki_contents TABLE_END
	{
		var tableContents = '';
		var rows = $2.split('<br />');
		for(var i = 0; i < rows.length; i++) {
			var cells = rows[i].split('|');
			tableContents += "<tr>";
			for(var j = 0; j < cells.length; j++) {
				tableContents += "<td>" + cells[j] + "</td>";
			}
			tableContents += "</tr>";
		}
		$$ = "<table style='width: 100%;'>" + tableContents + "</table>";
	}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{$$ = "<div class='titlebar'>" + $2 + "</div>";}
 | UNDERSCORE_START wiki_contents UNDERSCORE_END
	{$$ = "<u>" + $2 + "</u>";}
 | WIKILINK_START wiki_contents WIKILINK_END
	{
		var wikilink = $2.split('|');
		var href = $2;
		var text = $2;
		
		if ($2.match(/\|/)) {
			href = wikilink[0];
			text = wikilink[1];
		}
		
		$$ = "<a href='" + href + "'>" + text  + "</a>";
	}
 ;

np_content
 : NP_CONTENT
	{$$ = $1;}
 ;