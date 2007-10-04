<h2>{tr}Register as a new user{/tr}</h2>
<br />
{if $showmsg eq 'y'}
{$msg}
{elseif $notrecognized eq 'y'}
{tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}<br />
<form action="tiki-register.php" method="post">
<input type="text" name="email" value="{$email}"/>
<input type="hidden" name="name" value="{$login}"/>
<input type="hidden" name="pass" value="{$password}"/>
<input type="hidden" name="novalidation" value="yes"/>
<button type="submit" name="register">{tr}Register{/tr}</button>
</form>
{else}
{if $prefs.rnd_num_reg eq 'y'}
<small>{tr}Your registration code:{/tr}</small>
<img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/>
<br />
{/if}
<form action="tiki-register.php" method="post"> <br />
<table class="normal">
<tr><td class="formcolor">{tr}Username{/tr}:</td><td class="formcolor"><input type="text" name="name" /></td></tr>
{if $prefs.useRegisterPasscode eq 'y'}
<tr><td class="formcolor">{tr}Passcode to register (not your user password){/tr}:</td><td class="formcolor"><input type="password" name="passcode" /></td></tr>
{/if}
{if $prefs.rnd_num_reg eq 'y'}
<tr><td class="formcolor">{tr}Registration code{/tr}:</td>
<td class="formcolor"><input type="text" maxlength="8" size="8" name="regcode" /></td></tr>
{/if}
<tr><td class="formcolor">{tr}Password{/tr}:</td><td class="formcolor"><input id='pass1' type="password" name="pass" /></td></tr>
<tr><td class="formcolor">{tr}Repeat password{/tr}:</td><td class="formcolor"><input id='pass2' type="password" name="passAgain" /></td></tr>
<tr><td class="formcolor">{tr}Email{/tr}:</td><td class="formcolor"><input type="text" name="email" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><button type="submit" name="register">{tr}Register{/tr}</button></td></tr>
</table>
</form>
<br />
<table class="normal">
<tr><td class="formcolor"><a class="link" href="javascript:genPass('genepass','pass1','pass2');">{tr}Generate a password{/tr}</a></td>
<td class="formcolor"><input id='genepass' type="text" /></td></tr>
</table>
{/if}