{* $Id$ *}

{title help="SearchStats"}{tr}Search stats{/tr}{/title}

<div class="navbar">
	{button href="?clear=1" _text="{tr}Clear Stats{/tr}"}
</div>

{include file='find.tpl'}

<table class="normal">
<tr>
<!-- term -->
<th><a href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'term_desc'}term_asc{else}term_desc{/if}">{tr}Word{/tr}</a></th>

<!-- searched -->
<th>
<a href="tiki-search_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Searched{/tr}</a></th>

<!-- How can we increase the number of items displayed on a page? -->

</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
  <tr class="{cycle}">
    <td class="text">{$channels[user].term}</td>
    <td class="integer">{$channels[user].hits}</td>
  </tr>
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
