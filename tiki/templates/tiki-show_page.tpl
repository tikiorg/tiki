{if $feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page}" class="pagetitle">{$page}</a>
{if $lock}
<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
{/if}
</h1>{/if}
<table width="100%">
<tr>
<td>
{if $feature_wiki_description}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}
</td>
<td style="text-align:right;">



{if !$lock and ($tiki_p_edit eq 'y' or $page eq 'SandBox') and $beingEdited ne 'y'}
<a title="{tr}edit{/tr}" href="tiki-editpage.php?page={$page}"><img border="0" src="img/icons/edit.gif" alt='{tr}edit{/tr}' /></a>
{/if}


{if $feature_backlinks eq 'y'}
<a title="{tr}backlinks{/tr}" href="tiki-backlinks.php?page={$page}"><img border="0" src="img/icons/ico_link.gif" alt='{tr}backlinks{/tr}' /></a>
{/if}

{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page}&amp;refresh=1"><img border="0" src="img/icons/ico_redo.gif" alt='{tr}refresh{/tr}' /></a>
{/if}

<a title="{tr}print{/tr}" href="tiki-print.php?page={$page}"><img border="0" src="img/icons/ico_print.gif" alt='{tr}print{/tr}' /></a>

{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page}&amp;savenotepad=1"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
{/if}

{if $user and $feature_user_watches eq 'y'}
	{if $user_watching_page eq 'n'}
		<a href="{sameurl watch_event='wiki_page_changed' watch_object=$page watch_action='add'}"><img border='0' alt='{tr}monitor this page{/tr}' title='{tr}monitor this page{/tr}' src='img/icons/icon_watch.png' /></a>
	{else}
		<a href="{sameurl watch_event='wiki_page_changed' watch_object=$page watch_action='remove'}"><img border='0' alt='{tr}stop monitoring this page{/tr}' title='{tr}stop monitoring this page{/tr}' src='img/icons/icon_unwatch.png' /></a>
	{/if}
{/if}

</td>
</tr>
</table>
<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table width='100%'><tr><td width='33%'>{if $struct_prev}<a class="tocnavlink" href="tiki-index.php?page={$struct_prev}">&lt;&lt; {$struct_prev}</a>{else}&nbsp;{/if}</td><td align='center' width='33%'><a class="tocnavlink" href="tiki-index.php?page={$struct_struct}">{$struct_struct}</a></td><td align='right' width='33%'>{if $struct_next}<a class="tocnavlink" href="tiki-index.php?page={$struct_next}">{$struct_next} &gt;&gt;</a>{else}&nbsp;{/if}</td></tr></table>
</div>
{/if}{$parsed}</div>

{if $has_footnote eq 'y'}
<div class="wikitext">
{$footnote}
</div>
{/if}

<p class="editdate">{tr}Created by{/tr}: {$creator|userlink} {tr}last modification{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}</p>
{if $wiki_extras eq 'y'}
<br/>
{if $feature_wiki_attachments eq 'y'}
{if $tiki_p_wiki_view_attachments eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<table class="normal">
<tr> 
  <td width="28%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a></td>
  <td width="27%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a></td>
  <td style="text-align:right;"  width="10%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a></td>
  <td style="text-align:right;"  width="10%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}dls{/tr}</a></td>
  <td width="25%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}desc{/tr}</a></td>
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
 <td colspan="5">{tr}No attachments for this page{/tr}</td>
</tr>
{/section}
</table>
{if $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page}" method="post">
<table class="normal">
<tr>
 <td class="formcolor">{tr}Upload file{/tr}:<input type="hidden" name="MAX_FILE_SIZE" value="1000000000"><input  style="font-size:9px;" size="16
 " name="userfile1" type="file">
 {tr}comment{/tr}: <input  style="font-size:9px;"  type="text" name="attach_comment" maxlength="250" />
 <input style="font-size:9px;" type="submit" name="attach" value="{tr}attach{/tr}" />
 </td>
</tr>
</table>
</form>
{/if}
{/if}
{/if}
{if $feature_wiki_comments eq 'y'}
{include file=comments.tpl}
{/if}
{/if}
