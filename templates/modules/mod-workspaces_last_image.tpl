{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{if $feature_galleries eq 'y'}
{if $title==""}
{assign var=title value="{tr}Last Images{/tr}"}
{/if}

{tiki_workspaces_module title=$title name="workspaces_last_image" flip=$module_params.flip decorations=$module_params.decorations style_title=$style_title style_data=$style_data}
{include file="tiki-workspaces_module_error.tpl" error=$error_msg}
<form name="imageGalSelection" method="post" action="{$ownurl}">
  <input name="moduleId" type="hidden" id="moduleId" value="{$moduleId}">
  <select name="name" id="name">
  {foreach key=key item=galery from=$imageGals}
  	<option value="{$galery.name}" {if $galery.objId==$selectedGal.objId}selected{/if}>
  	{$galery.name}</option>
      {/foreach}
  </select>
  <input class="edubutton" type="submit" name="go" value="{tr}Go{/tr}">
</form>

{foreach key=key item=lastImage from=$modLastImages}
{cycle values="oddcenter,evencenter" assign="parImpar"}
<div class="{$parImpar}">
<a class="linkmodule" href="tiki-browse_image.php?imageId={$lastImage.imageId}">
<img border=0 src="./show_image.php?id={$lastImage.imageId}&thumb=1"/><br/>
{$lastImage.name}</a>
</div>
<br/>
{/foreach}
<div class="mini">
	{if $prev_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$prev_offset}"><img src="pics/icons/resultset_previous.png" border="0" alt="{tr}prev images{/tr}" width='16' height='16' /></a>
	{/if}
	{$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
	<a class="prevnext" href="{$ownurl}&amp;offset={$next_offset}"><img src="pics/icons/resultset_next.png" border="0" alt="{tr}next images{/tr}" width='16' height='16' /></a>
	{/if}
</div>
{/tiki_workspaces_module}
{/if}
