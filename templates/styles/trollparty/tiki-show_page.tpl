<div align="right">
<div class="button2top">
{if $print_page ne 'y'}
{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page}&amp;refresh=1" class="linkbuttop">{tr}cached{/tr}</a>
{/if}

{if !$lock and ($tiki_p_edit eq 'y' or $page|lower eq 'sandbox') and $beingEdited ne 'y'}
<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page}" class="linkbuttop">{tr}edit{/tr}</a>
{/if}

{if $feature_morcego eq "y" && $wiki_feature_3d eq "y"}
<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$wiki_3d_width}, {$wiki_3d_height})" class="linkbuttop">{tr}3d browser{/tr}</a>
{/if}

{if $feature_wiki_print eq 'y'}
<a title="{tr}print{/tr}" href="tiki-print.php?page={$page}" class="linkbuttop">{tr}print{/tr}</a>
{/if}

{if $feature_wiki_pdf eq 'y'}
<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?page={$page}" class="linkbuttop">{tr}pdf{/tr}</a>
{/if}

{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page}&amp;savenotepad=1" class="linkbuttop">{tr}save{/tr}</a>
{/if}

{if $user and $feature_user_watches eq 'y'}
	{if $user_watching_page eq 'n'}
		<a href="tiki-index.php?page={$page}&amp;watch_event=wiki_page_changed&amp;watch_object={$page}&amp;watch_action=add" class="linkbuttop">{tr}monitor this page{/tr}</a>
	{else}
		<a href="tiki-index.php?page={$page}&amp;watch_event=wiki_page_changed&amp;watch_object={$page}&amp;watch_action=remove" class="linkbuttop">{tr}stop monitoring this page{/tr}</a>
	{/if}
{/if}

{if $feature_backlinks eq 'y' and $backlinks}
<form action="tiki-index.php" method="get">
<select name="page" onchange="page.form.submit()">
<option>{tr}backlinks{/tr}...</option>
{section name=back loop=$backlinks}
<option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
{/section}
</select>
</form>
{/if}

{if $feature_multilingual == 'y'}{include file="translated-lang.tpl" div='y'}{/if}
{/if}
</div>
</div>

{if $feature_wiki_pageid eq 'y'}
	<small><a class="link" href="tiki-index.php?page_id={$page_id}">{tr}page id{/tr}: {$page_id}</a></small>
{/if}

{if $feature_wiki_description eq 'y'}
<div class="toptitledesc">
{if $lock}
<img src="pics/icons/lock.png" height="16" width="16" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
{/if}
{if $feature_page_title eq 'y'}
<a href="tiki-index.php?page={$page}" class="link" style="font-weight:bold;">{$page}</a> :: 
{/if}
{$description}</div>
{/if}

<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table width='100%'>
{foreach from=$struct_prev_next item=struct name=str key=key}
	<tr>
		<td width='33%'>
			{if $struct.prev_page}
				<a class="tocnavlink" href="tiki-index.php?page={$struct.prev_page}&amp;structID={$key}">&lt;&lt; 
					{if $struct.prev_page_alias}
						{$struct.prev_page_alias}
					{else}
						{$struct.prev_page}
					{/if} 
				</a>

			{else}
				&nbsp;
			{/if}
		</td>
		<td align='center' width='33%'>
{*			<a class="tocnavlink" href="tiki-index.php?page=">{$key}</a> *}
			{$key}
		</td>
		<td align='right' width='33%'>
			{if $struct.next_page}
				<a class="tocnavlink" href="tiki-index.php?page={$struct.next_page}&amp;structID={$key}">
					{if $struct.next_page_alias}
						{$struct.next_page_alias}
					{else}
						{$struct.next_page}
					{/if} 
					&gt;&gt;
				</a>
			{else}
				&nbsp;
			{/if}</td>
	</tr>
{/foreach}
</table>
</div>
{/if}
{if $feature_wiki_ratings eq 'y'}{include file="poll.tpl"}{/if}
{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?page={$page}&amp;pagenum={$first_page}"><img src='pics/icons/resultset_first.png' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' width='16' height='16' /></a>
		<a href="tiki-index.php?page={$page}&amp;pagenum={$prev_page}"><img src='pics/icons/resultset_previous.png' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' width='16' height='16' /></a>
		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>
		<a href="tiki-index.php?page={$page}&amp;pagenum={$next_page}"><img src='pics/icons/resultset_next.png' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' width='16' height='16' /></a>
		<a href="tiki-index.php?page={$page}&amp;pagenum={$last_page}"><img src='pics/icons/resultset_last.png' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' width='16' height='16' /></a>
	</div>
{/if}
</div> {* End of main wiki page *}

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

{if isset($wiki_authors_style) && $wiki_authors_style eq 'business'}
<p class="editdate">
  {tr}Last edited by{/tr} {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if $smarty.section.author.first}, {tr}based on work by{/tr}
   {else}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />                                         
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'collaborative'}
<p class="editdate">
  {tr}Contributors to this page{/tr}: {$lastUser|userlink}
  {section name=author loop=$contributors}
   {if !$smarty.section.author.last},
   {else} {tr}and{/tr}
   {/if}
   {$contributors[author]|userlink}
  {/section}.<br />
  {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
</p>
{elseif isset($wiki_authors_style) &&  $wiki_authors_style eq 'none'}
{else}
<p class="editdate">
  {tr}Created by{/tr}: {$creator|userlink}
  {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
</p>
{/if}

{if $wiki_feature_copyrights  eq 'y'}
{if $wikiLicensePage == $page}
{if $tiki_p_edit_copyrights eq 'y'}
<p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}click here{/tr}</a>.</p>
{/if}
{else}
<p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$wikiLicensePage}&amp;copyrightpage={$page}">{$wikiLicensePage}</a>.</p>
{/if}
{/if}

{if $print_page eq 'y'}
<div class="editdate" align="center"><p>{tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page={$page|escape:"url"}</p></div>
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}

{if $wiki_extras eq 'y' && $feature_wiki_attachments eq 'y' and $tiki_p_wiki_view_attachments eq 'y'}
{include file=attachments.tpl}
{/if}

{if $feature_wiki_comments and $tiki_p_wiki_view_comments eq 'y'}
{include file=comments.tpl}
{/if}


