{* $Id$ *}

{*
	parameters used in this template:

	* filegals_manager      : If value not empty, adds hidden input filegals_manager value=$filegals_manager
	* whatlabel             : Change form title. Default value (if $whatlabel empty) is "Find". If $whatlabel is not empty, the text presented is $whatlabel content
	* exact_match           : If set adds exact_match field
	* types                 : If not empty adds type dropdown whith types array values
	* types_tag             : HTML element used to display types ('select' or 'checkbox'). Defaults to 'select'.
	* find_type             : types selected value(s) - has to be a string for types_tag 'select' and an array for 'checkbox'
	* topics                : If not empty adds topic dropdown with topics array values
	* find_show_languages   : If value = 'y' adds lang dropdown with languages value dropdown
	* find_lang             : lang dropdown selected value
	* find_show_categories  : If value = 'y' adds categories dropdown with categories array values
	* find_show_categories_multi : If value = 'y' adds categories dropdown with categories array values with multi selector
	* find_categId          : categories selected value
	* find_show_num_rows    : If value = 'y' adds maxRecords field. Value: maxRecords
	* find_show_date_range  : If value = 'y' adds date range to filter within
	* find_show_orphans     : If value = 'y' adds a checkbox orphan
	* find_show_sub         : If value = 'y' add a checkbox in all the tree
	* filters               : array( filter_field1 => array( option1_value => option1_text, ... ), filter_field2 => ... )
	* filter_names          : array( filter_field1 => filter_field1_name, ... )
	* filter_values         : array( filter_fieldX => filter_fieldX_selected_value, ... )
	* autocomplete          : name of the variable you want for autocomplete of the input field (only for <input type="text" ... >
	* find_other            : If value != '', show an input box label with find_other
	* find_in               : popup to explain on what is the find
	* map_only              : to only show the pages map (used with tablesorter since other find functions aren't needed)
	* Usage examples : {include file='find.tpl'}
	*                  {include file='find.tpl' find_show_languages='y' find_show_categories='y' find_show_num_rows='y'}
*}

<div class="find clearfix">
	<form method="post" class="form" role="form">
	{if !isset($map_only) or $map_only ne 'y'}
		{if !empty($filegals_manager)}<input type="hidden" name="filegals_manager" value="{$filegals_manager|escape}">{/if}
		{query _type='form_input' maxRecords='NULL' type='NULL' types='NULL' find='NULL' topic='NULL' lang='NULL' exact_match='NULL' categId='NULL' cat_categories='NULL' filegals_manager='NULL' save='NULL' offset='NULL' searchlist='NULL' searchmap='NULL'}
		<div class="find-text form-group">
			<div class="input-group">
				<span class="input-group-addon">
					{icon name="search"}
				</span>
				<input class="form-control" type="text" name="find" id="find" value="{$find|escape}" placeholder="{if empty($whatlabel)}{tr}Find{/tr}...{else}{tr}{$whatlabel}{/tr}{/if}">
				{if isset($autocomplete)}
					{jq}$("#find").tiki("autocomplete", "{{$autocomplete}}"){/jq}
				{/if}
				{if $prefs.javascript_enabled eq 'y' and (isset($exact_match) or isset($types) or isset($types_tag) or isset($find_topics) or isset($find_show_languages) or isset($find_lang) or isset($find_show_categories) or isset($find_show_categories_multi) or isset($find_categId) or isset($find_show_num_rows) or isset($find_show_date_range) or isset($find_show_orphans) or isset($find_show_sub) or isset($find_show_other))}
					<div class="input-group-btn btn btn-default btn-sm find-parameters-btn">
						<span class="caret"></span>
					</div>
				{/if}
				<div class="input-group-btn">
					<button type="submit" class="btn btn-default" name="search">{tr}Find{/tr}</button>
				</div>
			</div>
			{if !empty($find) or !empty($find_type) or !empty($find_topic) or !empty($find_lang) or !empty($find_langOrphan) or !empty($find_categId) or !empty($find_orphans) or !empty($find_other_val) or $maxRecords ne $prefs.maxRecords}{* $find_date_from & $find_date_to get set usually *}
				<div class="find-clear-filter text-center">
					<a href="{$smarty.server.PHP_SELF}?{query find='' type='' types='' topic='' lang='' langOrphan='' exact_match='' categId='' maxRecords=$prefs.maxRecords find_from_Month='' find_from_Day='' find_from_Year='' find_to_Month='' find_to_Day='' find_to_Year=''}" title="{tr}Clear Filter{/tr}" class="btn btn-link">{tr}Clear Filter{/tr}</a>
				</div>
			{/if}
		</div>
		{*	{if isset($find_in)}{help url="#" desc="{tr}Find in:{/tr} {$find_in}"}{/if} *}
		<div class="find-parameters form-horizontal col-sm-10 small {if $prefs.javascript_enabled eq 'n' or (!empty($find_type) or !empty($find_topic) or !empty($find_lang) or !empty($find_langOrphan) or !empty($find_show_categories) or !empty($find_categId) or !empty($find_orphans) or !empty($find_other_val) or $maxRecords ne $prefs.maxRecords)}visible{else}hidden{/if}"> {* TODO: figure out why "or !empty($find_show_categories_multi)" does not work *}
			{if !empty($types) and ( !isset($types_tag) or $types_tag eq 'select' )}
				<div class="form-group">
					<label class="control-label col-sm-4">
						{tr}Article Type{/tr}
					</label>
					<div class="col-sm-8">
						<select name="type" class="findtypes form-control input-sm">
							<option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}any type{/tr}</option>
							{section name=t loop=$types}
								<option value="{$types[t].type|escape}" {if $find_type eq $types[t].type}selected="selected"{/if}>
									{$types[t].type|tr_if|escape}
								</option>
							{/section}
						</select>
					</div>
				</div>
			{/if}
			{if !empty($topics)}
				<div class="form-group">
					<label class="control-label col-sm-4">
						{tr}Article Topic{/tr}
					</label>
					<div class="col-sm-8">
						<select name="topic" class="findtopics form-control input-sm">
							<option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}any topic{/tr}</option>
							{foreach $topics as $topic}
								<option value="{$topic.topicId|escape}" {if $find_topic eq $topic.topicId}selected="selected"{/if}>
									{$topic.name|tr_if|escape}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/if}
			{if (isset($find_show_languages) && $find_show_languages eq 'y') and $prefs.feature_multilingual eq 'y'}
				<div class="form-group">
					<label class="control-label col-sm-4">
						{tr}Language{/tr}
					</label>
					<div class="col-sm-8">
						<span class="findlang">
							<select name="lang" class="in form-control input-sm">
								<option value='' {if $find_lang eq ''}selected="selected"{/if}>{tr}any language{/tr}</option>
								{section name=ix loop=$languages}
									<option value="{$languages[ix].value|escape}" {if $find_lang eq $languages[ix].value}selected="selected"{/if}>
										{$languages[ix].name}
									</option>
								{/section}
							</select>
							{if isset($find_show_languages_excluded) and $find_show_languages_excluded eq 'y'}
								<span>
									<label class="control-label">
										{tr}not in{/tr}
									</label>
								</span>
								<span>
									<select name="langOrphan" class="notin form-control input-sm">
										<option value='' {if $find_langOrphan eq ''}selected="selected"{/if}></option>
										{section name=ix loop=$languages}
											<option value="{$languages[ix].value|escape}" {if $find_langOrphan eq $languages[ix].value}selected="selected"{/if}>
												{$languages[ix].name}
											</option>
										{/section}
									</select>
								</span>
							{/if}
						</span>
					</div>
				</div>
			{/if}
			{if isset($find_show_date_range) && $find_show_date_range eq 'y'}
				<div class="form-group findDateFrom">
					<label class="text-right col-sm-4">
						{tr}Date From{/tr}
					</label>
					<div class="col-sm-8">
						{html_select_date time=$find_date_from prefix="find_from_" month_format="%m"}
					</div>
				</div>
				<div class="form-group findDateTo">
					<label class="text-right col-sm-4">
						{tr}Date To{/tr}
					</label>
					<div class="col-sm-8">
						{html_select_date time=$find_date_to prefix="find_to_" month_format="%m"}
					</div>
				</div>
			{/if}
			{if ((isset($find_show_categories) && $find_show_categories eq 'y') or (isset($find_show_categories_multi) && $find_show_categories_multi eq 'y')) and $prefs.feature_categories eq 'y' and !empty($categories)}
				<div class="form-group category_find">
					{if $find_show_categories_multi eq 'n' || $findSelectedCategoriesNumber <= 1}
						<label class="col-sm-4 control-label">
							{tr}Category{/tr}
						</label>
						<div id="category_singleselect_find" class="col-sm-8">
							<select name="categId" class="findcateg form-control input-sm">
								<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
								{foreach $categories as $identifier => $category}
									<option value="{$identifier}" {if $find_categId eq $identifier}selected="selected"{/if}>
										{$category.categpath|tr_if|escape}
									</option>
								{/foreach}
							</select>
							{if $prefs.javascript_enabled eq 'y' && $find_show_categories_multi eq 'y'}
								<a href="#category_select_find_type" onclick="show('category_multiselect_find');hide('category_singleselect_find');javascript:document.getElementById('category_select_find_type').value='y';">
									{tr}Multiple select{/tr}
								</a>
							{/if}
							<input id="category_select_find_type" name="find_show_categories_multi" value="n" type="hidden">
						</div>
					{/if}
					<div id="category_multiselect_find" class="col-sm-8 col-sm-offset-4" style="display: {if $find_show_categories_multi eq 'y' && $findSelectedCategoriesNumber > 1}block{else}none{/if};">
						<div class="multiselect">
							{if count($categories) gt 0}
								{$cat_tree}
								<div class="clearfix">
									{if $tiki_p_admin_categories eq 'y'}
										<div class="pull-right">
											<a href="tiki-admin_categories.php" class="link">
												{tr}Admin Categories{/tr} {icon name='wrench'}
											</a>
										</div>
									{/if}
									{select_all checkbox_names='cat_categories[]' label="{tr}Select/deselect all categories{/tr}"}
							{else}
								<div class="clearfix">
									{if $tiki_p_admin_categories eq 'y'}
										<div class="pull-right">
											<a href="tiki-admin_categories.php" class="link">
												{tr}Admin Categories{/tr} {icon name='wrench'}
											</a>
										</div>
									{/if}
									{tr}No categories defined{/tr}
							{/if}
								</div> {* end .clear *}
						</div> {* end #multiselect *}
					</div> {* end #category_multiselect_find *}
				</div>
			{/if}
			{if !empty($types) and isset($types_tag) and $types_tag eq 'checkbox'}
				<div class="form-group findtypes text-center">
					{foreach key=key item=value from=$types}
						<label class="checkbox-inline">
							<input type="checkbox" name="types[]" value="{$key|escape}" {if is_array($find_type) && in_array($key, $find_type)}checked="checked"{/if}> {tr}{$value}{/tr}
						</label>
					{/foreach}
				</div>
			{/if}
			{if !empty($filters)}
				<div class="form-group findfilter">
					{foreach key=key item=item from=$filters}
						<label class="control-label col-sm-4">
							{$filter_names.$key}
						</label>
						<div class="col-sm-8">
							<select name="findfilter_{$key}" class="form-control input-sm">
								<option value='' {if $filter_values.$key eq ''}selected="selected"{/if}>--</option>
								{foreach key=key2 item=value from=$item}
									<option value="{$key2}"{if $filter_values.$key eq $key2} selected="selected"{/if}>{$value}</option>
								{/foreach}
							</select>
						</div>
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
				<div class="form-group find-orphans">
					<label class="find_orphans control-label col-sm-4" for="find_orphans">
						{tr}Orphans{/tr}
					</label>
					<div class="col-sm-8">
						<input type="checkbox" name="find_orphans" id="find_orphans" {if isset($find_orphans) and $find_orphans eq 'y'}checked="checked"{/if}>
					</div>
				</div>
			{/if}
			{if !empty($find_other)}
				<div class="form-group find-other">
					<label class="find_other control-label col-sm-4" for="find_other">
						{tr}{$find_other}{/tr}
					</label>
					<div class="col-sm-4">
						<input type="text" name="find_other" id="find_other" value="{if !empty($find_other_val)}{$find_other_val|escape}{/if}" class="form-control input-sm">
					</div>
				</div>
			{/if}
			{if isset($find_show_num_rows) && $find_show_num_rows eq 'y'}
				<div class="form-group findnumrows">
					<label class="control-label col-sm-4" for="findnumrows">
						{tr}Displayed rows{/tr}
					</label>
					<div class="col-sm-4">
						<input type="text" name="maxRecords" id="findnumrows" value="{$maxRecords|escape}" size="3" class="form-control input-sm">
					</div>
				</div>
			{/if}
		</div><!-- End of find-parameters -->
		{/if}
		<div class="find-map form-group">
			{if (isset($gmapbuttons) && $gmapbuttons) and (isset($mapview) && $mapview)}
				<input class="btn btn-default btn-sm" type="submit" name="searchlist" value="{tr}Hide Map{/tr}">
				<input type="hidden" name="mapview" value="y">
			{elseif (isset($gmapbuttons) && $gmapbuttons)}
				<input type="submit" class="btn btn-default btn-sm" name="searchmap" value="{tr}Show Map{/tr}">
				<input type="hidden" name="mapview" value="n">
			{/if}
		</div>
		{jq notonready=true}
			$( ".find-parameters-btn" ).click(function() {
				$( ".find-parameters" ).toggleClass( "hidden" );
			});
		{/jq}
	</form>
</div>
<!-- End of find -->
