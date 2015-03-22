{* $Id$ *}
{title help="accounting"}
	{$book.bookName}:
	{if $action=="new"}{tr}Create new account{/tr}{else}{tr}Edit Account{/tr}{/if}
	{$account.accountId} {$account.accountName}
{/title}
{if !empty($errors)}
	<div class="alert alert-warning">
		{icon name='error' alt="{tr}Error{/tr}" style="vertical-align:middle" align="left"}
		{foreach from=$errors item=m name=errors}
			{$m}
			{if !$smarty.foreach.errors.last}<br>{/if}
		{/foreach}
	</div>
{/if}
<div id="account_form">
	<form method="post" action="tiki-accounting_account.php">
		<input type="hidden" name="action" value="{$action}">
		<input type="hidden" name="bookId" value="{$bookId}">
		<input type="hidden" name="accountId" value="{$account.accountId}">
		<fieldset>
			<legend>Account properties</legend>
			<label class="aclabel">{tr}Account number{/tr}</label>
			{if $account.changeable}<input type="text" name="newAccountId" id="newAccountId" value="{$account.accountId}">{else}{$account.accountId}{/if}<br>
			<label class="aclabel">{tr}Account name{/tr}</label>
			<input type="text" name="accountName" id="accountName" value="{$account.accountName}"><br>
			<label class="aclabel">{tr}Notes{/tr}</label>
			<textarea name="accountNotes" id="accountNotes" cols="40" rows="3">{$account.accountNotes}</textarea><br>
			<label class="aclabel">{tr}Budget{/tr}</label>
			<input type="text" name="accountBudget" id="accountBudget" value="{$account.accountBudget}"><br>
			<label class="aclabel">{tr}Locked{/tr}</label>
			<input type="radio" name="accountLocked" id="accountLocked" {if $account.accountLocked==1}checked="checked"{/if} value="1"> {tr}Yes{/tr}
			<input type="radio" name="accountLocked" id="accountUnlocked" {if $account.accountLocked!=1}checked="checked"{/if} value="0">{tr}No{/tr}<br>
			<input type="submit" class="btn btn-default btn-sm" name="submit" value="{if $action=='new'}{tr}Create account{/tr}{else}{tr}Modify account{/tr}{/if}">
			{if $account.changeable==1 && $action=="edit"}
				{button href="tiki-accounting_account.php?bookId=$bookId&accountId=$accountId&action=delete" _text="{tr}Delete this account{/tr}"}
			{/if}
			{button href="tiki-accounting.php?bookId=$bookId" _text="{tr}Back to book page{/tr}"}
		</fieldset>
	</form>
</div>
{if isset($journal)}
	<div id="account_journal">
		{include file='tiki-accounting_journal.tpl'}
	</div>
{/if}
