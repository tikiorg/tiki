//phpOption parserClass:JisonParser_Wiki
//phpOption lexerClass:JisonParser_Wiki_Lexer

//Lexical Grammer
%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
VARIABLE_NAME                   ([0-9A-Za-z ]{3,})
SYNTAX_CHARS                    [{}\n_\^:\~'-|=\(\)\[\]*#+%<≤]
LINE_CONTENT                    (.?)
LINES_CONTENT                   (.|\n)+
LINE_END                        (\n\r|\r\n|[\n\r])
BLOCK_START                     ([\!*#+;])
WIKI_LINK_TYPE                  (([a-z0-9-]+))
CAPITOL_WORD                    ([A-Z]{1,}[a-z]{1,}){2,}

%s np pp plugin line block bold box center code color italic unlink link strike table titlebar underscore wikilink

%%
<np><<EOF>>
	%{
		lexer.unput('~/np~'); //js

		//php $this->unput('~/np~');

		return 'CONTENT';
	%}
<np>"~/np~"
	%{
		if (this.npStack != true) return 'CONTENT'; //js
		lexer.popState(); //js
		lexer.npStack = false; //js
		yytext = parser.np(yytext); //js

		//php if ($this->npStack != true) return 'CONTENT';
		//php $this->popState();
		//php $this->npStack = false;
		//php $yytext = $this->np($yytext);

		return 'NP_END';
	%}
"~np~"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('np'); //js
		lexer.npStack = true; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('np');
		//php $this->npStack = true;

		return 'NP_START';
	%}


<pp><<EOF>>
	%{
		lexer.unput('~/pp~'); //js

		//php $this->unput('~/pp~');

		return 'CONTENT';
	%}
<pp>"~/pp~"
	%{
		if (this.ppStack != true) return 'CONTENT'; //js
		lexer.popState(); //js
		lexer.ppStack = false; //js
		yytext = parser.pp(yytext); //js

		//php if ($this->ppStack != true) return 'CONTENT';
		//php $this->popState();
		//php $this->ppStack = false;
		//php $yytext = $this->pp($yytext);

		return 'PP_END';
	%}
"~pp~"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('pp'); //js
		lexer.ppStack = true; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('pp');
		//php $this->ppStack = true;

		return 'PP_START';
	%}


"~tc~"{LINES_CONTENT}"~/tc~"                    return 'COMMENT';


[%][%]{VARIABLE_NAME}[%][%]
	%{
		if (parser.isContent()) return 'CONTENT'; //js

        //php if ($this->isContent()) return 'CONTENT';

		return 'DOUBLE_DYNAMIC_VAR';
	%}
[%]{VARIABLE_NAME}[%]
	%{
		if (parser.isContent()) return 'CONTENT'; //js

        //php if ($this->isContent()) return 'CONTENT';

		return 'SINGLE_DYNAMIC_VAR';
	%}

"{{"{VARIABLE_NAME}([|]{VARIABLE_NAME})?"}}"
	%{
        if (parser.isContent(['linkStack'])) return 'CONTENT'; //js

        //php if ($this->isContent(array('linkStack'))) return 'CONTENT';

        return 'ARGUMENT_VAR';
    %}

"{ELSE}"						return 'CONTENT';//For now let individual plugins handle else
{LINE_END}("{r2l}"|"{l2r}")
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        lexer.begin('block'); //js
        return 'BLOCK_START'; //js

        //php if ($this->isContent()) return 'CONTENT';
        //php $this->begin('block');
        //php return 'BLOCK_START';
	%}
"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		yytext = parser.inlinePlugin(yytext); //js
		return 'INLINE_PLUGIN'; //js

		//php $yytext = $this->inlinePlugin($yytext);
		//php return 'INLINE_PLUGIN';
	%}



"{"{PLUGIN_ID}"(".*?")}"
	%{
		if (parser.npStack || parser.ppStack) return 'CONTENT'; //js

		lexer.begin('plugin'); //js
		yy.pluginStack = parser.stackPlugin(yytext, yy.pluginStack); //js

		if (parser.size(yy.pluginStack) == 1) {//js
			return 'PLUGIN_START'; //js
		} else {//js
			return 'CONTENT'; //js
		}//js

		//php if ($this->npStack == true || $this->ppStack) return 'CONTENT';

		//php $this->begin('plugin');
		//php $this->stackPlugin($yytext);

		//php if (count($this->pluginStack) == 1) {
		//php 	return 'PLUGIN_START';
		//php } else {
		//php 	return 'CONTENT';
		//php }
	%}
