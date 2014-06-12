{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	{tabset name=mustread_detail toggle=n skipsingle=1}
		{tab key=detail name="{tr}Detail{/tr}"}
			{service_inline controller=tracker action=view id=$item.itemId}
		{/tab}
		{tab key=notification name="{tr}Notifications{/tr}"}
			<div class="row">
				<div class="col-md-4">
					<strong>{object_link type=trackeritem id=$item.itemId}:</strong>
					{$item.creation_date|tiki_long_date}
					<ol class="list-unstyled">
						<li><a href="{service controller=mustread action=list id=$item.itemId notification=sent}#contentmustread_detail-notification">123</a> {tr}Sent{/tr}</li>
						<li><a href="{service controller=mustread action=list id=$item.itemId notification=open}#contentmustread_detail-notification">123</a> {tr}Opened{/tr}</li>
						<li><a href="">123</a> {tr}Logged In{/tr}</li>
						<li><a href="{service controller=mustread action=list id=$item.itemId notification=unopen}#contentmustread_detail-notification">123</a> {tr}Unopened{/tr}</li>
					</ol>

					{if $canCirculate}
						<a href="{service controller=mustread action=circulate modal=1 id=$item.itemId}" class="btn btn-default" data-toggle="modal" data-target="#bootstrap-modal">{tr}Circulate{/tr}</a>
					{/if}
				</div>
				<div class="col-md-8">
					{if $resultset}
						<h3>{tr}People{/tr}</h3>
						{foreach $resultset as $row}
							<div class="media">
								<div class="pull-left">
									{$row.object_id|avatarize:'':'img/noavatar.png'}
								</div>
								<div class="media-body">
									<h4 class="media-heading">{$row.title|escape}</h4>
									<p>...</p>
								</div>
							</div>
						{/foreach}
						{pagination_links resultset=$resultset}{/pagination_links}
					{/if}
				</div>
			</div>
		{/tab}
		{tab key=actions name="{tr}Actions{/tr}"}
		{/tab}
	{/tabset}
{/block}
