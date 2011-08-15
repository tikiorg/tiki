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

{if $p.admin}
	<a href="{$p.admin|escape}">{icon _id=wrench title="{tr}Admin{/tr}"}</a>
{/if}

{if $p.permission}
	<a href="{$p.permission|escape}">{icon _id=key title="{tr}Permissions{/tr}"}</a>
{/if}

{if $p.view}
	<a href="{$p.view|escape}">{icon _id=magnifier title="{tr}View{/tr}"}</a>
{/if}

{if $p.module}
	<a href="{$p.module|escape}">{icon _id=module title="{tr}Module{/tr}"}</a>
{/if}

{if $p.plugin}
	<a href="{$p.plugin|escape}">{icon _id=plugin title="{tr}Plugin{/tr}"}</a>
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
