
        <script language="javascript" type="text/javascript">
        {literal}
                function submitbutton() {
                        var form = document.registrationForm;
                        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

                        // do field validation
                        /*if (form.name.value == "") {
                                alert( "{/literal}{$_REGWARN_NAME|escape}{literal}" );
                        } else*/
                        if (form.name.value == "") {
                                alert( "{/literal}{$_REGWARN_UNAME|escape}{literal}" );
                        } else if (r.exec(form.name.value) || form.name.value.length < 3) {
                                alert( "{/literal}{$_PROMPT_UNAME|escape}{literal}" );
                        //} else if (form.email.value == "") {
                                //alert( "{/literal}{$_REGWARN_MAIL|escape}{literal}" );
                        } else if (form.pass.value.length < 6) {
                                alert( "{/literal}{$_REGWARN_PASS|escape}{literal}" );
                        } else if (form.passAgain.value == "") {
                                alert( "{/literal}{$_REGWARN_VPASS1|escape}{literal}" );
                        } else if ((form.pass.value != "") && (form.pass.value != form.passAgain.value)){
                                alert( "{/literal}{$_REGWARN_VPASS2|escape}{literal}" );
                        } else if (r.exec(form.pass.value)) {
                                alert( "{/literal}{$_PROMPT_PASS|escape}{literal}" );
                        } else {
                                form.submit();
                        }
                }
        {/literal}
        </script>

<h2>{tr}Register as a new user{/tr}</h2>
<br />
{if $showmsg eq 'y'}
{$msg}
{elseif $notrecognized eq 'y'}
{tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}<br />
<form action="tiki-register.php" method="post" name="registrationForm">
<input type="text" name="email" value="{$email}"/>
<input type="hidden" name="name" value="{$login}"/>
<input type="hidden" name="pass" value="{$password}"/>
<input type="hidden" name="novalidation" value="yes"/>
<input type="hidden" name="register" value="register"/>
<input type="button" name="register" value="{tr}register{/tr}" onclick="submitbutton()"/>
</form>
{else}
{if $rnd_num_reg eq 'y'}
<small>{tr}Your registration code:{/tr}</small>
<img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/>
{/if}
<form action="tiki-register.php" method="post" name="registrationForm">
<p>{tr}Thank you for your interest in joining. Please complete the following form to create your account.{/tr}</p>
{tr}Fields marked with a * are mandatory.{/tr}<br /><br />
<table class="normal">
<tr><td class="formcolor">{tr}Username{/tr}: *</td><td class="formcolor"><input type="text" name="name" /></td></tr>
{if $useRegisterPasscode eq 'y'}
<tr><td class="formcolor">{tr}Passcode to register (not your user password){/tr}: *</td><td class="formcolor"><input type="password" name="passcode" /></td></tr>
{/if}
{if $rnd_num_reg eq 'y'}
<tr><td class="formcolor">{tr}Registration code{/tr}: *</td>
<td class="formcolor"><input type="text" maxlength="8" size="8" name="regcode" /></td></tr>
{/if}

<tr><td class="formcolor">{tr}Password{/tr}: *</td><td class="formcolor"><input id='pass1' type="password" name="pass" /></td></tr>

<tr><td class="formcolor">{tr}Repeat password{/tr}: *</td><td class="formcolor"><input id='pass2' type="password" name="passAgain" /></td></tr>

<tr><td class="formcolor">{tr}Email{/tr}: *</td><td class="formcolor"><input type="text" name="email" />
{if $validateUsers eq 'y' and $validateEmail ne 'y'}<br />{tr}A valid email is mandatory to register{/tr}{/if}</td></tr>

{* Custom fields *}
{section name=ir loop=$customfields}
<tr><td class="form">{tr}{$customfields[ir].label}{/tr}: *</td>
<td class="form"><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" /></td>
    </tr>
{/section}

<input type="hidden" name="register" value="register"/>

<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="button" name="register" value="{tr}register{/tr}" onclick="submitbutton()" /></td></tr>

</table>
</form>
<br />
<table class="normal">
<tr><td class="formcolor"><a class="link" href="javascript:genPass('genepass','pass1','pass2');">{tr}Generate a password{/tr}</a></td>
<td class="formcolor"><input id='genepass' type="text" /></td></tr>
</table>
{/if}
