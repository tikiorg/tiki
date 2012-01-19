//phpOption parserClass:JisonParser_Wiki_Parser
//phpOption lexerClass:JisonParser_Wiki_Lexer
//phpOption fileName:Parser.php

%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SMILE							[a-z]+

%s bold box center colortext italic header6 header5 header4 header3 header2 header1 link strikethrough table titlebar underscore wikilink
%%

"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		yytext = Parser.inlinePlugin(yytext); //js
		
		//php $yytext = JisonParser_Wiki_Handler::inlinePlugin($yytext);
		
		return 'INLINE_PLUGIN'
	%}

"{"{PLUGIN_ID}"(".*?")}"
	%{
		yy.pluginStack = Parser.stackPlugin(yytext, yy.pluginStack); //js
		
		if (Parser.size(yy.pluginStack) == 1) {//js
			return 'PLUGIN_START'; //js
		} else {//js
			return 'CONTENT'; //js
		}//js
		
		//php $yy->pluginStack = JisonParser_Wiki_Handler::stackPlugin($yytext, $yy->pluginStack);
		
		//php if (JisonParser_Wiki_Handler::size($yy.pluginStack) == 1) {
		//php 	return 'PLUGIN_START';
		//php } else {
		//php 	return 'CONTENT';
		//php }
	%}

"{"{PLUGIN_ID}"}"
	%{
		if (yy.pluginStack) { //js
			if ( //js
				Parser.size(yy.pluginStack) > 0 && //js
				Parser.substring(yytext, 1, -1) == yy.pluginStack[Parser.size(yy.pluginStack) - 1].name //js
			) { //js
				if (Parser.size(yy.pluginStack) == 1) { //js
					yytext = yy.pluginStack[Parser.size(yy.pluginStack) - 1]; //js
					yy.pluginStack = Parser.pop(yy.pluginStack); //js
					return 'PLUGIN_END'; //js
				} else { //js
					yy.pluginStack = Parser.pop(yy.pluginStack); //js
					return 'CONTENT'; //js
				} //js
			} //js
		} //js
		return 'CONTENT'; //js
		
		//php if ($yy->pluginStack) {
		//php 	if (
		//php 		JisonParser_Wiki_Handler::size($yy->pluginStack) > 0 &&
		//php 		JisonParser_Wiki_Handler::substring($yytext, 1, -1) == $yy->pluginStack[JisonParser_Wiki_Handler::size($yy->pluginStack) - 1]->name
		//php 	) {
		//php 		if (JisonParser_Wiki_Handler::size($yy->pluginStack) == 1) {
		//php 			$yytext = $yy->pluginStack[JisonParser_Wiki_Handler::size($yy->pluginStack) - 1];
		//php 			$yy->pluginStack = JisonParser_Wiki_Handler::pop($yy->pluginStack);
		//php 			return 'PLUGIN_END';
		//php 		} else {
		//php 			$yy->pluginStack = JisonParser_Wiki_Handler::pop($yy->pluginStack);
		//php 			return 'CONTENT';
		//php 		}
		//php 	}
		//php }
		//php return 'CONTENT';
	%}

("~np~")
	%{
		yy.npStack = Parser.push(yy.npStack, true); //js
		this.yy.npOn = true; //js
		
		return 'NP_START'; //js
		
		//php $yy->npStack = JisonParser_Wiki_Handler::push($yy->npStack, true);
		//php $this->npOn = true;
		
		//php return 'NP_START';
	%}

("~/np~")
	%{
		this.yy.npStack = Parser.pop(yy.npStack); //js
		if (Parser.size(yy.npStack) < 1) yy.npOn = false; //js
		
		return 'NP_END'; //js
		
		//php $this->npStack = JisonParser_Wiki_Handler::pop($yy->npStack);
		//php if (JisonParser_Wiki_Handler::size($yy->npStack) < 1) $yy->npOn = false;
		
		//php return 'NP_END';
	%}

"---" 
	%{
		yytext = Parser.hr(); //js
		//php $yytext = JisonParser_Wiki_Handler::hr();
		 
		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		yytext = Parser.substring(yytext, 2, -2); //js
		yytext = Parser.smile(yytext); //js
		
		//php $yytext = JisonParser_Wiki_Handler::substring($yytext, 2, -2);
		//php $yytext = JisonParser_Wiki_Handler::smile($yytext);
		
		return 'SMILE';
	%}

"[[".*?
	%{
		yytext = Parser.substring(yytext, 2, -1); //js
		
		//php $yytext = JisonParser_Wiki_Handler::substring($yytext, 2, -1);
		
		return 'CONTENT';
	%}

