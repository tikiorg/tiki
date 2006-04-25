{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $title==""}
{assign var=title value="{tr}Last Files{/tr}"}
{/if}
{tikimodule title=$title name="aulawiki_last_files" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
<div class="resourceSelect">
<form name="fileGalSelection" method="post" action="./tiki-list_file_gallery.php">
  <label for="galleryId">{tr}Galleries{/tr}:</label>
  <select name="galleryId" id="galleryId">
  {foreach key=key item=galery from=$fileGals}
  	<option value="{$galery.objId}">
  	{$galery.name}</option>
      {/foreach}
  </select>
  <input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
</form>
</div>

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

{/tikimodule}

