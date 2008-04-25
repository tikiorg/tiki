{* displays a cell with the languages of the translation set *}
	{if isset($trads) && (count($trads) > 1 || $trads[0].langName)}
		{if $td eq 'y'}<td style="vertical-align:top;text-align: left; width:42px;">{/if}
		{if isset($verbose) && $verbose eq 'y'}The main text of this page is available in the following languages:{/if}
		
			{if isset($type) && $type == 'article'}
				<form action="tiki-read_article.php" method="get">
				<select name="articleId" onchange="this.form.submit()">
					{section name=i loop=$trads}
					<option value="{$trads[i].objId}">{$trads[i].langName}</option>
					{/section}
				</select>
				</form>
			{else} {* get method to have the param in the url *}
				<script type="text/javascript">
				{if $beingStaged == 'y'}
					page_to_translate = "{$approvedPageName}";
				{else}
					page_to_translate = "{$page}";
				{/if}
				{literal}
				function quick_switch_language( element )
				{
					var index = element.selectedIndex;
					var option = element.options[index];

					if( option.value == "-" )
						return;
					else if( option.value == "_translate_" ) {
						element.form.action = "tiki-edit_translation.php";
						element.value = page_to_translate;
						element.form.submit();
					} else
						element.form.submit();
				}
				{/literal}
				</script>
				<form action="tiki-index.php" method="get">
				<select name="page" onchange="quick_switch_language( this )">
					{section name=i loop=$trads}
					<option value="{$trads[i].objName}">{$trads[i].langName}</option>
					{/section}
					{if $tiki_p_edit eq 'y'}
					<option value="-">---</option>
					<option value="_translate_">{tr}Translate{/tr}</option>
					{/if}
				</select>
				</form>
			{/if}

		{if $td eq 'y'}</td>{/if}
	{/if}
