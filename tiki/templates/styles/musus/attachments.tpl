{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/attachments.tpl,v 1.5 2004-01-17 01:11:54 musus Exp $ *}

<a name="attachments"></a>
{* Don't even generate DIV if no any needed rights *}
{if $tiki_p_wiki_view_attachments == 'y'
 || $tiki_p_wiki_admin_attachments == 'y'
 || $tiki_p_wiki_attach_files == 'y'}
<div id="attzone">

{* Generate table if view permissions granted
 * and if count of attached files > 0
 *}

{if ($tiki_p_wiki_view_attachments == 'y'
  || $tiki_p_wiki_admin_attachments == 'y') 
  && count($atts) > 0}

	<table>
		<caption> {tr}List of attached files{/tr} </caption>
		<tr>
			<td  class="heading">
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a>
			</td><td  class="heading">
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a>
			</td><td class="heading-right">
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a>
			</td><td class="heading-right">
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}dls{/tr}</a>
			</td><td  class="heading">
				<a href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}desc{/tr}</a>
			</td>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=ix loop=$atts}
			<tr>
				<td class="{cycle advance=false}">
					{$atts[ix].filename|iconify}
					<a class="tablename" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename}</a>
					{if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
						  <a href="tiki-index.php?page={$page|escape:"url"}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this attachment?{/tr}')" 
title="{tr}Click here to delete this attachment{/tr}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>  
					{/if}
				</td>
				<td class="{cycle advance=false}">
					<small>{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</small>
				</td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].filesize|kbsize}</td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].downloads}</td>
 <td class="{cycle}"><small>{$atts[ix].comment}</small></td>
</tr>
{/section}
</table>
{/if}{* Generate table if view ... attached files > 0 *}

{* It is allow to attach files or current user have admin rights *}

{if $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page|escape:"url"}" method="post">
<table>
<tr>
 <td>
   <label for="attach-upload">{tr}Upload file{/tr}:</label><input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
                        <input size="16" name="userfile1" type="file" />
   <label for="attach-comment">{tr}comment{/tr}:    </label><input type="text" name="attach_comment" maxlength="250"/>
                        <input type="submit" name="attach" value="{tr}attach{/tr}"/>
 </td>
</tr>
</table>
</form>
{/if}{* $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y' *}
</div>
{/if}
