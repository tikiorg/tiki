%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SMILE							[a-z]+

%s 
	bold box center	colortext italic header6 header5 header4 header3 header2 header1 link strikethrough table titlebar underscore wikilink
%%

"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		yytext = Parser.inlinePlugin(yytext);
		return 'INLINE_PLUGIN';
	%}

"{"{PLUGIN_ID}"(".*?")}"
	%{
		yy.pluginStack = Parser.stackPlugin(yytext, yy.pluginStack);
		
		if (Parser.size(yy.pluginStack) == 1) {
			return 'PLUGIN_START';
		} else {
			return 'CONTENT';
		}
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (yy.pluginStack) {
			if (
				Parser.size(yy.pluginStack) > 0 &&
				Parser.substring(yytext, 1, -1) == yy.pluginStack[Parser.size(yy.pluginStack) - 1].name
			) {
				if (Parser.size(yy.pluginStack) == 1) {
					yytext = yy.pluginStack[Parser.size(yy.pluginStack) - 1];
					yy.pluginStack = Parser.pop(yy.pluginStack);
					return 'PLUGIN_END';
				} else {
					yy.pluginStack = Parser.pop(yy.pluginStack);
					return 'CONTENT';
				}
			}
		}
		return 'CONTENT';
	%}

("~np~")
	%{
		yy.npStack = Parser.push(yy.npStack, true);
		this.yy.npOn = true;
		
		return 'NP_START';
	%}

("~/np~")
	%{
		this.yy.npStack = Parser.pop(yy.npStack);
		if (Parser.size(yy.npStack) < 1) yy.npOn = false;
		return 'NP_END';
	%}

"---" 
	%{
		yytext = Parser.hr();
		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		yytext = Parser.substring(yytext, 2, -2);
		yytext = Parser.smile(yytext);
		return 'SMILE';
	%}

"[[".*?
	%{
		yytext = Parser.substring(yytext, 2, -1);
		return 'CONTENT';
	%}

