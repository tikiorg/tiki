<div align="right">
<div class="button2top">
{if $print_page ne 'y'}
{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page}&amp;refresh=1" class="linkbuttop">{tr}cached{/tr}</a>
{/if}

{if !$lock and ($tiki_p_edit eq 'y' or $page eq 'SandBox') and $beingEdited ne 'y'}
<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page}" class="linkbuttop">{tr}edit{/tr}</a>
{/if}

{if $feature_backlinks eq 'y'}
<a title="{tr}backlinks{/tr}" href="tiki-backlinks.php?page={$page}" class="linkbuttop">{tr}backlinks{/tr}</a>
{/if}

<a title="{tr}print{/tr}" href="tiki-print.php?page={$page}" class="linkbuttop">{tr}print{/tr}</a>

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
{/if}
</div>
</div>

{if $feature_wiki_description eq 'y'}
<div class="toptitledesc">
{if $lock}
<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
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
{/if}{$parsed}
{if $pages > 1}
	<br/>
	<div align="center">
		<a href="tiki-index.php?page={$page}&amp;pagenum={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-index.php?page={$page}&amp;pagenum={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?page={$page}&amp;pagenum={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-index.php?page={$page}&amp;pagenum={$last_page}"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' /></a>
	</div>
{/if}
</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

<p class="editdate">{tr}Created by{/tr}: {$creator|userlink} {tr}last modification{/tr}: <b>{$lastModif|tiki_long_date}</b> {$lastModif|tiki_long_time} {tr}by{/tr} {$lastUser|userlink}
</p>
{if $wiki_feature_copyrights  eq 'y'}
{if $wikiLicensePage == $page}
{if $tiki_p_edit_copyrights eq 'y'}
<p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}click here{/tr}</a>.</p>
{/if}
{else}
<p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$wikiLicensePage}&amp;copyrightpage={$page}">{$wikiLicensePage}</a>.</p>
{/if}
{/if}
{if $wiki_extras eq 'y'}
<br/>
{if $feature_wiki_attachments eq 'y'}
{if $tiki_p_wiki_view_attachments eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<div id="attzone">
<table class="normal">
<tr> 
  <td  class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a></td>
  <td  class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a></td>
  <td style="text-align:right;"   class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a></td>
  <td style="text-align:right;"   class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}dls{/tr}</a></td>
  <td  class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}desc{/tr}</a></td>
</tr> 
{cycle values="odd,even" print=false}
{section name=ix loop=$atts}
<tr>
 <td class="{cycle advance=false}">
 {$atts[ix].filename|iconify}
 <a class="tablename" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename}</a>
 {if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
 <a class="link" href="tiki-index.php?page={$page}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">[x]</a>
 {/if}
 </td>
 <td class="{cycle advance=false}"><small>{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</small></td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].filesize|kbsize}</td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].downloads}</td>
 <td class="{cycle}"><small>{$atts[ix].comment}</small></td>
</tr>
{sectionelse}
<tr>
 <td colspan="19" class="even">{tr}No attachments for this page{/tr}</td>
</tr>
{/section}
</table>
{if $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page}" method="post">
<table class="normal">
<tr>
 <td class="formcolor">{tr}Upload file{/tr}:<input type="hidden" name="MAX_FILE_SIZE" value="1000000000"><input  style="font-size:9px;" size="16" name="userfile1" type="file">
 {tr}comment{/tr}: <input  style="font-size:9px;"  type="text" name="attach_comment" maxlength="250" />
 <input style="font-size:9px;" type="submit" name="attach" value="{tr}attach{/tr}" />
 </td>
</tr>
</table>
</form>
{/if}
</div>
{/if}
{/if}
{if $feature_wiki_comments eq 'y'}
{include file=comments.tpl}
{/if}
{/if}
{if $print_page eq 'y'}
<div class="editdate" align="center"><p>{tr}The original document is available at{/tr} {$urlprefix}tiki-index.php?page={$page|escape:"url"}</p></div>
{/if}
{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}

