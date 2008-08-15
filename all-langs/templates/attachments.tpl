{* $Id$ *}

<a name="attachments"></a>
{* Don't even generate DIV if no any needed rights *}
{if $tiki_p_wiki_view_attachments == 'y'
 || $tiki_p_wiki_admin_attachments == 'y'
 || $tiki_p_wiki_attach_files == 'y'}
{if (isset($smarty.session.tiki_cookie_jar.show_attzone) and $smarty.session.tiki_cookie_jar.show_attzone eq 'y')
or (!isset($smarty.session.tiki_cookie_jar.show_attzone) and $prefs.w_displayed_default eq 'y')}
<div id="attzone" style="display:block;">
{else}
<div id="attzone" style="display:none;">
{/if}

{* Generate table if view permissions granted
 * and if count of attached files > 0
 *}

{if ($tiki_p_wiki_view_attachments == 'y'
  || $tiki_p_wiki_admin_attachments == 'y') 
  && count($atts) > 0}

 <table class="normal">
 <caption> {tr}List of attached files{/tr} </caption>
 <tr>
  <td class="heading">&nbsp;</td><td class="heading"><a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'attId_desc'}attId_asc{else}attId_desc{/if}&amp;atts_show=y#attachments">{tr}id{/tr}</a></td>
  <td class="heading">
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}&amp;atts_show=y#attachments">{tr}Name{/tr}</a>
  </td><td class="heading">&nbsp;
  </td><td class="heading">
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'comment_desc'}comment_asc{else}comment_desc{/if}&amp;atts_show=y#attachments">{tr}desc{/tr}</a>
  </td><td class="heading">
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}&amp;atts_show=y#attachments">{tr}uploaded{/tr}</a>
  </td><td class="heading">
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}&amp;atts_show=y#attachments">{tr}Size{/tr}</a>
  </td><td class="heading">
   <a class="tableheading" href="tiki-index.php?page={$page|escape:"url"}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}&amp;atts_show=y#attachments">{tr}Downloads{/tr}</a>
  </td>
 </tr>
{cycle values="odd,even" print=false advance=false}
{section name=ix loop=$atts}
<tr>
<td class="{cycle advance=false}">{$smarty.section.ix.index_next}</td>
<td class="{cycle advance=false}">{$atts[ix].attId}</td>
<td class="{cycle advance=false}">
 {$atts[ix].filename|iconify}
 <a class="tablename" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}&amp;page={$page|escape:"url"}">{$atts[ix].filename}</a>
 </td><td class="{cycle advance=false}">
  <a title="{tr}View{/tr}" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}" target="_blank">{icon _id='monitor' alt="{tr}View{/tr}"}</a>
  <a title="{tr}Download{/tr}" href="tiki-download_wiki_attachment.php?attId={$atts[ix].attId}&amp;download=y">{icon _id='disk' alt="{tr}Download{/tr}"}</a> &nbsp;
 {if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
  <a title="{tr}Delete{/tr}" class="link" href="tiki-index.php?page={$page|escape:"url"}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}{if !empty($sort_mode)}&amp;sort_mode={$sort_mode}{/if}"{if !empty($target)} target="{$target}"{/if}>{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
 {/if}
 </td>
 <td class="{cycle advance=false}"><small>{$atts[ix].comment}</small></td>
 <td class="{cycle advance=false}"><small>{$atts[ix].created|tiki_short_datetime}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user|userlink}{/if}</small></td>
 <td style="text-align:right;" class="{cycle advance=false}">{$atts[ix].filesize|kbsize}</td>
 <td style="text-align:right;" class="{cycle}">{$atts[ix].hits}</td>
</tr>
{/section}
</table>
{/if}{* Generate table if view ... attached files > 0 *}

{* It is allow to attach files or current user have admin rights *}

{if ($tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y') and $attach_box ne 'n'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page|escape:"url"}" method="post">
{if $page_ref_id}<input type="hidden" name="page_ref_id" value="{$page_ref_id}" />{/if}
<table class="normal">
<tr class="formcolor">
 <td>
   <label for="attach-upload">{tr}Upload file{/tr}:</label><input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
                        <input size="16 " name="userfile1" type="file" id="attach-upload" />
   <label for="attach-comment">{tr}Comment{/tr}:    </label><input type="text" name="attach_comment" maxlength="250" id="attach-comment" />
                        <input type="submit" name="attach" value="{tr}Attach{/tr}"/>
 </td>
</tr>
</table>
</form>
{/if}

</div>
{/if}
