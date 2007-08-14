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
  <tr>
   <th>{tr}Object Type{/tr}</th>
   <td><select id="mypageedit_type" name="type">
   	<option id="wiki" value="wiki">{tr}Wiki{/tr}</option>
   	<option id="blog" value="blog">{tr}Blog{/tr}</option>
   	<option id="channel" value="channel">{tr}Channel{/tr}</option>
   	<option id="program" value="program">{tr}Program{/tr}</option>
   </select>
   </td>
  </tr>
  {if $feature_categories eq "y"}
	{include file=categorize.tpl}
  {/if}
  <tr>
   <th>{tr}Dimensions{/tr}</th>
   <td>
    <input id='mypageedit_width' type='text' name='width' value='' style='width: 55px'> x 
    <input id='mypageedit_height' type='text' name='height' value='' style='width: 55px'>
   </td>
  </tr>
 </table>
 <br />
 <input type='button' value='Cancel' onclick='closeMypageEdit();'>
 <input id='mypageedit_submit' type='button' value='Modify' onclick='saveMypageEdit();'>
</div>

<input type='button' value='Create' onclick='showMypageEdit(0);'>

<table class="normal">
<tr>
 <th class="heading">{tr}Name{/tr}</th>
 <th class="heading">{tr}Description{/tr}</th>
 <th class="heading">{tr}Dimensions{/tr}</th>
 <th class="heading">{tr}Action{/tr}</th>
</tr>
{foreach from=$mypages item=mypage}
<tr class="odd">
 <td><span id='mypagespan_name_{$mypage.id}'>{$mypage.name}</span></td>
 <td><span id='mypagespan_description_{$mypage.id}'>{$mypage.description}</span></td>
 <td>
  <span id='mypagespan_width_{$mypage.id}'>{$mypage.width}</span> x 
  <span id='mypagespan_height_{$mypage.id}'>{$mypage.height}</span>
 </td>
 <td>
  <a href='tiki-mypage.php?id_mypage={$mypage.id}' title='{tr}view content{/tr}'><img src="pics/icons/page.png" border="0" height="16" width="16" alt='{tr}view content{/tr}' /></a>
  <a href='tiki-mypage.php?id_mypage={$mypage.id}&amp;edit=1' title='{tr}edit content{/tr}'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit content{/tr}' /></a>
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
		"height": 200,
		"container": false,
		"destroyOnClose": false
	}).adopt(content);
	content.style.display='';
}

function showMypageEdit(id) {
	if (id > 0) {
		xajax_mypage_fillinfos(id);
		$('mypageedit_submit').value='{/literal}{tr}Modify{/tr}{literal}';
	} else {
		$('mypageedit_id').value=0;
		$('mypageedit_name').value='';
		$('mypageedit_description').value='';
		$('mypageedit_width').value='0';
		$('mypageedit_height').value='500';
		$('mypageedit_submit').value='{/literal}{tr}Create{/tr}{literal}';
	}

	curmodal.show();
}

function closeMypageEdit() {
	curmodal.close();
}

function saveMypageEdit() {
	var id=$('mypageedit_id').value;
	if (id > 0) {
		xajax_mypage_update(id, $('mypageedit_name').value,
					$('mypageedit_description').value,
					$('mypageedit_width').value,
					$('mypageedit_height').value
				   );
	} else {
		xajax_mypage_create($('mypageedit_name').value,
				    $('mypageedit_description').value,
				    $('mypageedit_width').value,
				    $('mypageedit_height').value
				   );
	}

	closeMypageEdit();	
}

function deleteMypage(id) {
	xajax_mypage_delete(id);
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
