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
{tabset}
	{tab name="{tr}General{/tr}"}
		<h2>{tr}General{/tr}</h2>
		<div class="box">
			<h3 class="boxtitle">{tr}This book{/tr}</h3>
			<div class="box-data" style="width: 500px">
				<div><div class="aclabel">{tr}Id{/tr}</div>{$book.bookId}</div>
				<div><div class="aclabel">{tr}Name{/tr}</div>{$book.bookName}</div>
				<div><div class="aclabel">{tr}Start date{/tr}</div>{$book.bookStartDate}</div>
				<div><div class="aclabel">{tr}End date{/tr}</div>{$book.bookEndDate}</div>
				<div><div class="aclabel">{tr}Closed{/tr}</div>{if $book.bookClosed=='y'}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</div>
				<div><div class="aclabel">{tr}Currency{/tr}</div>{$book.bookCurrency} ({if $book.bookCurrencyPos==-1}{tr}before{/tr}{elseif $book.bookCurrencyPos==1}{tr}after{/tr}{else}{tr}don't display{/tr}{/if})</div>
				<div><div class="aclabel">{tr}Decimals{/tr}</div>{$book.bookDecimals}</div>
				<div><div class="aclabel">{tr}Decimal Point{/tr}</div>{$book.bookDecPoint}</div>
				<div><div class="aclabel">{tr}Thousands separator{/tr}</div>{$book.bookThousand}</div>
				<div><div class="aclabel">{tr}Auto Tax{/tr}</div>{if $book.bookAutoTax=='y'}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</div>
			</div>
		</div>
		<div class="box">
			<h3 class="boxtitle">{tr}Tasks{/tr}</h3>
			<div class="box-data" style="width: 500px">
				{if $canBook}{button href="tiki-accounting_entry.php?bookId=$bookId" _text="{tr}Book new entries{/tr}"}<br>
				{button href="tiki-accounting_stack.php?bookId=$bookId&hideform=1" _text="{tr}Confirm stack entries{/tr}"}<br>{/if}
				{if $canStack}{button href="tiki-accounting_stack.php?bookId=$bookId" _text="{tr}Book into Stack{/tr}"}<br>{/if}
			</div>
		</div>
	{/tab}
	{tab name="{tr}Accounts{/tr}"}
		<h2>{tr}Accounts{/tr}</h2>
		<div style="max-height: 80%; overflow: scroll;">
			{include file="tiki-accounting_account_list.tpl"}
		</div>
	{/tab}
	{tab name="{tr}Bank acounts{/tr}"}
		<h2>{tr}Bank acounts{/tr}</h2>
	{/tab}
	{tab name="{tr}Journal{/tr}"}
		<h2>{tr}Journal{/tr}</h2>
		<div style="max-height: 80%; overflow: scroll;">
			{if $journalLimit!=0}
				{button href="tiki-accounting.php?bookId=$bookId&cookietab=4&journalLimit=0" text="{tr}Fetch all{/tr}"}
			{/if}
			{include file="tiki-accounting_journal.tpl"}
		</div>
	{/tab}
{/tabset}
