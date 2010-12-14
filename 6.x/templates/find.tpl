{* $Id$ *}

{*
	parameters used in this template:

	* filegals_manager      : If value not empty, adds hidden input filegals_manager value=$filegals_manager

	* whatlabel             : Change form title. Default value (if $whatlabel empty) is "Find". If $whatlabel is not empty, the text presented is $whatlabel content
	* exact_match           : If set adds exact_match field
	* types                 : If not empty adds type dropdown whith types array values
	*		types_tag             : HTML element used to display types ('select' or 'checkbox'). Defaults to 'select'.
	*		find_type             : types selected value(s) - has to be a string for types_tag 'select' and an array for 'checkbox'
	* topics                : If not empty adds topic dropdown with topics array values
	* find_show_languages   : If value = 'y' adds lang dropdown with languages value dropdown
	*		find_lang             : lang dropdown selected value
	* find_show_categories  : If value = 'y' adds categories dropdown with categories array values
	* find_show_categories_multi  : If value = 'y' adds categories dropdown with categories array values with multi selector
	*		find_categId          : categories selected value
	* find_show_num_rows    : If value = 'y' adds maxRecords field. Value: maxRecords
	* find_show_date_range  : If value = 'y' adds date range to filter within
	* find_show_orphans		: If value = 'y' adds a checkbox orphan
	* find_show_sub	: If value = 'y' add a checkbox in all the tree
	* filters               : array( filter_field1 => array( option1_value => option1_text, ... ), filter_field2 => ... )
	*		filter_names          : array( filter_field1 => filter_field1_name, ... )
	*		filter_values         : array( filter_fieldX => filter_fieldX_selected_value, ... )
	*
	* Usage examples : {include file='find.tpl'}
	*                  {include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y'} 
*}

<div class="clearfix">
		<form method="post" action="{$smarty.server.PHP_SELF}" class="findtable">
		{if $filegals_manager neq ''}<input type="hidden" name="filegals_manager" value="{$filegals_manager|escape}" />{/if}

		{query _type='form_input' maxRecords='NULL' type='NULL' types='NULL' find='NULL' topic='NULL' lang='NULL' exact_match='NULL' categId='NULL' cat_categories='NULL' filegals_manager='NULL' save='NULL' offset='NULL' searchlist='NULL' searchmap='NULL'}

	<label class="findtitle">
		{if empty($whatlabel)}
			{tr}Find{/tr}
		{else}
			{tr}{$whatlabel}{/tr}
		{/if}
		<input type="text" name="find" value="{$find|escape}" />
	</label>

{if isset($exact_match)}
	<label class="findexactmatch" for="findexactmatch">
			{tr}Exact&nbsp;match{/tr}
		<input type="checkbox" name="exact_match" id="findexactmatch" {if $exact_match ne 'n'}checked="checked"{/if}/>
	</label>
{/if}

{if !empty($find_show_sub) and $find_show_sub eq 'y'}
	<label class="findsub">
		{tr}and all the sub-objects{/tr}
		<input type="checkbox" name="find_sub" id="find_sub" {if $find_sub eq 'y'}checked="checked"{/if}/>
	</label>
{/if}

{if !empty($types) and ( !isset($types_tag) or $types_tag eq 'select' ) }
	<label class="findtypes"> 
		<select name="type">
			<option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}any type{/tr}</option>
			{section name=t loop=$types}
				<option value="{$types[t].type|escape}" {if $find_type eq $types[t].type}selected="selected"{/if}>
					{capture}{tr}{$types[t].type}{/tr}{/capture}{$smarty.capture.default|escape}
				</option>
			{/section}
		</select>
	</label>
{/if}

{if !empty($topics)}
	<label class="findtopics"> 
		<select name="topic">
			<option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all topic{/tr}</option>
			{section name=ix loop=$topics}
				<option value="{$topics[ix].topicId|escape}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>
					{capture}{tr}{$topics[ix].name}{/tr}{/capture}{$smarty.capture.default|escape}
				</option>
			{/section}
		</select>
	</label>
{/if}

{if $find_show_languages eq 'y' and $prefs.feature_multilingual eq 'y'}
	<label class="findlang">
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
		{if isset($find_show_languages_excluded) and $find_show_languages_excluded eq 'y'}
		<label>{tr}not in{/tr}
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
		</label>
		{/if}
	</label>
{/if}

