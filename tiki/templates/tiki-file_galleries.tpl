<a class="pagetitle" href="tiki-file_galleries.php?galleryId={$galleryId}">{tr}File Galleries{/tr}</a><br/><br/>
{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
<h3>{tr}Create or edit a file gallery using this form{/tr}</h3>
<div  align="center">
{if $individual eq 'y'}
<a class="fgallink" href="tiki-objectpermissions.php?objectName=file%20gallery%20{$name}&amp;objectType=file%20gallery&amp;permType=file%20galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this file gallery{/tr}</a>
{/if}
<form action="tiki-file_galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId}" />
<table class="editfgalform">
<tr><td class="editfgalform">{tr}Name{/tr}:</td><td class="editfgalform"><input type="text" name="name" value="{$name}"/></td></tr>
<tr><td class="editfgalform">{tr}Description{/tr}:</td><td class="editfgalform"><textarea rows="5" cols="40" name="description">{$description}</textarea></td></tr>
<!--<tr><td>{tr}Theme{/tr}:</td><td><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
{if $tiki_p_admin_file_galleries eq 'y'}       
<tr><td class="editfgalform">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="editfgalform"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
{/if}
<tr><td class="editfgalform">{tr}Max Rows per page{/tr}:</td><td class="editfgalform"><input type="text" name="maxRows" value="{$maxRows}" /></td></tr>
{include file=categorize.tpl}
<tr><td class="editfgalform">{tr}Other users can upload files to this gallery{/tr}:</td><td class="editfgalform"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="editfgalform">&nbsp;</td><td class="editfgalform"><input type="submit" value="{tr}edit/create{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br/>
{/if}
{/if}
{if $galleryId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the file gallery using the following URL{/tr}: <a class="fgallink" href="{$url}?galleryId={$galleryIdl}">{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}
<h2>{tr}Available File Galleries{/tr}</h2>
<a class="fgallink" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new gallery{/tr}</a><br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-file_galleries.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="listfgal">
<tr>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'files_desc'}files_asc{else}files_desc{/if}">{tr}Files{/tr}</a></td>
<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
<td class="listfgalheading">{tr}Actions{/tr}</td>
</tr>
{section name=changes loop=$galleries}
<tr>
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
{if $smarty.section.changes.index % 2}
<td class="listfgalnameodd">
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}">
{/if}
{$galleries[changes].name}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
</a>
{/if}
&nbsp;</td>
<td class="listfgaldescriptionodd">{$galleries[changes].description}&nbsp;</td>
<td class="listfgalcreatedodd">{$galleries[changes].created|tiki_short_datetime}&nbsp;</td>
<td class="listfgallastModifodd">{$galleries[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<!--<td class="odd">{$galleries[changes].theme}&nbsp;</td>-->
<td class="listfgaluserodd">{$galleries[changes].user}&nbsp;</td>
<td class="listfgalfilesodd">{$galleries[changes].files}&nbsp;</td>
<td class="listfgalvisitsodd">{$galleries[changes].hits}&nbsp;</td>
<td class="listfgalactionsodd">
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">Edit</a>
<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">Remove</a>
{/if}
{/if}
{if $tiki_p_upload_files eq 'y'}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_files eq 'y' ) }
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
<a class="fgallink" href="tiki-upload_file.php?galleryId={$galleries[changes].id}">Upload</a>
{/if}
{/if}
{/if}
{if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}({/if}<a class="fgallink" href="tiki-objectpermissions.php?objectName=file%20gallery%20{$galleries[changes].name}&amp;objectType=file%20gallery&amp;permType=file%20galleries&amp;objectId={$galleries[changes].id}">{tr}perms{/tr}</a>{if $galleries[changes].individual eq 'y'}){/if}
{/if}
</td>
{else}
<td class="listfgalnameeven">
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}">
{/if}
{$galleries[changes].name}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
</a>
{/if}
&nbsp;</td>
<td class="listfgaldescriptioneven">{$galleries[changes].description}&nbsp;</td>
<td class="listfgalcreatedeven">{$galleries[changes].created|tiki_short_datetime}&nbsp;</td>
<td class="listfgallastModifeven">{$galleries[changes].lastModif|tiki_short_datetime}&nbsp;</td>
<td class="listfgalusereven">{$galleries[changes].user}&nbsp;</td>
<td class="listfgalfileseven">{$galleries[changes].files}&nbsp;</td>
<td class="listfgalvisitseven">{$galleries[changes].hits}&nbsp;</td>
<td class="listfgalactionseven">
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}">{tr}Edit{/tr}</a> 
<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}">{tr}Remove{/tr}</a>
{/if}
{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
{if $tiki_p_upload_files eq 'y'}
{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_files eq 'y' ) }
<a class="fgallink" href="tiki-upload_file.php?galleryId={$galleries[changes].id}">Upload</a>
{/if}
{/if}
{/if}
{if $tiki_p_admin eq 'y'}
    {if $galleries[changes].individual eq 'y'}({/if}<a class="fgallink" href="tiki-objectpermissions.php?objectName=file%20gallery%20{$galleries[changes].name}&amp;objectType=file%20gallery&amp;permType=file%20galleries&amp;objectId={$galleries[changes].id}">{tr}perms{/tr}</a>{if $galleries[changes].individual eq 'y'}){/if}
{/if}
</td>
{/if}
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
