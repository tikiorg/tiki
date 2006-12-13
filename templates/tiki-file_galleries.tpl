{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-file_galleries.tpl,v 1.49 2006-12-13 14:40:39 sylvieg Exp $ *}

<h1><a class="pagetitle" href="tiki-file_galleries.php?galleryId={$galleryId}{if isset($edit_mode)}&amp;edit_mode=1{/if}">{tr}File Galleries{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-file_galleries.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}File Galleries tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}</h1>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=fgal"><img src='pics/icons/wrench.png' border='0' alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
<br /><br />
{/if}

{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h2>{tr}Create a file gallery{/tr}</h2>
{else}
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new file gallery{/tr}</a>
<h3>{tr}Edit this file gallery:{/tr} {$name}</h3>
{/if}
{if $individual eq 'y'}
<br /><a class="fgallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this file gallery{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-file_galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}"/> ({tr}required field for podcasts{/tr})</td></tr>
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
<!--<tr><td>{tr}Theme{/tr}:</td><td><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
<tr><td class="formcolor">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="formcolor"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
<tr>
	<td class="formcolor">{tr}Listing configuration{/tr}</td>
	<td class="formcolor">
		<table >
			<tr>
				<td class="formcolor">{tr}icon{/tr}</td>
				<td class="formcolor">{tr}id{/tr}</td>
				<td class="formcolor">{tr}name{/tr}</td>
				<td class="formcolor">{tr}size{/tr}</td>
				<td class="formcolor">{tr}description{/tr}</td>
				<td class="formcolor">{tr}downloads{/tr}</td>
				<td class="formcolor">{tr}locked{/tr}<br /></td>
			</tr>
			<tr>
				<td class="formcolor"><input type="checkbox" name="show_icon" {if $show_icon eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_id" {if $show_id eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor">
					<select name="show_name">
						<option value="a" {if $show_name eq 'a'}selected="selected"{/if}>{tr}Name-filename{/tr}</option>
						<option value="n" {if $show_name eq 'n'}selected="selected"{/if}>{tr}Name{/tr}</option>
						<option value="f" {if $show_name eq 'f'}selected="selected"{/if}>{tr}Filename only{/tr}</option>
					</select>
				</td>
				<td class="formcolor"><input type="checkbox" name="show_size" {if $show_size eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_description" {if $show_description eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_dl" {if $show_dl eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_lockedby" {if $show_lockedby eq 'y'} checked="checked"{/if} /></td>
			</tr>
		</table>
		<table>
			<tr>
				<td class="formcolor">{tr}lastMod{/tr}</td>
				<td class="formcolor">{tr}created{/tr}</td>
				<td class="formcolor">{tr}creator{/tr}</td>
				<td class="formcolor">{tr}author{/tr}</td>
			</tr>
			<tr>
				<td class="formcolor"><input type="checkbox" name="show_modified" {if $show_modified eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_created" {if $show_created eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_creator" {if $show_creator eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_author" {if $show_author eq 'y'} checked="checked"{/if} /><i>{tr}If creator is not checked, will display creator if author not set{/tr}</td>
			</tr>
		</table>
	</td>
</tr>
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
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Other users can upload files to this gallery{/tr}:</td><td class="formcolor"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">{tr}The files can be locked at download:{/tr} </td><td class="formcolor"><input type="checkbox" name="lockable" {if $lockable eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">{tr}Maximum number of archives for each file{/tr}: </td><td class="formcolor"><input size="5" type="text" name="archives" value="{$archives|escape}" /> <i>(0={tr}unlimited{/tr}) (-1={tr}none{/tr})</i></td></tr>

<tr><td class="formcolor">{tr}Parent gallery{/tr}:</td><td class="formcolor"><select name="parentId">
<option value="-1" {if $parentId == -1} selected="selected"{/if}>{tr}none{/tr}</option>
{foreach from=$allGalleries key=key item=item}
<option value="{$item.galleryId}" {if $parentId == $item.galleryId} selected="selected"{/if}>{$item.name|escape}</option>
{/foreach}
</select>
</td></tr>
{if $tiki_p_admin eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
<tr><td class="formcolor">{tr}Owner of the gallery{/tr}:</td><td class="formcolor">
<select name="user">
{section name=ix loop=$users}<option value="{$users[ix].login|escape}"{if $creator eq $users[ix].login}  selected="selected"{/if}>{$users[ix].login|escape}</option>{/section}
</select>
</td></tr>
{/if}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}save{/tr}" name="edit" /></td></tr>
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

<h2>{tr}Available File Galleries{/tr}</h2>
{if $tiki_p_create_file_galleries eq 'y'}
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new file gallery{/tr}</a><br /><br />
{/if}

{include file="file_galleries.tpl"}
