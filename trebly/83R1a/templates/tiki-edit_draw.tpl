{* $Id: tiki-edit_draw.tpl 38153 2011-10-11 13:37:03Z robertplummer $ *}
{title help="Draw"}{$name}{/title}

<div id="tiki_draw" style="text-align: center;">
	<div id="fileData" style="display: none;">{$data}</div>
	
	<input type="hidden" id="fileId" value="{$fileId}" />
	<input type="hidden" id="galleryId" value="{$galleryId}" />
	<input type="hidden" id="fileName" value="{$name}" />
	<input type="hidden" id="fileWidth" value="{$width}" />
	<input type="hidden" id="fileHeight" value="{$hight}" />
	<input type="hidden" id="archive" value="{$archive}" />
	
	<div id="drawEditor">
		<div id="drawMenu">
			<button id="drawSave" style="float left;">{tr}Save{/tr}</button>
			<button id="drawRename">{tr}Rename{/tr}</button>
			<button id="drawBack">{tr}Back{/tr}</button>
			<button id="drawFullscreen">{tr}Toggle Fullscreen{/tr}</button>
		</div>
	</div>
	
	<div id="map">{$map}</div>
</div>    
