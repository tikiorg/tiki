{*Smarty template*}
<link rel="StyleSheet"  href="styles/{$prefs.style}" type="text/css" />
<table class="email">
        <tr>
	    	<td class="heading">{tr}From{/tr}:</td>
		<td class="formcolor">{$user|capitalize:true}</td>
	</tr>
	<tr>
	    	<td class="heading">{tr}Date{/tr}:</td>
		<td class="formcolor" colspan="3">{$timestamp|date_format:"%A %e de %B, %Y %H:%M:%S"|capitalize:true}</td>
	</tr>
	<tr>
		<td class="heading">{tr}Subject{/tr}:</td>
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
