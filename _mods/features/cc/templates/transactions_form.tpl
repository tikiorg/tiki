<h1><a href="cc.php?page=my_tr&amp;new" class="pagetitle">Record a transaction</a>
{if $currency}in {$currency}{/if}
</h1>
<span class="button2"><a href="cc.php" class="linkbut">{tr}Help{/tr}</a></span><br />
<br />

{if $msg}<div class="simplebox">{$msg}</div>{/if}

<form action="cc.php?page=my_tr" method="post">
<table class="formcolor">

<tr class="formrow">
<td>Description</td>
{if $tiki_p_cc_admin eq 'y'}
<td>From account</td>	
{/if}
<td>To account</td>	
<td>Amount</td>
</tr>

<tr class="formrow">
<td><input type='text' name='tr_item' size="24" value="{$smarty.request.tr_item}" />
{if $tiki_p_cc_admin eq 'y'}
<td><input type='text' name='from_id' size="24" value="{$smarty.request.from_id}" /></td>
{/if}
<td><input type='text' name='to_id' size="24" value="{$smarty.request.to_id}" /></td>
<td>
<input type='text' name='tr_amount' size="8" value="{$smarty.request.tr_amount}" />
{if $currency}
<input type="hidden" name="cc_id" value="{$currency}" />{$currency}
{else}
<select name="cc_id" style="font-style:italic;">
<option value="">Select a Currency</option>
{foreach key=ccid item=ccinfo from=$currencies}
<option value="{$ccid}" style="font-style:normal;"{if $ccid eq $smarty.request.cc_id} selected="selected"{/if}>{$ccid}</option>
{/foreach}
</select>
{/if}
</td>


</tr>

<tr class="formrow">
<td><input type='submit' value='record' /></td>
</tr>

</table>
</form>
