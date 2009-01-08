
{include file='tiki-pagecontrols.tpl' controls=$object_page_controls}

{if $tiki_p_edit_categories eq 'y'}
	<form method="post" action="tiki-view_categories.php">
		{include file=categorize.tpl}
		<input type="hidden" name="objectId" value="{$objectId|escape}"/>
		<input type="hidden" name="objectType" value="{$objectType|escape}"/>
		<input type="hidden" name="objectName" value="{$objectName|escape}"/>
		<input type="submit" value="{tr}Edit categories{/tr}"/>
	</form>
{else}
	{include file=categorize.tpl}
{/if}

{include file='tiki-pagecontrols-footer.tpl' controls=$object_page_controls}

