<h1>{tr}File Galleries{/tr}</h1>
{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
<h3>{tr}Create or edit a file gallery using this form{/tr}</h3>
<div  align="center">
<table border="1" cellspacing="0" cellpadding="0"><tr><td>
<form action="tiki-file_galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId}" />
<table>
<tr><td class="form">{tr}Name{/tr}:</td><td class="form"><input type="text" name="name" value="{$name}"/></td></tr>
<tr><td class="form">{tr}Description{/tr}:</td><td class="form"><textarea rows="5" cols="40" name="description">{$description}</textarea></td></tr>
<!--<tr><td>{tr}Theme{/tr}:</td><td><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
<tr><td class="form">{tr}Max Rows per page{/tr}:</td><td class="form"><input type="text" name="maxRows" value="{$maxRows}" /></td></tr>
<tr><td class="form">{tr}Other users can upload files to this gallery{/tr}:</td><td class="form"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td>&nbsp;</td><td class="form"><input type="submit" value="{tr}edit/create{/tr}" name="edit" /></td></tr>
</table>
</form>
</td></tr></table>
</div>
<br/>
{/if}
{/if}
{if $galleryId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the file gallery using the following URL{/tr}: <a href="http://{$url}?galleryId={$galleryIdl}">http://{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}
<h1><a class="pagetitle" href="tiki-file_galleries.php">{tr}Available File Galleries{/tr}</a></h1>
<a class="link" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">create new gallery</a><br/><br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-file_galleries.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
<!--<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'theme_desc'}theme_asc{else}theme_desc{/if}">{tr}Theme{/tr}</a></td>-->
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'files_desc'}files_asc{else}files_desc{/if}">{tr}Files{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="heading">Actions</td>
</tr>
{section name=changes loop=$galleries}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">{$galleries[changes].name}&nbsp;</td>
<td class="odd">{$galleries[changes].description}&nbsp;</td>
<td class="odd">{$galleries[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">{$galleries[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<!--<td class="odd">{$galleries[changes].theme}&nbsp;</td>-->
<td class="odd">{$galleries[changes].user}&nbsp;</td>
<td class="odd">{$galleries[changes].files}&nbsp;</td>
<td class="odd">{$galleries[changes].hits}&nbsp;</td>
<td class="odd">
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
<a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">Edit</a>
<a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">Remove</a>
{/if}
{if $tiki_p_upload_files eq 'y'}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
<a class="link" href="tiki-upload_file.php?galleryId={$galleries[changes].id}">Upload</a>
{/if}
{/if}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}">{tr}List{/tr}</a>
</td>
{else}
<td class="even">{$galleries[changes].name}&nbsp;</td>
<td class="even">{$galleries[changes].description}&nbsp;</td>
<td class="even">{$galleries[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">{$galleries[changes].lastModif|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<!--<td class="even">{$galleries[changes].theme}&nbsp;</td>-->
<td class="even">{$galleries[changes].user}&nbsp;</td>
<td class="even">{$galleries[changes].files}&nbsp;</td>
<td class="even">{$galleries[changes].hits}&nbsp;</td>
<td class="even">
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
<a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">{tr}Edit{/tr}</a> 
<a class="link" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">{tr}Remove{/tr}</a>
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
<a class="link" href="tiki-upload_file.php?galleryId={$galleries[changes].id}">Upload</a>
{/if}
<a class="link" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}">{tr}List{/tr}</a></td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-file_galleries.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-file_galleries.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
