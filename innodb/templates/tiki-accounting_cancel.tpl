{* $Id$ *}
{title help="accounting"}
	{$book.bookName}
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
<div id="original">
	<p>{tr}The following transaction has been cancelled.{/tr}</p>
	<div>
		<span class="aclabel">{tr}id{/tr}</span>
		{$entry.journalId}
	</div>
	<div>
		<span class="aclabel">{tr}booking date{/tr}</span>
		{$entry.journalDate|date_format:"%Y-%m-%d"}
	</div>
	<div>
		<span class="aclabel">{tr}Description{/tr}</span>
		{$entry.journalDescription}
	</div>
	<div id="debit">
		<table id="tbl_debit">
		<tr><th colspan="3">{tr}Debit{/tr}</th></tr>
		<tr><th>{tr}Text{/tr}</th><th>{tr}Account{/tr}</th><th>{tr}Amount{/tr}</th></tr>
		{foreach from=$entry.debit item=d}{cycle values="odd,even" assign="style"}
 		<tr class="{$style}">
			<td>{$d.itemText}</td>
			<td>{$d.itemAccountId}</td>
			<td>
				{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
				{$d.itemAmount|currency:$book.bookDecPoint:$book.bookThousand}
				{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
			</td>
		</tr>
		{/foreach}
		</table>
	</div>
	<div id="credit">
		<table id="tbl_credit">
		<tr><th colspan="3">{tr}Credit{/tr}</th></tr>
		<tr><th>{tr}Text{/tr}</th><th>{tr}Account{/tr}</th><th>{tr}Amount{/tr}</th></tr>
		{foreach from=$entry.credit item=c}{cycle values="odd,even" assign="style"}
		<tr>
			<td>{$c.itemText}</td>
	   		<td>{$c.itemAccountId}</td>
			<td>
				{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
				{$c.itemAmount|currency:$book.bookDecPoint:$book.bookThousand}
				{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
			</td>
	 	</tr>
		{/foreach}
        </table>
	</div>
</div>
{button href="tiki-accounting.php?bookId=$bookId" _text="{tr}Back to book page{/tr}"}
