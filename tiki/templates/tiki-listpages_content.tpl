{if $cant_pages > 1 or $initial or $find}
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
<a {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?initial={$initials[ini]}{if $offset}&amp;offset={$offset}{/if}&amp;numrows={$maxRecords}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{/ajax_href} class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}{$smarty.server.PHP_SELF}?initial={if $offset}&amp;offset={$offset}{/if}&amp;numrows={$maxRecords}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{/ajax_href} class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

{*  at the moment, the only working option to use the checkboxes for is deleting pages.
    so for now the checkboxes are visible iff $tiki_p_remove is set. Other applications make 
    sense as well (categorize, convert to pdf, etc). Add necessary corresponding permission here:
*}    
{if $tiki_p_remove eq 'y'}              {* ... "or $tiki_p_other_sufficient_condition_for_checkboxes eq 'y'"  *}
  {assign var='checkboxes_on' value='y'}
{else}
  {assign var='checkboxes_on' value='n'}
{/if}

<table class="normal">
<tr>
{if $checkboxes_on eq 'y'}
<form name="checkboxes_on" method="post" action="{$smarty.server.PHP_SELF}">
  <td class="heading">&nbsp;</td>
{/if}
  <td class="heading">&nbsp;</td>
{if $wiki_list_name eq 'y'}
	<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Page{/tr}</a></td>
{/if}
{if $wiki_list_hits eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Hits{/tr}</a></td>
{/if}
{if $wiki_list_lastmodif eq 'y'}
	<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Last mod{/tr}</a></td>
{/if}
{if $wiki_list_creator eq 'y'}
	<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'creator_desc'}creator_asc{else}creator_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Creator{/tr}</a></td>
{/if}

{if $wiki_list_user eq 'y'}
	<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Last author{/tr}</a></td>
{/if}
{if $wiki_list_lastver eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'version_desc'}version_asc{else}version_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Last ver{/tr}</a></td>
{/if}
{if $wiki_list_comment eq 'y'}
	<td class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Com{/tr}</a></td>
{/if}
{if $wiki_list_status eq 'y'}
	<td style="text-align:center;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'flag_desc'}flag_asc{else}flag_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Status{/tr}</a></td>
{/if}
{if $wiki_list_versions eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'versions_desc'}versions_asc{else}versions_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Vers{/tr}</a></td>
{/if}
{if $wiki_list_links eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'links_desc'}links_asc{else}links_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Links{/tr}</a></td>
{/if}
{if $wiki_list_backlinks eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'backlinks_desc'}backlinks_asc{else}backlinks_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Backlinks{/tr}</a></td>
{/if}
{if $wiki_list_size eq 'y'}
	<td style="text-align:right;" class="heading"><a class="tableheading" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}{if $initial}&amp;initial={$initial}{/if}{/ajax_href}>{tr}Size{/tr}</a></td>
{/if}
</tr>
{cycle values="even,odd" print=false}
{section name=changes loop=$listpages}
<tr>
{if $checkboxes_on eq 'y'}
<td class="{cycle advance=false}"><input type="checkbox" name="checked[]" value="{$listpages[changes].pageName|escape}"/></td>
{/if}
<td class="{cycle advance=false}">
{if $tiki_p_edit eq 'y'}
&nbsp;<a class="link" href="tiki-editpage.php?page={$listpages[changes].pageName|escape:"url"}"><img border='0' title='{tr}edit{/tr}' alt='{tr}edit{/tr}' src='pics/icons/page_edit.png' height='16' width='16' /></a>
{/if}
</td>
{if $wiki_list_name eq 'y'}
	<td class="{cycle advance=false}"><a href="tiki-index.php?page={$listpages[changes].pageName|escape:"url"}" class="link" title="{$listpages[changes].pageName}">{$listpages[changes].pageName|truncate:$wiki_list_name_len:"...":true}</a>
	</td>
{/if}
{if $wiki_list_hits eq 'y'}	
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].hits}</td>
{/if}
{if $wiki_list_lastmodif eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].lastModif|tiki_short_datetime}</td>
{/if}
{if $wiki_list_creator eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].creator|userlink}</td>
{/if}

