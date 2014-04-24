{* $Id: layout_view.tpl 48366 2013-11-08 16:12:24Z lphuberdeau $ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
	<head>
		{include file='header.tpl'}
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body{html_body_attributes class="navbar-padding"}>
		{$cookie_consent_html}

		{if $prefs.feature_ajax eq 'y'}
			{include file='tiki-ajax_header.tpl'}
		{/if}
		
		<div class="container">
			<div class="row">
				{if zone_is_empty('left') and zone_is_empty('right')}
					<div class="col-md-12" id="col1">
                        {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                            {modulelist zone=pagetop}
                        {/if}
						{block name=title}{/block}
						{block name=navigation}{/block}
						{error_report}
						{block name=content}{/block}
                        {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                            {modulelist zone=pagebottom}
                        {/if}
					</div>
				{elseif zone_is_empty('left')}
					<div class="col-md-10" id="col1">
                        {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                            {modulelist zone=pagetop}
                        {/if}
						{block name=title}{/block}
						{block name=navigation}{/block}
						{error_report}
						{block name=content}{/block}
                        {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                            {modulelist zone=pagebottom}
                        {/if}
					</div>
					<div class="col-md-2" id="col3">
						{modulelist zone=right}
					</div>
				{elseif zone_is_empty('right')}
					<div class="col-md-10 col-md-push-2" id="col1">
                        {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                            {modulelist zone=pagetop}
                        {/if}
						{block name=title}{/block}
						{block name=navigation}{/block}
						{error_report}
						{block name=content}{/block}
                        {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                            {modulelist zone=pagebottom}
                        {/if}
					</div>
					<div class="col-md-2 col-md-pull-10" id="col2">
						{modulelist zone=left}
					</div>
				{else}
					<div class="col-md-8 col-md-push-2" id="col1">
                        {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                            {modulelist zone=pagetop}
                        {/if}
						{block name=title}{/block}
						{block name=navigation}{/block}
						{error_report}
						{block name=content}{/block}
                        {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                            {modulelist zone=pagebottom}
                        {/if}
					</div>
					<div class="col-md-2 col-md-pull-8" id="col2">
						{modulelist zone=left}
					</div>
					<div class="col-md-2" id="col3">
						{modulelist zone=right}
					</div>
				{/if}
			</div>
		</div>

		<div class="container-fluid well text-center">
			{modulelist zone=bottom}
		</div>
		
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">		
				 <div class="navbar-header col-lg-2">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#header-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand logo-nav" href="./">
						<img src="{$prefs.sitelogo_icon}" style="margin-top:-7px">
						{$prefs.sitetitle}
					</a>
				</div>
				
				<div class="collapse navbar-collapse" id="header-navbar-collapse-1">										
					<ul class="nav navbar-nav">
						{modulelist zone=top}
					</ul>
					<ul class="nav navbar-nav navbar-right">
						{if $user}
							<li>
								<div class="btn-group">
									<button type="button" class="btn btn-primary navbar-btn dropdown-toggle" data-toggle="dropdown">
										{glyph name="plus"} {tr}Create{/tr}
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li>
											<a href="{service controller=wiki action=create_page modal=true}" data-toggle="modal" data-target="#bootstrap-modal">
												{tr}Create Wiki Page{/tr}
											</a>
										</li>
										<li>
											<a href="tiki-blog_post.php">
												{tr}Create Blog Post{/tr}
											</a>
										</li>
										<li>
											<a href="{service controller=tracker action=select_tracker}">
												{tr}Create Tracker Item{/tr}
											</a>
										</li>
									</ul>
								</div>
							</li>
						{/if}
						{if $prefs.feature_search eq 'y'}
							<li>
								<form class="navbar-form col-md-3" role="search" action="tiki-searchindex.php">
									<div class="form-group">
										<input name="filter~content" type="search" class="form-control" placeholder="Search">
									</div>
									<button type="submit" class="btn btn-default">{glyph name=search}</button>
								</form>
							</li>
						{/if}
						{if $user}
							<li>{notification_link}</li>
							{if $tiki_p_admin eq y}
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="{tr}System Administration{/tr}"><span class="glyphicon glyphicon-cog"></span> <b class="caret"></b></a>
									<ul class="dropdown-menu">
										{if $tiki_p_admin_users eq y}
											<li><a href="tiki-adminusers.php">{tr}Users{/tr}</a></li>
										{/if}
										<li><a href="tiki-admingroups.php">{tr}Groups{/tr}</a></li>
										<li><a href="tiki-objectpermissions.php">{tr}Permissions{/tr}</a></li>
										<li class="divider"></li>
										<li><a href="tiki-admin_modules.php">{tr}Modules{/tr}</a></li>
										<li class="divider"></li>
										<li><a href="tiki-admin.php">{tr}System{/tr}</a></li>
										<li class="hidden">{module module=menu mode="module" id="42" type="vert" decorations="n" nobox="y" notitle="y"}</li>			
									</ul>
								</li>
							{/if}
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" title="{$userName}"><span class="glyphicon glyphicon-user"></span> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="tiki-user_preferences.php">{$user|userlink}</a></li>
									<li class="divider"></li>
									<li><a href="{if $prefs.feature_sefurl eq 'y'}logout{else}tiki-logout.php{/if}">{tr}Log out{/tr}</a></li>
								</ul>
							</li>
						{else}
							<li><a href="{if $prefs.feature_sefurl eq 'y'}login{else}tiki-login_scr.php{/if}">{tr}{glyph name=user} Log in{/tr}</a></li>
							{if $prefs.allowRegister eq 'y'}
								<li><a href="{if $prefs.feature_sefurl eq 'y'}register{else}tiki-register.php{/if}">{tr}Register{/tr}</a></li>
							{/if}
						{/if}
					</ul>
				</div>
			</div>
		</nav>
		{include file='footer.tpl'}
	</body>
</html>
{if !empty($smarty.request.show_smarty_debug)}
	{debug}
{/if}
