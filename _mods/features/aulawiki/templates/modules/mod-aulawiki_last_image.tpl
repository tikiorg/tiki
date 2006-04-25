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

{tikimodule title=$title name="aulawiki_last_image" flip=$module_params.flip decorations=$module_params.decorations}
{include file="aulawiki-module_error.tpl" error=$error_msg}
<form name="imageGalSelection" method="post" action="./tiki-browse_gallery.php">
  <select name="galleryId" id="galleryId">
  {foreach key=key item=galery from=$imageGals}
  	<option value="{$galery.objId}">
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

{/tikimodule}
{/if}
