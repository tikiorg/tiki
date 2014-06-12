{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{tabset id=mustread_detail toggle=n}
		{tab key=detail name="{tr}Detail{/tr}"}
			{service_inline controller=tracker action=view id=$item.itemId}
		{/tab}
		{tab key=notification name="{tr}Notifications{/tr}"}
			<div class="row">
				<div class="col-md-4">
					<strong>{object_link type=trackeritem id=$item.itemId}:</strong>
					{$item.creation_date|tiki_long_date}
					<ol class="list-unstyled">
						<li><a href="">123</a> {tr}Sent{/tr}</li>
						<li><a href="">123</a> {tr}Opened{/tr}</li>
						<li><a href="">123</a> {tr}Logged In{/tr}</li>
						<li><a href="">123</a> {tr}Unopened{/tr}</li>
					</ol>

					<a href="{service controller=mustread action=circulate modal=1 id=$item.itemId}" class="btn btn-default" data-toggle="modal" data-target="#bootstrap-modal">{tr}Circulate{/tr}</a>
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
