{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-print_multi_pages.tpl,v 1.5.10.2 2007-10-24 17:36:22 sylvieg Exp $ *}

<div id="tiki-clean">
  {section name=ix loop=$pages}
    {if $prefs.feature_page_title ne 'n'}<h{math equation="x+1" x=$pages[ix].h}>{if isset($pages[ix].pos)}{$pages[ix].pos} {/if}{$pages[ix].pageName}</h{math equation="x+1" x=$pages[ix].h}>{/if}
    <div class="wikitext">{$pages[ix].parsed}</div>
  <hr />
  {/section}
</div>
{include file="footer.tpl"}
