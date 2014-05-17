{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Configuration Profiles Wizard{/tr}" title="{tr}Configuration Profiles Wizard{/tr}" /></div>
{tr}Check out these more advanced configurations that demonstrate some other Tiki Features that you might be interested in for your site{/tr}. </br></br>
{remarksbox type="warning" title="{tr}Warning{/tr}"}
    <div target="tikihelp" class="tikihelp" style="float:right" title="{tr}Demo Profiles:{/tr}
		{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}.
		<br/><br/>
		{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.">
        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
    </div>
{tr}They are not to be initially applied in production environments since they cannot be easily reverted and changes and new objects in your site are created for real{/tr}
{/remarksbox}
<div class="adminWizardContent">
    <fieldset>
        <legend>{tr}Profiles:{/tr}</legend>
        <table style="width:100%">
            <tr>
                <td style="width:48%">
                    <b>{tr}Shopping Cart{/tr}</b> (<a href="tiki-admin.php?profile=Shopping_Cart&show_details_for=Shopping_Cart&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
                    <br>
                    {tr}This profile provides a Shopping Cart and the corresponding basic payment system.{/tr}
                    {tr}It currently uses the PayPal shopping cart rather than the built in Tiki{/tr}.
                    <br/><a href="https://doc.tiki.org/Shopping+Cart"  target="tikihelp" class="tikihelp" title="{tr}Shopping Cart{/tr}:
        	{tr}It creates:{/tr}
        	<ul>
	            <li>{tr}A tracker for products including price, weight, image and stock quantity{/tr}</li>
                <li>{tr}Some sample items which are open, pending and closed items, with different permissions to view or edit them for different groups of users{/tr}</li>
        	    <li>{tr}A small category subtree to classify products{/tr}</li>
                <li>{tr}Wiki pages to display the available products list, one product details page and a search form{/tr}</li>
            </ul>
            {tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display544" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_shopping_cart.png"  width="150" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                    <br/>
                </td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    <b>{tr}Easy GeoBlog{/tr}</b> (<a href="tiki-admin.php?profile=Easy+GeoBlog&show_details_for=Easy+GeoBlog&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
                    <br>
                    {tr}This profile demonstrates the geolocation of Blog posts, in conjunction with other associated features{/tr}
                    <br/><a href="https://doc.tiki.org/Geolocation"  target="tikihelp" class="tikihelp" title="{tr}Easy GeoBlog{/tr}:
        	{tr}More details{/tr}:
            <ul>
                <li>{tr}Single map with all geolocated blog posts{/tr}</li>
                <li>{tr}Different home page once the user logs in{/tr}</li>
                <li>{tr}Random header image from files included in a file gallery{/tr}</li>
                <li>{tr}Wysiwyg Editor (compatible mode with wiki syntax){/tr}</li>
                <li>{tr}Wiki, Search, Menu & Freetags{/tr}</li>
                <li>{tr}Comments moderation & Banning (for anonymous comments to your site){/tr}</li>
            </ul>
        	{tr}Click to read more{/tr}">
                        <img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
                    </a>
                    <div style="display:block; margin-left:auto; margin-right:auto; width:202px;">
                        <a href="http://tiki.org/display512" class="internal" rel="box" title="{tr}Click to expand{/tr}">
                            <img src="img/profiles/profile_thumb_easy_geoblog.png"  width="200" style="display:block; margin-left:auto; margin-right:auto;border:1px solid darkgray;" alt="Click to expand" class="regImage pluginImg" title="{tr}Click to expand{/tr}" />
                        </a>
                        <div class="mini" style="width:100px;">
                            <div class="thumbcaption text-center">{tr}Click to expand{/tr}</div>
                        </div>
                    </div>
                    <br/>
                </td>
            </tr>
            <tr>
                <td style="width:48%">
                    <!--
	<b>{tr}Groupmail{/tr}</b> (<a href="tiki-admin.php?profile=Groupmail&show_details_for=Groupmail&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
	<br>
	{tr}This profile allows to provide a way for a team (a Tiki group) to process email contact requests, save them in contact lists and act on them and record the process in wiki pages{/tr}
	{tr}It creates:{/tr}
	<ul>
	<li>{tr}A tracker for email messages received and store addresses in the Contacts feature{/tr}</li>
    <li>{tr}Webmail configuration to use an account for groupmail{/tr}</li>
    <li>{tr}A side module with markers to indicate who took which message{/tr}</li>
    <li>{tr}A system to review communication logs from that group mail account{/tr}</li>
	<br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Groupmail" target="_blank">{tr}Groupmail in doc.tiki.org{/tr}</a></em>
	</ul>
-->	</td>
                <td style="width:4%">
                    &nbsp;
                </td>
                <td style="width:48%">
                    <!--
	<b>{tr}Profile X{/tr}</b> (<a href="tiki-admin.php?profile=Profile_X&show_details_for=Profile_X&categories%5B%5D={$tikiMajorVersion}.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
	<br>
	{tr}This profile allows to {/tr}
	<ul>
	<li>{tr}...{/tr}</li>
    <li>{tr}...{/tr}</li>
    <li>{tr}...{/tr}</li>
	<br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Feature_X" target="_blank">{tr}Feature_X in doc.tiki.org{/tr}</a></em>
	</ul>
-->	</td>
            </tr>
        </table>
    </fieldset>
    <br>
</div>

