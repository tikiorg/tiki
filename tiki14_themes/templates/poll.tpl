{if $ratings|@count and $tiki_p_wiki_view_ratings eq 'y'}
	<div style="display:inline;float:right;padding: 1px 3px; border:1px solid #666666; -moz-border-radius : 10px;font-size:.8em;">
		<div id="pollopen">
			{button href="#" _onclick="show('pollzone');hide('polledit');hide('pollopen');return false;" class="link" _text="{tr}Rating{/tr}"}
		</div>
		{if $tiki_p_wiki_vote_ratings eq 'y'}
			<div id="polledit">
				<div class="pollnav">
					{button href="#" _onclick="hide('pollzone');hide('polledit');show('pollopen');return false;" _text="{tr}[-]{/tr}"}
					{button href="#" _onclick="show('pollzone');hide('polledit');hide('pollopen');return false;" class="link" _text="{tr}View{/tr}"}
				</div>

				{foreach from=$ratings item=r}
					{if $r.title}
						<div>{$r.title|escape}</div>
					{/if}
					<form method="post" action="tiki-index.php">
						{if $page}
							<input type="hidden" name="wikipoll" value="1">
							<input type="hidden" name="page" value="{$page|escape}">
						{/if}
						<input type="hidden" name="polls_pollId" value="{$r.info.pollId|escape}">
						<table>
							{foreach from=$r.options item=option}
								<tr>
									<td valign="top" {if $r.vote eq $option.optionId}class="highlight"{/if}>
										<input type="radio" name="polls_optionId" value="{$option.optionId|escape}" id="poll{$r.info.pollId|escape}{$option.optionId|escape}" {if $r.vote eq $option.optionId} checked="checked"{/if}>
									</td>
									<td valign="top" {if $r.vote eq $option.optionId}class="highlight"{/if}>
										<label for="poll{$r.info.pollId|escape}{$option.optionId|escape}">{$option.title|escape}</label>
									</td>
									<td valign="top" {if $r.vote eq $option.optionId}class="highlight"{/if}>
										({$option.votes|escape})
									</td>
								</tr>
							{/foreach}
						</table>
						<div align="center">
							<input type="submit" class="btn btn-default btn-sm" name="pollVote" value="{tr}vote{/tr}" style="border:1px solid #666666;font-size:.8em;">
						</div>
					</form>
				{/foreach}
			</div>
			<div id="pollzone">
				<div class="pollnav">
					{button href="#" _onclick="hide('pollzone');hide('polledit');show('pollopen');return false;" _text="[-]"}
					{button href="#" _onclick="hide('pollzone');show('polledit');hide('pollopen');return false;" _text="{tr}Vote{/tr}"}
				</div>
				{foreach from=$ratings item=r}
					<div>
						{if $r.title}
							<div>{$r.title|escape}</div>
						{/if}
						{foreach from=$r.options item=option}
							<div>{$option.votes|escape} : {$option.title|escape}</div>
						{/foreach}
					</div>
				{/foreach}
			</div>
		{else}
			<div id="pollzone">
				<div class="pollnav">
					{button href="#" _onclick="hide('pollzone');hide('polledit');show('pollopen');return false;" _text="[-]"}
				</div>
				{foreach from=$ratings item=r}
					<div>
						{if $r.title}
							<div>{$r.title|escape}</div>
						{/if}
						{foreach item=option from=$r.options}
							<div>{$option.votes|escape} : {$option.title|escape}</div>
						{/foreach}
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
{/if}
