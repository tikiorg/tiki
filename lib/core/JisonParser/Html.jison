//phpOption parserClass:JisonParser_Html
//phpOption lexerClass:JisonParser_Html_Lexer

//Lexical Grammer
%lex

LINE_END                        (\n\r|\r\n|[\n\r])
CAPITOL_WORD                    ([A-Z]{1,}[a-z_\-\x80-\xFF]{1,}){2,}

%%

{CAPITOL_WORD}                              return 'CAPITOL_WORD';
[<](.|\n)*?[>]                              return 'HTML_TAG';
([A-Za-z0-9 .,?;]+)                         return 'CONTENT';
([ ])                                       return 'CONTENT';
{LINE_END}                                  return 'LINE_END';
(.)                                         return 'CONTENT';
<<EOF>>										return 'EOF';

/lex

//Parsing Grammer
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
 | CAPITOL_WORD
    {
        $$ = parser.capitolWord($1); //js
        //php $$ = $this->capitolWord($1);
    }
 | LINE_END
    {
        $$ = parser.lineEnd($1); //js
        //php $$ = $this->lineEnd($1);
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