{if $find_show_date_range eq 'y'}
	<div id="date_range_find">
		<span class="findDateFrom">
			{tr}From{/tr}
			{html_select_date time=$find_date_from prefix="find_from_" start_year="-2" end_year="+2" month_format="%m" field_order=$prefs.display_field_order}
		</span>
		<span class="findDateTo">
			{tr}to{/tr}
			{html_select_date time=$find_date_to prefix="find_to_" start_year="-2" end_year="+2" month_format="%m" field_order=$prefs.display_field_order}
		</span>
	</div>
{/if}

{if ($find_show_categories eq 'y' or $find_show_categories_multi eq 'y') and $prefs.feature_categories eq 'y' and !empty($categories)}
	<div class="category_find">
	<div id="category_singleselect_find" style="display: {if $find_show_categories_multi eq 'y' && $find_cat_categories|@count > 1}none{else}block{/if};">
		<label class="findcateg"> 
			<select name="categId">
				<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
				{section name=ix loop=$categories}
					<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>
						{capture}{tr}{$categories[ix].categpath}{/tr}{/capture}{$smarty.capture.default|escape}
					</option>
				{/section}
			</select>
		</label>
		{if $prefs.javascript_enabled eq 'y' && $find_show_categories_multi eq 'y'}<a href="#" onclick="show('category_multiselect_find');hide('category_singleselect_find');">{tr}Multiple select{/tr}</a>{/if}
	</div>
	<div id="category_multiselect_find" style="display: {if $find_show_categories_multi eq 'y' && $find_cat_categories|@count > 1}block{else}none{/if};">
  		<div class="multiselect"> 
  			{if count($categories) gt 0}
				{$cat_tree}
				<div class="clear">
				{if $tiki_p_admin_categories eq 'y'}
    				<div class="floatright"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon _id='wrench'}</a></div>
				{/if}
				{select_all checkbox_names='cat_categories[]' label="{tr}Select/deselect all categories{/tr}"}
			{else}
				<div class="clear">
 				{if $tiki_p_admin_categories eq 'y'}
    				<div class="floatright"><a href="tiki-admin_categories.php" class="link">{tr}Admin Categories{/tr} {icon _id='wrench'}</a></div>
 				{/if}
    			{tr}No categories defined{/tr}
  			{/if}
			</div> {* end .clear *}
		</div> {* end #multiselect *}
	</div> {* end #category_multiselect_find *}
	</div>
{/if}

{if !empty($types) and isset($types_tag) and $types_tag eq 'checkbox' }
	<div class="findtypes">
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
	</div>
{/if}

{if !empty($filters)}
	<div class="findfilter">
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
	</div>
{/if}

{if !empty($find_durations)}
	{foreach key=key item=duration from=$find_durations}
		<label class="find_duration">
		{tr}{$duration.label}{/tr}
		{html_select_duration prefix=$duration.prefix default=$duration.default default_unit=$duration.default_unit}
		</label>
	{/foreach}
{/if}

{if !empty($show_find_orphans) and $show_find_orphans eq 'y'}
	<label class="find_orphans" for="find_orphans">
		   {tr}Orphans{/tr}
		   <input type="checkbox" name="find_orphans" id="find_orphans" {if $find_orphans eq 'y'}checked="checked"{/if}/>
	</label>
{/if}

{if $find_show_num_rows eq 'y'}
	<label class="findnumrows" for="findnumrows">
			{tr}Number of displayed rows{/tr}
			<input type="text" name="maxRecords" id="findnumrows" value="{$maxRecords|escape}" size="3" />
	</label>
{/if}

<label class="findsubmit">
	<input type="submit" name="search" value="{tr}Go{/tr}" />
	{if $find ne ''}
		<span class="button">
			<a href="{$smarty.server.PHP_SELF}?{query find='' type='' types='' topic='' lang='' langOrphan='' exact_match='' categId='' maxRecords='' find_from_Month='' find_from_Day='' find_from_Year='' find_to_Month='' find_to_Day='' find_to_Year=''}" title="{tr}Clear Filter{/tr}">{tr}Clear Filter{/tr}</a>
		</span>
	{/if}
	{if $gmapbuttons and $mapview}
		<input type="submit" name="searchlist" value="{tr}List View{/tr}" />
		<input type="hidden" name="mapview" value="y" />
	{elseif $gmapbuttons}
		<input type="submit" name="searchmap" value="{tr}Map View{/tr}" />
		<input type="hidden" name="mapview" value="n" />
	{/if}
</label>

</form>
</div>

