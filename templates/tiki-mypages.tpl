<h1>{tr}My {if $smarty.request.type}{$smarty.request.type} {/if}Pages{/tr}</h1>
<div id='mypageeditdiv' style='display: none;'>
 <input id='mypageedit_id' type='hidden' value=''>
 <table class="normal">
  <tr>
   <th>{tr}Name{/tr}</th>
   <td><input id='mypageedit_name' type='text' name='name' value=''></td>
  </tr>

  <tr>
   <th>{tr}Description{/tr}</th>
   <td><input id='mypageedit_description' type='text' name='description' value=''></td>
  </tr>
  <tr>
   <th>{tr}Access{/tr}</th>
   <td><img width="20" height="20" align="top" src="styles/netineo/unlock.png"/>
   <input class="register" type="radio" checked="" value="0" name="PageType"/>
     
     <img width="20" height="20" align="top" src="styles/netineo/lock.png"/>
      
      <input class="register" type="radio" value="1" name="PageType"/>
   </td>
  </tr>
  <tr id='mypageedit_tr_type' {if $id_types}style='display: none;'{/if}>
   <th>{tr}Object Type{/tr}</th>
   <td>
    <select id="mypageedit_type" onchange='mypageTypeChange(this.value);'>
    {foreach from=$mptypes item=mptype}
   	<option value="{$mptype.id|escape}">{$mptype.name|escape}</option>
    {/foreach}
   </select>
   </td>
  </tr>
  {if $feature_categories eq "y"}
	{include file=categorize.tpl}
  {/if}
  <tr id='mypageedit_tr_dimensions'>
   <th>{tr}Dimensions{/tr}</th>
   <td>
    <input id='mypageedit_width' type='text' name='width' value='' style='width: 55px'> x 
    <input id='mypageedit_height' type='text' name='height' value='' style='width: 55px'>
   </td>
  </tr>
 </table>

 <form id='form_mypageedit_typeconf'><div id='mypageedit_typeconf'></div></form>

 <br />
 <input type='button' value='Cancel' onclick='closeMypageEdit();'>
 <input id='mypageedit_submit' type='button' value='Modify' onclick='saveMypageEdit();'>
</div>

<input type='button' value='Create' onclick='showMypageEdit(0);'>

<table class="normal">
<tr>
 <th class="heading">{tr}Name{/tr}</th>
 <th class="heading">{tr}Description{/tr}</th>
 <th class="heading" {if $id_types}style='display: none;'{/if}>{tr}Type{/tr}</th>
 <th class="heading" {if $id_types && $mptypes[$id_types].fix_dimensions=='yes'}style='display: none;'{/if}>{tr}Dimensions{/tr}</th>
 <th class="heading">{tr}Action{/tr}</th>
</tr>
{foreach from=$mypages item=mypage}
<tr class="odd">
 <td><span id='mypagespan_name_{$mypage.id}'>{$mypage.name|escape}</span></td>
 <td><span id='mypagespan_description_{$mypage.id}'>{$mypage.description|escape}</span></td>
 <td {if $id_types}style='display: none;'{/if}><span id='mypagespan_type_{$mypage.id}'>{$mypage.type_name|escape}</span></td>
 <td {if $id_types && $mptypes[$id_types].fix_dimensions=='yes'}style='display: none;'{/if}>
  <span id='mypagespan_width_{$mypage.id}'>{$mypage.width}</span> x 
  <span id='mypagespan_height_{$mypage.id}'>{$mypage.height}</span>
 </td>
 <td>
  <a id='mypage_viewurl_{$mypage.id}' href='tiki-mypage.php?mypage={$mypage.name|escape:'url'}' title='{tr}view content{/tr}'><img src="pics/icons/page.png" border="0" height="16" width="16" alt='{tr}view content{/tr}' /></a>
  <a id='mypage_editurl_{$mypage.id}' href='tiki-mypage.php?mypage={$mypage.name|escape:'url'}&amp;edit=1' title='{tr}edit content{/tr}'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit content{/tr}' /></a>
  <a href='#' onclick='showMypageEdit({$mypage.id});' title='{tr}edit entry{/tr}'><img src="pics/icons/pencil.png" border="0" height="16" width="16" alt='{tr}edit entry{/tr}' /></a>
  <a href='#' onclick='deleteMypage({$mypage.id});' title='{tr}delete entry{/tr}'><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete entry{/tr}' /></a>
 </td>
