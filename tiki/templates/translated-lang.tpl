{* displays a cell with the languages of the translation set *}
	{if isset($trads) && (count($trads) > 1 || $trads[0].langName)}
		{if $td eq 'y'}<td style="vertical-align:top;text-align: left; width:42px;">{/if}
		{if isset($verbose) && $verbose eq 'y'}The main text of this page is available in the following languages:{/if}
		{if count($trads) > 1}
			{if isset($type) && $type == 'article'}
				<form action="tiki-read_article.php" method="get">
				<select name="articleId" onchange="this.form.submit()">
					{section name=i loop=$trads}
					<option value="{$trads[i].objId}">{$trads[i].langName}</option>
					{/section}
				</select>
				</form>
			{else} {* get method to have the param in the url *}
				<form action="tiki-index.php" method="get">
				<select name="page" onchange="this.form.submit()">
					{section name=i loop=$trads}
					<option value="{$trads[i].objName}">{$trads[i].langName}</option>
					{/section}
				</select>
				</form>
			{/if}
		{else}
			{$trads[0].langName}
		{/if}
		{if $td eq 'y'}</td>{/if}
	{/if}
