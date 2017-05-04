{* $Id$ *}
{title help="accounting"}
	{$book.bookName}
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
<div id="original">
	<div class="alert alert-success">
		<p>{tr}The following transaction has been cancelled.{/tr}</p>
	</div>
	<dl class="dl-horizontal">
		<dt>{tr}ID{/tr}</dt><dd>{$entry.journalId}</dd>
		<dt>{tr}Booking Date{/tr}</dt><dd>{$entry.journalDate|date_format:"%Y-%m-%d"}</dd>
		<dt>{tr}Description{/tr}</dt><dd>{$entry.journalDescription}</dd>
	</dl>
	<div id="debit">
		<h4>{tr}Debit{/tr}</h4>
		<table id="tbl_debit" class="table">
			<tr><th>{tr}Text{/tr}</th><th>{tr}Account{/tr}</th><th>{tr}Amount{/tr}</th></tr>
			{foreach from=$entry.debit item=d}{cycle values="odd,even" assign="style"}
				<tr class="{$style}">
					<td>{$d.itemText}</td>
					<td>{$d.itemAccountId}</td>
					<td>
						{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
						{$d.itemAmount|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}
						{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
	<div id="credit">
		<h4>{tr}Credit{/tr}</h4>
		<table id="tbl_credit" class="table">
			<tr><th>{tr}Text{/tr}</th><th>{tr}Account{/tr}</th><th>{tr}Amount{/tr}</th></tr>
			{foreach from=$entry.credit item=c}{cycle values="odd,even" assign="style"}
				<tr>
					<td>{$c.itemText}</td>
					<td>{$c.itemAccountId}</td>
					<td>
						{if $book.bookCurrencyPos==-1}{$book.bookCurrency}{/if}
						{$c.itemAmount|number_format:$book.bookDecimals:$book.bookDecPoint:$book.bookThousand}
						{if $book.bookCurrencyPos==1}{$book.bookCurrency}{/if}
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
</div>
{button href="tiki-accounting.php?bookId=$bookId" _text="{tr}Back to book page{/tr}"}
