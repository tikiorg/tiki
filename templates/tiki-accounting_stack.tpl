{* $Id$ *}
{literal}
<script language="javascript">

function setAmount() {
	document.getElementById('debitAmount').value=document.getElementById('totalAmount').value;
	document.getElementById('creditAmount').value=document.getElementById('totalAmount').value;
}

function splitDebit() {
	document.getElementById('Row_SplitCredit').style.display = "none";
	var tbl = document.getElementById('tbl_debit');
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow-1);
	row.innerHTML=document.getElementById('Row_StartDebit').innerHTML;
}

function splitCredit() {
	document.getElementById('Row_SplitDebit').style.display = "none";
	var tbl = document.getElementById('tbl_credit');
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow-1);
	row.innerHTML=document.getElementById('Row_StartCredit').innerHTML;
}

function setAccount(v) {
	account.value=v;
}

var account='';
</script>
{/literal}

{title help="accounting"}
	{$book.bookName}: {tr}Book a transaction into the stack{/tr}
{/title}

{if !empty($errors)}
	<div class="alert alert-warning">
		{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}{/if}
		{/foreach}
	</div>
{/if}

<div class="row">
	<div id="mask" class="col-md-8" style="{if $hideform==1} display: none;{/if}">
		<form method="post" action="{if $req_url}{$req_url}{else}tiki-accounting_stack.php{/if}" class="form-horizontal">
			{ticket}
			{if $firstid}<input type="hidden" name="firstid" value="{$firstid}">{/if}
			{if $statementId}<input type="hidden" name="statementId" value="{$statementId}">{/if}
			<input type="hidden" name="bookId" value="{$bookId}">
			<input type="hidden" name="stackId" value="{$stackId}">
			<input type="hidden" name="action" value="book">
			<fieldset>
				<legend>{tr}Post{/tr}</legend>
				<div class="form-group">
					<label class="control-label col-md-4">{tr}Booking Date{/tr} <span class="text-danger">*</span></label>
					<div class="col-md-8">
						{html_select_date prefix="stack_" time=$stackDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4">{tr}Description{/tr}</label>
					<div class="col-md-8">
						<textarea class="form-control" name="stackDescription" id="stackDescription" rows="3">{$stackDescription}</textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-4">{tr}Amount{/tr} <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<input name="totalAmount" id="totalAmount" type="number" class="form-control" value="{$totalAmount}" onchange="javascript:setAmount()">
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Debit{/tr}</legend>
				<table id="tbl_debit" class="table">
					<tr>
						<th>{tr}Text{/tr}</th>
						<th>{tr}Account{/tr}</th>
						<th>{tr}Amount{/tr} <span class="text-danger">*</span></th>
					</tr>
					{section name=debit loop=$debitAccount}{assign var='i' value=$smarty.section.debit.iteration-1}
						<tr {if $i==0}id="Row_StartDebit" {/if}>
							<td>
								<input type="text" class="form-control" name="debitText[]" value="{$debitText[$i]}">
							</td>
							<td>
								<select name="debitAccount[]" class="form-control" style="width:180px" onfocus="account=this">
									{foreach from=$accounts item=a}
										<option value="{$a.accountId}"{if $a.accountId==$debitAccount[$i]} selected="selected"{/if}>{$a.accountId} {$a.accountName}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<input name="debitAmount[]" class="form-control" {if $i==0}id="debitAmount" {/if}size="10" value="{$debitAmount[$i]}">
							</td>
						</tr>
					{/section}
					<tr id="Row_SplitDebit"{if count($creditAccount)>1} style="display:none;"{/if}>
						<td colspan="3">
							<input type="button" class="btn btn-default btn-sm pull-right" value="{tr}Add entry{/tr}" id="SplitDebit" onclick="javascript:splitDebit()">
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend>{tr}Credit{/tr}</legend>
				<table id="tbl_credit" class="table">
					{section name=credit loop=$creditAccount}{assign var='i' value=$smarty.section.credit.iteration-1}
						<tr>
							<th>{tr}Text{/tr}</th>
							<th>{tr}Account{/tr}</th>
							<th>{tr}Amount{/tr} <span class="text-danger">*</span></th>
						</tr>
						<tr {if $i==0}id="Row_StartCredit" {/if}>
							<td>
								<input class="form-control" type="text" name="creditText[]" value="{$creditText[$i]}">
							</td>
							<td>
								<select class="form-control" name="creditAccount[]" style="width:180px" onfocus="account=this">
									{foreach from=$accounts item=a}
										<option value="{$a.accountId}"{if $a.accountId==$creditAccount[$i]} selected="selected"{/if}>{$a.accountId} {$a.accountName}</option>
									{/foreach}
								</select>
							</td>
							<td>
								<input name="creditAmount[]" class="form-control" {if $i==0}id="creditAmount" {/if}size="10" value="{$creditAmount[$i]}">
							</td>
						</tr>
					{/section}
					<tr id="Row_SplitCredit"{if count($creditAccount)>1} style="display:none;"{/if}>
						<td colspan="3">
							<input type="button" class="btn btn-default btn-sm pull-right" value="{tr}Add entry{/tr}" id="SplitCredit" onclick="javascript:splitCredit()">
						</td>
					</tr>
				</table>
			</fieldset>
			<input type="submit" class="btn btn-primary timeout" name="bookstack" id="bookstack" value="{tr}Book{/tr}">
			{button href="tiki-accounting.php?bookId=$bookId" _text="{tr}Back to book page{/tr}"}
		</form>
	</div>
	<div class="col-md-4">
		<div id="accountlist" style="max-height=400px; overflow: scroll;{if $hideform==1} display: none;{/if}">
			<table class="table">
				<tr><th colspan="2">{tr}Accounts{/tr}</th></tr>
				{if $tiki_p_account_manage=='y'}
					<tr><td colspan="2"><a href="tiki-accounting_account.php?bookId={$bookId}&action=new">{tr}Create account{/tr}</a></td></tr>
				{/if}
				{foreach from=$accounts item=a}
					<tr class="{cycle values="odd,even"} tips" {popup caption="{tr}Notes{/tr}" text=$a.accountNotes}>
						<td class="accompactlist">
							{if $tiki_p_account_manage=='y'}
								<a class="timeout" href="tiki-accounting_account.php?bookId={$bookId}&action=edit&accountId={$a.accountId}{ticket mode=get}"><img src="img/icons/edit.gif" alt="edit"></a>
								<a class="timeout" href="tiki-accounting_account.php?bookId={$bookId}&action=delete&accountId={$a.accountId}{ticket mode=get}"><img src="img/icons/del.gif" alt="delete"></a>
							{/if}
							<a href="javascript:setAccount({$a.accountId})">{$a.accountId}</a>
						</td>
						<td class="accompactlist">
							{$a.accountName}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	</div>
</div>
<br>
<div id="journal">
	{include file='tiki-accounting_stacklist.tpl'}
</div>
