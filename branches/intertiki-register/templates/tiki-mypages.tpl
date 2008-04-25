<div id="mytiki">
 <div class="titlebar">{tr}My {if $smarty.request.type}{$smarty.request.type}{/if}s{/tr}</div>
 <div id='mypageeditdivparking' style='display: none;'>
  <div class="mypage_configure" id='mypageeditdiv' style='border: padding-left: 7px; padding-right: 7px;'>
   <input id='mypageedit_id' type='hidden' value=''>
   <table class="normal">
    <tr>
     <th>{tr}Name{/tr}</th>
     <td>
      <input id='mypageedit_name' type='text' name='name' value='' style='width: 100%' onkeyup='isNameFree();' />
      <input id='mypageedit_name_orig' type='hidden' name='name' value='' />
      <div id='mypageedit_name_unique' style='color: red;'></div>
     </td>
    </tr>

    <tr>
     <th>{tr}Description{/tr}</th>
     <td><textarea id='mypageedit_description' name='description' style='width: 100%; height: 64px;'></textarea></td>
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
    {if $prefs.feature_categories eq "y"}
     <tr><th>{tr}Categorize{/tr}</th><td id='mypageedit_categorize_tpl'>{$mypageedit_categorize_tpl}</td></tr>
    {/if}
    <tr id='mypageedit_tr_dimensions' style='display: none;'>
     <th>{tr}Dimensions{/tr}</th>
     <td>
      <input id='mypageedit_width' type='text' name='width' value='' style='width: 40px'> x 
      <input id='mypageedit_height' type='text' name='height' value='' style='width: 40px'> ({tr}pixels{/tr})
     </td>
    </tr>

    <tr id='mypageedit_tr_template' style='display: none;'>
     <th>{tr}Template{/tr}</th>
     <td id='mypageedit_td_template'>
      <select id='mypageedit_template'>
       <option value='0'>No template</option>
      </select>
     </td>
    </tr>

   </table>

   <form id='form_mypageedit_typeconf'><div id='mypageedit_typeconf'></div></form>

   <div style='text-align: right'>
    <input type='button' value='Cancel' onclick='closeMypageEdit();'>
    <input id='mypageedit_submit' type='button' value='Modify' onclick='saveMypageEdit();'>
   </div>
  </div>
 </div>

{if $mypage_admin}
{tr}view{/tr}: <select onchange='mypage_change_type(this.value);'>
  <option value=''>{tr}all types{/tr}</option>
 {foreach from=$mptypes item=mptype}
  <option value='{$mptype.name|escape}' {if $id_types==$mptype.id}selected{/if}>{$mptype.name|escape}</option>
 {/foreach}
</select> | 
{/if}
<input type='button' value='Create' onclick='showMypageEdit(0);'>

<table class="normal">
<tr>
 {foreach from=$mp_columns item=col}
  <th class="heading" {if $col.hidden}style='display: none;'{/if}>{if $col.header_tpl}{eval var=$col.header_tpl}{else}{tr}{$col.title}{/tr}{/if}</th>
 {/foreach}
</tr>

{cycle values="odd,even" print=false}
{foreach from=$mypages item=mypage}
<tr class="{cycle}">
 {foreach from=$mp_columns item=col}
  <td {if $col.hidden}style='display: none;'{/if}>{eval var=$col.content_tpl}</td>
 {/foreach}
</tr>
{/foreach}
</table>
<select id='mypage_page_select' onchange='changepage();'>
 {foreach from=$pagesnum key=k item=v}
 <option value='{$k}'{if $showpage == $k} selected{/if}>{$v} / {$pcount}</option>
 {/foreach}
</select>
</div>
{literal}
<script type="text/javascript">
function changepage() {
	var p=$('mypage_page_select').value;
	var url='?showpage='+p;
	{/literal}{if $mypage_admin}url+='&admin';{/if}{literal}
	{/literal}{if $mptype_name}url+='&type={$mptype_name|escape:javascript}';{/if}{literal}
	window.location=url;	
}

