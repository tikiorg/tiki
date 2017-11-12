{* $Id$ *}
{if !empty($track_profile_changes)}
	{foreach $track_profile_changes as $profile_changes}
		{if $profile_changes|is_array}
			{assign var="type" value=$profile_changes["type"]}
			{assign var="new_value" value="n"}
			{assign var="old_value" value="n"}
			{assign var="description" value=""}

			{if !empty($profile_changes["new"])}
				{assign var="new_value" value=$profile_changes["new"]}
			{/if}

			{if !empty($profile_changes["old"])}
				{assign var="old_value" value=$profile_changes["old"]}
			{/if}

			{if !empty($profile_changes["description"])}
				{assign var="description" value=$profile_changes["description"]}
			{/if}

			{if $type == "permission"}
				{if $new_value == "y"}
					<p>{tr}Permission added:{/tr} {$description[0]}</p>
				{else}
					<p>{tr}Permission removed:{/tr} {$description[0]}</p>
				{/if}
			{elseif $type == "user"}
				{if $old_value == "n"}
					<p>{tr}User added:{/tr} {$description}</p>
				{else}
					<p>{tr}User modified:{/tr} {$description}</p>
				{/if}
			{elseif $type == "group"}
				{if $old_value == "n"}
					<p>{tr}Group added:{/tr} {$description}</p>
				{else}
					<p>{tr}Group modified:{/tr} {$description}</p>
				{/if}
			{elseif $type == "preference"}
				<p>{tr}Preference set:{/tr} {$description}={$new_value}, {tr}old value{/tr}={$old_value}</p>
			{elseif $type == "installer"}
				<p>{tr}Installer added:{/tr} {$description}</p>
			{/if}
		{else}
			<p>{$profile_changes}</p>
		{/if}
	{/foreach}
{else}
	<div class="alert alert-info text-center">
		<h4><span class="icon icon-information fa fa-info-circle fa-fw"></span>&nbsp;<span class="rboxtitle">{tr}Information{/tr}</span></h4>
		<div class="rboxcontent">{tr}Profile without changes.{/tr}</div>
	</div>
{/if}