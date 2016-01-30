<ul>
	<li>
		<a href="#">{tr}Edit{/tr}</a>
		<ul>
			<li>
				<a href="#">{tr}Cell{/tr}</a>
				<ul>
					<li><a onclick="sheetInstance.setCellRef(); return false;">{tr}Set Reference{/tr}</a></li>
				</ul>
			</li>
			<li>
				<a menu="menuEditRow_menuInstance">{tr}Row{/tr}</a>
				<ul>
					<li><a onclick="sheetInstance.merge(); return false;">{tr}Merge{/tr}</a></li>
					<li><a onclick="sheetInstance.unmerge(); return false;">{tr}Un-Merge{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addRow(null, null, ':last'); return false;" title="{tr}Adds an additional row to bottom of the spreadsheet.{/tr}">{tr}Add Row{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addRowMulti(); return false;" title="{tr}Adds an additional rows to bottom of the spreadsheet.{/tr}">{tr}Add Multi-Rows{/tr}</a></li>
					<li><a onclick="sheetInstance.deleteRow(); return false;" title="{tr}Delets the current row thats highlighted.{/tr}">{tr}Delete Row{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addRow(null, true); return false;" title="{tr}Inserts an additional row after currently selected row.{/tr}">{tr}Insert Row Before{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addRow(); return false;" title="{tr}Inserts an additional row after currently selected row.{/tr}">{tr}Insert Row After{/tr}</a></li>
					<!--<li><a onclick="sheetInstance.toggleHide.rowAll();" title="{tr}Unhides all the hidden rows.{/tr}">{tr}Show All{/tr}</a></li>
					<li><a onclick="sheetInstance.toggleHide.row();" title="{tr}Hides or shows the currently selected row.{/tr}">{tr}Toggle Hide Row{/tr}</a></li>-->
				</ul>
			</li>
			<li>
				<a menu="menuEditColumn_menuInstance">{tr}Column{/tr}</a>
				<ul>
					<li><a onclick="sheetInstance.controlFactory.addColumn(null, null, ':last'); return false;" title="{tr}Adds an additional column to the right of the spreadsheet.{/tr}">{tr}Add Column{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addColumnMulti(); return false;" title="{tr}Adds an additional columns to the right of the spreadsheet.{/tr}">{tr}Add Multi-Columns{/tr}</a></li>
					<li><a onclick="sheetInstance.deleteColumn(); return false;" title="{tr}Deletes the current column thats highlighted.{/tr}">{tr}Delete Column{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addColumn(null, true); return false;" title="{tr}Inserts an additional column after currently selected column.{/tr}">{tr}Insert Column Before{/tr}</a></li>
					<li><a onclick="sheetInstance.controlFactory.addColumn(); return false;" title="{tr}Inserts an additional column after currently selected column.{/tr}">{tr}Insert Column After{/tr}</a></li>
					<li><a onclick="sheetInstance.fillUpOrDown(); return false;" title="{tr}Fill down current cell value.{/tr}">{tr}Fill Down{/tr}</a></li>
					<li><a onclick="sheetInstance.fillUpOrDown(true); return false;" title="{tr}Fill up current cell value.{/tr}">{tr}Fill Up{/tr}</a></li>
					<!--<li><a onclick="sheetInstance.toggleHide.columnAll();" title="{tr}Unhides all the hidden columns.{/tr}">{tr}Show All{/tr}</a></li>
					<li><a onclick="sheetInstance.toggleHide.column();" title="{tr}Hides or shows the currently selected column.{/tr}">{tr}Toggle Hide Column{/tr}</a></li>-->
				</ul>
			</li>
			<li>
				<a menu="menuEditSheet_menuInstance">{tr}Sheet{/tr}</a>
				<ul>
					<li><a onclick="sheetInstance.addSheet(); return false;" title="{tr}Add new spreadsheet.{/tr}">{tr}Add Spreadsheet{/tr}</a></li>
					<li><a onclick="$.sheet.deleteSheet(sheetInstance); return false;" title="{tr}Delete the current spreadsheet.{/tr}">{tr}Delete Spreadsheet{/tr}</a></li>
					<li><a onclick="sheetInstance.calc(sheetInstance.obj.tableBody()); return false;" title="{tr}Recompiles the current sheet{/tr}">{tr}Refresh calculations{/tr}</a></li>
					<li><a onclick="sheetInstance.sheetTitle(); return false;" title="{tr}Change the title of the sheet.{/tr}">{tr}Title{/tr}</a></li>
				</ul>
			</li>
			<li><a onclick="sheetInstance.cellFind(); return false;">{tr}Find{/tr}</a></li>
			<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val()); return false;">{tr}Get Cell Range{/tr}</a></li>
			<li>
				<a href="#">{tr}Wrap Cell Range{/tr}</a>
				<ul>
					<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val(), 'SUM'); return false;" title="{tr}Wrap with SUM{/tr}">SUM()</a></li>
					<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val(), 'CEILING'); return false;" title="{tr}Wrap with CEILING{/tr}">CEILING()</a></li>
					<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val(), 'COUNT'); return false;" title="{tr}Wrap with COUNT{/tr}">COUNT()</a></li>
					<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val(), 'MAX'); return false;" title="{tr}Wrap with MAX{/tr}">MAX()</a></li>
					<li><a onclick="sheetInstance.getTdRange(null, sheetInstance.obj.formula().val(), 'MIN'); return false;" title="{tr}Wrap with MIN{/tr}">MIN()</a></li>
				</ul>
			</li>
			<li><a onclick="sheetInstance.cellUndoable.undoOrRedo(true); return false;">{tr}Undo{/tr}</a></li>
			<li><a onclick="sheetInstance.cellUndoable.undoOrRedo(); return false;">{tr}Redo{/tr}</a></li>
			<li><a onclick="sheetInstance.toggleState(); return false;">{tr}Toggle State{/tr}</a></li>
		</ul>
	</li>
	<li>
		<a href="#">View</a>
		<ul>
			<li>
				<a menu="menuViewFunctionReference_menuInstance">{tr}Function Reference{/tr}</a>
				<ul>
					<li><a>{tr}Usage Example: =SUM(SUM(A1:B1) + SUM(D7)){/tr}</a></li>
					<li><a>{tr}=(TRUE(N(A1)) || FALSE(N(B1))){/tr}</a></li>
					<li><a title="{tr}Returns the absolute value of a number{/tr}">ABS()</a></li>
					<li><a title="{tr}Returns a rounded number{/tr}">AVG(), AVERAGE()</a></li>
					<li><a title="{tr}Returns a number rounded up based on a multiple of significance{/tr}">CEILING()</a></li>
					<li><a title="{tr}Counts the number of cells that contain a value{/tr}">COUNT()</a></li>
					<li><a title="{tr}Counts full days from a specific date - format(YYYY,MM,DD){/tr}">DAYSFROM()</a></li>
					<li><a title="{tr}Converts a number to text, using a currency format - options(number, decimals, symbol){/tr}">DOLLAR()</a></li>
					<li><a title="{tr}Returns a logical value of FALSE{/tr}">FALSE()</a></li>
					<li><a title="{tr}Returns a text representation of a number rounded to a specified number of decimal places - options(number, decimals, useCommas){/tr}">FIXED()</a></li>
					<li><a title="{tr}Returns a number rounded down based on a multiple of significance{/tr}">FLOOR()</a></li>
					<li><a title="{tr}Returns the integer portion of a number{/tr}">INT()</a></li>
					<li><a title="{tr}Returns the largest value from the numbers provided{/tr}">MAX()</a></li>
					<li><a title="{tr}Returns the smallest value from the numbers provided{/tr}">MIN()</a></li>
					<li><a title="{tr}Converts a value to a number{/tr}">N()</a></li>
					<li><a title="{tr}Gets full date of today{/tr}">NOW()</a></li>
					<li><a title="{tr}Returns the mathematical constant called pi, which is 3.14159265358979{/tr}">PI()</a></li>
					<li><a title="{tr}Returns the result of a number raised to a given power{/tr}">POWER(x, y)</a></li>
					<li><a title="{tr}Returns a random number that is greater than or equal to 0 and less than 1{/tr}">RAND(), RND()</a></li>
					<li><a title="{tr}Returns a number rounded to a specified number of digits{/tr}">ROUND()</a></li>
					<li><a title="{tr}Returns all of the values in each of the specified cells and added together{/tr}">SUM()</a></li>
					<li><a title="{tr}Gets full date of today{/tr}">TODAY()</a></li>
					<li><a title="{tr}Returns a logical value of TRUE{/tr}">TRUE()</a></li>
					<li><a title="{tr}Converts a text value that represents a number to a number{/tr}">VALUE()</a></li>
				</ul>
			</li>
			<li><a onclick="sheetInstance.toggleFullScreen();">{tr}Toggle Full Screen{/tr}</a></li>
		</ul>
	</li>
	<li>
		<a href="#">Style</a>
		<ul>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleBold'); return false;">{tr}Bold{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleItalics'); return false;">{tr}Italics{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleUnderline', 'styleLineThrough'); return false;">{tr}Underline{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleLineThrough', 'styleUnderline'); return false;">{tr}Strikethrough{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleLeft', 'styleCenter styleRight'); return false;">{tr}Left{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleCenter', 'styleLeft styleRight'); return false;">{tr}Center{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleRight', 'styleLeft styleCenter'); return false;">{tr}Right{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleUpper', 'styleCapital styleLower'); return false;">{tr}Uppercase{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleCapital', 'styleUpper styleLower'); return false;">{tr}Capitalize{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleLower', 'styleCapital styleUpper'); return false;">{tr}Lowercase{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleTop', 'styleMiddle styleBottom'); return false;">{tr}Top{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleMiddle', 'styleTop styleBottom'); return false;">{tr}Middle{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.cellStyleToggle('styleBottom', 'styleTop styleMiddle'); return false;">{tr}Bottom{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.fontReSize('up'); return false;">{tr}Font Size +{/tr}</a></li>
			<li><a class="cellStyleToggle" onclick="sheetInstance.fontReSize('down'); return false;">{tr}Font Size -{/tr}</a></li>
		</ul>
	</li>
</ul>
