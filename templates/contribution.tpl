{* $Header: /cvsroot/tikiwiki/tiki/templates/contribution.tpl,v 1.11 2006-11-29 18:29:08 sylvieg Exp $ *}
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
{/if}
