{* $Id$ *}
{if $prefs.feature_contribution eq 'y'}
	{if count($contributions) gt 0}
		<tr>
			<td>
			{if $contribution_needed eq 'y'}<span class="mandatory_note highlight">{/if}<label for="contributions">{tr}Type of contribution:{/tr}</label>{if $prefs.feature_contribution_mandatory eq 'y'}<em class='mandatory_star'> *</em>{/if}{if $contribution_needed eq 'y'}</span>{/if}</td>
				<td>
					<select id="contributions" name="contributions[]" multiple="multiple" size="5">
						{section name=ix loop=$contributions}
							<option value="{$contributions[ix].contributionId|escape}"{if $contributions[ix].selected eq 'y'} selected="selected"{/if} >{if $contributions[ix].contributionId > 0}{$contributions[ix].name|escape}{/if}</option>
							{assign var="help" value=$help|cat:$contributions[ix].name|cat:": "|cat:$contributions[ix].description|cat:"<br>"}
						{/section}
					</select>
					<a title="{tr}Help{/tr}" {popup text=$help|replace:'"':"'" width=500}>{icon name='help'}</a>
			</td>
		</tr>
		{if $prefs.feature_contributor_wiki eq 'y' and $section eq 'wiki page' and empty($in_comment)}
			<tr>
				<td><label for='contributors'>{tr}Contributors{/tr}</label></td>
				<td>
					<select id="contributors" name="contributors[]" multiple="multiple" size="5">
						{foreach key=userId item=u from=$users}
							{if $u ne $user}<option value="{$userId}"{if !empty($contributors) and in_array($userId, $contributors)} selected="selected"{/if}>{$u}</option>{/if}
						{/foreach}
					</select>
				</td>
			</tr>
		{/if}
	{elseif $tiki_p_admin eq 'y'}
		{tr}No records found{/tr}
	{else}
		<tr><td>&nbsp;</td></tr>
	{/if}
{/if}
