{* $Header: /cvsroot/tikiwiki/tiki/templates/attachments.tpl,v 1.14 2004-02-04 15:56:13 mose Exp $ *}

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

 <table class="normal">
 <caption> {tr}List of attached files{/tr} </caption>
 <tr>
  <th></th><th>
   <a title="{tr}Click here to sort by name{/tr}" class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a>
  </th><th>
   <a title="{tr}Click here to sort by description{/tr}" class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}desc{/tr}</a>
  </th><th>
   <a title="{tr}Click here to sort by the date uploaded{/tr}" class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a>
  </th><th>
   <a title="{tr}Click here to sort by size{/tr}" class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a>
  </th><th>
   <a title="{tr}Click here to sort by number of downloads{/tr}" class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}"><b>&gt;</b></a>
  </th>
 </tr>
{cycle values="odd,even" print=false advance=false}
{section name=ix loop=$atts}
<tr class="{cycle}">
<td><span class="mini">{$smarty.section.ix.index}</span></td>
<td>
 {$atts[ix].filename|iconify}
 <a title="{tr}Click here to download this file{/tr}" class="tablename" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename}</a>
 {if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
  &nbsp;&nbsp;<a title="{tr}Click here to delete this attachment{/tr}" class="link" href="tiki-index.php?page={$page|escape:"url"}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this attachment?{/tr}')"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>&nbsp;&nbsp;
 {/if}
 </td>
 <td><small>{$atts[ix].comment}</small></td>
 <td><small>{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</small></td>
 <td style="text-align:right;">{$atts[ix].filesize|kbsize}</td>
 <td style="text-align:right;">{$atts[ix].downloads}</td>
</tr>
{/section}
</table>
{/if}{* Generate table if view ... attached files > 0 *}

{* It is allow to attach files or current user have admin rights *}

{if $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page|escape:"url"}" method="post">
{if $page_ref_id}<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />{/if}
<table class="normal">
<tr class="formcolor">
 <td>
   <label for="attach-upload">{tr}Upload file{/tr}:</label><input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
                        <input size="16 " name="userfile1" type="file" id="attach-upload" />
   <label for="attach-comment">{tr}comment{/tr}:    </label><input type="text" name="attach_comment" maxlength="250" id="attach-comment" />
                        <input type="submit" name="attach" value="{tr}attach{/tr}"/>
 </td>
</tr>
</table>
</form>
{/if}

</div>
{/if}
