<h1>{tr}Mypages Types{/tr}</h1>

<div id='mptypeeditdiv' style='display: none;'>
 <input id='mptype_id' type='hidden' value=''>

 <table class="normal">

  <tr>
   <th>{tr}Name{/tr}</th>
   <td><input id='mptype_name' type='text' name='name' value=''></td>
  </tr>

  <tr>
   <th>{tr}Description{/tr}</th>
   <td><input id='mptype_description' type='text' name='description' value=''></td>
  </tr>

  <tr>
   <th>{tr}Section{/tr}</th>
   <td><input id='mptype_section' type='text' name='section' value=''></td>
  </tr>

  <tr>
   <th>{tr}Permissions{/tr}</th>
   <td><input id='mptype_permissions' type='text' name='permissions' value=''></td>
  </tr>

  <tr>
   <th>{tr}Default Dimensions{/tr}</th>
   <td>
    <input id='mptype_def_width' type='text' name='width' value='' style='width: 55px'> x 
    <input id='mptype_def_height' type='text' name='height' value='' style='width: 55px'><br />
    <input id='mptype_fix_dimensions' type='checkbox' value='yes' /> Not editable
   </td>
  </tr>

  <tr>
   <th>{tr}Default background color{/tr}</th>
   <td>
    <input id='mptype_def_bgcolor' type='text' name='permissions' value='' /><br />
    <input id='mptype_fix_bgcolor' type='checkbox' value='yes' /> Not editable
   </td>
  </tr>

  <tr>
   <th>{tr}Components{/tr}</th>
   <td>
    <!-- tom don't like my multiselect snif :(
    <div style='width: 150px; height: auto; max-height: 100px; overflow: auto;'>
    {foreach from=$components item=component}
     <input type='checkbox' name='mptype_components[]' value='{$component|escape}'> {$component|escape}<br />
    {/foreach}
    </div>
    -->
    <select id='mptype_components' size="4" multiple="multiple">
     {foreach from=$components item=component}
     <option id='mptype_components_{$component|escape}' value='{$component|escape}'>{$component|escape}</option>
     {/foreach}
    </select>
   </td>
  </tr>

  <tr>
   <th>{tr}Group{/tr}</th>
   <td>
    <!-- tom don't like my multiselect snif :(
    <div style='width: 150px; height: auto; max-height: 100px; overflow: auto;'>
    {foreach from=$groups item=group}
     <input type='checkbox' name='mptype_groups[]' value='{$group|escape}'> {$group|escape}<br />
    {/foreach}
    </div>
    -->
    <select id='mptype_groups' size="4" multiple="multiple">
     {foreach from=$groups item=group}
     <option id='mptype_groups_{$group|escape}' value='{$group|escape}'>{$group|escape}</option>
     {/foreach}
    </select>
   </td>
  </tr>

  <tr>
    <th>{tr}Template user{/tr}</th>
    <td>
     <select id='mptype_templateuser'>
      <option value='0'>{tr}No templates{/tr}</option>
      {foreach from=$mpt_users item=mpt_user}
       <option value='{$mpt_user.userId}'>{$mpt_user.user|escape}</option>
      {/foreach}
     </select>
    </td>
  </tr>

 </table>
 <br />
 <input type='button' value='Cancel' onclick='closeMptypeEdit();'>
 <input id='mptype_submit' type='button' value='Modify' onclick='saveMptypeEdit();'>
</div>

<input type='button' value='Create' onclick='showMptypeEdit(0);'>

<table class="normal">
<tr>
 <th class="heading">{tr}Name{/tr}</th>
 <th class="heading">{tr}Description{/tr}</th>
 <th class="heading">{tr}Section{/tr}</th>
 <th class="heading">{tr}Permissions{/tr}</th>
 <th class="heading">{tr}Components{/tr}</th>
 <th class="heading">{tr}Action{/tr}</th>
