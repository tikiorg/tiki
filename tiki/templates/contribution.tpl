{if $feature_contribution eq 'y' and count($contributions) gt 0}
{popup_init src="lib/overlib.js"}
<tr>
<td class="formcolor">{if $contribution_needed eq 'y'}<span class="highlight">{/if}{tr}Type of contribution:{/tr}{if $contribution_needed eq 'y'}</span>{/if}</td>
<td class="formcolor">
   <select name="contributions[]" multiple="multiple" size="3">
   {section name=ix loop=$contributions}
    <option value="{$contributions[ix].contributionId|escape}"{if $contributions[ix].selected eq 'y'} selected="selected"{/if} >{if $contributions[ix].contributionId > 0}{$contributions[ix].name|escape}{/if}</option>
{assign var="help" value=$help|cat:$contributions[ix].name|cat:": "|cat:$contributions[ix].description|cat:"<br />"}
   {/section}
   </select>
<a {popup text=$help|replace:'"':"'" width=500}><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
</td></tr>
{/if}