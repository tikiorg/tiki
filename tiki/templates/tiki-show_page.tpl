{if $feature_page_title eq 'y'}<h1><a  href="tiki-index.php?page={$page}" class="pagetitle">{$page}</a></h1>{/if}
{if $feature_wiki_description}
<small>{$description}</small>
{/if}
<div class="wikitext">{$parsed}</div>
<p class="editdate">{tr}Last modification date{/tr}: {$lastModif|date_format:"%A %d of %B, %Y [%H:%M:%S]"} by {$lastUser}</p>
{if $wiki_extras eq 'y'}
<br/>
{if $feature_wiki_attachments eq 'y'}
{if $tiki_p_wiki_view_attachments eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<table class="normal">
<tr> 
  <td width="28%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filename_desc'}filename_asc{else}filename_desc{/if}">{tr}name{/tr}</a></td>
  <td width="27%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}uploaded{/tr}</a></td>
  <td width="10%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}size{/tr}</a></td>
  <td width="10%" class="heading"><a class="tableheading" href="tiki-index.php?page={$page}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'downloads_desc'}downloads_asc{else}downloads_desc{/if}">{tr}dls{/tr}</a></td>
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
 <td class="{cycle advance=false}">{$atts[ix].created|date_format:"%d of %b [%H:%M]"}{if $atts[ix].user} {tr}by{/tr} {$atts[ix].user}{/if}</td>
 <td class="{cycle advance=false}">{$atts[ix].filesize}</td>
 <td class="{cycle advance=false}">{$atts[ix].downloads}</td>
 <td class="{cycle}">{$atts[ix].comment}</td>
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
 <td class="formcolor">{tr}Upload file{/tr}:<input type="hidden" name="MAX_FILE_SIZE" value="1000000000"><input name="userfile1" type="file">
 {tr}comment{/tr}: <input type="text" name="attach_comment" maxlenght="250" />
 <input type="submit" name="attach" value="{tr}attach{/tr}" />
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