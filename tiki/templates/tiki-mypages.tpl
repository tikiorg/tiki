{literal}
<script type="text/javascript" src="lib/mootools/mootools.js"></script>
<script type="text/javascript" src="lib/mootools/extensions/windoo/windoo.js"></script>
{/literal}

<div id='mypageeditdiv' style='display: none;'>
 <input id='mypageedit_id' type='hidden' value=''>
 <table class="normal">
  <tr>
   <th>Name</th>
   <td><input id='mypageedit_name' type='text' name='name' value=''></td>
  </tr>

  <tr>
   <th>Description</th>
   <td><input id='mypageedit_description' type='text' name='description' value=''></td>
  </tr>

  <tr>
   <th>Dimensions</th>
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
  <a href='tiki-mypage.php?id_mypage={$mypage.id}' title='{tr}view{/tr}'><img src="pics/icons/page.png" border="0" height="16" width="16" alt='{tr}view{/tr}' /></a>
  <a href='#' onclick='showMypageEdit({$mypage.id});' title='{tr}edit{/tr}'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
  <a href='#' onclick='deleteMypage({$mypage.id});' title='{tr}delete{/tr}'><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
  {if $tiki_p_assign_perm_mypage eq 'y'}<a href='tiki-objectpermissions.php?objectName={$mypage.name|escape:"url"}&amp;objectType=mypage&amp;permType=mypage&amp;objectId={$mypage.id}'  title='{tr}perms{/tr}'><img src="{if $mypage.perms.has_special_perm eq 'y'}pics/icons/key_active.png{else}pics/icons/key.png{/if}" border="0" height="16" width="16" alt='{tr}perms{/tr}' /></a>{/if}
 </td>
</tr>
{/foreach}
</table>
<select onselect='changepage();'>
 {foreach from=$pagesnum key=k item=v}
 <option value='{$k}'>{$v} / {$pcount}</option>
 {/foreach}
</select>

{literal}
<script>
function changepage() {
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
		$('mypageedit_submit').value='Modify';
	} else {
		$('mypageedit_id').value=0;
		$('mypageedit_name').value='';
		$('mypageedit_description').value='';
		$('mypageedit_width').value='0';
		$('mypageedit_height').value='500';
		$('mypageedit_submit').value='Create';
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

initMypageEdit();

</script>
{/literal}