<plugin><<EOF>>
	%{
		lexer.unput("{" + yy.pluginStack[parser.size(yy.pluginStack) - 1].name + "}"); //js

		//php $this->unput("{" . $this->pluginStack[count($this->pluginStack) - 1]['name'] . "}");
	%}
<plugin>"{"{PLUGIN_ID}"}"
	%{
		var plugin = yy.pluginStack[yy.pluginStack.length - 1]; //js
		if (('{' + plugin.name + '}') == yytext) { //js
			lexer.popState(); //js
			if (yy.pluginStack) { //js
				if ( //js
					parser.size(yy.pluginStack) > 0 && //js
					parser.substring(yytext, 1, -1) == yy.pluginStack[parser.size(yy.pluginStack) - 1].name //js
				) { //js
					if (parser.size(yy.pluginStack) == 1) { //js
						yytext = yy.pluginStack[parser.size(yy.pluginStack) - 1]; //js
						yy.pluginStack = parser.pop(yy.pluginStack); //js
						return 'PLUGIN_END'; //js
					} else { //js
						yy.pluginStack = parser.pop(yy.pluginStack); //js
						return 'CONTENT'; //js
					} //js
				} //js
			} //js
		} //js
		return 'CONTENT'; //js

		//php $plugin = end($this->pluginStack);
		//php if (('{' . $plugin['name'] . '}') == $yytext) {
		//php   $this->popState();
		//php   if (!empty($this->pluginStack)) {
		//php 	    if (
		//php 		    count($this->pluginStack) > 0 &&
		//php 		    substr($yytext, 1, -1) == $this->pluginStack[count($this->pluginStack) - 1]['name']
		//php 	    ) {
		//php 		    if (count($this->pluginStack) == 1) {
		//php 			    $yytext = $this->pluginStack[count($this->pluginStack) - 1];
		//php               $this->pluginStackCount--;
		//php 			    array_pop($this->pluginStack);
		//php 			    return 'PLUGIN_END';
		//php 		    } else {
		//php               $this->pluginStackCount--;
		//php 			    array_pop($this->pluginStack);
		//php 			    return 'CONTENT';
		//php 		    }
		//php 	    }
		//php   }
		//php }
		//php return 'CONTENT';
	%}



"---"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        return 'HORIZONTAL_BAR'; //js

        //php if ($this->isContent()) return 'CONTENT';
        //php return 'HORIZONTAL_BAR';
	%}
"%%%"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        return 'FORCED_LINE_END'; //js

        //php if ($this->isContent()) return 'CONTENT';
        //php return 'FORCED_LINE_END';
	%}



<bold><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('__'); //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->unput('__');
	%}
<bold>[_][_]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'BOLD_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOLD_END';
	%}
[_][_]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('bold'); //js
		return 'BOLD_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('bold');
		//php return 'BOLD_START';
	%}


<box><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('^'); //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->unput('^');
	%}
<box>[\^]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'BOX_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOX_END';
	%}
[\^]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('box'); //js
		return 'BOX_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('box');
		//php return 'BOX_START';
	%}


<center><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('::'); //js

		//php if ($this->isContent()) return 'CONTENT';
        //php $this->unput('::');
	%}
<center>[:][:]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'CENTER_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'CENTER_END';
	%}
[:][:]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('center'); //js
		return 'CENTER_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('center');
		//php return 'CENTER_START';
	%}



<code><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('+-'); //js

		//php if ($this->isContent()) return 'CONTENT';
        //php $this->unput('+-');
	%}
<code>"+-"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'CODE_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'CODE_END';
	%}
"-+"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('code'); //js
		return 'CODE_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('code');
		//php return 'CODE_START';
	%}



<color><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('~~'); //js

		//php if ($this->isContent()) return 'CONTENT';
        //php $this->unput('~~');
	%}
<color>[\~][\~]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'COLOR_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'COLOR_END';
	%}
[\~][\~]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('color'); //js
		return 'COLOR_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('color');
		//php return 'COLOR_START';
	%}



