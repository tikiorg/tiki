 
{* $Id$ *}
<!-- [tree data=$tree.data expanded=$expanded] -->

<a class="fg-gallery fg-gallery-open" onclick="FileGallery.open('tiki-list_file_gallery.php?filegals_manager={$filegals_manager}&view={$view}')">{tr}Root{/tr}</a>
<div class="fg-gallery-kids">
	{foreach from=$tree.data item=x}
		{include file='file_galleries_item.tpl'}
	{/foreach}
</div>