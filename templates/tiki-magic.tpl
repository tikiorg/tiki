<div id="magicPanel">
	<div class="iconbar">
		<a title="{tr}Refresh{/tr}" href="tiki-magic.php?featurechain={$feature.feature_path|escape:"url"}&amp;refresh=1">{icon _id='arrow_refresh'}</a>
	</div>

	<form method="post">
		<table class="configTable" width="100%" cellpadding="0" cellspacing="0">
			{assign var=counter value=1}
			{section name=feature loop=$features}

				{* Show a heading for features with the option to enable or disable the feature. *}
				{if $features[feature].feature_type eq 'feature' || $features[feature].feature_type eq 'subfeature'}
					<tr class="{$features[feature].feature_type}Heading {if $features[feature].value eq 'y'}enabled{else}disabled{/if}">
						<td class="featureName">
							<nobr>
								<h4>{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub></h4>
								{if $features[feature].keyword neq ''}
									<a href="http://doc.tikiwiki.org/{$features[feature].keyword}" title="{tr}Help{/tr}" target="tikihelp">{icon _id=help style="vertical-align:middle"}</a>
								{/if}
							</nobr>
						</td>
						<td class="featureEnabled">
							<nobr>
								<input type="checkbox" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="on" {if $features[feature].value eq 'y'}checked="checked"{/if} />
								<label for="{$features[feature].setting_name}" class="featureLabel">{tr}Enable{/tr}</label>
							</nobr>
						</td>
						<td class="useDefault">{* Invisible Feature *}</td>
						{if $features[feature].status eq 'experimental'}
							<td>
								<nobr>{tr}This is an experimental feature{/tr}</nobr>
							</td>
						{else}
							<td class="spacer">&nbsp;</td>
						{/if}
						{if ($features[feature].template neq '') and ($features[feature].value eq 'y')}
							<td class="goLink">
								<a href="{$features[feature].pageurl}" class="goLink">{tr}Go{/tr}&raquo;</a>
							</td>
						{else}
							<td class="spacer">&nbsp;</td>
						{/if}
					</tr>
				{elseif $features[feature].feature_type eq 'container' || $features[feature].feature_type eq 'configurationgroup' || $features[feature].feature_type eq 'system'}
					<tr class="{$features[feature].feature_type}Heading">
						<td colspan="4">
							<h4 class="configSection">{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub></h4>
							{if $features[feature].feature_count eq 0}
								{if ($features[feature].template neq '')}
									<td class="goLink">
										<a href="{$features[feature].pageurl}" class="goLink">{tr}Go{/tr}&raquo;</a>
									</td>
								{else}
									<td class="goLink">
										<a name="container{$features[feature].feature_id}" href="tiki-magic.php?featurechain={$features[feature].feature_path|escape:'url'}" class="goLink" title="{tr}Go{/tr}">{tr}Go{/tr}&raquo;</a>
									</td>
								{/if}
							{else}
								<td class="spacer">&nbsp;</td>
							{/if}
						</td>
					</tr>	
						{* It'd be superfun if you could go to, say, the article list page from the article configuration page; however some of the pages require
							additional parameters (i.e. for performing actions on a particular content item), so I'll need to distinguish between the two. *}
				
				{elseif $features[feature].feature_type eq 'functionality'}
					{* For anything else, display a label; followed by an appropriate input box. *}
					{* Flags *}
				
				{elseif false && $features[feature].feature_type eq 'flag'}
					<div class="">
						<input type="checkbox" class="flag" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="on" {if $features[feature].value eq 'y'}checked="checked"{/if} />
						<label for="{$features[feature].setting_name}" style="display:inline">
							{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub>
						</label>
					</div>
				{else}
					<tr class="setting">
						<td>
							<label for="{$features[feature].setting_name}" class="formLabel">
								{tr}{$features[feature].feature_name}{/tr}<sub>({$features[feature].feature_id})</sub>
							</label>
						</td>
					<td colspan="2">
						{* Flags *}
						{if $features[feature].feature_type eq 'flag'}
							<input type="checkbox" class="flag" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="on" {if $features[feature].value eq 'y'}checked="checked"{/if} />

						{* Simple text fields *}
						{elseif $features[feature].feature_type eq 'simple' || $features[feature].feature_type eq 'byref'}
							<input type="text" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="{$features[feature].value}" />

						{* Numeric fields (like a text field, but shorter) *}
						{elseif $features[feature].feature_type eq 'int'}
							<input type="text" size="3" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}" value="{$features[feature].value}" />

						{* Text Area *}
						{elseif $features[feature].feature_type eq 'textarea'}
							<textarea cols="50" rows="5" name="{$features[feature].setting_name}" id="{$features[feature].setting_name}">{$features[feature].value}</textarea>

						{* Timezone values *}
						{elseif $features[feature].feature_type eq 'timezone'}
							<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
								{foreach key=tz item=tzinfo from=$features[feature].enumeration}
									{math equation="floor(x / (3600000))" x=$tzinfo.offset assign=offset}{math equation="(x - (y*3600000)) / 60000" y=$offset x=$tzinfo.offset assign=offset_min format="%02d"}
									<option value="{$tz}" {if $features[feature].value eq $tz}selected="selected"{/if}>{$tz|escape:"html"} (UTC{if $offset >= 0}+{/if}{$offset}h{if $offset_min gt 0}{$offset_min}{/if})</option>
								{/foreach}
							</select>

						{* Limit Category (limit a content item to certain categories) *}
						{elseif $features[feature].feature_type eq 'limitcategory'}
							<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
								<option value="-1" {if $value eq -1 or $value eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
								<option value="0" {if $value eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
								{section name=ix loop=$features[feature].enumeration}
									<option value="{$features[feature].enumeration[ix].categId|escape}" {if $features[feature].enumeration[ix].categId eq $value}selected="selected"{/if}>{$features[feature].enumeration[ix].categpath}</option>
								{/section}
							</select>

						{* Limit Category (limit a content item to certain categories) *}
						{elseif $features[feature].feature_type eq 'selectcategory'}
							<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
								<option value="-1" {if $value eq -1 or $value eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
								{section name=ix loop=$features[feature].enumeration}
									<option value="{$features[feature].enumeration[ix].categId|escape}" {if $features[feature].enumeration[ix].categId eq $value}selected="selected"{/if}>{$features[feature].enumeration[ix].categpath}</option>
								{/section}
							</select>

						{elseif $features[feature].enumeration neq ''}
							{if $features[feature].multiple eq 'on'}
								<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}[]" multiple="true" size="5">
									{foreach item=label key=value from=$features[feature].enumeration}
										<option value="{$value}"{foreach item=i key=v from=$features[feature].value}{if $value eq $i} selected="selected"{/if}{/foreach}>{tr}{$label}{/tr}</option>
									{/foreach}
								</select>
							{else}
								<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
									{foreach item=label key=value from=$features[feature].enumeration}
										<option value="{$value}" {if $value eq $features[feature].value}selected="selected"{/if}>{tr}{$label}{/tr}</option>
									{/foreach}
								</select>
							{/if}
	
						{* Placeholder for things that need a custom handler, that I haven't written yet*}
						{elseif $features[feature].feature_type eq 'languages'}
							<select id="{$features[feature].setting_name}" name="{$features[feature].setting_name}">
								{section name=ix loop=$features[feature].enumeration}
									<option value="{$features[feature].enumeration[ix].value|escape}" {if $features[feature].value eq $features[feature].enumeration[ix].value}selected="selected"{/if}>{$features[feature].enumeration[ix].name}</option>
								{/section}
							</select>
						{elseif $features[feature].feature_type eq 'special'}
							This setting requires a special input handler.
							{* Just a reminder for anything else that I might have forgotten *}
						{else}
							This is a {$features[feature].feature_type}, and I haven't done anything with it yet.
						{/if}
					</td>
	
					{if $features[feature].tip neq ''}
						<td colspan="2">
							<div class="tip">{eval var=$features[feature].tip}</div>
						</td>
					{else}
						<td class="spacer" colspan="2">&nbsp;</td>
					{/if}
				</tr>
			{/if}
			{* SEXYTODO: Allow checking the box for this. Right here. Where it's needed. 
					p.s. remember to save the value too. 
					p.p.s. that will involve looking at the depends in addition to each of the features on the page. 
					p.p.p.s sometimes the depended upon setting will be on the same page, so look out for contradictory values. 
			*}
			{if $features[feature].depends_on neq 0}
				<tr>
					<td colspan="3">&nbsp;</td>
					<td colspan="3">
						{tr}Requires{/tr}{tr}{$features[feature].depends_on.feature_name}{/tr} ({if $features[feature].depends_on.value eq 'y'}{tr}Enabled{/tr}{else}{tr}Not Enabled{/tr}{/if}).
					</td>
				</tr>
			{/if}
		{/section}
		<tr>
			<td colspan="6">
				<input type="submit" name="submit" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>	
</form>
</div>
