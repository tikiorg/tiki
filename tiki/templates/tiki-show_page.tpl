{if $feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page|escape:"url"}" class="pagetitle">{$page}</a>
{if $lock}
<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
{/if}
</h1>{/if}
<table width="100%">
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
<tr>
<td>
{if $feature_wiki_description}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}
</td>
<td align="right">
{$display_catpath}
</td>
</tr>
{else}
<tr>
<td>
{if $feature_wiki_description}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}
</td>
<td>
</td>
</tr>
{/if}
<tr>
<td>
</td>

{if $print_page !== 'y'}
	<td style="text-align:right;">

	{if !$lock and ($tiki_p_edit eq 'y' or $page eq 'SandBox') and $beingEdited ne 'y'}
	<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page|escape:"url"}"><img border="0" src="img/icons/edit.gif" alt='{tr}edit{/tr}' /></a>
	{/if}


	{if $feature_backlinks eq 'y'}
	<a title="{tr}backlinks{/tr}" href="tiki-backlinks.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_link.gif" alt='{tr}backlinks{/tr}' /></a>
	{/if}

	{if $cached_page eq 'y'}
	<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1"><img border="0" src="img/icons/ico_redo.gif" alt='{tr}refresh{/tr}' /></a>
	{/if}

	<a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_print.gif" alt='{tr}print{/tr}' /></a>

	{if $feature_wiki_pdf eq 'y'}
	<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?page={$page|escape:"url"}"><img border="0" src="img/icons/ico_pdf.gif" alt='{tr}pdf{/tr}' /></a>
	{/if}

	{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
	<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
	{/if}

	{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_page eq 'n'}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add"><img border='0' alt='{tr}monitor this page{/tr}' title='{tr}monitor this page{/tr}' src='img/icons/icon_watch.png' /></a>
		{else}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove"><img border='0' alt='{tr}stop monitoring this page{/tr}' title='{tr}stop monitoring this page{/tr}' src='img/icons/icon_unwatch.png' /></a>
		{/if}
	{/if}
	</td>
{/if}

</tr>
</table>
<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table width='100%'><tr><td width='33%'>{if $struct_prev}<a class="tocnavlink" href="tiki-index.php?page={$struct_prev|escape:"url"}">&lt;&lt; {$struct_prev}</a>{else}&nbsp;{/if}</td><td align='center' width='33%'><a class="tocnavlink" href="tiki-index.php?page={$struct_struct|escape:"url"}">{$struct_struct}</a></td><td align='right' width='33%'>{if $struct_next}<a class="tocnavlink" href="tiki-index.php?page={$struct_next|escape:"url"}">{$struct_next} &gt;&gt;</a>{else}&nbsp;{/if}</td></tr></table>
</div>
{/if}{$parsed}
{if $pages > 1}
	<br />
	<div align="center">
		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-index.php?page={$page|escape:"url"}&amp;pagenum={$last_page}"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' /></a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

<p class="editdate">{tr}Created by{/tr}: {$creator|userlink} {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}</p>
{if $wiki_feature_copyrights  eq 'y'}
{if $wikiLicensePage}
{if $wikiLicensePage == $page}
{if $tiki_p_edit_copyrights eq 'y'}
<p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}click here{/tr}</a>.</p>
{/if}
{else}
<p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$wikiLicensePage}</a>.</p>
{/if}
{/if}
{/if}
{if $print_page eq 'y'}
<p><div class="editdate" align="center">{tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page{$page|escape:"url"}</div></p>
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
{$display_catobjects}
{/if}
