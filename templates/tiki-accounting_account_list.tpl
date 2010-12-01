<a href="tiki-accounting_export.php?bookId={$bookId}&action=print&what=accounts" target="new"><img src="images/ico_print.gif" border="0" align="right" alt="{tr}printable version{/tr}"/></a>
<a href="tiki-accounting_export.php?bookId={$bookId}&action=settings&what=accounts"><img src="images/ico_table.gif" border="0" align="right" alt="{tr}export table{/tr}"/></a>
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
  <th>{tr}Actions{/tr}</th>
 </tr>
{foreach from=$accounts item=a}{cycle values="odd,even" assign="style"}
 <tr class="{$style}">
  <td style="text-align:right"><a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}">{$a.accountId}</a></td>
  <td><a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}">{$a.accountName|escape}</a></td>
  <td>{$a.accountNotes|escape}</td>
  <td style="text-align:right">
  	{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
  	{$a.accountBudget|currency:$book.bookDecPoint:$book.bookThousand}
  	{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
  </td>
  <td>{if $a.accountLocked==1}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</td>
  <td style="text-align:right">
	{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
  	{$a.debit|currency:$book.bookDecPoint:$book.bookThousand}
  	{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
  </td>
  <td style="text-align:right">
  	{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
  	{$a.credit|currency:$book.bookDecPoint:$book.bookThousand}
  	{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
  </td>
  <td>{$a.accountTax}</td>
  <td>
   {if $tiki_p_acct_manage_accounts=='y'}
   <a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=edit"><img src="img/icons/edit.gif" alt="{tr}edit account{/tr}" border="0" /></a>
   <a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=lock"><img src="img/icons/lock_topic.gif" alt="{tr}lock account{/tr}" border="0" /></a>
   <a href="tiki-accounting_account.php?bookId={$bookId}&accountId={$a.accountId}&action=delete" onclick="return confirmDeleteAccount()"><img src="img/icons/del.gif" alt="delete account" border="0" /></a>{/if}
  </td>
 </tr>
{/foreach}
</table>
{button href="tiki-accounting_account.php?action=new&bookId=$bookId" _text="{tr}Create a new account{/tr}"}
 