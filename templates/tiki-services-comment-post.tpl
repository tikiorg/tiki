{if $threadId}
	<p>{tr}Your comment was posted.{/tr}</p>
	<p>{object_link type=$type objectId=$objectId}</p>
{else}
	<form method="post" action="tiki-ajax_services.php">
		<fieldset>
			<input type="hidden" name="controller" value="comment"/>
			<input type="hidden" name="action" value="post"/>
			<input type="hidden" name="type" value="{$type|escape}"/>
			<input type="hidden" name="objectId" value="{$objectId|escape}"/>
			<input type="hidden" name="parentId" value="{$parentId|escape}"/>
			<input type="hidden" name="post" value="1"/>
			<legend class="clearfix">{tr}Content{/tr}</legend>
			{if $prefs.comments_notitle neq 'y'}
				<label>{tr}Title:{/tr} <input type="text" name="title" value="{$title|escape}"/></label>
			{/if}
			<label class="clearfix">{tr}Comment:{/tr} {textarea name=data comments="y"}{$data|escape}{/textarea}</label>

			<input type="submit" class="clearfix" value="{tr}Post{/tr}"/>
		</fieldset>
	</form>
{/if}
