<div class="intertrans" id="intertrans-indicator">
	<input type="checkbox" id="intertrans-active"/>
	<label for="intertrans-active">{tr}Interactive Translation{/tr}</label>
	<div>{tr}Once checked, click on any string to translate it.{/tr}</div>
</div>
<div class="intertrans" id="intertrans-form">
	<form method="post" action="tiki-interactive_trans.php">
		<table>
		</table>
		<p class="center">
			<input type="submit" value="{tr}Save translations{/tr}"/>
			<input type="reset" value="{tr}Cancel{/tr}"/>
		</p>
		<p class="description center">{tr}Changes will be applied on next page load only.{/tr}</p>
	</form>
</div>
