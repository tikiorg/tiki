{title}{tr}Admin layout{/tr}{/title}

<div class="navbar">
	{button href="tiki-theme_control.php" _text="{tr}Theme control{/tr}"}
</div>

{section name=ix loop=$sections}
	<a name="{$sections[ix].name|escape}"></a>
	{assign var=first value=false}
	[{section name=ij loop=$sections}
		{if $first}|{else}{assign var=first value=true}{/if}
		<a href="#{$sections[ij].name}" class="link">{$sections[ij].name}</a>
	{/section}]

	<div class="cbox">
		<div class="cbox-title">{tr}layout options:{/tr} {tr}{$sections[ix].name}{/tr}</div>
		<div class="cbox-data">
			<div class="simplebox">
				{$sections[ix].name} {tr}layout options{/tr}
				<form method="post" action="tiki-admin_layout.php">
					<table class="formcolor">
						<tr>
							<td>{tr}Left column:{/tr}</td>
							<td>
								<input type="checkbox" name="{$sections[ix].name|escape}_left_column" {if $sections[ix].left_column eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>
						<tr>
							<td>{tr}Right column:{/tr}</td>
							<td>
								<input type="checkbox" name="{$sections[ix].name|escape}_right_column" {if $sections[ix].right_column eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>
							<tr>
							<td>{tr}Top bar:{/tr}</td>
							<td>
								<input type="checkbox" name="{$sections[ix].name|escape}_top_bar" {if $sections[ix].top_bar eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>
						<tr>
							<td>{tr}Bottom bar:{/tr}</td>
							<td>
								<input type="checkbox" name="{$sections[ix].name|escape}_bot_bar" {if $sections[ix].bot_bar eq 'y'}checked="checked"{/if}/>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="2">
								<input type="submit" name="{$sections[ix].name|escape}_layout" value="{tr}Set features{/tr}" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<br />
{/section}
