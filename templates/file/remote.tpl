{if ! $fileId}
	<form method="post" action="tiki-ajax_services.php?controller=file&amp;action=remote">
		<h3>{tr}Upload from URL{/tr}</h3>
		<p>
			<input type="hidden" name="galleryId" value="{$galleryId|escape}"/>
			<label>{tr}URL:{/tr} <input type="url" name="url" placeholder="http://"/></label>
			<input type="submit" value="{tr}Add{/tr}"/>
		</p>
	</form>
{else}
	<p>{tr}File added:{/tr} {object_link type=file id=$fileId title=$name}</p>
{/if}