<italic><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput("''"); //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->unput("''");
	%}
<italic>['][']
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'ITALIC_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'ITALIC_END';
	%}
['][']
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('italic'); //js
		return 'ITALIC_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('italic');
		//php return 'ITALIC_START';
	%}


<unlink><<EOF>>
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
        lexer.unput("@np"); //js

        //php if ($this->isContent(array('linkStack'))) return 'CONTENT';
        //php $this->unput("@np"); //js
	%}
<unlink>("@np"|"]]"|"]")
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'UNLINK_END'; //js

		//php if ($this->isContent(array('linkStack'))) return 'CONTENT';
		//php $this->popState();
		//php return 'UNLINK_END';
	%}
"[["
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('unlink'); //js
		return 'UNLINK_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('unlink');
		//php return 'UNLINK_START';
	%}



<link><<EOF>>
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
        lexer.unput(']'); //js

        //php if ($this->isContent(array('linkStack'))) return 'CONTENT';
        //php $this->unput(']');
	%}
<link>"]"
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
		parser.linkStack = false; //js
		lexer.popState(); //js
		return 'LINK_END'; //js

		//php if ($this->isContent(array('linkStack'))) return 'CONTENT';
		//php $this->linkStack = false;
		//php $this->popState();
		//php return 'LINK_END';
	%}
"["
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		parser.linkStack = true; //js
		lexer.begin('link'); //js
		$yytext = 'external'; //js
		return 'LINK_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->linkStack = true;
		//php $this->begin('link');
		//php $yytext = 'external';
		//php return 'LINK_START';
	%}


<strike><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        lexer.unput('--'); //js

        //php if ($this->isContent()) return 'CONTENT';
        //php $this->unput('--');
	%}
<strike>[-][-]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'STRIKE_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'STRIKE_END';
	%}
[-][-](?![ ]|<<EOF>>)
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('strike'); //js
		return 'STRIKE_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('strike');
		//php return 'STRIKE_START';
	%}
[ ][-][-][ ]
	%{
		//php return 'DOUBLE_DASH';
	%}

<table><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('||'); //js

		//php if ($this->isContent()) return 'CONTENT';
        //php $this->unput('||');
	%}
<table>[|][|]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
        lexer.tableStack.pop(); //js
		return 'TABLE_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
        //php array_pop($this->tableStack);
		//php return 'TABLE_END';
	%}
[|][|]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('table'); //js
		lexer.tableStack.push(true); //js
		return 'TABLE_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('table');
		//php $this->tableStack[] = true;
		//php return 'TABLE_START';
	%}


<titlebar><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('=-'); //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->unput('=-');
	%}
<titlebar>[=][-]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'TITLEBAR_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'TITLEBAR_END';
	%}
[-][=]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('titlebar'); //js
		return 'TITLEBAR_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('titlebar');
		//php return 'TITLEBAR_START';
	%}



<underscore><<EOF>>
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.unput('==='); //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->unput('===');
	%}
<underscore>[=][=][=]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'UNDERSCORE_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'UNDERSCORE_END';
	%}
[=][=][=]
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.begin('underscore'); //js
		return 'UNDERSCORE_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->begin('underscore');
		//php return 'UNDERSCORE_START';
	%}


<wikilink><<EOF>>
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
		lexer.unput('))'); //js

		//php if ($this->isContent(array('linkStack'))) return 'CONTENT';
		//php $this->unput('))');
	%}
<wikilink>"))"|"(("
	%{
		if (parser.isContent(['linkStack'])) return 'CONTENT'; //js
		parser.linkStack = false; //js
		lexer.popState(); //js
		return 'WIKILINK_END'; //js

		//php if ($this->isContent(array('linkStack'))) return 'CONTENT';
		//php $this->linkStack = false;
		//php $this->popState();
		//php return 'WIKILINK_END';
	%}
"(("
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		parser.linkStack = true; //js
		lexer.begin('wikilink'); //js
		$yytext = 'wiki'; //js
		return 'WIKILINK_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->linkStack = true;
		//php $this->begin('wikilink');
		//php $yytext = 'wiki';
		//php return 'WIKILINK_START';
	%}
