{* $Id$ *}
<table>
	<tr>
		<th>{tr}Account{/tr}</th>
		<th>{tr}Account name{/tr}</th>
		<th>{tr}Notes{/tr}</a></th>
		<th>{tr}Budget{/tr}</th>
		<th>{tr}Locked{/tr}</th>
		<th>{tr}Debit{/tr}</th>
		<th>{tr}Credit{/tr}</th>
		<th>{tr}Tax{/tr}</th>
		{if $tiki_p_acct_manage_accounts=='y'}
			<th style="width:64px;">{tr}Actions{/tr}</th>
		{/if}
	</tr>
	{foreach from=$accounts item=a}{cycle values="odd,even" assign="style"}
		<tr class="{$style}">
			<td style="text-align:right"><a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}">{$a.accountId}</a></td>
			<td><a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}">{$a.accountName|escape}</a></td>
			<td>{$a.accountNotes|escape}</td>
			<td style="text-align:right">
				{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
				{$a.accountBudget|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}
				{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
			</td>
			<td>{if $a.accountLocked==1}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</td>
			<td style="text-align:right">
				{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
				{$a.debit|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}
				{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
			</td>
			<td style="text-align:right">
				{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
				{$a.credit|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}
				{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
			</td>
			<td>{$a.accountTax}</td>
			{if $tiki_p_acct_manage_accounts=='y'}
				<td style="width:64px;">
					<a class="icon" href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=edit">
						{icon name="edit" alt="{tr}edit account{/tr}"}
					</a>
					<a class="icon" href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=lock">
						{if $a.accountLocked==1}
							{icon name="unlock" alt="{tr}unlock account{/tr}" _confirm="{tr}Are you sure you want to unlock this account?{/tr}"}
						{else}
							{icon name="lock" alt="{tr}lock account{/tr}" _confirm="{tr}Are you sure you want to lock this account?{/tr}"}
						{/if}
					</a>
					<a class="icon" href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=delete">
						{icon name="remove" alt="{tr}delete account{/tr}" _confirm="{tr}Are you sure, you want to delete this account{/tr}"}
					</a>
				</td>
			{/if}
		</tr>
	{/foreach}
</table>
{button href="tiki-accounting_account.php?action=new&bookId=$bookId" _text="{tr}Create a new account{/tr}"}
<a class="icon" href="tiki-accounting_export.php?action=print&bookId={$bookId}&what=accounts" target="new">
	{icon name="print" alt="{tr}printable version{/tr}"}
</a>
<a class="icon" href="tiki-accounting_export.php?action=settings&bookId={$bookId}&what=accounts">
	{icon name="export" alt="{tr}export table{/tr}"}
</a>
