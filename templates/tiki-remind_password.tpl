{* $Id$ *}
{title admpage='login'}{tr}I forgot my password{/tr}{/title}

{if $showmsg ne 'n'}
	{if $showmsg eq 'e'}
		<span class="warn">{icon _id=exclamation alt="{tr}Error{/tr}" style="vertical-align:middle;align:left;"}
	{else}
		{icon _id=accept alt="{tr}OK{/tr}" style="vertical-align:middle;align:left;"}
	{/if}
	{if $prefs.login_is_email ne 'y'}
		{$msg|escape:'html'|@default:"{tr}Enter your username or email.{/tr}"}
	{else}
		{$msg|escape:'html'|@default:"{tr}Enter your email.{/tr}"}
	{/if}
	{if $showmsg eq 'e'}</span>{/if}
	<br><br>
{/if}
{if $showfrm eq 'y'}
    <form class="form-horizontal col-md-10" action="tiki-remind_password.php" method="post">
		{if $prefs.login_is_email ne 'y'}
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label" for="name">Username</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" placeholder="Username" name="name" id="name">
                </div>
            </div>
        <div class="col-sm-offset-3 col-md-offset-2 col-sm-10">
            <p><strong>OR</strong></p>
            </div>

        {/if}
        <div class="form-group">
            <label class="col-sm-3 col-md-2 control-label" for="email">Email</label>
            <div class="col-sm-6">
                {if $prefs.login_is_email ne 'y'}
                    <input type="email" class="form-control" placeholder="Email" name="email" id="email">
                {else}
                    <input type="email" class="form-control" placeholder="Email" name="name" id="name">
                {/if}
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-md-offset-2 col-sm-10">
                <input type="submit" class="btn btn-default" name="remind" value="{if $prefs.feature_clear_passwords eq 'y'}{tr}Send me my Password{/tr}{else}{tr}Request Password Reset{/tr}{/if}">
            </div>
        </div>
		</table>
	</form>
{/if}
