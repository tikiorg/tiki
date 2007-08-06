<table>
 <tr>
  <th>Name</th>
  <td><input type='text' name='name' value=''></td>
 </tr>

 <tr>
  <th>Description</th>
  <td><input type='text' name='description' value=''></td>
 </tr>

</table>

<table>
<tr>
 <th>name</th>
 <th>description</th>
 <th>action</th>
</tr>
{foreach from=$mypages item=mypage}
<tr>
 <td>{$mypage.name}</td>
 <td>{$mypage.description}</td>
 <td><a href='tiki-mypage.php?mypageid={$mypage.id}'><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt='{tr}edit{/tr}' /></a></td>
</tr>
{/foreach}
</table>
<select onChange='changepage();'>
 {foreach from=$pagesnum key=k item=v}
 <option value='{$k}'>{$v} / {$pcount}</option>
 {/foreach}
</select>

{literal}
<script>
function changepage() {
}
</script>
{/literal}