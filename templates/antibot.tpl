{if empty($user)}
<tr>
<td class="formcolor">{tr}Anti-Bot verification code{/tr}:</td>
<td class="formcolor"><img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/></td>
</tr>
<tr>
<td class="formcolor">{tr}Enter the code you see above{/tr}:</td>
<td class="formcolor"><input type="text" maxlength="8" size="8" name="antibotcode" /></td>
</tr>
{/if}