"))"
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		parser.linkStack = true; //js
		lexer.begin('wikilink'); //js
		$yytext = 'np'; //js
		return 'WIKILINK_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->linkStack = true;
		//php $this->begin('wikilink');
		//php $yytext = 'np';
		//php return 'WIKILINK_START';
	%}
"("{WIKI_LINK_TYPE}"("
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		parser.linkStack = true; //js
		lexer.begin('wikilink'); //js
		$yytext = $yytext.substring(1, $yytext.length - 1); //js
		return 'WIKILINK_START'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->linkStack = true;
		//php $this->begin('wikilink');
		//php $yytext = substr($yytext, 1, -1);
		//php return 'WIKILINK_START';
	%}
{CAPITOL_WORD}
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		return 'WIKILINK'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php return 'WIKILINK';
	%}



<block>(?={LINE_END})
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'BLOCK_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php $this->popState();
		//php return 'BLOCK_END';
	%}
<block>{LINE_END}
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        lexer.begin('block'); //js
        return 'BLOCK_END'; //js

        //php if ($this->isContent()) return 'CONTENT';
        //php $this->begin('block');
        //php $this->unput("\n");
        //php return 'BLOCK_END';
	%}
{LINE_END}(?={BLOCK_START})
	%{
		if (parser.isContent()) return 'CONTENT'; //js
        lexer.begin('block'); //js
        return 'BLOCK_START'; //js

        //php if ($this->isContent()) return 'CONTENT';
        //php $this->begin('block');
        //php return 'BLOCK_START';
	%}
{LINE_END}
	%{
		if (parser.isContent()) return 'CONTENT'; //js
		return 'LINE_END'; //js

		//php if ($this->isContent()) return 'CONTENT';
		//php return 'LINE_END';
	%}

[<](.|\n)*?[>]                              return 'HTML_TAG';
"≤REAL_LT≥"(.|\n)*?"≤REAL_GT≥"    	        return 'HTML_TAG';
("§"[a-z0-9]{32}"§")                        return 'CONTENT';
("≤"(.)+"≥")                                return 'CONTENT';
([A-Za-z0-9 .,?;]+)                         return 'CONTENT';
(?!{SYNTAX_CHARS})({LINE_CONTENT})?(?={SYNTAX_CHARS})
											return 'CONTENT';
([ ]+?)                                     return 'CONTENT';
(.)                                         return 'CONTENT';
<<EOF>>										return 'EOF';
/lex

%%

wiki
 : lines
 	{return $1;}
 | lines EOF
	{return $1;}
 | EOF
    {return " ";}
 ;


lines
 : line
    {$$ = $1;}
 | line lines
    {
        $$ = $1 + $2; //js
        //php $$ = $1 . $2;
    }
 ;

line
 : contents
    {$$ = $1;}
 | BLOCK_START BLOCK_END
    {
	    $$ = parser.block($1); //js
	    //php $$ = $this->block($1);
	}
 | BLOCK_START contents BLOCK_END
    {
        $$ = parser.block($1 + $2); //js
        //php $$ = $this->block($1 . $2);
    }
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{
		$$ = $1 + $2; //js

		//php $$ = $1 . $2;
	}
 ;

