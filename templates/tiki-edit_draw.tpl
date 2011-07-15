{* $Id$ *}
{title help="Draw"}{$title}{/title}

<div style="text-align: center;">
	<div id="svg-editHeaderLeft" style="position: absolute; left: 7px;top: 5px;">
		<button id="tiki-draw_save" style="float left;">{tr}Save{/tr}</button>
	</div>
	
	<div id="svg-editHeaderRight" style="position: absolute; right: 15px;top: 5px;">
		<button id="tiki-draw_back">{tr}Back{/tr}</button>
	</div>
	
	<div id="svg-data" style="display: none;">{$data}</div>
	
	<iframe src="lib/svg-edit/svg-editor.html" id="svgedit"></iframe>
</div>    