{if $wiki_list_user eq 'y'}
	<td class="{cycle advance=false}">{$listpages[changes].user|userlink}</td>
{/if}
{if $wiki_list_lastver eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].version}</td>
{/if}
{if $wiki_list_comment eq 'y'}
	<td class="{cycle advance=false}">{if $listpages[changes].comment eq ""}&nbsp;{else}{$listpages[changes].comment}{/if}</td>
{/if}
{if $wiki_list_status eq 'y'}
	<td style="text-align:center;" class="{cycle advance=false}">
	{if $listpages[changes].flag eq 'locked'}
		<img src='pics/icons/lock.png' alt='{tr}locked{/tr}' border='0' height='16' width='16' />
	{else}
		<img src='pics/icons/lock_break.png' alt='{tr}unlocked{/tr}' border='0' height='16' width='16' />
	{/if}
	</td>
{/if}
{if $wiki_list_versions eq 'y'}
	{if $feature_history eq 'y' and $tiki_p_wiki_view_history eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}"><a class="link" href="tiki-pagehistory.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].versions}</a></td>
	{else}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].versions}</td>
	{/if}
{/if}
{if $wiki_list_links eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].links}</td>
{/if}
{if $wiki_list_backlinks eq 'y'}
	{if $feature_backlinks eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}"><a class="link" href="tiki-backlinks.php?page={$listpages[changes].pageName|escape:"url"}">{$listpages[changes].backlinks}</a></td>
	{else}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].backlinks}</td>
	{/if}
{/if}
{if $wiki_list_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$listpages[changes].len|kbsize}</td>
{/if}
       {cycle print=false}
</tr>
{sectionelse}
<tr><td colspan="16">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
{if $checkboxes_on eq 'y'}
  <script type='text/javascript'>
  <!--
  // check / uncheck all.
  // in the future, we could extend this to happen serverside as well for the convenience of people w/o javascript.
  // for now those people just have to check every single box
  document.write("<tr><td><input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'checked[]',this.checked)\"/></td>");
  document.write("<td colspan=\"15\"><label for=\"clickall\">{tr}all{/tr}</label></td></tr>");
  //-->                     
  </script>
{/if}
</table>

{if $checkboxes_on eq 'y'} {* what happens to the checked items? *}
  <p align="left"> {*on the left to have it close to the checkboxes*}
  <select name="submit_mult" onchange="this.form.submit();">
    <option value="" selected="selected">{tr}with checked{/tr}:</option>
    {if $tiki_p_remove eq 'y'} 
      <option value="remove_pages" >{tr}remove{/tr}</option>
    {/if}
    {* add here e.g. <option value="categorize" >{tr}categorize{/tr}</option> *}
  </select>                
  <script type='text/javascript'>
  <!--
  // Fake js to allow the use of the <noscript> tag (so non-js-users can still submit)
  //-->
  </script>
  <noscript>
    <input type="submit" value="{tr}ok{/tr}" />
  </noscript>
  </p>
{/if}
</form>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}{if $initial}&amp;initial={$initial}{/if}{if $find}&amp;find={$find|escape}{/if}{if $exact_match eq 'y'}&amp;exact_match=on{/if}{/ajax_href}>{tr}prev{/tr}</a>]
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
[<a class="prevnext" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}{if $initial}&amp;initial={$initial}{/if}{if $find}&amp;find={$find|escape}{/if}{if $exact_match eq 'y'}&amp;exact_match=on{/if}{/ajax_href}>{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" {ajax_href template="tiki-listpages_content.tpl" htmlelement="tiki-listpages-content"}tiki-listpages.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}{if $initial}&amp;initial={$initial}{/if}{if $find}&amp;find={$find|escape}{/if}{if $exact_match eq 'y'}&amp;exact_match=on{/if}{/ajax_href}>
{$smarty.section.foo.index_next}</a>
{/section}
{/if}
</div>
