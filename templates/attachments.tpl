{* $Header: /cvsroot/tikiwiki/tiki/templates/attachments.tpl,v 1.16 2004-02-05 10:34:47 damosoft Exp $ *}

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
 <tr class="heading">
  <td>
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a>
  </td><td>
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}">{tr}desc{/tr}</a>
  </td><td>
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a>
  </td><td>
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a>
  </td><td>
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}"><b>&gt;</b></a>
  </td>
 </tr>
{cycle values="odd,even" print=false advance=false}
{section name=ix loop=$atts}
<tr>
<td class="{cycle advance=false}"><span class="mini">{$smarty.section.ix.index_next}</span></td>
<td class="{cycle advance=false}">
 {$atts[ix].filename|iconify}
 <a class="tablename" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename}</a>
 {if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
  &nbsp;&nbsp;<a class="link" href="tiki-index.php?page={$page|escape:"url"}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this attachment?{/tr}')"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>&nbsp;&nbsp;
 {/if}
 </td>
 <td class="{cycle advance=false}"><small>{$atts[ix].comment}</small></td>
 <td class="{cycle advance=false}"><small>{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</small></td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].filesize|kbsize}</td>
 <td style="text-align:right;" class="{cycle}">{$atts[ix].downloads}</td>
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
