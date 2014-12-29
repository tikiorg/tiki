<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="file" value="{$file|escape}">
	<input type="submit" class="btn btn-default btn-sm" name="ftp_download" value="{if empty($ftptitle)}{tr}Download{/tr}{else}{tr}{$ftptitle}{/tr}{/if}">
</form>