<bold>[_][_]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'BOLD_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'BOLD_END');
	%}
[_][_]
	%{
		this.begin('bold'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'BOLD_START'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'BOLD_START');
	%}


<box>[\^]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'BOX_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'BOX_END');
	%}
[\^]
	%{
		this.begin('box'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'BOX_START'); //js
		
		//php this.begin('box');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'BOX_START');
	%}


<center>[:][:]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'CENTER_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'CENTER_END');
	%}
[:][:]
	%{
		this.begin('center'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'CENTER_START'); //js
		
		//php this.begin('center');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'CENTER_START');
	%}


<colortext>[\~][\~]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'COLORTEXT_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'COLORTEXT_END');
	%}
[\~][\~][#]
	%{
		this.begin('colortext'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'COLORTEXT_START'); //js
		
		//php this.begin('colortext');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'COLORTEXT_START');
	%}


<header6>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER6_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER6_END');
	%}
[\n]("!!!!!!")
	%{
		this.begin('header6'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER6_START'); //js
		
		//php this.begin('header6');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER6_START');
	%}


<header5>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER5_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER5_END');
	%}
[\n]("!!!!!")
	%{
		this.begin('header5'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER5_START'); //js
		
		//php this.begin('header5');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER5_START');
	%}


<header4>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER4_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER4_END');
	%}
[\n]("!!!!")
	%{
		this.begin('header4'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER4_START'); //js
		
		//php JisonParser_Wiki_Handler::begin('header4');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER4_START');
	%}


<header3>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER3_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER3_END');
	%}
[\n]("!!!")
	%{
		this.begin('header3'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER3_START'); //js
		
		//php this.begin('header3');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER3_START');
	%}


<header2>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER2_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER2_END');
	%}
[\n]("!!")
	%{
		this.begin('header2'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER2_START'); //js
		
		//php this.begin('header2');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER2_START');
	%}


<header1>[\n]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER1_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER1_END');
	%}
[\n]("!")
	%{
		this.begin('header1'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'HEADER1_START'); //js
		
		//php this.begin('header1');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'HEADER1_START');
	%}


<italic>['][']
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'ITALIC_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'ITALIC_END');
	%}
['][']
	%{
		this.begin('italic'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'ITALIC_START'); //js
		
		//php this.begin('italic');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'ITALIC_START');
	%}


<link>("]")
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'LINK_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'LINK_END');
	%}
("[")
	%{
		this.begin('link'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'LINK_START'); //js
		
		//php this.begin('link');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'LINK_START');
	%}


<strikethrough>[-][-]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'STRIKETHROUGH_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'STRIKETHROUGH_END');
	%}
[-][-]
	%{
		this.begin('strikethrough'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'STRIKETHROUGH_START'); //js
		
		//php this.begin('strikethrough');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'STRIKETHROUGH_START');
	%}


<table>[|][|]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'TABLE_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'TABLE_END');
	%}
[|][|]
	%{
		this.begin('table'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'TABLE_START'); //js
		
		//php this.begin('table');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'TABLE_START');
	%}


<titlebar>[=][-]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'TITLEBAR_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'TITLEBAR_END');
	%}
[-][=]
	%{
		this.begin('titlebar'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'TITLEBAR_START'); //js
		
		//php this.begin('titlebar');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'TITLEBAR_START');
	%}


<underscore>[=][=][=]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'UNDERSCORE_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'UNDERSCORE_END');
	%}
[=][=][=]
	%{
		this.begin('underscore'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'UNDERSCORE_START'); //js
		
		//php this.begin('underscore');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'UNDERSCORE_START');
	%}


<wikilink>[)][)]
	%{
		this.popState(); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_END'); //js
		
		//php JisonParser_Wiki_Handler::popState();
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'WIKILINK_END');
	%}
