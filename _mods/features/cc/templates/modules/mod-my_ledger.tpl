{* $Header: /cvsroot/tikiwiki/_mods/features/cc/templates/modules/mod-my_ledger.tpl,v 1.1 2005-02-27 10:29:27 mose Exp $ *}

{* remove that *}{assign var=feature_cc value='y'}{* end of remove that *}

{if $feature_cc eq 'y' and $user}
{tikimodule title='My ledger' name="user_ledger" flip=$module_params.flip decorations=$module_params.decorations}
<table  border="0" cellpadding="0" cellspacing="0">
<tr>
<th>Currency</th>
<th>Balance</th>
<th>#</th>
</tr>
{foreach key=k item=cc from=$myinfo}
<tr>
<td class="module"><a href="cc.php?page=ledgers&cc_id={$k}">{$k}</a></td>
<td class="module" align="right">{$cc.balance}</td>
<td class="module" align="right">{$cc.tr_count}</td>
</tr>
{/foreach}
</table>
{/tikimodule}
{/if}

