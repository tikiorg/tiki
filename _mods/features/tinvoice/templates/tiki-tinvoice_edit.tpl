<h1>
 <a href="tiki-edit-invoice.php" class="pagetitle">
 {if $invoiceId > 0}
  {tr}Edit Invoice{/tr}
 {else}
  {tr}Add Invoice{/tr}
 {/if}
 </a>
</h1>
<div class="navbar">
{if $tiki_p_admin eq 'y'}<a class="linkbut" href="tiki-tinvoice_list.php">{tr}Invoices list{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}Invoices preferences{/tr}</a>
</div>
<form method='POST' action='tiki-tinvoice_edit.php'>

<table width='100%'><tr><td>
<table>
 <tr><th>Date:</th><td>
 {if $feature_jscalendar ne 'y'} 
 <input type='text' name='invoice_date' value='{$invoice_date|escape}'></td></tr>
{else}
{jscalendar id="start" date=$invoice_date fieldname="invoice_date" ifFormat="%Y-%m-%d" align="Bc" showtime='n'}
{/if}

 <tr>
  <th>Client:</th>
  <td>
   <select name='invoice_id_receiver' id='invoice_id_receiver' onChange='xajax_myajax_getcontact(document.getElementById("invoice_id_receiver").value)'>
    <option >Choisir...</option>
    {foreach from=$contacts item=contact}
    <option {if $contact.contactId eq $invoice_id_receiver} selected {/if} value='{$contact.contactId}'>{if $contact.ext.7}{$contact.ext.7}{else}{$contact.firstName|escape} {$contact.lastName|escape}{/if}</option>
    {/foreach}
   </select>
  </td>
 </tr>
 <tr><th>Libelle:</th><td><input type='text' name='invoice_libelle' value='{$invoice_libelle|escape}'></td></tr>
 <tr><th>Date limite de paiement:</th><td><input type='text' name='invoice_datelimit' value='{$invoice_datelimit|escape}'></td></tr>
 <tr><th>Ref devis:</th><td><input type='text' name='invoice_refdevis' value='{$invoice_refdevis|escape}'></td></tr>
 <tr><th>Ref Bon de Commande:</th><td><input type='text' name='invoice_refbondecommande' value='{$invoice_refbondecommande|escape}'></td></tr>
 <tr><th>TVA intra du client:</th><td><input type='text' id='invoice_receiver_tvanumber' name='invoice_receiver_tvanumber' value='{$invoice_receiver_tvanumber|escape}'></td></tr>
</table>
</td><td width='50%'>
<b>Addresse de facturation :</b><br />
<textarea id='receiveraddress' name='receiveraddress' style='width: 100%; height: 100px;'>
{$receiveraddress|escape}
</textarea>
</td></tr></table>
<table>
 <tbody id='invoicetbody'>
  <tr><th>-</th><th>ref</th><th>désignation</th><th>VAT</th><th>qty</th><th>P.U H.T</th><th>P.T H.T</th></tr>
 </tbody>
</table>
<input type='button' value='+' onClick="add_invoice_line(--last_invoice_id, '', '', 19.60, 1, 0.00);">
<p>Total H.T: <span id='tht'></span></p>
<p>VAT: <span id='tva'></span></p>
<p>Total T.T.C: <span id='ttc'></span></p>
 {if $invoiceId > 0}
  <input type='submit' name='create_invoice' value='{tr}Modify{/tr}'>
 {else}
  <input type='submit' name='create_invoice' value='{tr}Create{/tr}'>
 {/if}
<input type='hidden' id='invoice_idlist' name='invoice_idlist' value=''>
<input type='hidden' id='invoiceId' name='invoiceId' value='{$invoiceId|escape}'>
<input type='hidden' id='invoice_lines_removed' name='invoice_lines_removed' value='0'>
</form>
{literal}
<script language='JavaScript'>
function newelem(type, vals) {
	var elem=document.createElement(type);
	for (key in vals) {
		elem.setAttribute(key, vals[key]);
	}
	return elem;
}

