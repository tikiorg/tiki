<h1>{tr}Edit Bank{/tr}</h1>
<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_transaction_edit.php?id_emitter={$me_tikiid}">{tr}new transaction{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_graph.php">{tr}Graphs{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_transactions.php">{tr}Transactions{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_banks.php">{tr}Banks{/tr}</a>
</div>
<hr />
<br />
<h2>{tr}Edit bank account{/tr}</h2>
<hr />
<form method='POST'action="" >
<input type="hidden" name="bankId" value="{$bank.id}" />
<input type="hidden" name="userId" value="{$bank.userId}" />
 <span style="width: 100px; display: block; float: left" >{tr}Name{/tr}:</span><input type="texte"  style="width: 200px" name="bankName" value="{$bank.name}" /><br />
 <span style="width: 100px; display: block; float: left">{tr}Bank{/tr}:</span><input type="texte" style="width: 200px" name="bank" value="{$bank.bank}" /><br />
 <span style="width: 100px; display: block; float: left">{tr}Account{/tr}:</span><input type="texte" style="width: 200px" name="bankNumber" value="{$bank.account_nb}" /><br />
 <span style="width: 100px; display: block; float: left">{tr}RIB{/tr}:</span><input type="texte" style="width: 400px" name="bankRib" value="{$bank.rib}" /><br />
 <span style="width: 100px; display: block; float: left">{tr}SWIFT{/tr}:</span><input type="texte" style="width: 400px" name="bankSwift" value="{$bank.swift}" /><br />
	<input type="submit" name="save" value="{tr}save{/tr}" />
</form>
