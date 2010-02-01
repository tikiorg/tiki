<a class="fg-gallery" onclick="FileGallery.open('tiki-list_file_gallery.php?galleryId={$x.id}&filegals_manager={$filegals_manager}&view={$view}')">{$x.name}</a>
<div class="fg-gallery-kids">
	{foreach from=$x.data item=x}
		{include file='file_galleries_item.tpl'}
	{/foreach}
</div>