</tr>
{/foreach}
</table>
<select id='mypage_page_select' onchange='changepage();'>
 {foreach from=$pagesnum key=k item=v}
 <option value='{$k}'{if $showpage == $k} selected{/if}>{$v} / {$pcount}</option>
 {/foreach}
</select>

{literal}
<script type="text/javascript">
function changepage() {
	var p=$('mypage_page_select').value;
	window.location='?showpage='+p;
}

var curmodal=0;

function initMypageEdit() {
	var content=$('mypageeditdiv');
	curmodal=new Windoo({
		"modal": true,
		"width": 300,
		"height": 500,
		"container": false,
		"destroyOnClose": false
	}).adopt(content);
	content.style.display='';
}

function showMypageEdit(id) {
	xajax_mypage_fillinfos(id);
	if (id > 0) {
		$('mypageedit_submit').value='{/literal}{tr}Modify{/tr}{literal}';
		curmodal.setTitle("Edit... ");
	} else {
		$('mypageedit_id').value=0;
		$('mypageedit_name').value='';
		$('mypageedit_description').value='';
		{/literal}{if $id_types}{literal}
		  $('mypageedit_type').value={/literal}{$id_types}{literal};
		{/literal}{else}{literal}
		  $('mypageedit_type').selectedIndex=0;
		{/literal}{/if}{literal}
		$('mypageedit_width').value='0';
		$('mypageedit_height').value='500';
		$('mypageedit_submit').value='{/literal}{tr}Create{/tr}{literal}';
		mypageTypeChange($('mypageedit_type').value);
		curmodal.setTitle("New...");
	}

	curmodal.show();
}

function closeMypageEdit() {
	curmodal.close();
}

function saveMypageEdit() {
	var id=$('mypageedit_id').value;
	var vals={
		'name': $('mypageedit_name').value,
		'description': $('mypageedit_description').value,
		'id_types': $('mypageedit_type').value,
		'width': $('mypageedit_width').value,
		'height': $('mypageedit_height').value
	};

	if (id > 0) {
		xajax_mypage_update(id, vals, xajax.getFormValues('form_mypageedit_typeconf'));
	} else {
		xajax_mypage_create(vals, xajax.getFormValues('form_mypageedit_typeconf'));
	}

	closeMypageEdit();	
}

function deleteMypage(id) {
	xajax_mypage_delete(id);
}

function mypageTypeChange(id) {
	if (id) {
		mptype=mptypes[id];
		$('mypageedit_tr_dimensions').style.display=(mptype.fix_dimensions == 'yes' ? 'none' : '');
		//$('mypageedit_tr_bgcolor').style.display=(mptype.fix_bgcolor == 'yes' ? 'none' : '');
	}
}

function htmlspecialchars(ch) {
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/\"/g,"&quot;");
	ch = ch.replace(/\'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch;
}

var mptypes={/literal}{$mptypes_js}{literal};
function updateMypageParams(id, vals) {
	for (var k in vals) {
		switch(k) {
			case 'name':
				$('mypagespan_name_'+id).innerHTML=htmlspecialchars(vals[k]);
				$('mypage_viewurl_'+id).href="tiki-mypage.php?mypage="+encodeURI(vals[k]);
				$('mypage_editurl_'+id).href="tiki-mypage.php?mypage="+encodeURI(vals[k])+"&edit=1";
				break;
			case 'description':
			case 'width':
			case 'height':
				$('mypagespan_'+k+'_'+id).innerHTML=htmlspecialchars(vals[k]);
				break;
			case 'type':
				$('mypagespan_type_'+id).innerHTML=htmlspecialchars(vals[k]);
				break;
			case 'id_types':
				$('mypagespan_type_'+id).innerHTML=htmlspecialchars(mptypes[vals[k].name]);
				break;
		}
	}
}

{/literal}
{if $feature_phplayers eq 'y'}{* this is an ugly hack ... *}
window.onload=initMypageEdit;
{else}
window.addEvent('domready', initMypageEdit);
{/if}
{literal}

</script>
{/literal}
