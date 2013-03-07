{* $Id$ *}<!DOCTYPE html>
<html id="print" xmlns="http://www.w3.org/1999/xhtml" xml:lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}" lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}">
	<head>
{include file='header.tpl'}
	</head>
	<body{html_body_attributes}>

<div id="tiki-clean">
  {section name=ix loop=$pages}
    {if $prefs.feature_page_title ne 'n'}<h{math equation="x+1" x=$pages[ix].h}>{if isset($pages[ix].pos)}{$pages[ix].pos} {/if}{$pages[ix].pageName}</h{math equation="x+1" x=$pages[ix].h}>{/if}
    <div class="wikitext">{$pages[ix].parsed}</div>
  <hr />
  {/section}
</div>
{include file='footer.tpl'}
	</body>
</html>
