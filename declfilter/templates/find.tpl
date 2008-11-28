{* $Id$ *}

{*
	parameters used in this template:

	* filegals_manager      : If value not empty, adds hidden input filegals_manager value=$filegals_manager
	* _sort_mode             : If value = 'y' adds hidden input sort_mode value=$sort_mode

	* what                  : Change form title. Default value (if $what empty) is "Find". If $what is not empty, the text presented is $what content
	* exact_match           : If set adds exact_match field
	* types                 : If not empty adds type dropdown whith types array values
	*		types_tag             : HTML element used to display types ('select' or 'checkbox'). Defaults to 'select'.
	*		find_type             : types selected value(s) - has to be a string for types_tag 'select' and an array for 'checkbox'
	* topics                : If not empty adds topic dropdown with topics array values
	* find_show_languages   : If value = 'y' adds lang dropdown with languages value dropdown
	*		find_lang             : lang dropdown selected value
	* find_show_categories  : If value = 'y' adds categories dropdown with categories array values
	*		find_categId          : categories selected value
	* find_show_num_rows    : If value = 'y' adds maxRecords field. Value: maxRecords
	* filters               : array( filter_field1 => array( option1_value => option1_text, ... ), filter_field2 => ... )
	*		filter_names          : array( filter_field1 => filter_field1_name, ... )
	*		filter_values         : array( filter_fieldX => filter_fieldX_selected_value, ... )
	*
	* Usage examples : {include file='find.tpl' _sort_mode='y'}
	*                  {include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'} 
*}

<div class="clearfix">
		<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
	<ul>
		{if $filegals_manager neq ''}<input type="hidden" name="filegals_manager" value="{$filegals_manager|escape}" />{/if}
		{if $_sort_mode eq 'y'}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}

		{query _type='form_input' maxRecords='NULL' type='NULL' types='NULL' find='NULL' topic='NULL' lang='NULL' exact_match='NULL' categId='NULL' filegals_manager='NULL' save='NULL'}

<li Class="findtitle">	
	<label for="findwhat">
		{if empty($what)}
			{tr}Find{/tr}
		{else}
			{tr}{$what}{/tr}
		{/if}
	</label>
	<input type="text" name="find" id="findwhat" value="{$find|escape}" />
</li>

{if isset($exact_match)}
	<li class="findexactmatch">
		<label for="findexactmatch">
			{tr}Exact&nbsp;match{/tr}
		</label>
		<input type="checkbox" name="exact_match" id="findexactmatch" {if $exact_match ne 'n'}checked="checked"{/if}/>
	</li>
{/if}

{if !empty($types) and ( !isset($types_tag) or $types_tag eq 'select' ) }
	<li class="findtypes"> 
		<select name="type">
			<option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}any type{/tr}</option>
			{section name=t loop=$types}
				<option value="{$types[t].type|escape}" {if $find_type eq $types[t].type}selected="selected"{/if}>
					{tr}{$types[t].type}{/tr}
				</option>
			{/section}
		</select>
	</li>
{/if}

{if !empty($topics)}
	<li class="findtopics"> 
		<select name="topic">
			<option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all topic{/tr}</option>
			{section name=ix loop=$topics}
				<option value="{$topics[ix].topicId|escape}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>
					{tr}{$topics[ix].name}{/tr}
				</option>
			{/section}
		</select>
	</li>
{/if}

{if $find_show_languages eq 'y' and $prefs.feature_multilingual eq 'y'}
	<li class="findlang">
		<select name="lang" class="in">
		<option value='' {if $find_lang eq ''}selected="selected"{/if}>{tr}any language{/tr}</option>
		{section name=ix loop=$languages}
			{if !is_array($prefs.available_languages) || count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
			<option value="{$languages[ix].value|escape}" {if $find_lang eq $languages[ix].value}selected="selected"{/if}>
				{tr}{$languages[ix].name}{/tr}
			</option>
			{/if}
		{/section}
		</select>
		{tr}not in{/tr}
		<select name="langOrphan" class="notin">
		<option value='' {if $find_langOrphan eq ''}selected="selected"{/if}></option>
		{section name=ix loop=$languages}
			{if !is_array($prefs.available_languages) || count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
				<option value="{$languages[ix].value|escape}" {if $find_langOrphan eq $languages[ix].value}selected="selected"{/if}>
					{tr}{$languages[ix].name}{/tr}
				</option>
			{/if}
		{/section}
		</select>
	</li>
{/if}

{if $find_show_categories eq 'y' and $prefs.feature_categories eq 'y' and !empty($categories)}
	<li class="findcateg"> 
		<select name="categId">
			<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
			{section name=ix loop=$categories}
				<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>
					{tr}{$categories[ix].categpath}{/tr}
				</option>
			{/section}
		</select>
	</li>
{/if}

{if $find_show_num_rows eq 'y'}
	<li class="findnumrows"> 
		<label for="findnumrows">
			{tr}Number of displayed rows{/tr}
			<input type="text" name="maxRecords" id="findnumrows" value="{$maxRecords|escape}" size="3" />
		</label>
	</li>
{/if}

<li class="findsubmit">
	<input type="submit" name="search" value="{tr}Go{/tr}" />
	{if $find ne ''}
		<span class="button">
			<a href="{$smarty.server.PHP_SELF}" title="{tr}Clear Filter{/tr}">{tr}Clear Filter{/tr}</a>
		</span>
	{/if}
</li>

{if !empty($types) and isset($types_tag) and $types_tag eq 'checkbox' }
	<li class="findtypes">
		<ul>
			<li>
				{tr}in:{/tr}
			</li>
			{foreach key=key item=value from=$types}
				<li>
					<input type="checkbox" name="types[]" value="{$key|escape}" {if is_array($find_type) && in_array($key, $find_type)}checked="checked"{/if} /> {tr}{$value}{/tr}
			</li>
		{/foreach}
		</ul>
	</li>
{/if}

{if !empty($filters)}
	<li class="findfilter">
		{foreach key=key item=item from=$filters}
			<span>
				{$filter_names.$key}{tr}:{/tr}
			</span>
			<select name="findfilter_{$key}">
				<option value='' {if $filter_values.$key eq ''}selected="selected"{/if}>--</option>
				{foreach key=key2 item=value from=$item}
					<option value="{$key2}"{if $filter_values.$key eq $key2} selected="selected"{/if}>{$value}</option>
				{/foreach}
			</select>
		{/foreach}
	</li>
{/if}

</ul>
</form>
</div>
<div class="clear"></div>