<bold>[_][_]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'BOLD_END'); %}
[_][_]						%{ this.begin('bold');			return Parser.npState(this.yy.npOn, 'CONTENT', 'BOLD_START'); %}
<box>[\^]					%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'BOX_END'); %}
[\^]						%{ this.begin('box');			return Parser.npState(this.yy.npOn, 'CONTENT', 'BOX_START'); %}
<center>[:][:]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'CENTER_END'); %}
[:][:]						%{ this.begin('center');		return Parser.npState(this.yy.npOn, 'CONTENT', 'CENTER_START'); %}
<colortext>[\~][\~]			%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'COLORTEXT_END'); %}
[\~][\~][#]					%{ this.begin('colortext');		return Parser.npState(this.yy.npOn, 'CONTENT', 'COLORTEXT_START'); %}
<header6>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER6_END'); %}
[\n]("!!!!!!")				%{ this.begin('header6');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER6_START'); %}
<header5>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER5_END'); %}
[\n]("!!!!!")				%{ this.begin('header5');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER5_START'); %}
<header4>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER4_END'); %}
[\n]("!!!!")				%{ this.begin('header4');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER4_START'); %}
<header3>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER3_END'); %}
[\n]("!!!")					%{ this.begin('header3');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER3_START'); %}
<header2>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER2_END'); %}
[\n]("!!")					%{ this.begin('header2');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER2_START'); %}
<header1>[\n]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER1_END'); %}
[\n]("!")					%{ this.begin('header1');		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER1_START'); %}
<italic>['][']				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'ITALIC_END'); %}
['][']						%{ this.begin('italic');		return Parser.npState(this.yy.npOn, 'CONTENT', 'ITALIC_START'); %}
<link>("]")					%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'LINK_END'); %}
("[")						%{ this.begin('link');			return Parser.npState(this.yy.npOn, 'CONTENT', 'LINK_START'); %}
<strikethrough>[-][-]		%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'STRIKETHROUGH_END'); %}
[-][-]						%{ this.begin('strikethrough');	return Parser.npState(this.yy.npOn, 'CONTENT', 'STRIKETHROUGH_START'); %}
<table>[|][|]				%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'TABLE_END'); %}
[|][|]						%{ this.begin('table');			return Parser.npState(this.yy.npOn, 'CONTENT', 'TABLE_START'); %}
<titlebar>[=][-]			%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'TITLEBAR_END'); %}
[-][=]						%{ this.begin('titlebar');		return Parser.npState(this.yy.npOn, 'CONTENT', 'TITLEBAR_START'); %}
<underscore>[=][=][=]		%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'UNDERSCORE_END'); %}
[=][=][=]					%{ this.begin('underscore');	return Parser.npState(this.yy.npOn, 'CONTENT', 'UNDERSCORE_START'); %}
<wikilink>[)][)]			%{ this.popState();				return Parser.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_END'); %}
[(][(]						%{ this.begin('wikilink');		return Parser.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_START'); %}

"<"(.|\n)*?">"								return 'HTML'
(.)											return 'CONTENT'
(\n)
	%{
		if (Parser.npState(this.yy.npOn, false, true) == true) {
			yytext = Parser.formatContent(yytext);
		}
		
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
	{
		$$ = Parser.join($1, $2);//js
		//php $$ = $this->join($1, $2);
	}
 | wiki_contents plugin contents
	{
		$$ = Parser.join($1, $2, $3);//js
		//php $$ = $this->join($1, $2, $3);
	}
 ;

plugin
 : INLINE_PLUGIN
	{
		$$ = Parser.plugin($1);//js
		//php $$ = $this->plugin($1);
	}
 | PLUGIN_START wiki_contents PLUGIN_END
	{
		$3.body = $2;//js
		$$ = Parser.plugin($3);//js
		//php $3->body = $2;
		//php $$ = $this->plugin($3);
	}
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{
		$$ =  Parser.join($1, $2);//js
		//php $$ = $this->join($1, $2);
	}
 ;

content
 : CONTENT
	{$$ = $1;}
 | HTML
	{
		$$ = Parser.html($1);//js
		//php $$ = $this->html($1);
	}
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
 | BOLD_START wiki_contents BOLD_END
	{
		$$ = Parser.bold($2);//js
		//php $$ = $this->bold($2);
		
	}
 | BOX_START wiki_contents BOX_END
	{
		$$ = Parser.box($2);//js
		//php $$ = $this->box($2);
	}
 | CENTER_START wiki_contents CENTER_END
	{
		$$ = Parser.center($2);//js
		//php $$ = $this->center($2);
	}
 | COLORTEXT_START wiki_contents COLORTEXT_END
	{
		$$ = Parser.colortext($2);//js
		//php $$ = $this->colortext($2);
	}
 | ITALIC_START wiki_contents ITALIC_END
	{
		$$ = Parser.italics($2);//js
		//php $$ = $this->italics($2);
	}
 | HEADER6_START wiki_contents HEADER6_END
	{
		$$ = Parser.header6($2);//js
		//php $$ = $this->header6($2);
	}
 | HEADER5_START wiki_contents HEADER5_END
	{
		$$ = Parser.header5($2);//js
		//php $$ = $this->header5($2);
	}
 | HEADER4_START wiki_contents HEADER4_END
	{
		$$ = Parser.header4($2);//js
		//php $$ = $this->header4($2);
	}
 | HEADER3_START wiki_contents HEADER3_END
	{
		$$ = Parser.header3($2);//js
		//php $$ = $this->header3($2);
	}
 | HEADER2_START wiki_contents HEADER2_END
	{
		$$ = Parser.header2($2);//js
		//php $$ = $this->header2($2);
	}
 | HEADER1_START wiki_contents HEADER1_END
	{
		$$ = Parser.header1($2);//js
		//php $$ = $this->header1($2);
	}
 | LINK_START wiki_contents LINK_END
	{
		$$ = Parser.link($2);//js
		//php $$ = $this->link($2);
	}
 | NP_START wiki_contents NP_END
	{$$ = $2;}
 | STRIKETHROUGH_START wiki_contents STRIKETHROUGH_END
	{
		$$ = Parser.strikethrough($2);//js
		//php $$ = $this->strikethrough($2);
	}
 | TABLE_START wiki_contents TABLE_END
	{
		$$ = Parser.tableParser($2);//js
		//php $$ = $this->tableParser($2);
	}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{
		$$ = Parser.titlebar($2);//js
		//php $$ = $this->titlebar($2);
	}
 | UNDERSCORE_START wiki_contents UNDERSCORE_END
	{
		$$ = Parser.underscore($2);//js
		//php $$ = $this->underscore($2);
	}
 | WIKILINK_START wiki_contents WIKILINK_END
	{
		$$ = Parser.wikilink($2);//js
		//php $$ = $this->wikilink($2);
	}
 ;
