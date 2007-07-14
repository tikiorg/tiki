<h1>{tr}Add Transactions{/tr}</h1>
{popup_init src="lib/overlib.js"}
<div class="navbar">
{if $tiki_p_tinvoice_edit eq 'y'}<a class="linkbut" href="tiki-tinvoice_transactions.php?id_emitter={$me_tikiid}">{tr}Transactions{/tr}</a>&nbsp;{/if}
<a class="linkbut" href="tiki-tinvoice_prefs.php">{tr}preferences{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_graph.php">{tr}Graphs{/tr}</a>
<a class="linkbut" href="tiki-tinvoice_banks.php">{tr}Banks{/tr}</a>
</div>
<hr />
<br />
<h2>{tr}Add transaction{/tr}</h2>
<hr />
<form method='POST'action="" >
<input type="hidden" name="userId" value="{$transaction.userId}" />
<input type="hidden" name="status" value="{$transaction.status}" />
 <span style="width: 130px; display: block; float: left" >{tr}Bank Account{/tr}:</span><select  style="width: 200px" name="bankId" >
	{section name=b loop=$banks}
	<option value="{$banks[b].id}">{$banks[b].name}  ({$banks[b].bank} - {$banks[b].account_nb})</option>
	{/section}
</select>
 <br />


 <span style="width: 130px; display: block; float: left">{tr}Date{/tr}:</span>{if $feature_jscalendar ne 'y'}<input type='text' name='date' value='{$transaction.date|escape}' />{else}<input type='text' id="id_start" name='date' style="width: 70px"  value='{$transaction.date|escape}' />{jscalendar id="start" date=$transaction.date fieldname="date" ifFormat="%Y-%m-%d" align="Bc" showtime='n'}{/if}<br />
 <span style="width: 130px; display: block; float: left">{tr}transaction nb{/tr}:</span><input type="texte" style="width: 200px" name="operationNb" value="{$transaction.operationNb}" /><br />
 <span style="width: 130px; display: block; float: left">{tr}label{/tr}:</span><input type="texte" style="width: 200px" name="label" value="{$transaction.label}" /><br />
 <span style="width: 130px; display: block; float: left">{tr}debit{/tr}:</span><input type="texte" style="width: 200px" name="debit" value="{$transaction.debit}" /><br />
 <span style="width: 130px; display: block; float: left">{tr}credit{/tr}:</span><input type="texte" style="width: 200px" name="credit" value="{$transaction.credit}" /><br />
	<input type="submit" name="save" value="{tr}save{/tr}" />
</form>
<hr />
<br />
<h2>{tr}Import batch transaction{/tr}</h2>
<hr />
<form method='POST'action="" >
<input type="hidden" name="userId" value="{$transaction.userId}" />
 <span style="width: 130px; display: block; float: left" >{tr}Account{/tr}:</span><select  style="width: 200px" name="bankId" >
	{section name=b loop=$banks}
	<option value="{$banks[b].id}">{$banks[b].name}  ({$banks[b].bank} - {$banks[b].account_nb})</option>
	{/section}
</select><br />
 <span style="height: 100px; width: 130px; display: block; float: left" >{tr}Batch upload (CSV file):{/tr} <a {popup text='login,password,email,groups<br />user1,password1,email1,&quot;group1,group2&quot;<br />user2, password2,email2'}><img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a></span>
<input type="file" name="csvlist"/><br /><input type="radio" name="overwrite" value="y" checked="checked" />&nbsp;{tr}Overwrite{/tr}<br /><input type="radio" name="overwrite" value="c"/>&nbsp;{tr}Overwrite{/tr} {tr} but keep the previous id{/tr}<br /><input type="radio" name="overwrite" value="n" />&nbsp;{tr}Don't overwrite{/tr}<br /><input type="checkbox" name="overwriteGroup" />{tr}Show overwrited records:{/tr}<br />
<input type="submit" name="save_batch" value="{tr}batch import{/tr}" />
</form>
