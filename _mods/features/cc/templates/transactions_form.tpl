<a href="cc.php?page=my_tr&amp;new" class="pagetitle">Record a transaction</a>
<br /><br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<form action="cc.php?page=my_tr" method="post">
<table class="formcolor">

<tr class="formrow">
<td>Currency</td>
<td><select name="cc_id" style="font-style:italic;">
<option value="">Select a Currency</option>
{foreach key=ccid item=ccinfo from=$currencies}
<option value="{$ccid}" style="font-style:normal;"{if $ccid eq $smarty.request.cc_id} selected="selected"{/if}>{$ccid}</option>
{/foreach}
</select></td>
</tr>
<tr class="formrow">
{if $tiki_p_cc_admin eq 'y'}
<tr class="formrow">
<td>From account</td>	
<td><input type='text' name='from_id' size="24" value="{$smarty.request.from_id|default:$user}" /></td>
</tr>
{/if}
<tr class="formrow">
<td>To account</td>	
<td><input type='text' name='to_id' size="24" value="{$smarty.request.to_id}" /></td>
</tr>
<tr class="formrow">
<td>Amount</td>
<td><input type='text' name='tr_amount' size="8" value="{$smarty.request.tr_amount}" /></td>
</tr>
<tr class="formrow">
<td>Description</td>
<td><input type='text' name='tr_item' size="24" value="{$smarty.request.tr_item}" />
</tr>
<tr class="formrow">
<td></td><td><input type='submit' value='record' /></td>
</tr>

</table>
</form>
