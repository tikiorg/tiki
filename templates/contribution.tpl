{* $Id$ *}
{if empty($contributions) and $tiki_p_admin eq 'y'}
{tr}No records found{/tr}
{/if}
{if $prefs.feature_contribution eq 'y' and count($contributions) gt 0}
<tr>
<td class="formcolor">
{popup_init src="lib/overlib.js"}
{if $contribution_needed eq 'y'}<span class="highlight">{/if}<label for="contributions">{tr}Type of contribution:</label>{/tr}{if $contribution_needed eq 'y'}</span>{/if}</td>
<td class="formcolor">
   <select id="contributions" name="contributions[]" multiple="multiple" size="5">
   {section name=ix loop=$contributions}
    <option value="{$contributions[ix].contributionId|escape}"{if $contributions[ix].selected eq 'y'} selected="selected"{/if} >{if $contributions[ix].contributionId > 0}{$contributions[ix].name|escape}{/if}</option>
{assign var="help" value=$help|cat:$contributions[ix].name|cat:": "|cat:$contributions[ix].description|cat:"<br />"}
   {/section}
   </select>
<a {popup text=$help|replace:'"':"'" width=500}>{icon _id='help'}</a>
</td></tr>

{if $prefs.feature_contributor_wiki eq 'y' and $section eq 'wiki page' and empty($in_comment)}
<tr>
<td class="formcolor"><label for='contributors'>{tr}Contributors{/tr}</label></td>
<td class="formcolor">
   <select id="contributors" name="contributors[]" multiple="multiple" size="5">
	{foreach key=userId item=u from=$users}
	{if $u ne $user}<option value="{$userId}"{if !empty($contributors) and in_array($userId, $contributors)} selected="selected"{/if}>{$u}</option>{/if}
	{/foreach}
   </select>

</td></tr>
{/if}
{else}
<tr><td>&nbsp;</td></tr>
{/if}
