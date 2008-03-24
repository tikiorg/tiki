<h2>{tr}Register as a new user{/tr}</h2>
<br />
{if $prefs.feature_ajax eq 'y'}
  <script src="lib/registration/register_ajax.js" type="text/javascript"></script>
{/if}

{if $showmsg eq 'y'}
{$msg}

{elseif $userTrackerData}
{$userTrackerData}

{elseif $email_valid eq 'n'}

  {tr}Your email could not be validated; make sure you email is correct and click register below.{/tr}<br />
  <form action="tiki-register.php" method="post">
    <input type="text" name="email" value="{$smarty.post.email}"/>
    <input type="hidden" name="name" value="{$smarty.post.name}"/>
    <input type="hidden" name="pass" value="{$smarty.post.pass}"/>
    <input type="hidden" name="regcode" value="{$smarty.post.regcode}"/>
    <input type="hidden" name="novalidation" value="yes"/>
    {if $chosenGroup}<input type="hidden" name="chosenGroup" value="{$smarty.post.chosenGroup}" />{/if}
    <input type="submit" name="register" value="{tr}Register{/tr}" />
  </form>

{else}

  {if $prefs.rnd_num_reg eq 'y'}
    <small>{tr}Your registration code:{/tr}</small>
    <img src="tiki-random_num_img.php" alt='{tr}Random Image{/tr}'/>
    <br />
  {/if}
  <form action="tiki-register.php" method="post"> <br />
    <table class="normal">


      <tr><td class="formcolor">{tr}Username{/tr}:</td>
      <td class="formcolor">
        <input style="float:left" type="text" name="name" id="name"
	  {if $prefs.feature_ajax eq 'y'}onKeyUp="return check_name()"{/if}/>
          {if $prefs.feature_ajax eq'y'}<div id="checkfield" style="float:left"></div>{/if}
		{if $prefs.login_is_email eq 'y'}
		({tr}Use your email as login{/tr})
		{else}
	  {if $prefs.lowercase_username eq 'y'}({tr}lowercase only{/tr}){/if}</td>
		{/if}
      </tr>

      {if $prefs.useRegisterPasscode eq 'y'}
        <tr><td class="formcolor">{tr}Passcode to register (not your user password){/tr}:</td>
	<td class="formcolor"><input type="password" name="passcode" /></td></tr>
      {/if}
      {if $prefs.rnd_num_reg eq 'y'}
        <tr><td class="formcolor">{tr}Registration code{/tr}:</td>
        <td class="formcolor"><input type="text" maxlength="8" size="8" name="regcode" /></td></tr>
      {/if}
 
      <tr><td class="formcolor">{tr}Password{/tr}:</td>
      <td class="formcolor"><input id='pass1' type="password" name="pass"
        {if $prefs.feature_ajax eq'y'}onKeyUp="check_pass()"{/if}/></td>
      </tr>

      <tr><td class="formcolor">{tr}Repeat password{/tr}:</td>
      <td class="formcolor"><input style="float:left" id='pass2' type="password" name="passAgain"
        {if $prefs.feature_ajax eq'y'}onKeyUp="check_pass()"{/if}/>{if $prefs.feature_ajax eq'y'}<div style="float:left" id="checkpass"></div>{/if}</td>
      </tr>

{if $prefs.login_is_email ne 'y'}
      <tr><td class="formcolor">{tr}Email{/tr}:</td>
      <td class="formcolor"><input style="float:left" type="text" id="email" name="email"
        {if $prefs.validateUsers eq 'y' and $prefs.feature_ajax eq 'y'}onKeyUp="return check_mail()"{/if}/>{if $prefs.feature_ajax eq'y'}<div id="checkmail" style="float:left"></div>{/if}
        {if $prefs.validateUsers eq 'y' and $prefs.validateEmail ne 'y'}<br />
        <div style="float:left">{tr}A valid email is mandatory to register{/tr}</div>{/if}</td>
      </tr>
{/if}
      {* Custom fields *}
      {section name=ir loop=$customfields}
        {if $customfields[ir].show}
          <tr><td class="form">{tr}{$customfields[ir].label}{/tr}:</td>
            <td class="form"><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" /></td>
          </tr>
        {/if}
      {/section}
      
      {* Groups *}
      {if isset($theChoiceGroup)}
        <input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
      {elseif $listgroups}
        <tr><td class="formcolor">{tr}Select your group{/tr}</td><td class="formcolor">
        {foreach item=gr from=$listgroups}
          {if $gr.registrationChoice eq 'y'}<input type="radio" name="chosenGroup" value="{$gr.groupName|escape}">{if $gr.groupDesc}{$gr.groupDesc}{else}{$gr.groupName}{/if}</input><br />{/if}
        {/foreach}</td></tr>
      {/if}
      {if $prefs.useRegisterAntibot eq 'y'}{include file='antibot.tpl'}{/if}

      <tr><td class="formcolor">&nbsp;</td>
      <td class="formcolor"><input type="submit" name="register" value="{tr}Register{/tr}" /></td>
      </tr>
    </table>
  </form>
<div class="simplebox">
{tr}NOTE: Make sure to whitelist this domain to prevent<br />registration emails being canned by your spam filter!{/tr}
</div>
  <br />

  <table class="normal">
    <tr><td class="formcolor"><a class="link" href="javascript:genPass('genepass','pass1','pass2');">{tr}Generate a password{/tr}</a></td>
    <td class="formcolor"><input id='genepass' type="text" /></td>
    </tr>
  </table>
{/if}
