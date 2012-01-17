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
			$$ = Parser.tagHandler($1);//js
			//php $$ = $this->tagHandler($1);
		}
	| WORD
		{
			$$ = Parser.wordHandler($1);//js
			//php $$ = $this->wordHandler($1);
		}
	| CHAR
		{
			$$ = Parser.charHandler($1);//js
			//php $$ = $this->charHandler($1);
		}
 ;