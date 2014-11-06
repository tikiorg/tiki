{* $Id$ *}
{tr}Configure the Tiki theme and other look & feel preferences{/tr}.
<img class="pull-right" src="img/icons/large/gnome-settings-background48x48.png" alt="{tr}Set up Look & Feel{/tr}" />
<div class="media">
    <img class="pull-left" img src="img/icons/large/wizard_admin48x48.png" alt="{tr}Configuration Wizard{/tr}" title="{tr}Configuration Wizard{/tr}" />
    <div class="media-body">
        <fieldset>
            <legend>{tr}Look & Feel options{/tr}</legend>
            <div class="row">
                <div class="col-md-3 col-md-push-9">
                    <div  class="thumbnail">
                        <img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="style_thumb">
                    </div>
                </div>
                <div class="col-md-9 col-md-pull-3 adminoptionbox">
                    {preference name=theme_active}
                    <div class="adminoptionbox theme_active_childcontainer custom">
                        {preference name=theme_custom}
                    </div>
                    <div class="adminoptionbox theme_active_childcontainer legacy">
                        {preference name=style}
                        {preference name=style_option}
                        {preference name=style_admin}
                        {preference name=style_admin_option}
                    </div>
                    {preference name=site_layout}
                    {preference name=site_layout_per_object}
                </div>
            </div>
<!--
    <div style="position:relative;">
        <div class="adminoptionbox">
            {preference name=feature_fixed_width}
            <div class="adminoptionboxchild" id="feature_fixed_width_childcontainer">
                {preference name=layout_fixed_width}
            </div>
        </div>
    </div>
-->
            <br>
            <em>{tr}See also{/tr} <a href="tiki-admin.php?page=look&amp;alt=Look+%26+Feel" target="_blank">{tr}Look & Feel admin panel{/tr}</a></em>
    </fieldset>

    <fieldset>
	    <legend>{tr}Title{/tr}</legend>
	    {preference name=sitetitle}
	    {preference name=sitesubtitle}
    </fieldset>
    <fieldset>
	    <legend>{tr}Logo{/tr}</legend>
	    {preference name=sitelogo_src}
    </fieldset>
    <fieldset>
	    <legend>{tr}Favicon{/tr}</legend>
	    {preference name=site_favicon}
	    {preference name=site_favicon_type}
    </fieldset>
    </div>
</div>