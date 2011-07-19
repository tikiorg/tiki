{if $translations|@count}
	<p>{tr}Current translation set:{/tr}</p>
	<ul>
		{foreach from=$translations item=trans}
			<li>{object_link type=$type id=$trans.objId}, {$trans.language|escape}</li>
		{/foreach}
	</ul>
{else}
	<p>{tr}No translations available at this time.{/tr}</p>
{/if}

{if $canAttach}
	{if $filters.language}
		<form class="simple" method="post" action="tiki-ajax_services.php">
			<label>
				{tr}Add a new object to the set{/tr}
				{object_selector _name=target _filter=$filters}
			</label>
			<div>
				<input type="hidden" name="controller" value="translation"/>
				<input type="hidden" name="action" value="attach"/>
				<input type="hidden" name="type" value="{$type|escape}"/>
				<input type="hidden" name="source" value="{$source|escape}"/>
				<input type="submit" value="{tr}Add{/tr}"/>
			</div>
		</form>
	{else}
		<p>{tr}All possible translations exist.{/tr}</p>
	{/if}
{/if}
