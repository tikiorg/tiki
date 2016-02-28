{* $Id$ *}
{if $drawFullscreen neq 'true'}
	{title help="Draw"}{$name}{/title}
{/if}

<form id="tiki_draw" class="submit no-ajax" style="text-align: center;" onsubmit="return false;">
	<span style="display: none;">
		<textarea id="fileData">{$data}</textarea>
	</span>

	<input type="hidden" id="fileId" value="{$fileId}">
	<input type="hidden" id="galleryId" value="{$galleryId}">
	<input type="hidden" id="fileName" value="{$name}">
	<input type="hidden" id="fileWidth" value="{$width}">
	<input type="hidden" id="fileHeight" value="{$height}">
	<input type="hidden" id="archive" value="{$archive}">
	<input type="hidden" name="action" value="replace">

	<div id="drawEditor">
		<div id="drawMenu">
			{if $drawFullscreen neq 'true'}
				<button id="drawSave" onclick="$('#tiki_draw').saveDraw();return false;">{tr}Save{/tr}</button>
				<button id="drawSaveBack" onclick="$('#tiki_draw').saveAndBackDraw();return false;">{tr}Save and Back{/tr}</button>
				<button id="drawRename" onclick="$('#fileName').val($('#tiki_draw').renameDraw());return false;">{tr}Rename{/tr}</button>
				<button id="drawBack">{tr}Back{/tr}</button>
				<button id="drawFullscreen">{tr}Toggle Fullscreen{/tr}</button>
			{else}
				<input type="submit" class="btn btn-default btn-sm" style="display: none;" value="{tr}Save{/tr}">
			{/if}
		</div>
	</div>

	<div id="map">{$map}</div>
</form>
