{* displays a cell with the languages of the translation set *}
	{if count($trads) > 1 || $trads[0].langName}
		{if $td}<td style="text-align:right;">{/if}
		{if count($trads) > 1}
			<form action="tiki-index.php" method="post">
			<select name="page_id" onchange="page_id.form.submit()">
			{section name=i loop=$trads}
			<option value="{$trads[i].objId}">{$trads[i].langName}</option>
			{/section}
			</select>
			</form>
		{else}
			{$trads[0].langName}
		{/if}
		{if $td}</td>{/if}
	{/if}
