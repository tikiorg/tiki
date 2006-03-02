{if $feature_contribution eq 'y' and count($contributions) gt 0}
<tr>
<td class="formcolor">{tr}Type of contribution:{/tr}</td>
<td class="formcolor">
   <select name="contributions[]" multiple="multiple" size="3">
   {section name=ix loop=$contributions}
    <option value="{$contributions[ix].contributionId|escape}">{$contributions[ix].name|escape}</option>
   {/section}
   </select>
</td></tr>
{/if}