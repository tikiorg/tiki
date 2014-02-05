{* $Id$ *}
{* ==> put in this file what is not displayed in the layout (javascript, debug..)*}
<div id="bootstrap-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		</div>
	</div>
</div>
{if (! isset($display) or $display eq '')}
	{if count($phpErrors)}
		{if ($prefs.error_reporting_adminonly eq 'y' and $tiki_p_admin eq 'y') or $prefs.error_reporting_adminonly eq 'n'}
		{button _ajax="n" _id="show-errors-button" _onclick="flip('errors');return false;" _text="{tr}Show php error messages{/tr}"}
		<div id="errors" class="rbox warning" style="display:{if (isset($smarty.session.tiki_cookie_jar.show_errors) and $smarty.session.tiki_cookie_jar.show_errors eq 'y') or $prefs.javascript_enabled ne 'y'}block{else}none{/if};">
			&nbsp;{listfilter selectors='#errors>div'}
			{foreach item=err from=$phpErrors}
				{$err}
			{/foreach}
		</div>
		{/if}
	{/if}

	{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
		{* Include debugging console.*}
		{debugger}
	{/if}

{/if}
{*needs to be in the footer.tpl so that it runs at the end rather than in antibot.tpl where it breaks tracker validation*}
{jq}
if ($("#antibotcode").parents('form').data("validator")) {
    $( "#antibotcode" ).rules( "add", {
        required: true,
        remote: {
            url: "validate-ajax.php",
            type: "post",
            data: {
                validator: "captcha",
                parameter: function() {
                    return $jq("#captchaId").val();
                },
                input: function() {
                    return $jq("#antibotcode").val();
                }
            }
        }
    });
} else {
    var form = $("#antibotcode").parents('form');
    $("form[name="+ form.attr('name') +"]").validate({
        rules: {
            "captcha[input]": {
                required: true,
                remote: {
                    url: "validate-ajax.php",
                    type: "post",
                    data: {
                        validator: "captcha",
                        parameter: function() {
                            return $jq("#captchaId").val();
                        },
                        input: function() {
                            return $jq("#antibotcode").val();
                        }
                    }
                }
            }
        },
        messages: {
            "captcha[input]": { required: "This field is required"},
        },
        submitHandler: function(){form.submit();}
    });
}
{/jq}
