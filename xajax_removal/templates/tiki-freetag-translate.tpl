{title}{tr}Tag translation{/tr}{if isset($data)}: {$data.pageName}{/if}{/title}

{if isset($data)}
	<div class="navbar">
		{button  href="tiki-index.php?page=$objId" _text="{tr}View page{/tr}"}
	</div>
{/if}

	<p>{tr}Note that tags that were created on pages with no language set will remain
	universal (i.e. is the same tag in all languages) until a language has been set for the tag.{/tr}
	{tr}Until then, they cannot be translated.{/tr}</p>
<form method="post" action="tiki-freetag_translate.php">
	<input type="hidden" name="type" value="{$type|escape}"/>
	<input type="hidden" name="objId" value="{$objId|escape}"/>
	<input type="hidden" name="offset" value="{$freetags_offset|escape}"/>
{jq}
	function show_cleartra_checkboxes()
	{
		var table = document.getElementById( 'tagtranslationtable' );
		var list = table.getElementsByTagName( 'input' );
		for( key in list )
		{
			if( list[key].type == 'checkbox' )
				list[key].style.display = 'inline';
		}

		document.getElementById('scblink').style.display = 'none';
	}
{/jq}
		
{button _onclick="javascript:show_cleartra_checkboxes()" id="scblink" _text="{tr}Show checkboxes to clear language information on tags{/tr}"}

<div class="resultspagelinks">
	{if $previous}<a class="neatlink" href="{$previous|escape}">&laquo; {tr}Previous{/tr}</a>{/if}
	<a class="neatlink" href="{$next|escape}">{tr}Next{/tr} &raquo;</a>
</div>

<table class="formcolor" id="tagtranslationtable">
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
	{if !$tagList}
		<tr>
	<td colspan="{$languageList|@count - (in_array('',$languageList)?1:0)}">
			{tr}There are no tags on this page in your preferred languages{/tr}
			</td>
		</tr>
	{/if}
	{foreach item=tag key=group from=$tagList}
		<tr>
		{if $tag[$blank] eq ''}
		{foreach item=lang from=$languageList}
		{if $lang neq ''}
			<td>
			{if !$tag[$lang]}
				<div>
					<input type="text" name="newtag[{$group}][{$lang}]" value="{$newtags[$group][$lang]}"/>
					<input type="hidden" name="rootlang[{$group}][{$lang}]" value="{$rootlang[$group]}"/>
				</div>
			{else}
				<div>{$tag[$lang].tag} <input style="display: none" type="checkbox" name="clear[]" value="{$tag[$lang].tagId}"/></div>
			{/if}
			</td>
		{/if}
		{/foreach}
		{else}
			{assign var=btag value=$tag[$blank]}
			<td colspan="{$languageList|@count - (in_array('',$languageList)?1:0)}">
				{$btag.tag}
				- {tr}Set language{/tr}
				<select name="setlang[{$btag.tagId}]">
					<option value="">{tr}Universal{/tr}</option>
					{foreach item=lang from=$languageList}{if $lang neq ''}
					<option value="{$lang}"{if $setlang[$btag.tagId] eq $lang} selected="selected"{/if}>{$lang}</option>
					{/if}{/foreach}
				</select>
			</td>
		{/if}
		</tr>
	{/foreach}
		<tr>
			<td colspan="{$languageList|@count - (in_array('',$languageList)?1:0)}">
				<input type="submit" name="save" value="{tr}Save{/tr}"/>
			</td>
		</tr>
	</tbody>
</table>
<div>
{tr}Show the following languages{/tr}:
<select multiple="multiple" name="additional_languages[]">
{foreach item=lang from=$fullLanguageList}
	<option value="{$lang.value}"{if in_array($lang.value, $languageList)} selected="selected"{/if}>{$lang.name}</option>
{/foreach}
</select>
<input type="submit" value="{tr}Select{/tr}"/>
</div>
</form>

