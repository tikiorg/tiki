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
	<div class="simplebox highlight">
		{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br />{/if}
		{/foreach}
	</div>
{/if}


<div id="accountlist" style="float:right; max-height=400px; overflow: scroll;{if $hideform==1} display: none;{/if}">
	<table>
 		<tr><th colspan="2">{tr}Accounts{/tr}</th></tr>
 		{if $tiki_p_account_manage=='y'}
 		<tr><td colspan="2"><a href="tiki-accounting_account.php?bookId={$bookId}&action=new">{tr}Create account{/tr}</a></td></tr>{/if}
{foreach from=$accounts item=a}
		<tr class="{cycle values="odd,even"}"  {popup caption='{tr}Notes{/tr}' text=$a.accountNotes}>
 			<td class="accompactlist">
 				{if $tiki_p_account_manage=='y'}
 				<a href="tiki-accounting_account.php?bookId={$bookId}&action=edit&accountId={$a.accountId}"><img src="img/icons/edit.gif" alt="edit" border="0" /></a>
				<a href="tiki-accounting_account.php?bookId={$bookId}&action=delete&accountId={$a.accountId}"><img src="img/icons/del.gif" alt="delete" border="0" /></a>
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
<div id="mask" style="float:left;{if $hideform==1} display: none;{/if}">
	<form method="post" action="tiki-accounting_stack.php">
		{if $firstid}<input type="hidden" name="firstid" value="{$firstid}" />{/if}
		{if $statementId}<input type="hidden" name="statementId" value="{$statementId}" />{/if}
		<input type="hidden" name="bookId" value="{$bookId}" />
		<input type="hidden" name="stackId" value="{$stackId}" />
		<input type="hidden" name="action" value="book" />
		<fieldset>
			<legend>{tr}Post{/tr}</legend>
			<div>
				<label class="aclabel">{tr}booking date{/tr}</label>
				<input name="stackDate" id="stackDate" value="{$stackDate|default:$smarty.now|date_format:"%Y-%m-%d"}" />
			</div>
			<div>
				<label class="aclabel">{tr}Description{/tr}</label>
				<textarea name="stackDescription" id="stackDescription" cols="40" rows="3">{$stackDescription}</textarea>
			</div>
			<div>
				<label class="aclabel">{tr}Amount{/tr}</label>
				<input name="totalAmount" id="totalAmount" value="{$totalAmount}" onchange="javascript:setAmount()"/>		
			</div>
		</fieldset>	
		<fieldset>
			<legend>{tr}Debit{/tr}</legend>
			<table id="tbl_debit">
{section name=debit loop=$debitAccount}{assign var='i' value=`$smarty.section.debit.iteration-1`}
	 			<tr {if $i==0}id="Row_StartDebit" {/if}>
	   				<td>
	   					<label>{tr}Text{/tr}</label>
	   					<input type="text" name="debitText[]" value="{$debitText[$i]}" />
	   				</td>
	   				<td>
	   					<label>{tr}Account{/tr}</label>
						<select name="debitAccount[]" style="width:180px" onfocus="account=this">
						{foreach from=$accounts item=a}
							<option value="{$a.accountId}"{if $a.accountId==$debitAccount[$i]} selected="selected"{/if}>{$a.accountId} {$a.accountName}</option>
						{/foreach}
						</select>
					</td>
					<td>
						<label>{tr}Amount{/tr}</label>
						<input name="debitAmount[]" {if $i==0}id="debitAmount" {/if}size="10" value="{$debitAmount[$i]}"/>
					</td>
	 			</tr>
{/section}
				<tr id="Row_SplitDebit"{if count($creditAccount)>1} style="display:none;"{/if}>
					<td colspan="3">
						<input type="button" value="{tr}Add entry{/tr}" id="SplitDebit" onclick="javascript:splitDebit()" />
					</td>
				</tr>
        	</table>	
		</fieldset>
		<fieldset>
			<legend>{tr}Credit{/tr}</legend>
			<table id="tbl_credit">
{section name=credit loop=$creditAccount}{assign var='i' value=`$smarty.section.credit.iteration-1`}
				<tr {if $i==0}id="Row_StartCredit" {/if}>
					<td>
						<label>{tr}Text{/tr}</label>
						<input type="text" name="creditText[]" value="{$creditText[$i]}" />
					</td>
	   				<td>
	   					<label>{tr}Account{/tr}</label>
						<select name="creditAccount[]" style="width:180px" onfocus="account=this">
						{foreach from=$accounts item=a}
							<option value="{$a.accountId}"{if $a.accountId==$creditAccount[$i]} selected="selected"{/if}>{$a.accountId} {$a.accountName}</option>
						{/foreach}
            			</select>
	   				</td>
	   				<td>
	   					<label>{tr}Amount{/tr}</label>
						<input name="creditAmount[]" {if $i==0}id="creditAmount" {/if}size="10" value="{$creditAmount[$i]}"/>
					</td>
	 			</tr>
{/section}
	 			<tr id="Row_SplitCredit"{if count($creditAccount)>1} style="display:none;"{/if}>
	 				<td colspan="3">
	 					<input type="button" value="{tr}Add entry{/tr}" id="SplitCredit" onclick="javascript:splitCredit()" />
	 				</td>
	 			</tr>
        	</table>
		</fieldset>
		<input type="submit" name="bookstack" id="bookstack" value="{tr}Book{/tr}" />
		{button href="tiki-accounting.php?bookId=$bookId" _text="{tr}Back to book page{/tr}"}
	</form>
</div>
<div id="journal" style="clear: both;">
{include file="tiki-accounting_stacklist.tpl}
</div>
