{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{tabset id=mustread_detail}
		{tab key=notification name="{tr}Notifications{/tr}"}
			<div class="row">
				<div class="col-md-4">
					{object_link type=trackeritem id=$item.itemId}:
					{$item.creation_date|tiki_long_date}
					<ol class="list-unstyled">
						<li><a href="">123</a> {tr}Sent{/tr}</li>
						<li><a href="">123</a> {tr}Opened{/tr}</li>
						<li><a href="">123</a> {tr}Logged In{/tr}</li>
						<li><a href="">123</a> {tr}Unopened{/tr}</li>
					</ol>
				</div>
				<div class="col-md-8">
				</div>
			</div>
		{/tab}
		{tab key=actions name="{tr}Actions{/tr}"}
		{/tab}
		{tab key=notes name="{tr}Notes{/tr}"}
		{/tab}
	{/tabset}
{/block}
