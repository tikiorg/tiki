{* displays a cell with the languages of the translation set *}
	{if count($trads) > 1 || $trads[0].langName}
		{if $td eq 'y'}<td style="text-align: left; width:auto;">{/if}
		{if count($trads) > 1}
			{if $type == 'article'}
				<form action="tiki-read_article.php" method="post">
				<select name="articleId" onchange="this.form.submit()">
			{else}
				<form action="tiki-index.php" method="post">
				<select name="page_id" onchange="this.form.submit()">
			{/if}
			{section name=i loop=$trads}
			<option value="{$trads[i].objId}">{$trads[i].langName}</option>
			{/section}
			</select>
			</form>
		{else}
			{$trads[0].langName}
		{/if}
		{if $td eq 'y'}</td>{/if}
	{/if}
