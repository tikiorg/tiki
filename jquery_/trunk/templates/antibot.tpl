{if empty($user)}
<tr>
<td>{tr}Anti-Bot verification code{/tr}:</td>
<td><img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/></td>
</tr>
<tr>
<td>{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}*{/if}:</td>
<td><input type="text" maxlength="8" size="8" name="antibotcode" /></td>
</tr>
{/if}