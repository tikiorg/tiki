<h1><a hField="tiki-tinvoice_prefs.php" class="pagetitle">{tr}Format Invoice{/tr}</a></h1>
<form id='invoiceprefsform' method='POST' action='tiki-tinvoice_prefs.php'>
<table>
  <tr><th>-</th><th>Field</th><th>Value</th></tr>
  <tr id='tr_invoice_emitter_address' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_emitter_address", 0);'></td>
    <th>Adresse</th>
    <td><textarea  name="invoice_emitter_address" cols="30" rows="5" >{$invoice_emitter_address|escape}</textarea><td>
  </tr>
  <tr id='tr_invoice_image' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_image", 0);'></td>
    <th>Image</th>
    <td>
      <table>
	<tr><th>URL:</th><td><input type="text" size="50" name="invoice_image[4]" value="{$invoice_image[4]|escape}" /></td>
	<tr>
	  <th>Position:</th>
	  <td>
	    x:<input type="text" size="4" name="invoice_image[0]" value="{$invoice_image[0]|escape}" />mm, y:<input type="text" size="4" name="invoice_image[1]" value="{$invoice_image[2]|escape}" />mm, 
	    Size: <input type="text" size="4" name="invoice_image[2]" value="{$invoice_image[2]|escape}" />x<input type="text" size="4" name="invoice_image[3]" value="{$invoice_image[3]|escape}" /> mm
	  </td>
	</tr>
      </table>
    </tr>
  <tr id='tr_invoice_emitter_rib' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_emitter_rib", 0);'></td>
    <th>RIB</th>
    <td>
      <table>
	<tr><th>domiciliation:</th><td><input type="text" size="16" name="invoice_emitter_rib[0]" value="{$invoice_emitter_rib[0]|escape}" /></td></tr>
	<tr><th>code banque:</th><td><input type="text" size="16" name="invoice_emitter_rib[1]" value="{$invoice_emitter_rib[1]|escape}" /></td></tr>
	<tr><th>code guichet:</th><td><input type="text" size="16" name="invoice_emitter_rib[2]" value="{$invoice_emitter_rib[2]|escape}" /></td></tr>
	<tr><th>numero compte:</th><td><input type="text" size="16" name="invoice_emitter_rib[3]" value="{$invoice_emitter_rib[3]|escape}" /></td></tr>
	<tr><th>clé RIB:</th><td><input type="text" size="16" name="invoice_emitter_rib[4]" value="{$invoice_emitter_rib[4]|escape}" /></td></tr>
	<tr><th>IBAN:</th><td><input type="text" size="16" name="invoice_emitter_rib[5]" value="{$invoice_emitter_rib[5]|escape}" /></td></tr>
	<tr><th>BIC:</th><td><input type="text" size="16" name="invoice_emitter_rib[6]" value="{$invoice_emitter_rib[6]|escape}" /></td></tr>
      </table>
    <td>
  </tr>
  <tr id='tr_invoice_emitter_tvanumber' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_emitter_tvanumber", 0);'></td>
    <th>N°TVA intracomunautaire</th>
    <td><input type="text" size="60" name="invoice_emitter_tvanumber" value="{$invoice_emitter_tvanumber|escape}" /><td>
  </tr>
  <tr id='tr_invoice_footer' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_footer", 0);'></td>
    <th>Pied de page</th>
    <td><textarea  name="invoice_footer" cols="30" rows="5" >{$invoice_footer}</textarea><td>
  </tr>
  <tr id='tr_invoice_locale' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_locale", 0);'></td>
    <th>Monetary Locale</th>
    <td><input type="text" size="60" name="invoice_locale" value="{$invoice_locale|escape}" /><td>
  </tr>
  <tr id='tr_invoice_numberingformat' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_numberingformat", 0);'></td>
    <th>Invoice numbering format</th>
    <td><input type="text" size="60" name="invoice_numberingformat" value="{$invoice_numberingformat|escape}" /><br />see strftime documentation, you can use for exemple %Y for current year, plus an unique number using ####, this will replace with an incremented number every time you emit an invoice. This number will be reset every year.<td>
  </tr>
  <tr id='tr_invoice_daycountlimit' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_daycountlimit", 0);'></td>
    <th>Day Count Limit</th>
    <td><input type="text" size="60" name="invoice_daycountlimit" value="{$invoice_daycountlimit|escape}" /><td>
  </tr>
  <tr id='tr_invoice_custom0' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_custom0", 0);'></td>
    <th>Custom 0</th>
    <td>
      title: <input type="text" size="60" name="invoice_custom0[0]" value="{$invoice_custom0[0]|escape}" /><br>
      value: <input type="text" size="60" name="invoice_custom0[1]" value="{$invoice_custom0[1]|escape}" />
    <td>
  </tr>
  <tr id='tr_invoice_custom1' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_custom1", 0);'></td>
    <th>Custom 1</th>
    <td>
      title: <input type="text" size="60" name="invoice_custom1[0]" value="{$invoice_custom1[0]|escape}" /><br>
      value: <input type="text" size="60" name="invoice_custom1[1]" value="{$invoice_custom1[1]|escape}" />
    <td>
  </tr>
  <tr id='tr_invoice_custom2' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_custom2", 0);'></td>
    <th>Custom 2</th>
    <td>
      title: <input type="text" size="60" name="invoice_custom2[0]" value="{$invoice_custom2[0]|escape}" /><br>
      value: <input type="text" size="60" name="invoice_custom2[1]" value="{$invoice_custom2[1]|escape}" />
    <td>
  </tr>
  <tr id='tr_invoice_custom3' style='display: none;'>
    <td><input type='button' value='-' onClick='show_hide_line("invoice_custom3", 0);'></td>
    <th>Custom 3</th>
    <td>
      title: <input type="text" size="60" name="invoice_custom3[0]" value="{$invoice_custom3[0]|escape}" /><br>
      value: <input type="text" size="60" name="invoice_custom3[1]" value="{$invoice_custom3[1]|escape}" />
    <td>
  </tr>
