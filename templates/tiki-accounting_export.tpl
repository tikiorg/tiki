{title help="accounting"}
	{$book.bookName}: {tr}Export {$what}{/tr}
{/title}
<div id="export">
 <form action="tiki-accounting_export.php" method="post">
 	<input type="hidden" name="action" value="export" />
 	<input type="hidden" name="what" value="{$what}" />
 	<input type="hidden" name="bookId" value="{$bookId}" />
 	{if isset($accountId)}<input type="hidden" name="accountId" value="{$accountId}" />{/if}
	<fieldset>
		<legend>{tr}Export CSV settings{/tr}</legend>
			<label>{tr}Separator between fields{/tr}</label>
			<input type="text" name="separator" id="separator" value="{$book.exportSeparator|escape}" /><br />
			<label>{tr}End of Line{/tr}</label>
			<select name="eol" id="eol">
				<option value="CR"{if $book.exportEOL=='CR'} selected="selected"{/if}>{tr}Carriage return{/tr}</option>
				<option value="LF"{if $book.exportEOL=='LF'} selected="selected"{/if}>{tr}Line feed{/tr}</option>
				<option value="CRLF"{if $book.exportEOL=='CRLF'} selected="selected"{/if}>{tr}Carriage Return/Line feed{/tr}</option>
			</select><br />
			<label>{tr}Quote character for text{/tr}</label>
			<input type="text" name="quote" id="quote" value="{$book.exportQuote|escape}" /><br />
			<input type="submit" name="submit" value="{tr}Export as CSV{/tr}" onclick="this.form.target='_blank';return true;"/>
			{button href="tiki-accounting.php?bookId=$bookId" _text="Back to book page"}  
	</fieldset>
 </form>
</div>
