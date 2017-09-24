{* $Id$ *}
{tabset}
	{tab name="{tr}My books{/tr}"}
		<h2>{tr}My books{/tr}</h2>
		<div id="booklist" class="table-responsive">
			<table class="table">
				<tr>
					<th>{tr}Id{/tr}</th>
					<th>{tr}Name{/tr}</th>
					<th>{tr}Start date{/tr}</th>
					<th>{tr}End date{/tr}</th>
					<th>{tr}Currency{/tr}</th>
					<th>{tr}Tax automation{/tr}</th>
					<th>{tr}Status{/tr}</th>
				</tr>
				{foreach item=element from=$books}
					<tr>
						<td><a href="tiki-accounting.php?bookId={$element.bookId}">{$element.bookId}</a></td>
						<td><a href="tiki-accounting.php?bookId={$element.bookId}">{$element.bookName}</a></td>
						<td>{$element.bookStartDate|tiki_short_datetime}</td>
						<td>{$element.bookEndDate|tiki_short_datetime}</td>
						<td>{$element.bookCurrency}
							({if $element.bookCurrencyPos==-1}{tr}before{/tr}{elseif $element.bookCurrencyPos==0}{tr}hide{/tr}{else}{tr}behind{/tr}){/if})
						</td>
						<td>{$element.taxAutomation}</td>
						<td>
							{if $element.bookClosed=='y'}{tr}closed{/tr}{else}{tr}open{/tr}
								{if $canCreate}
									{icon name="file-archive" class="icon timeout"
										href="tiki-accounting_books.php?action=close&bookId={$element.bookId}{ticket mode=get}"
										_confirm="{tr}Are you sure, you want to close this book?{/tr}" alt="{tr}Close book{/tr}"
									}
								{/if}
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		</div>
	{/tab}
	{if $canCreate}
		{tab name="{tr}Create a book{/tr}"}
			<h2>{tr}Create a book{/tr}</h2>
			<div id="createbookform">
				<form action="tiki-accounting_books.php" method="post" class="form-horizontal" data-toggle="validator">
					{ticket}
					<input type="hidden" name="action" value="create">
					<input type="hidden" name="bookClosed" id="bookClosed" value="n">
					<fieldset>
						<legend>{tr}Book properties{/tr}</legend>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Name of the book{/tr} <span class="text-danger">*</span></label>
							<div class="col-md-8">
								<input type="text" class=" form-control" name="bookName" id="bookName" {if $bookName}value="{$bookName}"{/if}>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}First date in journal{/tr} <span class="text-danger">*</span></label>
							<div class="col-md-8">
								{*<input type="text" class=" form-control" name="bookStartDate" id="bookStartDate" value="{$bookStartDate}">*}
								{html_select_date prefix="book_start_" time=$bookStartDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Last date in journal{/tr} <span class="text-danger">*</span></label>
							<div class="col-md-8">
								{*<input type="text" class=" form-control" name="bookEndDate" id="bookEndDate" value="{$bookEndDate}">*}
								{html_select_date prefix="book_end_" time=$bookEndDate start_year="-10" end_year="+10" field_order=$prefs.display_field_order}
							</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>{tr}Currency settings{/tr}</legend>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Currency (up to 3 letters) {/tr}</label>
							<div class="col-md-8">
								<input type="text" class=" form-control" name="bookCurrency" id="bookCurrency" value="{$bookCurrency}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Position of the currency {/tr}</label>
							<div class="col-md-8">
								<select class=" form-control" name="bookCurrencyPos" id="bookCurrencyPos">
									<option value="0"{if $bookCurrencyPos==0} selected="selected"{/if}>{tr}Show no currency{/tr}</option>
									<option value="-1"{if $bookCurrencyPos==-1} selected="selected"{/if}>{tr}Show currency in front of numbers{/tr}</option>
									<option value="1"{if $bookCurrencyPos==1} selected="selected"{/if}>{tr}Show currency behind numbers{/tr}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Decimals{/tr}</label>
							<div class="col-md-8">
								<input type="text" class=" form-control" name="bookDecimals" id="bookDecimals" value="{$bookDecimals}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Decimal point{/tr}</label>
							<div class="col-md-8">
								<select class=" form-control" name="bookDecPoint" id="bookDecPoint">
									<option value="," {if $bookDecPoint eq ','}selected="selected"{/if}>{tr}Comma{/tr}</option>
									<option value="." {if empty($bookDecPoint) or $bookDecPoint eq '.'}selected="selected"{/if}>{tr}Decimal{/tr}</option>
									<option value=" " {if $bookDecPoint eq ' '}selected="selected"{/if}>{tr}Space{/tr}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Thousands separator{/tr}</label>
							<div class="col-md-8">
								<select class=" form-control" name="bookThousand" id="bookThousand" >
									<option value="," {if empty($bookThousand) or $bookThousand eq ','}selected="selected"{/if}>{tr}Comma{/tr}</option>
									<option value="." {if $bookThousand eq '.'}selected="selected"{/if}>{tr}Decimal point{/tr}</option>
									<option value=" " {if $bookThousand eq ' '}selected="selected"{/if}>{tr}Space{/tr}</option>
								</select>
						</div>
					</fieldset>
					<fieldset>
						<legend>CSV export settings</legend>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Separator{/tr}</label>
							<div class="col-md-8">
								<input type="text" class=" form-control" name="exportSeparator" id="exportSeparator" value="{$exportSeparator}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}Quote strings with{/tr}</label>
							<div class="col-md-8">
								<input type="text" class=" form-control" name="exportQuote" id="exportQuote" value="{$exportQuote}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">{tr}End of Line{/tr}</label>
							<div class="col-md-8">
								<select class=" form-control" name="exportEOL" id="exportEOL">
									<option value="CR"{if $exportEOL=='CR'} selected="selected"{/if}>{tr}Carriage return{/tr}</option>
									<option value="LF"{if $exportEOL=='LF'} selected="selected"{/if}>{tr}Line feed{/tr}</option>
									<option value="CRLF"{if $exportEOL=='CRLF'} selected="selected"{/if}>{tr}Carriage Return/Line feed{/tr}</option>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>{tr}Automatic Tax Calculation{/tr}</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label class="control-label">{tr}Allow automatic tax calculation{/tr}</label>
							</div>
							<div class="col-md-8">
								<div class="radio">
									<label>
										<input type="radio" name="bookAutoTax" id="bookAutoTaxY"{if $bookAutoTax!='n'} checked="checked"{/if} value="y">{tr}Yes{/tr}<br>
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="bookAutoTax" id="bookAutoTaxN"{if $bookAutoTax=='n'} checked="checked"{/if} value="n">{tr}No{/tr}<br>
									</label>
								</div>
							</div>
						</div>
					</fieldset>
					<input type="submit" class="btn btn-default btn-sm timeout col-md-offset-4" name="create" value="{tr}Create a new book{/tr}">
				</form>
			</div>
		{/tab}
	{/if}
{/tabset}
