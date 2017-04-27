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
			{if !$smarty.foreach.errors.last}{/if}
		{/foreach}
	</div>
{/if}
<div id="account_form">
	<form class="form-horizontal" method="post" action="tiki-accounting_account.php">
		{ticket}
		<input class="form-control" type="hidden" name="action" value="{$action}">
		<input class="form-control" type="hidden" name="bookId" value="{$bookId}">
		<input class="form-control" type="hidden" name="accountId" value="{$account.accountId}">
		<fieldset>
			<legend>Account properties</legend>
			<div class="form-group">
				<label class="control-label col-md-4">{tr}Account number{/tr}</label>
				<div class="col-md-8">
					<input class="form-control" class="form-control" type="text" name="newAccountId" id="newAccountId" {if !$account.changeable}readonly{/if} value="{$account.accountId}">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">{tr}Account name{/tr}</label>
				<div class="col-md-8">
					<input class="form-control" type="text" name="accountName" id="accountName" value="{$account.accountName}">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">{tr}Notes{/tr}</label>
				<div class="col-md-8">
					<textarea class="form-control" name="accountNotes" id="accountNotes" cols="40" rows="3">{$account.accountNotes}</textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">{tr}Budget{/tr}</label>
				<div class="col-md-8">
					<input class="form-control" type="text" name="accountBudget" id="accountBudget" value="{$account.accountBudget}">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">{tr}Locked{/tr}</label>
				<div class="col-md-8">
					<div class="radio">
						<label>
							<input type="radio" name="accountLocked" id="accountLocked" {if $account.accountLocked==1}checked="checked"{/if} value="1">
							{tr}Yes{/tr}
						</label>
					</div>
					<div class="radio">
						<label>
							<input type="radio" name="accountLocked" id="accountUnlocked" {if $account.accountLocked!=1}checked="checked"{/if} value="0">
							{tr}No{/tr}
						</label>
					</div>
				</div>
			</div>

			<input type="submit" class="btn btn-primary timeout col-md-offset-4" name="submit" value="{if $action=='new'}{tr}Create account{/tr}{else}{tr}Modify account{/tr}{/if}">
			{if $account.changeable==1 && $action=="edit"}
				{button href="tiki-accounting_account.php?bookId={$bookId}&accountId={$accountId}&action=delete{ticket mode=get}" _class="timeout" _text="{tr}Delete this account{/tr}"}
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
