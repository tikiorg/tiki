{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/tiki-print_multi_pages.tpl,v 1.1 2003-09-19 19:38:21 luciash Exp $ *}

{include file="header.tpl"}
<div id="tiki-clean">
  <h1>Wiki Pages</h1>
  {section name=ix loop=$pages}
    <h2>{$pages[ix].pageName}</h2>
    <div class="wikitext">{$pages[ix].parsed}</div>
  <hr/>
  {/section}
</div>
{include file="footer.tpl"}
