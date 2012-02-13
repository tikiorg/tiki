//phpOption parserClass:JisonParser_Wiki
//phpOption lexerClass:JisonParser_Wiki_Lexer

%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SMILE							[a-z]+

%s bold box center colortext italic header6 header5 header4 header3 header2 header1 ulist1 olist1 link strikethrough table titlebar underscore wikilink

%%

"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		yytext = Wiki.inlinePlugin(yytext); //js
		
		//php $yytext = $this->inlinePlugin($yytext);
		
		return 'INLINE_PLUGIN'
	%}

"{"{PLUGIN_ID}"(".*?")}"
	%{
		yy.pluginStack = Wiki.stackPlugin(yytext, yy.pluginStack); //js
		
		if (Wiki.size(yy.pluginStack) == 1) {//js
			return 'PLUGIN_START'; //js
		} else {//js
			return 'CONTENT'; //js
		}//js
		
		//php $this->stackPlugin($yytext);
		
		//php if ($this->size($this->pluginStack) == 1) {
		//php 	return 'PLUGIN_START';
		//php } else {
		//php 	return 'CONTENT';
		//php }
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (yy.pluginStack) { //js
			if ( //js
				Wiki.size(yy.pluginStack) > 0 && //js
				Wiki.substring(yytext, 1, -1) == yy.pluginStack[Wiki.size(yy.pluginStack) - 1].name //js
			) { //js
				if (Wiki.size(yy.pluginStack) == 1) { //js
					yytext = yy.pluginStack[Wiki.size(yy.pluginStack) - 1]; //js
					yy.pluginStack = Wiki.pop(yy.pluginStack); //js
					return 'PLUGIN_END'; //js
				} else { //js
					yy.pluginStack = Wiki.pop(yy.pluginStack); //js
					return 'CONTENT'; //js
				} //js
			} //js
		} //js
		return 'CONTENT'; //js
		
		//php if (!empty($this->pluginStack)) {
		//php 	if (
		//php 		$this->size($this->pluginStack) > 0 &&
		//php 		$this->substring($yytext, 1, -1) == $this->pluginStack[$this->size($this->pluginStack) - 1]['name']
		//php 	) {
		//php 		if ($this->size($this->pluginStack) == 1) {
		//php 			$yytext = $this->pluginStack[$this->size($this->pluginStack) - 1];
		//php 			$this->pluginStack = $this->pop($this->pluginStack);
		//php 			return 'PLUGIN_END';
		//php 		} else {
		//php 			$this->pluginStack = $this->pop($this->pluginStack);
		//php 			return 'CONTENT';
		//php 		}
		//php 	}
		//php }
		//php return 'CONTENT';
	%}

("~np~")
	%{
		yy.npStack = Wiki.push(yy.npStack, true); //js
		this.yy.npOn = true; //js
		
		return 'NP_START'; //js
		
		//php $this->npStack = $this->push($this->npStack, true);
		//php $this->npOn = true;
		
		//php return 'NP_START';
	%}

("~/np~")
	%{
		this.yy.npStack = Wiki.pop(yy.npStack); //js
		if (Wiki.size(yy.npStack) < 1) yy.npOn = false; //js
		
		return 'NP_END'; //js
		
		//php $this->npStack = $this->pop($this->npStack);
		//php if ($this->size($this->npStack) < 1) $this->npOn = false;
		
		//php return 'NP_END';
	%}

"---" 
	%{
		yytext = Wiki.hr(); //js
		//php $yytext = $this->hr();
		 
		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		yytext = Wiki.substring(yytext, 2, -2); //js
		yytext = Wiki.smile(yytext); //js
		
		//php $yytext = $this->substring($yytext, 2, -2);
		//php $yytext = $this->smile($yytext);
		
		return 'SMILE';
	%}

"[[".*?
	%{
		yytext = Wiki.substring(yytext, 2, -1); //js
		
		//php $yytext = $this->substring($yytext, 2, -1);
		
		return 'CONTENT';
	%}

<bold>[_][_]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'BOLD_END'); //js
		
		//php $this->popState();
		//php return ($this->npOn == true ? 'CONTENT' : 'BOLD_END');
	%}
[_][_]
	%{
		this.begin('bold'); //js
		return (this.yy.npOn ? 'CONTENT' : 'BOLD_START'); //js
		
		//php $this->begin('bold');
		//php return $this->npState($this->npOn, 'CONTENT', 'BOLD_START');
	%}


<box>[\^]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'BOX_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'BOX_END');
	%}
[\^]
	%{
		this.begin('box'); //js
		return (this.yy.npOn ? 'CONTENT' : 'BOX_START'); //js
		
		//php $this->begin('box');
		//php return $this->npState($this->npOn, 'CONTENT', 'BOX_START');
	%}


<center>[:][:]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'CENTER_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'CENTER_END');
	%}
[:][:]
	%{
		this.begin('center'); //js
		return (this.yy.npOn ? 'CONTENT' : 'CENTER_START'); //js
		
		//php $this->begin('center');
		//php return $this->npState($this->npOn, 'CONTENT', 'CENTER_START');
	%}


