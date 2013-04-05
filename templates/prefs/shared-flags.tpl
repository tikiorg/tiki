{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon _id=help alt=''}
	</a>
{elseif $p.description}
	<span class="tikihelp" title="{$p.name|escape}: {$p.description|escape} {if $p.separator}{tr}Separator is {/tr}<b>{$p.separator|simplewiki}</b>{/if}">
		{icon _id=information alt=''}
	</span>
{/if}

{if $p.warning}
	<a href="#" target="tikihelp" class="tikihelp" title="{tr}Warning:{/tr} {$p.warning|escape}">
		{icon _id=error alt=''}
	</a>
{/if}

{if $p.modified and $p.available}
	<input class="pref-reset system" type="checkbox" name="lm_reset[]" value="{$p.preference|escape}" style="display:none">
	<input type="hidden" id="{$p.preference|escape}_default" value="{$p.default|escape}">
{/if}

{if !empty($p.popup_html)}
	<a class="icon" title="{tr}Actions{/tr}" href="#" style="padding:0; margin:0; border:0"
			 {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml=1 center="true" text=$p.popup_html|escape:"javascript"|escape:"html"}>
		{icon _id='application_form' alt="{tr}Actions{/tr}"}
	</a>
{/if}
{if !empty($p.voting_html)}
	{$p.voting_html}
{/if}
{jq}
$('.pref-reset')
	.change( function() {
		var c = $(this).prop('checked') === "checked";
		var $el = $(this).closest('.adminoptionbox').find('input:not(:hidden),select,textarea')
			.not('.system').attr( 'disabled', c )
			.css("opacity", c ? .6 : 1 );
		var defval = $("#" + $(this).val() + "_default").val();
		if ($el.attr("type") == "checkbox") {
			$el.prop('checked', defval === "y" ? c : !c);
		} else {
			var temp = $("[name=" + $(this).val() + "]").val();
			$el.val( defval );
			$("#" + $(this).val() + "_default").val( temp );
		}
		$el.change();
	})
	.wrap('<span/>')
	.closest('span')
	.append('{{icon _id=arrow_undo alt="{tr}Reset to default{/tr}" href="#"}}')
	.find('a')
	.click( function() {
		var box = $(this).closest('span').find(':checkbox');
		box.prop('checked', box.filter(':checked').length == 0).change();
		var $i = $(this).find("img");
		if ($i.attr("src").indexOf("undo") > -1) {
			$i.attr({
				"src": $i.attr("src").replace("undo", "redo"),
				"title": "{tr}Restore current value{/tr}",
				"alt": "{tr}Restore current value{/tr}"
			});
		} else {
			$i.attr({
				"src": $i.attr("src").replace("redo", "undo"),
				"title": "{tr}Reset to default{/tr}",
				"alt": "{tr}Reset to default{/tr}"
			});
		}
		return false;
	});
{/jq}

{$p.pages}
