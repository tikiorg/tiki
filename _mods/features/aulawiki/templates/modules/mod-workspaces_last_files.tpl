{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}Last Files{/tr}"}
{/if}
{tiki_workspaces_module title=$title name="workspaces_last_files" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
{if $showBar!='n'}
<div class="resourceSelect">
<form name="fileGalSelection" method="post" action="{$ownurl}">
  <input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
  <select name="name" id="name">
  {foreach key=key item=galery from=$fileGals}
  	<option value="{$galery.name}" {if $galery.objId==$selectedGal.objId}selected{/if}>
  	{$galery.name}</option>
      {/foreach}
  </select>
  <input class="edubutton" type="submit" name="refresh" value="{tr}Refresh{/tr}">
  <a class="edubutton" href="./tiki-list_file_gallery.php?galleryId={$selectedGal.objId}">{tr}Go{/tr}</a>
  <a title="{tr}upload file{/tr}" href="./tiki-upload_file.php?galleryId={$selectedGal.objId}"><img src="img/icons2/upload.gif" border="0"  width="20" height="16" alt="{tr}upload file{/tr}" /></a>
</form>
</div>
{/if}

{foreach key=key item=lastFile from=$modLastFiles}
{cycle values="oddcenter,evencenter" assign="parImpar"}
<div class="{$parImpar}">
<a class="linkmodule" href="./tiki-download_file.php?fileId={$lastFile.fileId}">
{$lastFile.filename|iconify} 
{if $lastFile.name != ""}
		{$lastFile.name}
{else}
		{$lastFile.filename}
{/if}
({$lastFile.filesize|kbsize})
{$lastFile.created|tiki_short_date}{if $lastFile.user} {tr}by{/tr} {$lastFile.user}{/if}
</a>
<br/>
{$lastFile.description|truncate:100:"..."}
</div>
<br/>
{/foreach}

<div class="mini">
	{if $prev_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$prev_offset}"><img src="./img/icons2/nav_dot_right.gif" border="0" alt="{tr}prev files{/tr}"/></a>
	{/if}
	{$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$next_offset}"><img src="./img/icons2/nav_dot_left.gif" border="0" alt="{tr}next files{/tr}"/></a>
	{/if}
</div>
{/tiki_workspaces_module}

