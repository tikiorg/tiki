<form method="post"  enctype="multipart/form-data">
	  <input type="hidden" name="file" value="{$file|escape}" />
	  <input type="submit" name="ftp_download" value="{if empty($title)}{tr}Download{/tr}{else}{tr}{$title}{/tr}{/if}" />
</form>