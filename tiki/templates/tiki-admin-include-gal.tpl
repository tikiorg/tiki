<a name="gal"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a> |
<a href="#directory" class="link">{tr}directory{/tr}</a> |
<a href="#userfiles" class="link">{tr}userfiles{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}Image galleries{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php#gal" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Gallery (main gallery){/tr}</td><td>
<select name="homeGallery">
{section name=ix loop=$galleries}
<option value="{$galleries[ix].galleryId}" {if $galleries[ix].galleryId eq $home_gallery}selected="selected"{/if}>{$galleries[ix].name|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="galset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>

    

<div class="simplebox">
{tr}Galleries features{/tr}<br/>
<form action="tiki-admin.php#gal" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_gal_rankings" {if $feature_gal_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_image_galleries_comments" {if $feature_image_galleries_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store images{/tr}:</td><td><input type="radio" name="gal_use_db" value="y" {if $gal_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store images{/tr}:</td><td class="form"><input type="radio" name="gal_use_db" value="n" {if $gal_use_db eq 'n'}checked="checked"{/if}/> {tr}Directory path{/tr}:<input type="text" name="gal_use_dir" value="{$gal_use_dir}" /> </tr>
    <tr><td class="form">{tr}Library to use for processing images{/tr}:</td><td><input type="radio" name="gal_use_lib" value="gd" {if $gal_use_lib ne 'imagick'}checked="checked"{/if}/>GD</td></tr>
    <tr><td class="form"></td><td><input type="radio" name="gal_use_lib" value="imagick" {if $gal_use_lib eq 'imagick'}checked="checked"{/if}/>Imagick</td></tr>
    <tr><td class="form">{tr}Uploaded image names must match regex{/tr}:</td><td><input type="text" name="gal_match_regex" value="{$gal_match_regex}"/></td></tr>
    <tr><td class="form">{tr}Uploaded image names cannot match regex{/tr}:</td><td><input type="text" name="gal_nmatch_regex" value="{$gal_nmatch_regex}"/></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="galfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
<div class="simplebox">
<a class="link" href="tiki-admin.php#gal?rmvorphimg=1">{tr}Remove images in the system gallery not being used in Wiki pages, articles or blog posts{/tr}</a>
</div>

	<div class="simplebox">
	{tr}Gallery listing configuration{/tr}
	<form method="post" action="tiki-admin.php#gal">
	<table>
	<tr>
		<td class="form">{tr}Name{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_name" {if $gal_list_name eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Description{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_description" {if $gal_list_description eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Created{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_created" {if $gal_list_created eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Last modified{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_lastmodif" {if $gal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}User{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_user" {if $gal_list_user eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Images{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_imgs" {if $gal_list_imgs eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr>
		<td class="form">{tr}Visits{/tr}</td>
		<td class="form"><input type="checkbox" name="gal_list_visits" {if $gal_list_visits eq 'y'}checked="checked"{/if} /></td>
	</tr>
	<tr><td align="center" colspan="2"><input type="submit" name="imagegallistprefs" value="{tr}Set prefs{/tr}" /></td></tr>
	</table>	
	</form>	
	</div>

    <div class="simplebox">
    {tr}Image galleries comments settings{/tr}
    <form method="post" action="tiki-admin.php#gal">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="image_galleries_comments_per_page" value="{$image_galleries_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="image_galleries_comments_default_ordering">
    <option value="commentDate_desc" {if $image_galleries_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $image_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="imagegalcomprefs" value="{tr}Set prefs{/tr}" /></td></tr>
    </table>
    </form>
    </div>


</div>
</div>
