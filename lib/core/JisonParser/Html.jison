//phpOption parserClass:JisonParser_Html
//phpOption lexerClass:JisonParser_Html_Lexer

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
CAPITOL_WORD                    ([A-Z]{1,}[a-z_\-\x80-\xFF]{1,}){2,}

%s np pp plugin line block bold box center code color italic unlink link strike table titlebar underscore wikilink

%%

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
 : HTML_TAG
    {
        $$ = parser.htmlTag($1); //js
        //php $$ = $this->htmlTag($1);
    }
 | CONTENT
    {
        $$ = parser.content($1); //js
        //php $$ = $this->content($1);
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
