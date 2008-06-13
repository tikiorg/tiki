<h1><a href="tiki-contacts.php" class="pagetitle">{tr}Contacts{/tr}</a></h1>

<div class="page-bar">
<div style="float:right;margin:5px;">
<span class="button2">
{if $view eq 'list'}
<a href="{$myurl}?view=group" class="linkbut" title="{tr}Group View{/tr}">{tr}Group View{/tr}</a>
{else}
<a href="{$myurl}?view=list" class="linkbut" title="{tr}List View{/tr}">{tr}List View{/tr}</a>
{/if}
</span>
</div>
<div style="float:left;margin:5px;">
<span class="button2"><a href="#" onclick="flip('editform');return false;" class="linkbut">{tr}Create/edit contacts{/tr}</a></span>
<span class="button2"><a href="tiki-user_contacts_prefs.php" class="linkbut" title="{tr}Preferences{/tr}">{tr}Preferences{/tr}</a></span>
</div>
</div>

<form action="tiki-contacts.php" method="post" id="editform" name='editform_contact' style="clear:both;margin:5px;display:{ if $contactId}block{else}none{/if};">
<input type="hidden" name="locSection" value="contacts" />
<input type="hidden" name="contactId" value="{$contactId|escape}" />
<table class="normal"><tbody id='tbody_editcontact'>
<tr class="formcolor"><td>{tr}First Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" /></td>
<td rowspan="5">
{tr}Publish this contact to groups{/tr}:<br />
<select multiple="multiple" name="groups[]" size="6">
<option value=""></option>
{foreach item=group from=$groups}
<option value="{$group|escape}"{if in_array($group,$info.groups)} selected="selected"{/if}>{$group}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Last Name{/tr}:</td><td><input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Email{/tr}:</td><td><input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Nickname{/tr}:</td><td><input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" /></td></tr>
<tr class="formcolor" id='tr_exts'>
  <td><select id='select_exts' onchange='ext_select();'>
    <option>{tr}More...{/tr}</option>
  </select></td>
  <td></td>
</tr>
<tr class="formcolor"><td></td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</tbody></table>
</form>

<div align="center" style="clear:both;margin:5px;">

{include file='find.tpl' _sort_mode='y'}

<span class=alphafilter>
  <a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}">{tr}All{/tr}</a>
  {section name=ix loop=$letters}
    <a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;letter={$letters[ix]}">{$letters[ix]}</a>
  {/section}
</span>

<table class="normal">
<tr>
<th class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a></th>
<th class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a></th>
<th class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a></th>
<th class="heading"><a class="tableheading" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a></th>
{foreach from=$exts item=ext key=k}{if $ext.show eq 'y'}
<th class="heading"><a class="tableheading">{$ext.tra}</a></th>
{/if}{/foreach}
{if $view eq 'list'}<th class="heading">{tr}Groups{/tr}</th>{/if}
<th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{foreach key=k item=channels from=$all}
{if count($channels)}
{if $view neq 'list'}
<tr><td colspan="6" style="font-size:80%;color:#999;">{tr}from{/tr} <b>{$k}</b></td></tr>
{/if}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}"><a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">{$channels[user].firstName}</a></td>
<td class="{cycle advance=false}"><a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">{$channels[user].lastName}</a></td>
<td class="{cycle advance=false}"><a class="link" href="mailto:{$channels[user].email}">{$channels[user].email}</a></td>
<td class="{cycle advance=false}">{$channels[user].nickname}</td>
{foreach from=$exts item=ext key=e}{if $ext.show eq 'y'}
	<td class="{cycle advance=false}">{$channels[user].ext[$e]}</td>
{/if}{/foreach}
{if $view eq 'list'}<td class="{cycle advance=false}">{if isset($channels[user].groups)}{foreach item=it name=gr from=$channels[user].groups}{$it}{if $smarty.foreach.gr.index+1 ne $smarty.foreach.gr.last}, {/if}{/foreach}{else}&nbsp;{/if}</td>{/if}
<td class="{cycle advance=false}">&nbsp;
{if $channels[user].user eq $user}
<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}" 
title="{tr}Edit{/tr}">{icon _id='page_edit'}</a><a 
href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:20px;"
title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
{elseif $tiki_p_admin eq 'y'}
<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}" style="margin-left:36px;"
title="{tr}Delete{/tr}">{icon _id='cross_admin' alt='{tr}Delete{/tr}'}</a>
{/if}
</td>
</tr>
{/section}
{else}<tr class="odd"><td>{tr}No records found.{/tr}</td></tr>
{/if}
{/foreach}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

{literal}
<script lang='JavaScript'>
    function newelem(type, vals) {
        var elem=document.createElement(type);
        for (key in vals) {
	    elem.setAttribute(key, vals[key]);
        }
        return elem;
    }

    function htmlspecialchars(ch) {
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/\"/g,"&quot;");
	ch = ch.replace(/\'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch;
    }

    function ext_add(extid, text, defaultvalue) {
	var option=document.getElementById('ext_option_'+extid);
	option.disabled='1';

	var tr,td, input;
	tr=newelem('tr', { 'class':'formcolor', 'id':'tr_ext_'+extid });
	td=newelem('td', { });
	input=newelem('input', { 'type':'button', 'value':'-', 'onclick':'ext_remove(\''+extid+'\');' });
	td.appendChild(input);
	td.innerHTML+=text+':';
	tr.appendChild(td);

	td=newelem('td', { });
	input=newelem('input', { 'maxlength':'80', 'size':'20', 'name':'ext_'+extid, 'value':defaultvalue });
	td.appendChild(input);
	tr.appendChild(td);

	var tbody=document.getElementById('tbody_editcontact');
	tbody.insertBefore(tr, document.getElementById('tr_exts'));	
    }

    function ext_select() {
	var value, text;
	value=document.editform_contact.select_exts.options[document.editform_contact.select_exts.selectedIndex].value;
	text=document.editform_contact.select_exts.options[document.editform_contact.select_exts.selectedIndex].text;
	document.editform_contact.select_exts.selectedIndex=0;

	ext_add(value, htmlspecialchars(text), '');
    }

    function ext_remove(extid) {
	var elem=document.getElementById('tr_ext_'+extid);
	var tbody=document.getElementById('tbody_editcontact');
	tbody.removeChild(elem);
	var option=document.getElementById('ext_option_'+extid);
	option.disabled='';
    }
    
    function extmenu_add(extid, text, defaultvalue) {
	var selectelem=document.getElementById('select_exts');
	var option=newelem('option', { 'id':'ext_option_'+extid, 'value':extid });
	option.innerHTML=text;
	selectelem.appendChild(option);
	if (defaultvalue != '')
	    ext_add(extid, text, defaultvalue);
    }
{/literal}{foreach from=$exts item=ext key=k}extmenu_add('{$k|escape}', '{$ext.tra|escape}', '{$info.ext[$ext.id]|escape:quotes}');{/foreach}{literal}
</script>
{/literal}
