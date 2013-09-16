{* $Id$ *}
{strip}
<div id="{$prefs.cookie_consent_dom_id}"
		{if $prefs.javascript_enabled eq 'y' and not empty($prefs.cookie_consent_mode)}
	 		style="display:none;" class="{$prefs.cookie_consent_mode}"
		{/if}
	>
	{if $prefs.javascript_enabled neq 'y'}<form action="{$smarty.server.PHP_SELF}" method="POST">{/if}
	<div class="description">
		{wiki}{tr}{$prefs.cookie_consent_description}{/tr}{/wiki}
	</div>
	{if !empty($prefs.cookie_consent_question)}
		<span class="question">
			{wiki}{tr}{$prefs.cookie_consent_question}{/tr}{/wiki}
		</span>
		<input type="checkbox" name="cookie_consent_checkbox">&nbsp;
	{else}
		<input type="hidden" name="cookie_consent_checkbox" value="1">
	{/if}
	{if $prefs.javascript_enabled neq 'y'}
			<input type="submit" class="btn btn-default" name="cookie_consent_button" value="{tr}{$prefs.cookie_consent_button}{/tr}">
		</form>
	{else}
		{button _text=$prefs.cookie_consent_button|tr_if _id='cookie_consent_button'}
	{/if}
</div>
{jq}
$("#cookie_consent_button").click(function(){
	if ($("input[name=cookie_consent_checkbox]:checked").length || $("input[name=cookie_consent_checkbox]").val()) {
		var exp = new Date();
		exp.setTime(exp.getTime()+(24*60*60*1000*{{$prefs.cookie_consent_expires}}));
		jqueryTiki.no_cookie = false;
		setCookie("{{$prefs.cookie_consent_name}}", "y", "", exp);
	}
	$container = $("#cookie_consent_div").parents(".ui-dialog");
	if ($container.length) {
		$("#cookie_consent_div").dialog("close");
	} else {
		$("#cookie_consent_div").fadeOut("fast");
	}
	return false;
});
{/jq}
{if $prefs.cookie_consent_mode eq 'banner'}{jq}
	setTimeout(function () {$("#cookie_consent_div").slideDown("slow");}, 500);
{/jq}{elseif $prefs.cookie_consent_mode eq 'dialog'}{jq}
	setTimeout(function () {$("#cookie_consent_div").dialog();}, 500);
{/jq}{/if}
{/strip}
