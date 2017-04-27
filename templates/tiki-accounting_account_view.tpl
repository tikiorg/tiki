{* $Id$ *}
{title help="accounting"}
	{$book.bookName}:
	{tr}View account{/tr} {$account.accountId} {$account.accountName}
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
<div id="account_view">
	<dl class="dl-horizontal">
		<dt>{tr}Account number{/tr}</dt><dd>{$account.accountId}</dd>
		<dt>{tr}Account name{/tr}</dt><dd>{$account.accountName}</dd>
		<dt>{tr}Notes{/tr}</dt><dd>{$account.accountNotes}</dd>
		<dt>{tr}Budget{/tr}</dt><dd>{if $book.bookCurrencyPos==-1}{$book.bookCurrency} {/if}{$account.accountBudget|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}{if $book.bookCurrencyPos==1} {$book.bookCurrency}{/if}</dd>
		<dt>{tr}Locked{/tr}</dt><dd>{if $account.accountLocked==1}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</dd>
	</dl>
	{button href="tiki-accounting.php?bookId=$bookId" _text="Back to book page"}
	{if $tiki_p_acct_manage_accounts=='y'}
		{button href="tiki-accounting_account.php?action=edit&bookId=$bookId&accountId={$account.accountId}{ticket mode=get}" _class="timeout" _text="{tr}Edit this account{/tr}"}
		{if $account.changeable==1}{button _class="timeout" href="tiki-accounting_account.php?action=delete&bookId=$bookId&accountId={$account.accountId}{ticket mode=get}" _text="{tr}Delete this account{/tr}"}{/if}
	{/if}
</div>
{if isset($journal)}
	<div id="account_journal">
		{include file='tiki-accounting_journal.tpl'}
	</div>
{/if}