content
 : CONTENT
	{$$ = $1;}
 | COMMENT
	{
        $$ = parser.comment($1); //js
        //php $$ = $this->comment($1); //js
    }
 | NP_START NP_END
 | NP_START contents NP_END
    {
        $$ = parser.np($2); //js
        //php $$ = $this->np($2); //js
    }
 | PP_START PP_END
 | PP_START contents PP_END
    {
        $$ = parser.pp($2); //js
        //php $$ = $this->pp($2); //js
    }
 | DOUBLE_DYNAMIC_VAR
    {
        $$ = parser.doubleDynamicVar($1); //js
        //php $$ = $this->doubleDynamicVar($1);
    }
 | SINGLE_DYNAMIC_VAR
     {
        $$ = parser.singleDynamicVar($1); //js
        //php $$ = $this->singleDynamicVar($1);
     }
 | ARGUMENT_VAR
    {
        $$ = parser.argumentVar($1); //js
        //php $$ = $this->argumentVar($1);
    }
 | HTML_TAG
    {
        $$ = parser.htmlTag($1); //js
        //php $$ = $this->htmlTag($1);
    }
 | HORIZONTAL_BAR
	{
		$$ = parser.hr(); //js
		//php $$ = $this->hr();
	}
 | BOLD_START BOLD_END
 | BOLD_START contents BOLD_END
	{
		$$ = parser.bold($2); //js
		//php $$ = $this->bold($2);
	}
 | BOX_START BOX_END
 | BOX_START contents BOX_END
	{
		$$ = parser.box($2); //js
		//php $$ = $this->box($2);
	}
 | CENTER_START CENTER_END
 | CENTER_START contents CENTER_END
	{
		$$ = parser.center($2); //js
		//php $$ = $this->center($2);
	}
 | CODE_START CODE_END
 | CODE_START contents CODE_END
	{
		$$ = parser.code($2); //js
		//php $$ = $this->code($2);
	}
 | COLOR_START COLOR_END
 | COLOR_START contents COLOR_END
	{
		$$ = parser.color($2); //js
		//php $$ = $this->color($2);
	}
 | ITALIC_START ITALIC_END
 | ITALIC_START contents ITALIC_END
	{
		$$ = parser.italics($2); //js
		//php $$ = $this->italics($2);
	}
 | UNLINK_START UNLINK_END
 | UNLINK_START contents UNLINK_END
	{
		$$ = parser.unlink($1 + $2 + $3); //js
		//php $$ = $this->unlink($1 . $2 . $3);
	}
 | LINK_START LINK_END
 | LINK_START contents LINK_END
	{
		$$ = parser.link($1, $2); //js
		//php $$ = $this->link($1, $2);
	}
 | STRIKE_START STRIKE_END
 | STRIKE_START contents STRIKE_END
	{
		$$ = parser.strike($2); //js
		//php $$ = $this->strike($2);
	}
 | DOUBLE_DASH
    {
        $$ = parser.doubleDash(); //js
        //php $$ = $this->doubleDash();
    }
 | TABLE_START TABLE_END
 | TABLE_START contents TABLE_END
	{
		$$ = parser.tableParser($2); //js
		//php $$ = $this->tableParser($2);
	}
 | TITLEBAR_START TITLEBAR_END
 | TITLEBAR_START contents TITLEBAR_END
	{
		$$ = parser.titlebar($2); //js
		//php $$ = $this->titlebar($2);
	}
 | UNDERSCORE_START UNDERSCORE_END
 | UNDERSCORE_START contents UNDERSCORE_END
	{
		$$ = parser.underscore($2); //js
		//php $$ = $this->underscore($2);
	}
 | WIKILINK_START WIKILINK_END
 | WIKILINK_START contents WIKILINK_END
	{
		$$ = parser.link($1, $2); //js
		//php $$ = $this->link($1, $2);
	}
 | WIKILINK
    {
        $$ = parser.link('word', $1); //js
        //php $$ = $this->link('word', $1);
    }
 | INLINE_PLUGIN
 	{
 		$$ = parser.plugin($1); //js

 		//php $$ = $this->plugin($1);
 	}
 | PLUGIN_START PLUGIN_END
  	{
  		$2.body = ''; //js
        $$ = parser.plugin($2); //js

        //php $2['body'] = '';
        //php $$ = $this->plugin($2);
  	}
 | PLUGIN_START contents PLUGIN_END
 	{
 		$3.body = $2; //js
 		$$ = parser.plugin($3); //js

 		//php $3['body'] = $2;
 		//php $$ = $this->plugin($3);
 	}
 | LINE_END
    {
        $$ = parser.line($1); //js
        //php $$ = $this->line($1);
    }
 | FORCED_LINE_END
    {
        $$ = parser.forcedLineEnd(); //js
        //php $$ = $this->forcedLineEnd();
    }
 | BLOCK_START contents BLOCK_END
    {
        $$ = parser.block($1 + $2); //js
        //php $$ = $this->block($1 . $2);
    }
 ;

%% /* parser extensions */

// additional module code //js
parser.extend = { //js
	parser: function(extension) { //js
        if (extension) { //js
            for (var attr in extension) { //js
                parser[attr] = extension[attr]; //js
            } //js
        } //js
    }, //js
    lexer: function() { //js
		if (extension) { //js
			for (var attr in extension) { //js
				parser[attr] = extension[attr]; //js
			} //js
       	} //js
	} //js
}; //js