<colortext>[\~][\~]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'COLORTEXT_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'COLORTEXT_END');
	%}
[\~][\~][#]
	%{
		this.begin('colortext'); //js
		return (this.yy.npOn ? 'CONTENT' : 'COLORTEXT_START'); //js
		
		//php $this->begin('colortext');
		//php return $this->npState($this->npOn, 'CONTENT', 'COLORTEXT_START');
	%}


<header6>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER6_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER6_END');
	%}
[\n]("!!!!!!")
	%{
		this.begin('header6'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER6_START'); //js
		
		//php $this->begin('header6');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER6_START');
	%}


<header5>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER5_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER5_END');
	%}
[\n]("!!!!!")
	%{
		this.begin('header5'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER5_START'); //js
		
		//php $this->begin('header5');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER5_START');
	%}


<header4>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER4_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER4_END');
	%}
[\n]("!!!!")
	%{
		this.begin('header4'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER4_START'); //js
		
		//php $this->begin('header4');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER4_START');
	%}


<header3>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER3_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER3_END');
	%}
[\n]("!!!")
	%{
		this.begin('header3'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER3_START'); //js
		
		//php $this->begin('header3');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER3_START');
	%}


<header2>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER2_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER2_END');
	%}
[\n]("!!")
	%{
		this.begin('header2'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER2_START'); //js
		
		//php $this->begin('header2');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER2_START');
	%}


<header1>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER1_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER1_END');
	%}
[\n]("!")
	%{
		this.begin('header1'); //js
		return (this.yy.npOn ? 'CONTENT' : 'HEADER1_START'); //js
		
		//php $this->begin('header1');
		//php return $this->npState($this->npOn, 'CONTENT', 'HEADER1_START');
	%}


<ulist1>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'ULIST1_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'ULIST1_END');
	%}
"\n*"
	%{
		this.begin('ulist1'); //js
		return (this.yy.npOn ? 'CONTENT' : 'ULIST1_START'); //js
		
		//php $this->begin('ulist1');
		//php return $this->npState($this->npOn, 'CONTENT', 'ULIST1_START');
	%}

<olist1>[\n]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'OLIST1_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'OLIST1_END');
	%}
"\n#"
	%{
		this.begin('olist1'); //js
		return (this.yy.npOn ? 'CONTENT' : 'OLIST1_START'); //js
		
		//php $this->begin('olist1');
		//php return $this->npState($this->npOn, 'CONTENT', 'OLIST1_START');
	%}


<italic>['][']
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'ITALIC_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'ITALIC_END');
	%}
['][']
	%{
		this.begin('italic'); //js
		return (this.yy.npOn ? 'CONTENT' : 'ITALIC_START'); //js
		
		//php $this->begin('italic');
		//php return $this->npState($this->npOn, 'CONTENT', 'ITALIC_START');
	%}


<link>("]")
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'LINK_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'LINK_END');
	%}
("[")
	%{
		this.begin('link'); //js
		return (this.yy.npOn ? 'CONTENT' : 'LINK_START'); //js
		
		//php $this->begin('link');
		//php return $this->npState($this->npOn, 'CONTENT', 'LINK_START');
	%}


<strikethrough>[-][-]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'STRIKETHROUGH_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'STRIKETHROUGH_END');
	%}
[-][-]
	%{
		this.begin('strikethrough'); //js
		return (this.yy.npOn ? 'CONTENT' : 'STRIKETHROUGH_START'); //js
		
		//php $this->begin('strikethrough');
		//php return $this->npState($this->npOn, 'CONTENT', 'STRIKETHROUGH_START');
	%}


<table>[|][|]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'TABLE_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'TABLE_END');
	%}
[|][|]
	%{
		this.begin('table'); //js
		return (this.yy.npOn ? 'CONTENT' : 'TABLE_START'); //js
		
		//php $this->begin('table');
		//php return $this->npState($this->npOn, 'CONTENT', 'TABLE_START');
	%}


<titlebar>[=][-]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'TITLEBAR_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'TITLEBAR_END');
	%}
[-][=]
	%{
		this.begin('titlebar'); //js
		return (this.yy.npOn ? 'CONTENT' : 'TITLEBAR_START'); //js
		
		//php $this->begin('titlebar');
		//php return $this->npState($this->npOn, 'CONTENT', 'TITLEBAR_START');
	%}


<underscore>[=][=][=]
	%{
		this.popState(); //js
		return (this.yy.npOn ? 'CONTENT' : 'UNDERSCORE_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'UNDERSCORE_END');
	%}
[=][=][=]
	%{
		this.begin('underscore'); //js
		return this.npState(this.yy.npOn, 'CONTENT', 'UNDERSCORE_START'); //js
		
		//php $this->begin('underscore');
		//php return $this->npState($this->npOn, 'CONTENT', 'UNDERSCORE_START');
	%}


<wikilink>[)][)]
	%{
		this.popState(); //js
		return Wiki.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_END'); //js
		
		//php $this->popState();
		//php return $this->npState($this->npOn, 'CONTENT', 'WIKILINK_END');
	%}
