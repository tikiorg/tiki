{* $Id$ *}
{title help="Draw"}{$name}{/title}

<div id="tiki_draw" style="text-align: center;">
	<div id="svg-data" style="display: none;">{$data}</div>
	
	<input type="hidden" id="svg_file_id" value="{$fileId}" />
	<input type="hidden" id="svg_gallery_id" value="{$galleryId}" />
	<input type="hidden" id="svg_file_name" value="{$name}" />
	<input type="hidden" id="svg_file_width" value="{$width}" />
	<input type="hidden" id="svg_file_height" value="{$hight}" />
	
	<div id="tiki_draw_editor">
		<iframe src="lib/svg-edit/svg-editor.html" id="svgedit"></iframe>
		<div id="svg-menu">
			<button id="tiki-draw_save" style="float left;">{tr}Save{/tr}</button>
			<button id="tiki-draw_rename">{tr}Rename{/tr}</button>
			<button id="tiki-draw_back">{tr}Back{/tr}</button>
			<button id="tiki-draw_fullscreen">{tr}Toggle Fullscreen{/tr}</button>
		</div>
	</div>
</div>    
