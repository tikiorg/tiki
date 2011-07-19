{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
		{icon _id=help alt=''}
	</a>
{elseif $p.description}
	<span class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
		{icon _id=information alt=''}
	</span>
{/if}
{if $p.warning}
	<a href="" target="tikihelp" class="tikihelp" title="{tr}Warning:{/tr} {$p.warning|escape}">
		{icon _id=error alt=''}
	</a>
{/if}

{if $p.modified and $p.available}
	<input class="pref-reset system" type="checkbox" name="lm_reset[]" value="{$p.preference|escape}" style="display:none" />
	<input type="hidden" id="{$p.preference|escape}_default" value="{$p.default|escape}" />
{/if}

{jq}
$('.pref-reset')
	.change( function() {
		var c = $(this).attr('checked') === "checked";
		var $el = $(this).closest('.adminoptionbox').find('input:not(:hidden),select,textarea')
			.not('.system').attr( 'disabled', c )
			.css("opacity", c ? .6 : 1 );
		var defval = $("#" + $(this).val() + "_default").val();
		if ($el.attr("type") == "checkbox") {
			$el.attr('checked', defval === "y" ? c : !c);
		} else {
			var temp = $("[name=" + $(this).val() + "]").val();
			$el.val( defval );
			$("#" + $(this).val() + "_default").val( temp );
		}
		$el.change();
	} )
	.wrap('<span/>')
	.closest('span')
		.append('{{icon _id=arrow_undo alt="{tr}Reset to default{/tr}" href="#"}}')
		.find('a')
			.click( function() {
				var box = $(this).closest('span').find(':checkbox');
				box.attr('checked', box.filter(':checked').length == 0).change();
				var $i = $(this).find("img");
				$i.attr("src", $i.attr("src").indexOf("undo") > -1 ? $i.attr("src").replace("undo", "redo") :  $i.attr("src").replace("redo", "undo"));
				return false;
			} );
{/jq}

{$p.pages}
