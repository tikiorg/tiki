{literal}
<script type="text/javascript" src="lib/mootools/mootools.js"></script>
<script type="text/javascript" src="lib/mootools/extensions/windoo/windoo.js"></script>
{/literal}

<div id='mypageeditdiv' style='display: none;'>
 <input id='mypageedit_id' type='hidden' value=''>
 <table>
  <tr>
   <th>Name</th>
   <td><input id='mypageedit_name' type='text' name='name' value=''></td>
  </tr>

  <tr>
   <th>Description</th>
   <td><input id='mypageedit_description' type='text' name='description' value=''></td>
  </tr>
 </table>
 <br />
 <input type='button' value='Cancel' onclick='closeMypageEdit();'>
 <input id='mypageedit_submit' type='button' value='Modify' onclick='saveMypageEdit();'>
</div>

<input type='button' value='Create' onclick='showMypageEdit(0);'>

<table>
<tr>
 <th>name</th>
 <th>description</th>
 <th>action</th>
</tr>
{foreach from=$mypages item=mypage}
<tr>
 <td><span id='mypagespan_name_{$mypage.id}'>{$mypage.name}</span></td>
 <td><span id='mypagespan_description_{$mypage.id}'>{$mypage.description}</span></td>
 <td>
  <a href='tiki-mypage.php?mypageid={$mypage.id}'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
  <a href='#' onclick='showMypageEdit({$mypage.id});'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a>
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

var content_mypageeditdiv=$('mypageeditdiv');
var curmodal=0;

function showMypageEdit(id) {
	if (id > 0) {
		xajax_mypage_fillinfos(id);
		$('mypageedit_submit').value='Modify';
	} else {
		$('mypageedit_id').value=0;
		$('mypageedit_name').value='';
		$('mypageedit_description').value='';
		$('mypageedit_submit').value='Create';
	}
	content_mypageeditdiv.style.display='';
	curmodal=new Windoo({
		"modal": true,
		"width": 300,
		"height": 150,
		"container": false
	}).adopt(content_mypageeditdiv)
	.show();
}

function closeMypageEdit() {
	curmodal.close();
	curmodal=0;
}

function saveMypageEdit() {
	var id=$('mypageedit_id').value;
	if (id > 0) {
		xajax_mypage_update(id, $('mypageedit_name').value, $('mypageedit_description').value);
	} else {
		xajax_mypage_create($('mypageedit_name').value, $('mypageedit_description').value);
	}

	closeMypageEdit();	
}

</script>
{/literal}