function mypage_change_type(name) {
	var url='?showpage=0';
	if (name != '')
		url+='&type='+escape(name);
	{/literal}{if $mypage_admin}url+='&admin';{/if}{literal}
	window.location=url;
}

var curmodal=0;

function initMypageEdit() {
}

function showMypageEdit(id) {
	curmodal=new Windoo({
		"modal": true,
		"width": 600,
		"height": 435,
		"container": false,
		"theme": "mypage"
	}).adopt($('mypageeditdiv'));

	if (id > 0) {
		xajax_mypage_fillinfos(id);
		$('mypageedit_submit').value='{/literal}{tr}Modify{/tr}{literal}';
		{/literal}{if $id_types}{literal}
		  curmodal.setTitle({/literal}"{tr}Edit{/tr} "+mptypes[{$id_types}].name{literal});
		{/literal}{else}{literal}
		  curmodal.setTitle({/literal}"{tr}Edit{/tr}..."{literal});
		{/literal}{/if}{literal}
		$('mypageedit_template').selectedIndex=0;
	} else {
		$('mypageedit_id').value=0;
		$('mypageedit_name').value='';
		$('mypageedit_name_orig').value='';
		$('mypageedit_description').value='';
		{/literal}{if $id_types}{literal}
		  curmodal.setTitle({/literal}"{tr}New{/tr} "+mptypes[{$id_types}].name{literal});
		  $('mypageedit_type').value={/literal}{$id_types}{literal};
		  xajax_mypage_fillinfos(0, {/literal}{$id_types}{literal});
		{/literal}{else}{literal}
		  curmodal.setTitle({/literal}"{tr}New{/tr}..."{literal});
		  $('mypageedit_type').selectedIndex=0;
		  xajax_mypage_fillinfos(0, $('mypageedit_type').value);
		{/literal}{/if}{literal}
		$('mypageedit_width').value=mptypes[$('mypageedit_type').value].def_width;
		$('mypageedit_height').value=mptypes[$('mypageedit_type').value].def_height;
		$('mypageedit_submit').value='{/literal}{tr}Create{/tr}{literal}';
		mypageTypeChange($('mypageedit_type').value);
	}

	curmodal.addEvent('onClose', function() {
		$('mypageeditdivparking').adopt($('mypageeditdiv'));
		curmodal=null;
	});
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

	if ($('cat-check').value == 'on')
		vals['categories']=$('cat_categories').value;

	if (id > 0) {
		xajax_mypage_update(id, vals, xajax.getFormValues('form_mypageedit_typeconf'));
	} else {
		vals['template']=$('mypageedit_template').value;
		xajax_mypage_create(vals, xajax.getFormValues('form_mypageedit_typeconf'));
	}

}

function deleteMypage(id) {
	if (confirm("{/literal}{tr}Are you sure you want to delete this entry ?{/tr}{literal}" ))
		xajax_mypage_delete(id);
}

function isNameFree() {
	name=$('mypageedit_name').value;
	name_orig=$('mypageedit_name_orig').value;
	if ((name == '') || (name == name_orig))
		$('mypageedit_name_unique').innerHTML='';
	else xajax_mypage_isNameFree(name, mptypes[$('mypageedit_type').value].name);
}

function mypageTypeChange(id) {
	if (id) {
		mptype=mptypes[id];
		$('mypageedit_tr_dimensions').style.display=(mptype.fix_dimensions == 'yes' ? 'none' : '');
		//$('mypageedit_tr_bgcolor').style.display=(mptype.fix_bgcolor == 'yes' ? 'none' : '');
	}
	xajax_mypage_fillinfos($('mypageedit_id').value, id, true);

}

function mypageTemplateChange(id_template) {
	if (id_template == 0) return;
	xajax_mypage_fillinfos(0, 0, false, id_template);
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

window.addEvent('domready', initMypageEdit);

</script>
{/literal}
