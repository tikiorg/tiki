{if $trackerId}
	<form class="simple" method="post" action="{service controller=tracker action=remove}">
		<p>{tr _0=$name}Do you really want to remove the %0 tracker?{/tr}</p>
		<div class="submit">
			<input type="hidden" name="confirm" value="1">
			<input type="hidden" name="trackerId" value="{$trackerId|escape}">
			<input type="submit" value="{tr}Remove{/tr}">
		</div>
	</form>
{else}
	<a href="tiki-list_trackers.php">{tr}Back to tracker list{/tr}
{/if}
