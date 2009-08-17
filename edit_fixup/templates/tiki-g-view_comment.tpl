{*Smarty template*}
<link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
<table class="email">
        <tr>
	    	<th>{tr}From{/tr}:</th>
		<td class="formcolor">{$user|capitalize:true}</td>
	</tr>
	<tr>
	    	<th>{tr}Date{/tr}:</th>
		<td class="formcolor" colspan="3">{$timestamp|date_format:"%A %e de %B, %Y %H:%M:%S"|capitalize:true}</td>
	</tr>
	<tr>
		<th>{tr}Subject{/tr}:</th>
		<td class="formcolor" colspan="3">{$title}</td>
	</tr>
	<tr>
		{*<td class="body">{tr}Body{/tr}:</td>*}
		<td class="body" colspan="4">{$comment}</td>
	</tr>
	<tr>
	    <td class="formcolor" colspan="4">
	    	<input type="button" name="print" value="Print" onclick="{$jPrint}" />
		<input type="button" name="print" value="Close" onclick="{$jClose}" />
	    </td>
	</tr>
</table>
