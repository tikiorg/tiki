{* $Id$ *}

<h1>{tr}File storage setup{/tr}</h1>

<div style="float:left; width:60px"><img src="img/icons/large/icon-configuration48x48.png" alt="{tr}File storage setup{/tr}"></div>
<div align="left" style="margin-top:1em;">
<p>

{if isset($promptFileGalleryStorage) AND $promptFileGalleryStorage eq 'y'}
<div>
<fieldset>
	<legend>{tr}File Gallery storage{/tr}</legend>
	<img src="img/icons/large/file-manager.png" style="float:right" />	
	{preference name='fgal_use_dir'}
</fieldset>
</div>
<br>
{/if}

{if isset($promptAttachmentStorage) AND $promptAttachmentStorage eq 'y'}
<div>
<fieldset>
	<legend>{tr}Attachment storage{/tr}</legend>
	<img src="img/icons/large/wikipages.png" style="float:right" />	
	{preference name=w_use_db}
	{preference name=w_use_dir}
</fieldset>
</div>
{/if}

</p>


</div>
