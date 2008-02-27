{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-file_galleries.tpl,v 1.66.2.4 2008-02-27 15:18:50 nyloth Exp $ *}
{popup_init src="lib/overlib.js"}
<h1><a class="pagetitle" href="tiki-file_galleries.php{if $galleryId}?galleryId={$galleryId}{if isset($edit_mode) and $edit_mode ne 'n'}&amp;edit_mode=1{/if}{if $filegals_manager eq 'y'}&filegals_manager{/if}{else}{if $filegals_manager eq 'y'}?filegals_manager{/if}{/if}">{tr}File Galleries{/tr}</a>
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">
{icon _id='help'}</a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-file_galleries.tpl{if $filegals_manager eq 'y'}?filegals_manager{/if}" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}File Galleries tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}
{if $tiki_p_admin eq 'y' and $filegals_manager ne 'y'}
<a href="tiki-admin.php?page=fgal">{icon _id='wrench' alt="{tr}Admin Feature{/tr}"}</a>
{/if}
</h1>
{if $filegals_manager eq 'y'}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}Be carefull to set the right permissions on the files you link to{/tr}.</div>
</div>
<br />
{/if}

<div class="navbar">
{if $edit_mode eq 'y' or $dup_mode eq 'y'}
<a class="linkbut" href="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager{/if}">{tr}List Galleries{/tr}</a>
{if $galleryId}<a class="linkbut" href="tiki-list_file_gallery.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}">{tr}Browse Gallery{/tr}</a>{/if}
{/if}
{if $tiki_p_create_file_galleries eq 'y'and $edit_mode ne 'y'}
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}">{tr}Create New File Gallery{/tr}</a>
{/if}

{if $tiki_p_create_file_galleries eq 'y'and $dup_mode ne 'y'}
<a class="linkbut" href="tiki-file_galleries.php?dup_mode=1{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}">{tr}Duplicate File Gallery{/tr}</a>
{/if}
</div>

{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h2>{tr}Create a file gallery{/tr}</h2>
{else}
<h2>{tr}Edit this file gallery:{/tr} {$name}</h2>
{/if}
{if $individual eq 'y'}
<br /><a class="fgallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this file gallery{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager{/if}" method="post">
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" size="50" name="name" value="{$name|escape}"/> ({tr}required field for podcasts{/tr})</td></tr>
<tr><td class="formcolor">{tr}Type{/tr}:</td><td class="formcolor">
					<select name="fgal_type">
						<!-- TODO: make this a configurable list read from database -->
						<option value="default" {if $fgal_type eq 'default'}selected="selected"{/if}>{tr}any file{/tr}</option>
						<option value="podcast" {if $fgal_type eq 'podcast'}selected="selected"{/if}>{tr}podcast (audio){/tr}</option>
						<option value="vidcast" {if $fgal_type eq 'vidcast'}selected="selected"{/if}>{tr}podcast (video){/tr}</option>
					</select>
				</td>
</tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea> ({tr}required field for podcasts{/tr})</td></tr>
<tr><td class="formcolor">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="formcolor"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
<tr><td class="formcolor">{tr}Default sort order{/tr}:</td><td class="formcolor"><select name="sortorder">
{foreach from=$options_sortorder key=key item=item}
<option value="{$item|escape}" {if $sortorder == $item} selected="selected"{/if}>{$key}</option>
{/foreach}
</select>
<input type="radio" name="sortdirection" value="desc" {if $sortdirection == 'desc'}checked="checked"{/if} />{tr}descending{/tr}
<input type="radio" name="sortdirection" value="asc" {if $sortdirection == 'asc'}checked="checked"{/if} />{tr}ascending{/tr}
</td></tr><tr>
	<td class="formcolor">{tr}Max description display size{/tr}</td>
	<td class="formcolor"><input type="text" name="max_desc" value="{$max_desc|escape}" /></td>
</tr>
<tr><td class="formcolor">{tr}Max Rows per page{/tr}:</td><td class="formcolor"><input type="text" name="maxRows" value="{$maxRows|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Listing configuration{/tr}</td><td class="formcolor"><table>
{include file='fgal_listing_conf.tpl'}</table>
</td></tr>
{include file='categorize.tpl'}
<tr><td class="formcolor">{tr}Other users can upload files to this gallery{/tr}:</td><td class="formcolor"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">{tr}The files can be locked at download:{/tr} </td><td class="formcolor"><input type="checkbox" name="lockable" {if $lockable eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">{tr}Maximum number of archives for each file{/tr}: </td><td class="formcolor"><input size="5" type="text" name="archives" value="{$archives|escape}" /> <i>(0={tr}unlimited{/tr}) (-1={tr}none{/tr})</i></td></tr>

<tr><td class="formcolor">{tr}Parent gallery{/tr}:</td><td class="formcolor"><select name="parentId">
<option value="-1" {if $parentId == -1} selected="selected"{/if}>{tr}none{/tr}</option>
{foreach from=$all_galleries key=key item=item}
<option value="{$item.id}" {if $parentId == $item.id} selected="selected"{/if}>{$item.name|escape}</option>
{/foreach}
</select>
</td></tr>
{if $tiki_p_admin eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
<tr><td class="formcolor">{tr}Owner of the gallery{/tr}:</td><td class="formcolor">
<select name="user">
{section name=ix loop=$users}<option value="{$users[ix].login|escape}"{if $creator eq $users[ix].login} selected="selected"{/if}>{$users[ix].login|username}</option>{/section}
</select>
</td></tr>
{/if}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}Save{/tr}" name="edit" /> <input type="checkbox" name="viewitem" checked="checked"/> {tr}View inserted gallery{/tr}</td></tr>
</table>
</form>
</div>
<br />
{/if}
{/if}
{if $galleryId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the file gallery using the following URL{/tr}: <a class="fgallink" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}

{if $tiki_p_create_file_galleries eq 'y' and $dup_mode eq 'y'}
<h2>{tr}Duplicate File Gallery{/tr}</h2>
<form action="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager{/if}" method="post">
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}</td><td><input type="text" size="50" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}</td><td><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr class="formcolor"><td>{tr}File Gallery{/tr}</td>
<td>
<select name="galleryId">
{section name=ix loop=$all_galleries}
<option value="{$all_galleries[ix].id}">{$all_galleries[ix].name}</option>
{/section}
</select>
</td>
</tr>
<tr class="formcolor"><td>{tr}Duplicate categories{/tr}</td><td><input type="checkbox" name="dupCateg" /></td></tr>
<tr class="formcolor"><td>{tr}Duplicate perms{/tr}</td><td><input type="checkbox" name="dupPerms" /></td></tr>
<tr class="formcolor"><td></td><td><input type="submit" name="duplicate" value="{tr}duplicate{/tr}" /></td></tr>
</table>
</form>
{/if}

{if $edit_mode ne 'y' and $dup_mode ne 'y'}
	{include file='find.tpl' find_show_languages='n' find_show_categories='n'}
	{include file='list_file_gallery.tpl'}
{/if}
