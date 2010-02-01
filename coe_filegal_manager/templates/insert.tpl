<div class="fg-insert">
	<h2>Choose the insert method</h2>
	<a class="fg-upload-close" onclick="FileGallery.upload.close()"><img src="images/file_gallery/close.gif" border="0"/></a>
	<div class="fg-insert-choose">
		<a id="fg-insert-mode-image" onclick="FileGallery.upload.switchto('image')"{if $as='image'} class="fg-insert-active"{/if}>Insert as an image</a>
		<a id="fg-insert-mode-link" onclick="FileGallery.upload.switchto('link')"{if $as<>'image'} class="fg-insert-active"{/if}>Insert as a link</a>
	</div>
	<div class="fg-insert-form">
		<div class="fg-insert-thumb"><img src="{$file|sefurl:thumbnail}" border="0"/></div>
		<div class="fg-insert-details">
			<h3>File 1</h3>
			Size: <b>15.71 Kb</b><br/>
			Created/uploaded: <b>Thu, Dec 05, 2009</b>
		</div>
		<div id="fg-insert-as-image"{if $as<>image} style="display:none"{/if}>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x1"/></td>
				<td><label for="fg-insert-link-x1">Original size</label></td>
			</tr>
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x2"/></td>
				<td><label for="fg-insert-link-x2">Thumbnail</label></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="text" class="fg-insert-size" id="fg-insert-size-width"/> x <input type="text" class="fg-insert-size" id="fg-insert-size-height"/></td>
			</tr>
			</table>
			<input value="Insert" type="submit" class="fg-insert-submit" onclick="FileGallery.upload.insertImage('{$file}',document.getElementById('fg-insert-link-x1').checked,$('#fg-insert-size-width').val(),$('#fg-insert-size-height').val())"/>
		</div>
		<div id="fg-insert-as-link"{if $as=image} style="display:none"{/if}>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><label for="fg-insert-title">Link title</label></td>
				<td><input type="text" id="fg-insert-title"/></td>
			</tr>
			<!--tr>
				<td><label for="fg-insert-text">Text</label></td>
				<td><input type="text" id="fg-insert-text"/></td>
			</tr-->
			</table>
			<input value="Insert" type="submit" class="fg-insert-submit" onclick="FileGallery.upload.insertLink('{$file}',$('#fg-insert-title').val())"/>
		</div>
	</div>
</div>
