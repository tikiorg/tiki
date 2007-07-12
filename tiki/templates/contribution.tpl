{* $Header: /cvsroot/tikiwiki/tiki/templates/contribution.tpl,v 1.14 2007-07-12 18:46:30 sylvieg Exp $ *}
{if $feature_contribution eq 'y' and count($contributions) gt 0}
<tr>
<td class="formcolor">
{popup_init src="lib/overlib.js"}
{if $contribution_needed eq 'y'}<span class="highlight">{/if}{tr}Type of contribution:{/tr}{if $contribution_needed eq 'y'}</span>{/if}</td>
<td class="formcolor">
   <select name="contributions[]" multiple="multiple" size="5">
   {section name=ix loop=$contributions}
    <option value="{$contributions[ix].contributionId|escape}"{if $contributions[ix].selected eq 'y'} selected="selected"{/if} >{if $contributions[ix].contributionId > 0}{$contributions[ix].name|escape}{/if}</option>
{assign var="help" value=$help|cat:$contributions[ix].name|cat:": "|cat:$contributions[ix].description|cat:"<br />"}
   {/section}
   </select>
<a {popup text=$help|replace:'"':"'" width=500}><img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
</td></tr>

{if $feature_contributor_wiki eq 'y' and $section eq 'wiki page' and empty($in_comment)}
<tr>
<td class="formcolor">{tr}Contributors{/tr}</td>
<td class="formcolor">
   <select name="contributors[]" multiple="multiple" size="5">
	{foreach key=userId item=u from=$users}
	{if $u ne $user}<option value="{$userId}"{if !empty($contributors) and in_array($userId, $contributors)} selected="selected"{/if}>{$u}</option>{/if}
	{/foreach}
   </select>

</td></tr>
{/if}

{/if}
