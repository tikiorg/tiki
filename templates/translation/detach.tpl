<form method="post" action="tiki-ajax_services.php">
	<p>{tr}Are you sure you want to detach these translations?{/tr}</p>
	<ul>
		<li>{object_link type=$type id=$source}</li>
		{if $source neq $target}
			<li>{object_link type=$type id=$target}</li>
		{/if}
	</ul>
	<p>
		<input type="hidden" name="controller" value="translation"/>
		<input type="hidden" name="action" value="detach"/>
		<input type="hidden" name="type" value="{$type|escape}"/>
		<input type="hidden" name="source" value="{$source|escape}"/>
		<input type="hidden" name="target" value="{$target|escape}"/>
		<input type="hidden" name="confirm" value="1"/>
		<input type="submit" value="{tr}Confirm{/tr}"/>
	</p>
</form>
