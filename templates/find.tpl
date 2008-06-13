{* $Id$ *}

{*
  parameters used in this template:

  * filegals_manager      : If value = 'y' adds hidden input filegals_manager value=y
  * _sort_mode             : If value = 'y' adds hidden input sort_mode value=$sort_mode

  * what                  : Change form title. Default value (if $what empty) is "Find". If $what is not empty, the text presented is $what content
  * exact_match           : If set adds exact_match field
  * types                 : If not empty adds type dropdown whith types array values
    * find_type             : types dropdown selected value
  * topics                : If not empty adds topic dropdown with topics array values
  * find_show_languages   : If value = 'y' adds lang dropdown with languages value dropdown
    * find_lang             : lang dropdown selected value
  * find_show_categories  : If value = 'y' adds categories dropdown with categories array values
    * find_categId          : categories selected value
  * find_show_num_rows    : If value = 'y' adds maxRecords field. Value: maxRecords
  *
  * Usage examples : {include file='find.tpl' _sort_mode='y'}
  *                  {include file="find.tpl" find_show_languages='y' find_show_categories='y' find_show_num_rows='y'} 
  *}

<div class="findtable">
<form method="post" action="{$smarty.server.PHP_SELF}">

{if $filegals_manager eq 'y'}<input type="hidden" name="filegals_manager" value="y" />{/if}
{if $_sort_mode eq 'y'}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}

{query _type='form_input' maxRecords='NULL' type='NULL' find='NULL' topic='NULL' lang='NULL' exact_match='NULL' categId='NULL' filegals_manager='NULL' save='NULL'}

<label class="findtitle" for="findwhat">
  {if empty($what)}
    {tr}Find{/tr}
  {else}
    {tr}{$what}{/tr}
  {/if}
</label>
<input type="text" name="find" id="findwhat" value="{$find|escape}" />
{if isset($exact_match)}
  <label class="findtitle" for="findexactmatch">
    {tr}Exact&nbsp;match{/tr}
  </label>
  <input type="checkbox" name="exact_match" id="findexactmatch" {if $exact_match ne 'n'}checked="checked"{/if}/>
{/if}
{if !empty($types)}
	<div class="findtitle findtypes">
		<select name="type">
		<option value='' {if $find_type eq ''}selected="selected"{/if}>{tr}any type{/tr}</option>
		{section name=t loop=$types}
			<option value="{$types[t].type|escape}" {if $find_type eq $types[t].type}selected="selected"{/if}>{tr}{$types[t].type}{/tr}</option>
		{/section}
		</select>
	</div>
{/if}
{if !empty($topics)}
	<div class="findtitle findtopics">
		<select name="topic">
		<option value='' {if $find_topic eq ''}selected="selected"{/if}>{tr}all topic{/tr}</option>
		{section name=ix loop=$topics}
			<option value="{$topics[ix].topicId|escape}" {if $find_topic eq $topics[ix].topicId}selected="selected"{/if}>{tr}{$topics[ix].name}{/tr}</option>
		{/section}
		</select>
	</div>
{/if}
{if $find_show_languages eq 'y' and $prefs.feature_multilingual eq 'y'}
	<div class="findtitle findlang">
		<select name="lang">
		<option value='' {if $find_lang eq ''}selected="selected"{/if}>{tr}any language{/tr}</option>
		{section name=ix loop=$languages}
			{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
			<option value="{$languages[ix].value|escape}" {if $find_lang eq $languages[ix].value}selected="selected"{/if}>{tr}{$languages[ix].name}{/tr}</option>
			{/if}
		{/section}
		</select>
	</div>
{/if}
{if $find_show_categories eq 'y' and $prefs.feature_categories eq 'y'}
	<div class="findtitle findcateg">
		<select name="categId">
		<option value='' {if $find_categId eq ''}selected="selected"{/if}>{tr}any category{/tr}</option>
		{section name=ix loop=$categories}
			<option value="{$categories[ix].categId|escape}" {if $find_categId eq $categories[ix].categId}selected="selected"{/if}>{tr}{$categories[ix].categpath}{/tr}</option>
		{/section}
		</select>
	</div>
{/if}
{if $find_show_num_rows eq 'y'}
  <label class="findtitle" for="findnumrows">
    {tr}Number of displayed rows{/tr}
  </label>
  <input type="text" name="maxRecords" id="findnumrows" value="{$maxRecords|escape}" size="3" />
{/if}
<div class="findtitle findsubmit">
  <input type="submit" name="search" value="{tr}Find{/tr}" />
</div>
</form>
</div>
 
