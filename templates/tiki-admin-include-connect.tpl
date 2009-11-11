{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Tiki is a community project!{/tr} {tr}<a href="http://info.tikiwiki.org/Join+the+community">{tr}Join the community{/tr}</a>{/tr}
{/remarksbox}

<div class="adminoptionbox">

		<fieldset>
			<legend>{tr}Promote your site{/tr}</legend>
				{tr}To submit your site to TikiWiki.org:{/tr} <a href="tiki-register_site.php">{tr}Submit site{/tr}</a>
		</fieldset>


<form class="admin" id="connect" name="connect" action="tiki-admin.php?page=connect" method="post">

		<fieldset>
			<legend>{tr}Help Tiki spread{/tr}</legend>
			{preference name=feature_bot_bar_power_by_tw}
		</fieldset>

		<fieldset>
                        <legend>{tr}Help improve Tiki{/tr}</legend>
                        {tr}To submit a feature request or to report a bug:{/tr} <a href="http://dev.tikiwiki.org/Report+a+Bug">{tr}Click here to go to our development site{/tr}</s> 
                </fieldset>

		<div class="heading input_submit_container" style="text-align: center;">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
		
</form>


</div>
