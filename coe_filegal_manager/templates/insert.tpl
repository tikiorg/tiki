<div class="fg-insert">
	<h2>{tr}Choose the insert method{/tr}</h2>
	<a class="fg-upload-close" onclick="FileGallery.upload.close()"><img src="images/file_gallery/close.gif" border="0"/></a>
	<div class="fg-insert-choose">
		<a id="fg-insert-mode-image" onclick="FileGallery.upload.switchto('image')"{if $as='image'} class="fg-insert-active"{/if}>{tr}Insert as an image{/tr}</a>
		<a id="fg-insert-mode-link" onclick="FileGallery.upload.switchto('link')"{if $as<>'image'} class="fg-insert-active"{/if}>{tr}Insert as a link{/tr}</a>
	</div>
	<div class="fg-insert-form">
		<div class="fg-insert-thumb"><img src="{$file|sefurl:thumbnail}" border="0"/></div>
		<div class="fg-insert-details">
			<h3>{tr}File{/tr}</h3>
			{tr}Size{/tr}: <b>15.71 Kb</b><br/>
			{tr}Created/uploaded{/tr}: <b>Thu, Dec 05, 2009</b>
		</div>
		<div id="fg-insert-as-image"{if $as<>image} style="display:none"{/if}>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x1"/></td>
				<td><label for="fg-insert-link-x1">{tr}Original size{/tr}</label></td>
			</tr>
			<tr>
				<td><input type="radio" name="x" id="fg-insert-link-x2"/></td>
				<td><label for="fg-insert-link-x2">{tr}Thumbnail{/tr}</label></td>
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
				<td><label for="fg-insert-title">{tr}Link title{/tr}</label></td>
				<td><input type="text" id="fg-insert-title"/></td>
			</tr>
			<!--tr>
				<td><label for="fg-insert-text">Text</label></td>
				<td><input type="text" id="fg-insert-text"/></td>
			</tr-->
			</table>
			<input value="{tr}Insert{/tr}" type="submit" class="fg-insert-submit" onclick="FileGallery.upload.insertLink('{$file}',$('#fg-insert-title').val())"/>
		</div>
	</div>
</div>