</tr>
{foreach from=$mptypes item=mptype}
<tr class="odd">
 <td><span id='mptype_name_{$mptype.id}'><a href="tiki-mypages.php?type={$mptype.name}">{$mptype.name}</a></span></td>
 <td><span id='mptype_description_{$mptype.id}'>{$mptype.description}</span></td>
 <td><span id='mptype_section_{$mptype.id}'>{$mptype.section}</span></td>
 <td><span id='mptype_permissions_{$mptype.id}'>{$mptype.permissions}</span></td>
 <td>
  <span id='mptype_components_{$mptype.id}'>{foreach from=$mptype.components item=component}
   {$component.compname|escape}
  {/foreach}</span>
 </td>
 <td>
  <a href='#' onclick='showMptypeEdit({$mptype.id});' title='{tr}edit entry{/tr}'>{icon _id='pencil' alt='{tr}edit entry{/tr}'}</a>
  <a href='#' onclick='deleteMptype({$mptype.id});' title='{tr}delete entry{/tr}'>{icon _id='cross' alt='{tr}delete entry{/tr}'}</a>
 </td>
</tr>
{/foreach}
</table>
<select id='mptype_page_select' onchange='changepage();'>
 {foreach from=$pagesnum key=k item=v}
 <option value='{$k}'{if $showpage == $k} selected{/if}>{$v} / {$pcount}</option>
 {/foreach}
</select>

{literal}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function changepage() {
	var p=$('mptype_page_select').value;
	window.location='?showpage='+p;
}

var curmodal=0;

function initMptypeEdit() {
	var content=$('mptypeeditdiv');
	curmodal=new Windoo({
		"modal": true,
		"width": 300,
		"height": 400,
		"container": false,
		"destroyOnClose": false
	}).adopt(content);
	content.style.display='';
}

function showMptypeEdit(id) {
	for (var i=0; i<$('mptype_components').options.length; i++)
		$('mptype_components').options[i].selected=0;

	for (var i=0; i<$('mptype_groups').options.length; i++)
		$('mptype_groups').options[i].selected=0;

	if (id > 0) {
		xajax_mptype_fillinfos(id);
		$('mptype_submit').value='{/literal}{tr}Modify{/tr}{literal}';
	} else {
		$('mptype_id').value=0;
		$('mptype_name').value='';
		$('mptype_description').value='';
		$('mptype_section').value='';
		$('mptype_permissions').value='';
		$('mptype_def_width').value='';
		$('mptype_def_height').value='';
		$('mptype_fix_dimensions').checked='';
		$('mptype_def_bgcolor').value='';
		$('mptype_fix_bgcolor').checked='';
		$('mptype_templateuser').selectedIndex=0;
		$('mptype_submit').value='{/literal}{tr}Create{/tr}{literal}';
	}

	curmodal.show();
}

function closeMptypeEdit() {
	curmodal.close();
}

function saveMptypeEdit() {
	var id=$('mptype_id').value;

	var components=new Array();
	for (var i=0; i<$('mptype_components').options.length; i++) {
		if ($('mptype_components').options[i].selected)
			components.push($('mptype_components').options[i].value);
	}

	var groups=new Array();
	for (var i=0; i<$('mptype_groups').options.length; i++) {
		if ($('mptype_groups').options[i].selected)
			groups.push($('mptype_groups').options[i].value);
	}

	var vals={
		'name' : $('mptype_name').value,
		'description' : $('mptype_description').value,
		'section' : $('mptype_section').value,
		'permissions' : $('mptype_permissions').value,
		'def_width' : $('mptype_def_width').value,
		'def_height' : $('mptype_def_height').value,
		'fix_dimensions' : $('mptype_fix_dimensions').checked ? 'yes' : 'no',
		'def_bgcolor' : $('mptype_def_bgcolor').value,
		'fix_bgcolor' : $('mptype_fix_bgcolor').value ? 'yes' : 'no',
		'templateuser' : $('mptype_templateuser').value,
		'components': components,
		'groups': groups
	};

	if (id > 0) {
		xajax_mptype_update(id, vals);
	} else {
		xajax_mptype_create(vals);
	}

	closeMptypeEdit();	
}

function deleteMptype(id) {
	xajax_mptype_delete(id);
}

{/literal}
{if $prefs.feature_phplayers eq 'y'}{* this is an ugly hack ... *}
window.onload=initMptypeEdit;
{else}
window.addEvent('domready', initMptypeEdit);
{/if}
{literal}
//--><!]]>
</script>
{/literal}
