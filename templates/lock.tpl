{* $Id: $ *}

<span class="lock_block">
	<a class="lock_button" data-type="{$data.type}" data-object="{$data.object}" data-is_locked="{$data.is_locked}"
			{if $data.is_locked} title="{tr _0=$data.lockedby|username}Locked by %0{/tr}"{/if} href="#">
		{if $data.is_locked}
			{icon name='lock'}
		{else}
			{icon name='unlock'}
		{/if}
	</a>
	{if not $data.object}
		<input type='hidden' name='locked' value=''>
	{/if}
</span>

{if $data.can_change}{jq}
	$(".lock_button").click(function(e) {
		e.preventDefault();
		var $this = $(this).tikiModal(" ");
		$.post($.service(
			"attribute",
			"set",
			{
				attribute:"tiki.object.lock",
				type: $this.data("type"),
				object: $this.data("object"),
				value: $this.data("is_locked") ? "" : jqueryTiki.username
			}
			), function(data) {
				if (data && data.value) {
					$this.find(".icon").setIcon("lock");
					$this.data("is_locked", "1")
						.attr("title", tr("Locked by " + jqueryTiki.userRealName))
						.parent().find("input[name=locked]").val(jqueryTiki.username);
				} else {
					$this.find(".icon").setIcon("unlock");
					$this.data("is_locked", "")
						.attr("title", "")
						.parent().find("input[name=locked]").val("");
				}
			},
		"json").done(function () {
			$this.tikiModal();
		});
	});
{/jq}{else}{jq}
	$(".lock_button").click(function(e) {
		return false;
	});
{/jq}{/if}
