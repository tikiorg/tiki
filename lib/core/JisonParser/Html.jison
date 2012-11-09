//phpOption parserClass:JisonParser_Html

//Lexical Grammer
%lex

LINE_END                        (\n\r|\r\n|[\n\r])
HTML_TAG_INLINE                 "<"(.|\n)[^>]*?"/>"
HTML_TAG_CLOSE                  "</"(.|\n)[^>]*?">"
HTML_TAG_OPEN                   "<"(.|\n)[^>]*?">"

%s htmlElement

%%
{HTML_TAG_INLINE}
	%{
		//php $yytext = $this->inlineTag($yytext);
		//php return "HTML_TAG_INLINE";
	%}


<htmlElement><<EOF>>
	%{
		//php $tag = $this->htmlElementStack[count($this->htmlElementStack) - 1];
		//php $this->htmlElementStack[count($this->htmlElementStack) - 1]['state'] = 'repaired';
		//php $this->unput('</' . $tag['name'] . '>');
		//php return 'CONTENT';
	%}
<htmlElement>{HTML_TAG_CLOSE}
	%{
		//php $element = $this->unStackHtmlElement($yytext);
		//php if ($this->compareElementClosingToYytext($element, $yytext) && $this->htmlElementStackCount == 0) {
		//php   $yytext = $element;
		//php   $this->popState();
    	//php   return "HTML_TAG_CLOSE";
    	//php }
    	//php return 'CONTENT';
	%}
{HTML_TAG_OPEN}
	%{
		//php $this->stackHtmlElement($yytext);
		//php if ($this->htmlElementStackCount == 1) {
		//php   $this->begin('htmlElement');
    	//php   return "HTML_TAG_OPEN";
    	//php }
    	//php return 'CONTENT';
	%}
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
    {return "";}
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
    {
        //php $$ = $this->content($1);
    }
 | LINE_END
    {
        //php $$ = $this->lineEnd($1);
    }
 | HTML_TAG_INLINE
	{
	    //php $$ = $this->toWiki($1);
	}
 | HTML_TAG_OPEN contents HTML_TAG_CLOSE
	{
	    //php $$ = $this->toWiki($3, $2);
	}
 | HTML_TAG_OPEN contents HTML_TAG_CLOSE_IGNORED
	{
	    //php $$ = $1 . $2 . $3;
	}
 | HTML_TAG_OPEN HTML_TAG_CLOSE
	{
	    //php $$ = $this->toWiki($2);
	}
 ;