%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SMILE							[a-z]+

%s 
	bold box
	center
	colortext
	italic
	header6
	header5
	header4
	header3
	header2
	header1
	link
	strikethrough
	table
	titlebar
	underscore
	wikilink

%%

"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		var pluginName = yy.cmd.match(/^\{([a-z]+)/, yytext)[1];
		var pluginParams = yy.cmd.match(/[ ].*?[}]|[/}]/, yytext);
		yytext = {
			name: pluginName,
			params: pluginParams,
			body: ''
		};
		return 'INLINE_PLUGIN';
	%}

"{"{PLUGIN_ID}"(".*?")}"
	%{
		var pluginName = yy.cmd.match(/^\{([A-Z]+)/, yytext)[1];
		var pluginParams =  yy.cmd.match(/[(].*?[)]/, yytext);
		
		if (!yy.pluginStack) yy.pluginStack = [];
		yy.pluginStack.push({
			name: pluginName,
			params: pluginParams,
			body: ''
		});
		
		if (yy.pluginStack.length == 1) {
			return 'PLUGIN_START';
		} else {
			return 'CONTENT';
		}
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (yy.pluginStack) {
			if (
				yy.pluginStack.length &&
				yy.cmd.match(yy.pluginStack[yy.pluginStack.length - 1].name, yytext)
			) {
				var readyPlugin = yy.pluginStack.pop();
				if (yy.pluginStack.length == 0) {
					yytext = readyPlugin;
					return 'PLUGIN_END';
				} else {
					return 'CONTENT';
				}
			}
		}
		return 'CONTENT';
	%}

("~np~")
	%{
		if (!yy.npStack) yy.npStack = [];
		yy.npStack.push(true);
		yy.npOn = true;
		
		return 'NP_START';
	%}

("~/np~")
	%{
		if (!yy.npStack) yy.npStack = [];
		yy.npStack.pop();
		if (!yy.npStack.length) yy.npOn = false;
		
		return 'NP_END';
	%}

"---" 
	%{
		yytext = this.yy.cmd.make_hr();
		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		var smile = this.yy.cmd.substring(yytext, 2, -2);
		yytext = this.yy.cmd.make_smile(smile);
		return 'SMILE';
	%}

"[[".*?
	%{
		yytext = this.yy.cmd.substring(yytext, 2, -1);
		return 'CONTENT';
	%}

<bold>[_][_]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'BOLD_END'); %}
[_][_]						%{ this.begin('bold');			return (this.yy.npOn ? 'CONTENT' : 'BOLD_START'); %}
<box>[\^]					%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'BOX_END'); %}
[\^]						%{ this.begin('box');			return (this.yy.npOn ? 'CONTENT' : 'BOX_START'); %}
<center>[:][:]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'CENTER_END'); %}
[:][:]						%{ this.begin('center');		return (this.yy.npOn ? 'CONTENT' : 'CENTER_START'); %}
<colortext>[\~][\~]			%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'COLORTEXT_END'); %}
[\~][\~][#]					%{ this.begin('colortext');		return (this.yy.npOn ? 'CONTENT' : 'COLORTEXT_START'); %}
<header6>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER6_END'); %}
[\n]("!!!!!!")				%{ this.begin('header6');		return (this.yy.npOn ? 'CONTENT' : 'HEADER6_START'); %}
<header5>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER5_END'); %}
[\n]("!!!!!")				%{ this.begin('header5');		return (this.yy.npOn ? 'CONTENT' : 'HEADER5_START'); %}
<header4>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER4_END'); %}
[\n]("!!!!")				%{ this.begin('header4');		return (this.yy.npOn ? 'CONTENT' : 'HEADER4_START'); %}
<header3>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER3_END'); %}
[\n]("!!!")					%{ this.begin('header3');		return (this.yy.npOn ? 'CONTENT' : 'HEADER3_START'); %}
<header2>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER2_END'); %}
[\n]("!!")					%{ this.begin('header2');		return (this.yy.npOn ? 'CONTENT' : 'HEADER2_START'); %}
<header1>[\n]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'HEADER1_END'); %}
[\n]("!")					%{ this.begin('header1');		return (this.yy.npOn ? 'CONTENT' : 'HEADER1_START'); %}
<italic>['][']				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'ITALIC_END'); %}
['][']						%{ this.begin('italic');		return (this.yy.npOn ? 'CONTENT' : 'ITALIC_START'); %}
<link>("]")					%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'LINK_END'); %}
("[")						%{ this.begin('link');			return (this.yy.npOn ? 'CONTENT' : 'LINK_START'); %}
<strikethrough>[-][-]		%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'STRIKETHROUGH_END'); %}
[-][-]						%{ this.begin('strikethrough');	return (this.yy.npOn ? 'CONTENT' : 'STRIKETHROUGH_START'); %}
<table>[|][|]				%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'TABLE_END'); %}
[|][|]						%{ this.begin('table');			return (this.yy.npOn ? 'CONTENT' : 'TABLE_START'); %}
<titlebar>[=][-]			%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'TITLEBAR_END'); %}
[-][=]						%{ this.begin('titlebar');		return (this.yy.npOn ? 'CONTENT' : 'TITLEBAR_START'); %}
<underscore>[=][=][=]		%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'UNDERSCORE_END'); %}
[=][=][=]					%{ this.begin('underscore');	return (this.yy.npOn ? 'CONTENT' : 'UNDERSCORE_START'); %}
<wikilink>[)][)]			%{ this.popState();				return (this.yy.npOn ? 'CONTENT' : 'WIKILINK_END'); %}
[(][(]						%{ this.begin('wikilink');		return (this.yy.npOn ? 'CONTENT' : 'WIKILINK_START'); %}

"<"(.|\n)*?">"								return 'HTML'
(.)											return 'CONTENT'
(\n)
	%{
		yytext = this.yy.cmd.replace(/\n/g, '<br />', yytext);
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
	{$$ = yy.cmd.plugin($1);}
 | PLUGIN_START wiki_contents PLUGIN_END
	{
		$3.body = $2;
		$$ = yy.cmd.plugin($3);
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
	{$$ = yy.cmd.html($1);}
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
 | BOLD_START wiki_contents BOLD_END
	{$$ = yy.cmd.make_bold($2);}
 | BOX_START wiki_contents BOX_END
	{$$ = yy.cmd.make_box($2);}
 | CENTER_START wiki_contents CENTER_END
	{$$ = yy.cmd.make_center($2);}
 | COLORTEXT_START wiki_contents COLORTEXT_END
	{
		var text = $2.split(':');
		$$ = yy.cmd.make_colortext(text[0], text[1]);
	}
 | ITALIC_START wiki_contents ITALIC_END
	{$$ = yy.cmd.make_italics($2);}
 | HEADER6_START wiki_contents HEADER6_END
	{$$ = yy.cmd.make_header6($2);}
 | HEADER5_START wiki_contents HEADER5_END
	{$$ = yy.cmd.make_header5($2);}
 | HEADER4_START wiki_contents HEADER4_END
	{$$ = yy.cmd.make_header4($2);}
 | HEADER3_START wiki_contents HEADER3_END
	{$$ = yy.cmd.make_header3($2);}
 | HEADER2_START wiki_contents HEADER2_END
	{$$ = yy.cmd.make_header2($2);}
 | HEADER1_START wiki_contents HEADER1_END
	{$$ = yy.cmd.make_header1($2);}
 | LINK_START wiki_contents LINK_END
	{
		var link = $2.split('|');
		var href = $2;
		var text = $2;
		
		if (yy.cmd.match(/\|/, $2)) {
			href = link[0];
			text = link[1];
		}
		
		$$ = yy.cmd.make_link(href, text);
	}
 | NP_START wiki_contents NP_END
	{$$ = $2;}
 | STRIKETHROUGH_START wiki_contents STRIKETHROUGH_END
	{$$ = yy.cmd.make_strikethrough($2);}
 | TABLE_START wiki_contents TABLE_END
	{
		var tableContents = '';
		var rows = yy.cmd.split('<br />', $2);
		for(var i = 0; i < rows.length; i++) {
			var row = '';
			
			var cells = yy.cmd.split('|',  rows[i]);
			for(var j = 0; j < cells.length; j++) {
				row += yy.cmd.make_table_td(cells[j]);
			}
			tableContents += yy.cmd.make_table_tr(row);
		}
		$$ = yy.cmd.make_table(tableContents);
	}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{$$ = yy.cmd.make_titlebar($2);}
 | UNDERSCORE_START wiki_contents UNDERSCORE_END
	{$$ = yy.cmd.make_underscore($2);}
 | WIKILINK_START wiki_contents WIKILINK_END
	{
		var wikilink = $2.split('|');
		var href = $2;
		var text = $2;
		
		if (yy.cmd.match(/\|/, $2)) {
			href = wikilink[0];
			text = wikilink[1];
		}
		
		$$ = yy.cmd.make_wikilink(href, text);
	}
 ;