[(][(]
	%{
		this.begin('wikilink'); //js
		return Parser.npState(this.yy.npOn, 'CONTENT', 'WIKILINK_START'); //js
		
		//php this.begin('wikilink');
		//php return JisonParser_Wiki_Handler::npState($this->npOn, 'CONTENT', 'WIKILINK_START');
	%}


"<"(.|\n)*?">"								return 'HTML'
(.)											return 'CONTENT'
(\n)
	%{
		if (Parser.npState(this.yy.npOn, false, true) == true) { //js
			yytext = Parser.formatContent(yytext); //js
		} //js
		
		//php if (JisonParser_Wiki_Handler::npState($this->npOn, false, true) == true) {
		//php 	$yytext = JisonParser_Wiki_Handler::formatContent($yytext);
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
		$$ = Parser.join($1, $2); //js
		
		//php $$ = JisonParser_Wiki_Handler::join($1, $2);
	}
 | wiki_contents plugin contents
	{
		$$ = Parser.join($1, $2, $3); //js
		
		//php $$ = JisonParser_Wiki_Handler::join($1, $2, $3);
	}
 ;

plugin
 : INLINE_PLUGIN
	{
		$$ = Parser.plugin($1); //js
		
		//php $$ = JisonParser_Wiki_Handler::plugin($1);
	}
 | PLUGIN_START wiki_contents PLUGIN_END
	{
		$3.body = $2; //js
		$$ = Parser.plugin($3); //js
		
		//php $3->body = $2;
		//php $$ = JisonParser_Wiki_Handler::plugin($3);
	}
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{
		$$ =  Parser.join($1, $2); //js
		
		//php $$ = JisonParser_Wiki_Handler::join($1, $2);
	}
 ;

content
 : CONTENT
	{$$ = $1;}
 | HTML
	{
		$$ = Parser.html($1); //js
		//php $$ = JisonParser_Wiki_Handler::html($1);
	}
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
 | BOLD_START wiki_contents BOLD_END
	{
		$$ = Parser.bold($2); //js
		//php $$ = JisonParser_Wiki_Handler::bold($2);
		
	}
 | BOX_START wiki_contents BOX_END
	{
		$$ = Parser.box($2); //js
		//php $$ = JisonParser_Wiki_Handler::box($2);
	}
 | CENTER_START wiki_contents CENTER_END
	{
		$$ = Parser.center($2); //js
		//php $$ = JisonParser_Wiki_Handler::center($2);
	}
 | COLORTEXT_START wiki_contents COLORTEXT_END
	{
		$$ = Parser.colortext($2); //js
		//php $$ = JisonParser_Wiki_Handler::colortext($2);
	}
 | ITALIC_START wiki_contents ITALIC_END
	{
		$$ = Parser.italics($2); //js
		//php $$ = JisonParser_Wiki_Handler::italics($2);
	}
 | HEADER6_START wiki_contents HEADER6_END
	{
		$$ = Parser.header6($2); //js
		//php $$ = JisonParser_Wiki_Handler::header6($2);
	}
 | HEADER5_START wiki_contents HEADER5_END
	{
		$$ = Parser.header5($2); //js
		//php $$ = JisonParser_Wiki_Handler::header5($2);
	}
 | HEADER4_START wiki_contents HEADER4_END
	{
		$$ = Parser.header4($2); //js
		//php $$ = JisonParser_Wiki_Handler::header4($2);
	}
 | HEADER3_START wiki_contents HEADER3_END
	{
		$$ = Parser.header3($2); //js
		//php $$ = JisonParser_Wiki_Handler::header3($2);
	}
 | HEADER2_START wiki_contents HEADER2_END
	{
		$$ = Parser.header2($2); //js
		//php $$ = JisonParser_Wiki_Handler::header2($2);
	}
 | HEADER1_START wiki_contents HEADER1_END
	{
		$$ = Parser.header1($2); //js
		//php $$ = JisonParser_Wiki_Handler::header1($2);
	}
 | LINK_START wiki_contents LINK_END
	{
		$$ = Parser.link($2); //js
		//php $$ = JisonParser_Wiki_Handler::link($2);
	}
 | NP_START wiki_contents NP_END
	{$$ = $2;}
 | STRIKETHROUGH_START wiki_contents STRIKETHROUGH_END
	{
		$$ = Parser.strikethrough($2); //js
		//php $$ = JisonParser_Wiki_Handler::strikethrough($2);
	}
 | TABLE_START wiki_contents TABLE_END
	{
		$$ = Parser.tableParser($2); //js
		//php $$ = JisonParser_Wiki_Handler::tableParser($2);
	}
 | TITLEBAR_START wiki_contents TITLEBAR_END
	{
		$$ = Parser.titlebar($2); //js
		//php $$ = JisonParser_Wiki_Handler::titlebar($2);
	}
 | UNDERSCORE_START wiki_contents UNDERSCORE_END
	{
		$$ = Parser.underscore($2); //js
		//php $$ = JisonParser_Wiki_Handler::underscore($2);
	}
 | WIKILINK_START wiki_contents WIKILINK_END
	{
		$$ = Parser.wikilink($2); //js
		//php $$ = JisonParser_Wiki_Handler::wikilink($2);
	}
 ;
