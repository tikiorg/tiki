{* $Id$ *}<!DOCTYPE html>
<html lang="{if !empty($pageLang)}{$pageLang}{else}{$prefs.language}{/if}"{if !empty($page_id)} id="page_{$page_id}"{/if}>
<head>
    {include file='header.tpl'}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body{html_body_attributes}>
{$cookie_consent_html}

{include file="layout_fullscreen_check.tpl"}

{if $prefs.feature_ajax eq 'y'}
    {include file='tiki-ajax_header.tpl'}
{/if}

<div class="header_outer">
    <div class="header_container container">
        <header class="header page-header">
            {modulelist zone=top class='row top_modules'}
        </header>
    </div>
</div>
<div class="middle_outer">
    <div class="container clearfix middle" id="middle">
		<div class="topbar row">
			{modulelist zone=topbar}
        </div>
        <div class="row">
            {if zone_is_empty('left') and zone_is_empty('right')}
                <div class="col-md-12 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {error_report}
                    <div class="pull-right">
                        {block name=quicknav}{/block}
                    </div>
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
            {elseif zone_is_empty('left')}
                <div class="col-md-9 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {error_report}
                    <div class="pull-right">
                        {block name=quicknav}{/block}
                    </div>
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
                <div class="col-md-3" id="col3">
                    {modulelist zone=right}
                </div>
            {elseif zone_is_empty('right')}
                <div class="col-md-9 col-md-push-3 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop}
                    {/if}
                    {error_report}
                    <div class="pull-right">
                        {block name=quicknav}{/block}
                    </div>
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom}
                    {/if}
                </div>
                <div class="col-md-3 col-md-pull-9" id="col2">
                    {modulelist zone=left}
                </div>
            {else}
                <div class="col-md-6 col-md-push-3 col1" id="col1">
                    {if $prefs.module_zones_pagetop eq 'fixed' or ($prefs.module_zones_pagetop ne 'n' && ! zone_is_empty('pagetop'))}
                        {modulelist zone=pagetop class=row}
                    {/if}
                    {error_report}
                    <div class="pull-right">
                        {block name=quicknav}{/block}
                    </div>
                    {block name=title}{/block}
                    {block name=navigation}{/block}
                    {block name=content}{/block}
                    {if $prefs.module_zones_pagebottom eq 'fixed' or ($prefs.module_zones_pagebottom ne 'n' && ! zone_is_empty('pagebottom'))}
                        {modulelist zone=pagebottom class=row}
                    {/if}
                </div>
                <div class="col-md-3 col-md-pull-6" id="col2">
                    {modulelist zone=left}
                </div>
                <div class="col-md-3" id="col3">
                    {modulelist zone=right}
                </div>
            {/if}
        </div>
    </div>
</div>
<footer class="footer" id="footer">
    <div class="footer_liner">
        <div class="container">
            {modulelist zone=bottom class='row row-sidemargins-zero'}
        </div>
    </div>
</footer>
{include file='footer.tpl'}
</body>
</html>
{if !empty($smarty.request.show_smarty_debug)}
    {debug}
{/if}
