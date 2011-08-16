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

{if $p.admin or $p.permission or $p.view or $p.module or $p.plugin}
	{capture name=over_actions}{strip}
		<div class='opaque'>
			<div class='box-title'>{tr}Actions{/tr}</div>
			<div class='box-data'>
				{if $p.admin}
					<a class="iconmenu" href="{$p.admin|escape}">{icon _id=application_form title="{tr}Admin{/tr}"} {tr}Admin{/tr}</a>
				{/if}
				{if $p.permission}
					<a class="iconmenu" href="{$p.permission|escape}">{icon _id=key title="{tr}Permissions{/tr}"} {tr}Permissions{/tr}</a>
				{/if}
				{if $p.view}
					<a class="iconmenu" href="{$p.view|escape}">{icon _id=magnifier title="{tr}View{/tr}"} {tr}View{/tr}</a>
				{/if}
				{if $p.module}
					<a class="iconmenu" href="{$p.module|escape}">{icon _id=module title="{tr}Module{/tr}"} {tr}Module{/tr}</a>
				{/if}
				{if $p.plugin}
					<a class="iconmenu" href="{$p.plugin|escape}">{icon _id=plugin title="{tr}Plugin{/tr}"} {tr}Plugin{/tr}</a>
				{/if}
			</div>
		</div>
	{/strip}{/capture}
	<a class="icon" title="{tr}Actions{/tr}" href="#" {popup trigger="onClick" sticky=1 mouseoff=1 fullhtml="1" center=true text=$smarty.capture.over_actions|escape:"javascript"|escape:"html"} style="padding:0; margin:0; border:0">
		{icon _id='wrench' alt="{tr}Actions{/tr}"}
	</a>
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
	})
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
	});
{/jq}

{$p.pages}
