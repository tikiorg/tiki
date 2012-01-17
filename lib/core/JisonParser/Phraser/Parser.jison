/* description: Parses words out of html, ignouring html in the parse, but returning it in the end */

/* lexical grammar */
%lex
%%
"<"(.|\n)*?">"						return 'TAG'
[a-zA-Z0-9]+						return 'WORD'
(.|\n)								return 'CHAR'
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
	{$$ =  $1 + $2;}
 ;

content
	: TAG
		{
			$$ = parser.tagHandler($1);//js
			//php $$ = $this->tagHandler($1);
		}
	| WORD
		{
			$$ = parser.wordHandler($1);//js
			//php $$ = $this->wordHandler($1);
		}
	| CHAR
		{
			$$ = parser.charHandler($1);//js
			//php $$ = $this->charHandler($1);
		}
 ;