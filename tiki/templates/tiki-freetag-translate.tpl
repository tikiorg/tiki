<h1>{$data.pageName}</h1>
<a href="tiki-index.php?page={$objId}" class="linkbut">View page</a>
<form method="post" action="tiki-freetag_translate.php">
	<input type="hidden" name="type" value="{$type}"/>
	<input type="hidden" name="objId" value="{$objId}"/>
<table>
	<thead>
		<tr>
		{foreach item=lang from=$languageList}
			{if $lang neq ''}
			<th>{$lang}</th>
			{/if}
		{/foreach}
		</tr>
	</thead>
	<tbody>
	{foreach item=tag key=group from=$tagList}
		<tr class="formcolor">
		{if $tag[''] eq ''}
		{foreach item=lang from=$languageList}
		{if $lang neq ''}
			<td>
			<div>{$tag[$lang].tag}</div>
			{if !$tag[$lang]}
				<div>
					<input type="text" name="newtag[{$group}][{$lang}]" value="{$newtags[$group][$lang]}"/>
					<input type="hidden" name="rootlang[{$group}][{$lang}]" value="{first($tag).rootlang}"/>
				</div>
			{/if}
			</td>
		{/if}
		{/foreach}
		{else}
			<td colspan="{$languageList|@count - (in_array('',$languageList)?1:0)}">
				{$tag[$blank].tag}
				- {tr}Set language{/tr}
				<select name="setlang[{$tag[$blank].tagId}]">
					<option value="">{tr}Universal{/tr}</option>
					{foreach item=lang from=$languageList}{if $lang neq ''}
					<option value="{$lang}">{$lang}</option>
					{/if}{/foreach}
				</select>
			</td>
		{/if}
		</tr>
	{/foreach}
		<tr>
			<td class="button" colspan="{$languageList|@count - (in_array('',$languageList)?1:0)}">
				<input type="submit" name="save" value="{tr}Save{/tr}"/>
			</td>
		</tr>
	</tbody>
</table>
<div>
{tr}Show additional languages{/tr}:
<select multiple="multiple" name="additional_languages[]">
{foreach item=lang from=$fullLanguageList}
	<option value="{$lang.value}"{if in_array($lang.value, $languageList)} selected="selected"{/if}>{$lang.name}</option>
{/foreach}
</select>
<input type="submit" value="{tr}Add{/tr}"/>
</div>
<div>
</form>
