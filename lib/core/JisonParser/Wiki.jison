//phpOption parserClass:JisonParser_Wiki
//phpOption lexerClass:JisonParser_Wiki_Lexer

//Lexical Grammer
%lex

PLUGIN_ID   					[A-Z]+
INLINE_PLUGIN_ID				[a-z]+
SYNTAX_START_CHARS              [\_\^:\~'\[-|=\(]
SMILE							[a-z]+

%s plugin bold box center colortext italic header list link strikethrough table titlebar underscore wikilink
%options flex

%%

("§"[a-z0-9]{32}"§")                        return 'CONTENT';
("≤"(.)+"≥")                                return 'CONTENT';
("<"(.|\n)*?">")							return 'CONTENT';
([A-Za-z0-9 ]+)                            return 'CONTENT';


"{ELSE}"						return 'CONTENT';//For now let individual plugins handle else
"{"{INLINE_PLUGIN_ID}.*?"}"
	%{
		yytext = parser.inlinePlugin(yytext); //js
		return 'INLINE_PLUGIN'; //js

		//php $yytext = $this->inlinePlugin($yytext);
		//php return 'INLINE_PLUGIN';
	%}



"{"{PLUGIN_ID}"(".*?")}"
	%{
		lexer.begin('plugin'); //js
		yy.pluginStack = parser.stackPlugin(yytext, yy.pluginStack); //js

		if (parser.size(yy.pluginStack) == 1) {//js
			return 'PLUGIN_START'; //js
		} else {//js
			return 'CONTENT'; //js
		}//js

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
		//php 		    $this->substring($yytext, 1, -1) == $this->pluginStack[count($this->pluginStack) - 1]['name']
		//php 	    ) {
		//php 		    if (count($this->pluginStack) == 1) {
		//php 			    $yytext = $this->pluginStack[count($this->pluginStack) - 1];
		//php 			    array_pop($this->pluginStack);
		//php 			    return 'PLUGIN_END';
		//php 		    } else {
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
		yytext = parser.hr(); //js
		//php $yytext = $this->hr();

		return 'HORIZONTAL_BAR';
	%}

"(:"{SMILE}":)"
	%{
		yytext = parser.substring(yytext, 2, -2); //js
		yytext = parser.smile(yytext); //js

		//php $yytext = $this->substring($yytext, 2, -2);
		//php $yytext = $this->smile($yytext);

		return 'SMILE';
	%}

"[[".*?
	%{
		yytext = parser.substring(yytext, 2, -1); //js

		//php $yytext = $this->substring($yytext, 2, -1);

		return 'CONTENT';
	%}


<bold><<EOF>>
	%{
		if (parser.isPlugin()) return 'EOF'; //js
		lexer.unput('__'); //js

		//php if ($this->isPlugin()) return 'EOF';
        //php $this->unput('__');
	%}
<bold>[_][_]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'BOLD_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOLD_END';
	%}
[_][_]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('bold'); //js
		return 'BOLD_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('bold');
		//php return 'BOLD_START';
	%}


<box><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('^'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('^');
	%}
<box>[\^]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'BOX_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'BOX_END';
	%}
[\^]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('box'); //js
		return 'BOX_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('box');
		//php return 'BOX_START';
	%}


<center><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('::'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('::');
	%}
<center>[:][:]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'CENTER_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'CENTER_END';
	%}
[:][:]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('center'); //js
		return 'CENTER_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('center');
		//php return 'CENTER_START';
	%}


<colortext><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js\
		lexer.unput('~~'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('~~');
	%}
<colortext>[\~][\~]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'COLORTEXT_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'COLORTEXT_END';
	%}
[\~][\~][#]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('colortext'); //js
		return 'COLORTEXT_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('colortext');
		//php return 'COLORTEXT_START';
	%}


<header><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput("\n"); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput("\n");
	%}
<header>[\n\r]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'HEADER_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'HEADER_END';
	%}
[\n\r][!]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		parser.begin('header'); //js
		return 'HEADER_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('header');
		//php return 'HEADER_START';
	%}



<list><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput("\n"); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput("\n");
	%}
<list>[\n\r]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'LIST_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'LIST_END';
	%}
