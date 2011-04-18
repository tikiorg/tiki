{* $Id$ *}
{title help="Draw"}{$title}{/title}

<div style="text-align: center;">
	<div id="svg-editHeaderLeft" style="position: absolute; left: 7px;top: 5px;">
		<button id="tiki-draw_save" onclick="window.saveSvg();">{tr}Save{/tr}</button>
	</div>
	
	<div id="svg-editHeaderRight" style="position: absolute; right: 15px;top: 5px;">
		<button id="tiki-draw_back" onclick="document.location = 'tiki-list_file_gallery.php?galleryId={$galleryId}'">{tr}Back{/tr}</button>
	</div>
	
	<iframe src="lib/svg-edit/svg-editor.html" id="svgedit"></iframe>
	
	
</div>    