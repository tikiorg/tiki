<div class="intertrans" id="intertrans-indicator">
	<input type="checkbox" id="intertrans-active">
	<label for="intertrans-active">{tr}Interactive Translation{/tr}</label>
	<div>{tr}Once checked, click on any string to translate it.{/tr}</div>
</div>
<div class="intertrans" id="intertrans-form">
	<form method="post" action="tiki-interactive_trans.php">
		<div class="center" id="intertrans-empty" style="display: none">
			<strong>{tr}Couldn't find any translatable string.{/tr}</strong>
		</div>
		<table>
		</table>
		<p class="center">
			<input id="intertrans-submit" type="submit" class="btn btn-default" value="{tr}Save translations{/tr}">
			<input id="intertrans-cancel" class="btn btn-warning" type="reset" value="{tr}Cancel{/tr}">
			<input id="intertrans-close" class="btn btn-warning" type="reset" value="{tr}Close{/tr}" style="display: none;">
		</p>
		<p id="intertrans-help" class="description center">{tr}Changes will be applied on next page load only.{/tr}</p>
	</form>
</div>
