/* description: Parses words out of html, ignouring html in the parse, but returning it in the end */
//phpOption parserClass:JisonParser_Phraser
//phpOption lexerClass:JisonParser_Phraser_Lexer

/* lexical grammar */
%lex
%%
"<"(.|\n)*?">"+ 					return 'TAG'
(\w|\d)+							return 'WORD'
(.|\n|\s)							return 'CHAR'
<<EOF>>								return 'EOF'


/lex

%start html

%% /* language grammar */

html
 : contents EOF
     {return $1;}
 ;

contents
 : content
	{$$ = $1;}
 | contents content
	{
		$$ =  $1 + $2; //js
		//php $$ = $1 . $2;
	}
 ;

content
	: TAG
		{
			$$ = Phraser.tagHandler($1);//js
			//php $$ = $this->tagHandler($1);
		}
	| WORD
		{
			$$ = Phraser.wordHandler($1);//js
			//php $$ = $this->wordHandler($1);
		}
	| CHAR
		{
			$$ = Phraser.charHandler($1);//js
			//php $$ = $this->charHandler($1);
		}
 ;
