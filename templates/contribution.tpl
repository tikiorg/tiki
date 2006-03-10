{if $feature_contribution eq 'y' and count($contributions) gt 0}
<tr>
<td class="formcolor">{if $contribution_needed eq 'y'}<span class="highlight">'{/if}{tr}Type of contribution:{/tr}{if $contribution_needed eq 'y'}</span>'{/if}</td>
<td class="formcolor">
   <select name="contributions[]" multiple="multiple" size="3">
   {section name=ix loop=$contributions}
    <option value="{$contributions[ix].contributionId|escape}"{if $contributions[ix].selected eq 'y'} selected="selected"{/if} >{if $contributions[ix].contributionId > 0}{$contributions[ix].name|cat:": "|cat:$contributions[ix].description|truncate:50:"...":true|escape}{/if}</option>
   {/section}
   </select>
</td></tr>
{/if}