</table>
<select id='myselect' onchange='select_change();'>
  <option id='option_none' value='none' disabled selected>Choisissez...</option>
  <option id='option_invoice_emitter_address' value='invoice_emitter_address'>Adresse</option>
  <option id='option_invoice_image' value='invoice_image'>Image</option>
  <option id='option_invoice_emitter_rib' value='invoice_emitter_rib'>RIB</option>
  <option id='option_invoice_emitter_tvanumber' value='invoice_emitter_tvanumber'>N° TVA intra</option>
  <option id='option_invoice_footer' value='invoice_footer'>Pied de page</option>
  <option id='option_invoice_locale' value='invoice_locale'>Monetary Locale</option>
  <option id='option_invoice_numberingformat' value='invoice_numberingformat'>Invoice numbering format</option>
  <option id='option_invoice_daycountlimit' value='invoice_daycountlimit'>Day count limit</option>
  <option id='option_invoice_custom0' value='invoice_custom0'>Custom 0</option>
  <option id='option_invoice_custom1' value='invoice_custom1'>Custom 1</option>
  <option id='option_invoice_custom2' value='invoice_custom2'>Custom 2</option>
  <option id='option_invoice_custom3' value='invoice_custom3'>Custom 3</option>
</select>
<br><input type='submit' name='invoice_saveprefs' value='Enregistrer'>
<input type='hidden' name='enabled_prefs' value=''>
</form>

{literal}
<script language='JavaScript'>

function show_hide_line(line, show) {
	document.getElementById('option_'+line).disabled=show ? '1' : '';
	document.getElementById('myselect').value='none';
	document.getElementById('tr_'+line).style.display=show ? '' : 'none';

        var elem;
        elem=document.getElementById('active_'+line);
        if (!elem) {
                elem=document.createElement('input');
                elem.setAttribute('type', 'hidden');
                elem.setAttribute('id', 'active_'+line);
                elem.setAttribute('name', 'active_'+line);
                document.getElementById('invoiceprefsform').appendChild(elem);
        }
        document.getElementById('active_'+line).value=show ? '1' : '0';
}

function select_change() {
	var value=document.getElementById("myselect").value;
	if (value == "none") return;
	show_hide_line(value, 1);
}

{/literal}{$invoice_showcmd}{literal}

</script>
{/literal}
