{* $Header: /cvsroot/tikiwiki/_mods/features/cc/templates/modules/mod-my_ledger.tpl,v 1.2 2005-04-13 12:05:27 mose Exp $ *}

{* remove that *}{assign var=feature_cc value='y'}{* end of remove that *}

{if $feature_cc eq 'y' and $user}
{tikimodule title='My ledger' name="user_ledger" flip=$module_params.flip decorations=$module_params.decorations}
<div id="ledgeo"><a href="#" onclick="document.getElementById('ledge').style.display='block';document.getElementById('ledgeo').style.display='none';">{tr}Show{/tr}</a></div>
<div id="ledge" style="display:none;">
<div><a href="#" onclick="document.getElementById('ledge').style.display='none';document.getElementById('ledgeo').style.display='block';">{tr}Hide{/tr}</a></div>
<table  border="0" cellpadding="0" cellspacing="0">
<tr>
<th>Currency</th>
<th>#</th>
<th>Balance</th>
</tr>
{foreach key=k item=cc from=$myinfo}
<tr>
<td class="module"><a href="cc.php?page=ledgers&cc_id={$k}">{$k|truncate:'16':'...'}</a></td>
<td class="module" align="right">{$cc.tr_count}</td>
<td class="module" style="text-align:right;">{$cc.balance}</td>
</tr>
{/foreach}
</table>
</div>


{/tikimodule}
{/if}

