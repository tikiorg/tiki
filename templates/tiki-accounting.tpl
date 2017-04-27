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
				<dl class="dl-horizontal">
					<dt>{tr}Id{/tr}</dt><dd>{$book.bookId}</dd>
					<dt>{tr}Name{/tr}</dt><dd>{$book.bookName}</dd>
					<dt>{tr}Start date{/tr}</dt><dd>{$book.bookStartDate}</dd>
					<dt>{tr}End date{/tr}</dt><dd>{$book.bookEndDate}</dd>
					<dt>{tr}Closed{/tr}</dt><dd>{if $book.bookClosed=='y'}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</dd>
					<dt>{tr}Currency{/tr}</dt><dd>{$book.bookCurrency} ({if $book.bookCurrencyPos==-1}{tr}before{/tr}{elseif $book.bookCurrencyPos==1}{tr}after{/tr}{else}{tr}don't display{/tr}{/if})</dd>
					<dt>{tr}Decimals{/tr}</dt><dd>{$book.bookDecimals}</dd>
					<dt>{tr}Decimal Point{/tr}</dt><dd>{$book.bookDecPoint}</dd>
					<dt>{tr}Thousands separator{/tr}</dt><dd>{$book.bookThousand}</dd>
					<dt>{tr}Auto Tax{/tr}</dt><dd>{if $book.bookAutoTax=='y'}{tr}Yes{/tr}{else}{tr}No{/tr}{/if}</dd>
				</dl>
			</div>
		</div>
		<div class="box">
			<h3 class="boxtitle">{tr}Tasks{/tr}</h3>
			<div class="box-data" style="width: 500px">
				{if $canBook}{button href="tiki-accounting_entry.php?bookId={$bookId}{ticket mode=get}" _class="timeout" _text="{tr}Book new entries{/tr}"}<br>
				{button href="tiki-accounting_stack.php?bookId={$bookId}&hideform=1{ticket mode=get}" _class="timeout" _text="{tr}Confirm stack entries{/tr}"}<br>{/if}
				{if $canStack}{button href="tiki-accounting_stack.php?bookId={$bookId}{ticket mode=get}" _class="timeout" _text="{tr}Book into Stack{/tr}"}<br>{/if}
			</div>
		</div>
	{/tab}
	{tab name="{tr}Accounts{/tr}"}
		<h2>{tr}Accounts{/tr}</h2>
		<div style="max-height: 80%; overflow: scroll;">
			{include file="tiki-accounting_account_list.tpl"}
		</div>
	{/tab}
	{*{tab name="{tr}Bank accounts{/tr}"}*}
		{*<h2>{tr}Bank accounts{/tr}</h2>*}
	{*{/tab}*}
	{tab name="{tr}Journal{/tr}"}
		<h2>{tr}Journal{/tr}</h2>
		<div style="max-height: 80%; overflow: scroll;">
			{if $journalLimit!=0}
				{button href="tiki-accounting.php?bookId={$bookId}&cookietab=4&journalLimit=0{ticket mode=get}" text="{tr}Fetch all{/tr}"}
			{/if}
			{include file="tiki-accounting_journal.tpl"}
		</div>
	{/tab}
{/tabset}
