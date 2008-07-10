<?php

//include ("common.inc");

include ("htmlgrammarparser.inc");
$p = new HtmlGrammarParser("htmlgrammar.dat");
$p->Parse();
$p->PrintErrors();
$p->SaveGrammar("htmlgrammar.cmp");
print "Done.";
//PrintArray($p->pg);

?>
