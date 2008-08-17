<div style="float:right">
	<a title="{tr}Refresh{/tr}" href="tiki-magic.php?featurechain={$featurechain|escape:"url"}&amp;refresh=1">{icon _id='arrow_refresh'}</a>
</div>
<form method="post">
{section name=container loop=$containers}
<a href="#container{$containers[container].feature_id}">{tr}{$containers[container].feature_name}{/tr}</a> 
{/section}<br />

{section name=feature loop=$features}
{* Show a heading for features with the option to enable or disable the feature.  *}
{if $features[feature].feature_type eq 'feature'}
	<div class="configSetting"><a name="container{$features[feature].feature_id}"></a><h4 class="configSection">{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub></h4>
	<div class="configSetting"><label for="{$features[feature].setting_name}" class="formLabel">{tr}Enabled{/tr}</label><input type="checkbox" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="on" {if $features[feature].value eq 'y'}checked="checked"{/if} />{if $features[feature].status eq 'experimental'}<em>{tr}This is an experimental feature{/tr}</em>{/if}
{if $features[feature].template neq ''}<a href="{$features[feature].template}.php">{tr}Go{/tr}!</a>{/if}
{* Check to see if system help is on;  and use that base URL. *}	
{if $features[feature].keyword neq ''} <a href="http://doc.tikiwiki.org/{$features[feature].keyword}">{tr}Help{/tr}</a>{/if}
	</div>
	</div>
{* Show a heading with a dividing line for things that are containers  *}
{elseif ($features[feature].feature_type eq 'container' || $features[feature].feature_type eq 'configurationgroup' || $features[feature].feature_type eq 'system') and ($features[feature].feature_count > 0)}
	<hr style="clear: both" />
	<div class="configSetting"><a name="container{$features[feature].feature_id}"></a><h4 class="configSection">{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub></h4>
	</div>
{* It'd be superfun if you could go to, say, the article list page from the article configuration page; however some of the pages require
	 additional parameters (i.e. for performing actions on a particular content item), so I'll need to distinguish between the two. *}
{elseif $features[feature].feature_type eq 'functionality'}
{* For anything else,  display a label;  followed by an appropriate input box.  *}
{else}
	<div class="configSetting"><label for="{$features[feature].setting_name}" class="formLabel">{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub></label>
	{* Flags  *}
	{if $features[feature].feature_type eq 'flag'}
		<input type="checkbox" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="on" {if $features[feature].value eq 'y'}checked="checked"{/if} />
	{* Simple text fields *}
	{elseif $features[feature].feature_type eq 'simple' || $features[feature].feature_type eq 'byref'}
		<input type="text" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="{$features[feature].value}" />
	{* Numeric fields (like a text field, but shorter) *}
	{elseif $features[feature].feature_type eq 'int'}
		<input type="text" size="3" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="{$features[feature].value}" />
	{* Text Area  *}
	{elseif $features[feature].feature_type eq 'textarea'}
		<textarea cols="50" rows="5" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}">{$features[feature].value}</textarea>
	{* Special cases  *}
	{elseif $features[feature].enumeration neq ''}
		<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">{foreach item=label key=value from=$features[feature].enumeration}<option value="{$value}" {if $value eq $features[feature].value}selected="selected"{/if}>{$label}</option>{/foreach}</select>
	{* Timezone values *}
	{elseif $features[feature].feature_type eq 'timezone'}
	<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
		{foreach key=tz item=tzinfo from=$timezones}
			{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
			<option value="{$tz}" {if $features[feature].value eq $tz}selected="selected"{/if}>{$tz|escape:"html"} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
		{/foreach}
	</select>
	{* Limit Category (limit a content item to certain categories) *}
	{elseif $features[feature].feature_type eq 'limitcategory'}
		<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}"><option value="-1" {if $value eq -1 or $value eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
			<option value="0" {if $value eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
			{section name=ix loop=$catree}
			<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $value}selected="selected"{/if}>{$catree[ix].categpath}</option>
			{/section}
		</select>
	{* Limit Category (limit a content item to certain categories) *}
	{elseif $features[feature].feature_type eq 'selectcategory'}
		<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
			<option value="-1" {if $value eq -1 or $value eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
			{section name=ix loop=$catree}
			<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $value}selected="selected"{/if}>{$catree[ix].categpath}</option>
			{/section}
			</select>
	{* Placeholder for things that need a custom handler, that I haven't written yet*}
	{elseif $features[feature].feature_type eq 'languages'}
		<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
			{section name=ix loop=$languages}
			<option value="{$languages[ix].value|escape}" {if $features[feature].value eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
			{/section}
		</select>
	{elseif $features[feature].feature_type eq 'special'}
		This setting requires a special input handler.
	{* Just a reminder for anything else that I might have forgotten *}
	{else}
		This is a {$features[feature].feature_type}, and I haven't done anything with it yet.
	{/if}
	</div>
{/if}
<!-- SEXYTODO: Allow checking the box for this.  Right here.  Where it's needed.  p.s. remember to save the value too.  p.p.s. that will involve looking at the depends in addition to each of the features on the page. p.p.p.s sometimes the depended upon setting will be on the same page, so look out for contradictory
values. -->
{if $features[feature].depends_on neq 0}{tr}This depends on {/tr}{tr}{$features[feature].depends_on.feature_name}{/tr} ({if $features[feature].depends_on.value eq 'y'}{tr}Enabled{/tr}{else}{tr}Not Enabled{/tr}{/if}){/if}
{/section}
<input type="submit" name="submit" />
</form>
