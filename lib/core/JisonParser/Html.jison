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
		//A tag that doesn't need to track state
		//php if (JisonParser_Html_Handler::isHtmlTag($yytext) == true) {
		//php   $yytext = $this->inlineTag($yytext);
		//php   return "HTML_TAG_INLINE";
		//php }

		//A non-valid html tag, return "<" put the rest back into the parser
        //php $tag = $yytext;
        //php $yytext = "<";
        //php $this->unput(substr($tag, 1));
        //php return 'CONTENT';
	%}


<htmlElement><<EOF>>
	%{
		//A tag that was left open, and needs to close
		//php $tag = $this->htmlElementStack[count($this->htmlElementStack) - 1];
		//php $this->htmlElementStack[count($this->htmlElementStack) - 1]['state'] = 'repaired';
		//php $this->unput('</' . $tag['name'] . '>');
		//php return 'CONTENT';
	%}
<htmlElement>{HTML_TAG_CLOSE}
	%{
		//A tag that is open and we just found the close for it
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
		//An tag open
		//php if (JisonParser_Html_Handler::isHtmlTag($yytext) == true) {
		//php   $this->stackHtmlElement($yytext);
		//php       if ($this->htmlElementStackCount == 1) {
		//php           $this->begin('htmlElement');
    	//php           return "HTML_TAG_OPEN";
    	//php       }
    	//php   return 'CONTENT';
    	//php }

    	//A non-valid html tag, return "<" put the rest back into the parser
        //php $tag = $yytext;
        //php $yytext = "<";
        //php $this->unput(substr($tag, 1));
        //php return 'CONTENT';
	%}
{HTML_TAG_CLOSE}
	%{
		//A tag that was not opened, needs to be ignored
    	//php return 'CONTENT';
	%}
([A-Za-z0-9 .,?;]+)                         return 'CONTENT';

([ ])                                       return 'CONTENT';
{LINE_END}
	%{
		//php if ($this->htmlElementStackCount == 0 || $this->isStaticTag == true) {
		//php   return 'LINE_END';
		//php }
		//php return 'CONTENT';
	%}
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
 | HTML_TAG_OPEN HTML_TAG_CLOSE
	{
	    //php $$ = $this->toWiki($2);
	}
 ;