[(][(]
	%{
		this.begin('wikilink'); //js
		return Wiki.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_START'); //js
		
		//php $this->begin('wikilink');
		//php return $this->npState($this->npOn, 'CONTENT', 'WIKILINK_START');
	%}


"<"(.|\n)*?">"								return 'HTML'
(.)											return 'CONTENT'
(\n)
	%{
		if (Wiki.npState(this.yy.npOn, false, true) == true) { //js
			yytext = Wiki.formatContent(yytext); //js
		} //js
		
		//php if ($this->npState($this->npOn, false, true) == true) {
		//php 	$yytext = $this->formatContent($yytext);
		//php }
		
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
		$$ = Wiki.join($1, $2); //js
		
		//php $$ = $this->join($1, $2);
	}
 | wiki_contents plugin contents
	{
		$$ = Wiki.join($1, $2, $3); //js
		
		//php $$ = $this->join($1, $2, $3);
	}
 ;

plugin
 : INLINE_PLUGIN
	{
		$$ = Wiki.plugin($1); //js
		
		//php $$ = $this->plugin($1);
	}
 | PLUGIN_START wiki_contents PLUGIN_END
	{
		$3.body = $2; //js
		$$ = Wiki.plugin($3); //js
		
		//php $3['body'] = $2;
		//php $$ = $this->plugin($3);
	}
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{
		$$ =  Wiki.join($1, $2); //js
		
		//php $$ = $this->join($1, $2);
	}
 ;

content
 : CONTENT
	{$$ = $1;}
 | HTML
	{
		$$ = Wiki.html($1); //js
		//php $$ = $this->html($1);
	}
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
 | BOLD_START wiki_contents BOLD_END
	{
		$$ = Wiki.bold($2); //js
		//php $$ = $this->bold($2);
	}
 | BOX_START wiki_contents BOX_END
	{
		$$ = Wiki.box($2); //js
		//php $$ = $this->box($2);
	}
 | CENTER_START wiki_contents CENTER_END
	{
		$$ = Wiki.center($2); //js
		//php $$ = $this->center($2);
	}
 | COLORTEXT_START wiki_contents COLORTEXT_END
	{
		$$ = Wiki.colortext($2); //js
		//php $$ = $this->colortext($2);
	}
 | ITALIC_START wiki_contents ITALIC_END
	{
		$$ = Wiki.italics($2); //js
		//php $$ = $this->italics($2);
	}
 | HEADER6_START wiki_contents HEADER6_END
	{
		$$ = Wiki.header6($2); //js
		//php $$ = $this->header6($2);
	}
 | HEADER5_START wiki_contents HEADER5_END
	{
		$$ = Wiki.header5($2); //js
		//php $$ = $this->header5($2);
	}
 | HEADER4_START wiki_contents HEADER4_END
	{
		$$ = Wiki.header4($2); //js
		//php $$ = $this->header4($2);
	}
 | HEADER3_START wiki_contents HEADER3_END
	{
		$$ = Wiki.header3($2); //js
		//php $$ = $this->header3($2);
	}
 | HEADER2_START wiki_contents HEADER2_END
	{
		$$ = Wiki.header2($2); //js
		//php $$ = $this->header2($2);
	}
 | HEADER1_START wiki_contents HEADER1_END
	{
		$$ = Wiki.header1($2); //js
		//php $$ = $this->header1($2);
	}
 | ULIST1_START wiki_contents ULIST1_END
	{
		$$ = Wiki.ulist1($2); //js
		//php $$ = $this->ulist1($2);
	}
 | OLIST1_START wiki_contents OLIST1_END
	{
		$$ = Wiki.olist1($2); //js
		//php $$ = $this->olist1($2);
	}
 | LINK_START wiki_contents LINK_END
	{
		$$ = Wiki.link($2); //js
		//php $$ = $this->link($2);
	}
 | NP_START wiki_contents NP_END
	{$$ = $2;}
 | STRIKETHROUGH_START wiki_contents STRIKETHROUGH_END
	{
		$$ = Wiki.strikethrough($2); //js
		//php $$ = $this->strikethrough($2);
	}
 | TABLE_START wiki_contents TABLE_END
	{
		$$ = Wiki.tableParser($2); //js
		//php $$ = $this->tableParser($2);
	}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{
		$$ = Wiki.titlebar($2); //js
		//php $$ = $this->titlebar($2);
	}
 | UNDERSCORE_START wiki_contents UNDERSCORE_END
	{
		$$ = Wiki.underscore($2); //js
		//php $$ = $this->underscore($2);
	}
 | WIKILINK_START wiki_contents WIKILINK_END
	{
		$$ = Wiki.wikilink($2); //js
		//php $$ = $this->wikilink($2);
	}
 ;