[\n\r][*#+]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		parser.begin('list'); //js
		return 'LIST_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('list');
		//php return 'LIST_START';
	%}



<italic><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput("''"); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput("''");
	%}
<italic>['][']
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'ITALIC_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'ITALIC_END';
	%}
['][']
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('italic'); //js
		return 'ITALIC_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('italic');
		//php return 'ITALIC_START';
	%}


<link><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
        lexer.unput(']'); //js

        //php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput(']');
	%}
<link>"]"
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'LINK_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'LINK_END';
	%}
"["
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('link'); //js
		return 'LINK_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('link');
		//php return 'LINK_START';
	%}


"-- "               return 'CONTENT';
<strikethrough><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
        lexer.unput('--'); //js

        //php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('--');
	%}
<strikethrough>[-][-]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'STRIKETHROUGH_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'STRIKETHROUGH_END';
	%}
[-][-]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('strikethrough'); //js
		return 'STRIKETHROUGH_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('strikethrough');
		//php return 'STRIKETHROUGH_START';
	%}


<table><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('||'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
        //php $this->unput('||');
	%}
<table>[|][|]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'TABLE_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'TABLE_END';
	%}
[|][|]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('table'); //js
		return 'TABLE_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('table');
		//php return 'TABLE_START';
	%}


<titlebar><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('=-'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('=-');
	%}
<titlebar>[=][-]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'TITLEBAR_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'TITLEBAR_END';
	%}
[-][=]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('titlebar'); //js
		return 'TITLEBAR_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('titlebar');
		//php return 'TITLEBAR_START';
	%}



<underscore><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('==='); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('===');
	%}
<underscore>[=][=][=]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'UNDERSCORE_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'UNDERSCORE_END';
	%}
[=][=][=]
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('underscore'); //js
		return 'UNDERSCORE_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('underscore');
		//php return 'UNDERSCORE_START';
	%}


<wikilink><<EOF>>
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.unput('))'); //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->unput('))');
	%}
<wikilink>"))"
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.popState(); //js
		return 'WIKILINK_END'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->popState();
		//php return 'WIKILINK_END';
	%}
"(("
	%{
		if (parser.isPlugin()) return 'CONTENT'; //js
		lexer.begin('wikilink'); //js
		return 'WIKILINK_START'; //js

		//php if ($this->isPlugin()) return 'CONTENT';
		//php $this->begin('wikilink');
		//php return 'WIKILINK_START';
	%}



(.)+?/({SYNTAX_START_CHARS}|"{"{INLINE_PLUGIN_ID}|"{"{PLUGIN_ID})
											return 'CONTENT'; //anything before a syntax or plugin
(.)											return 'CONTENT';

(\s)                                        return 'CONTENT';
<<EOF>>										return 'EOF';
/lex

%%

wiki
 : contents
 	{return $1;}
 | contents EOF
	{return $1;}
 | EOF
    {return " ";}
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
 | HORIZONTAL_BAR
	{$$ = $1;}
 | SMILE
	{$$ = $1;}
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
 | COLORTEXT_START COLORTEXT_END
 | COLORTEXT_START contents COLORTEXT_END
	{
		$$ = parser.colortext($2); //js
		//php $$ = $this->colortext($2);
	}
 | ITALIC_START ITALIC_END
 | ITALIC_START contents ITALIC_END
	{
		$$ = parser.italics($2); //js
		//php $$ = $this->italics($2);
	}
 | LINK_START LINK_END
 | LINK_START contents LINK_END
	{
		$$ = parser.link($2); //js
		//php $$ = $this->link($2);
	}
 | STRIKETHROUGH_START STRIKETHROUGH_END
 | STRIKETHROUGH_START contents STRIKETHROUGH_END
	{
		$$ = parser.strikethrough($2); //js
		//php $$ = $this->strikethrough($2);
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
		$$ = parser.wikilink($2); //js
		//php $$ = $this->wikilink($2);
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
 | HEADER_START HEADER_END
 | HEADER_START contents HEADER_END
	{
		$$ = parser.header($2); //js
		//php $$ = $this->header($2);
	}
 | LIST_START LIST_END
 | LIST_START contents LIST_END
	{
		$$ = parser.stackList($1 + $2); //js
		//php $$ = $this->stackList($1 . $2);
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
