{* $Id$ *}
{title help="Draw"}{$title}{/title}

<div style="text-align: center;">
	<div id="svg-editHeader" style="position: absolute; right: 5px;">
		<button onclick="window.loadSvg();">Load example</button>
		<button onclick="window.saveSvg();">Save data</button>
	</div>
	
	<iframe src="lib/svg-edit/svg-editor.html" id="svgedit"></iframe>
	
	<form class="upform" id="upform" name="form{$fileId}" method="post" action="tiki-list_file_gallery.php?galleryId={$galleryId}&amp;fast" enctype="multipart/form-data">
			<input type="input" id="file" name="upfile{$fileId}" />
	</form>
</div>    