function tomoney(num, euro) {
	return (num+0.001).toFixed(2) + (euro ? " &euro;" : "");
}

var last_invoice_id=0;
var invoice_line_count=0;
var invoice_line_removed='';

function add_invoice_line(id, ref, designation, tva, qty, unitprice) {
	var tr, td, input;
	
	unitprice=tomoney(unitprice);
	invoice_line_count++;
	tr=newelem('tr', { 'id' : 'invoice_id_'+id });
	
	td=newelem('td', {});
	input=newelem('input', { 'type':'button', 'value':'-', 'onClick':"remove_invoice_line("+id+");" });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('input', { 'type':'text', 'id':'invoice_ref_'+id, 'name':'invoice_ref_'+id, 'value':ref, 'size':'10' });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('input', { 'type':'text', 'id':'invoice_designation_'+id, 'name':'invoice_designation_'+id, 'value':designation, 'size':'60' });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('input', { 'type':'text', 'id':'invoice_tva_'+id, 'name':'invoice_tva_'+id, 'value':tva, 'size':'5', 'style':'text-align:right' });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('input', { 'type':'text', 'id':'invoice_qty_'+id, 'name':'invoice_qty_'+id, 'value':qty, 'size':'4', 'style':'text-align:right',
				 'onChange':"update_line_price("+id+"); update_total_price();" });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('input', { 'type':'text', 'id':'invoice_unitprice_'+id, 'name':'invoice_unitprice_'+id, 'value':unitprice, 'size':'7', 'style':'text-align:right',
				 'onChange':"update_line_price("+id+"); update_total_price();" });
	td.appendChild(input);
	tr.appendChild(td);

	td=newelem('td', {});
	input=newelem('div', { 'id':'invoice_totalprice_'+id, 'style':'text-align:right' });
	td.appendChild(input);
	tr.appendChild(td);

	var tbody=document.getElementById('invoicetbody');
	tbody.appendChild(tr);
	update_line_price(id);
	update_total_price();
}

function remove_invoice_line(id) {
	var tbody=document.getElementById('invoicetbody');
	for (var i=0; i<tbody.rows.length; i++) {
		var row=tbody.rows[i];
		if (row.id == ("invoice_id_"+id)) {
			tbody.deleteRow(i);
			invoice_line_count--;
		}
	}
	if (id > 0) {
		document.getElementById('invoice_lines_removed').value+=','+id;
	}
	update_total_price();
}

function update_line_price(id) {
	var unitprice=document.getElementById('invoice_unitprice_'+id).value;
	var qty=document.getElementById('invoice_qty_'+id).value;
	var price=unitprice*qty;
	document.getElementById('invoice_totalprice_'+id).innerHTML=''+tomoney(price);
}

function update_total_price() {
	var tbody=document.getElementById('invoicetbody');
	var totalprice_ht=0.00;
	var total_tva=0.00;
	var totalprice_ttc=0.00;
	var idlist='';
	for (var i=0; i<tbody.rows.length; i++) {
		var id=tbody.rows[i].id.substr(11, 999);
		if (id == '') continue;
		var unitprice=parseFloat(document.getElementById('invoice_unitprice_'+id).value);
		var qty=document.getElementById('invoice_qty_'+id).value;
		var tva=document.getElementById('invoice_tva_'+id).value;
		var price=unitprice*qty;
		totalprice_ht+=unitprice*qty;
		total_tva+=unitprice*qty*tva/100;
		idlist+=''+id+',';
	}
	totalprice_ttc+=totalprice_ht + total_tva;
	document.getElementById('tht').innerHTML=''+tomoney(totalprice_ht, 1);
	document.getElementById('tva').innerHTML=''+tomoney(total_tva, 1);
	document.getElementById('ttc').innerHTML=''+tomoney(totalprice_ttc, 1);
	if (idlist != '') idlist=idlist.substr(0, idlist.length-1);
	document.getElementById('invoice_idlist').value=''+idlist;
}

{/literal}{$initinvoicelines}{literal}

</script>
{/literal}
