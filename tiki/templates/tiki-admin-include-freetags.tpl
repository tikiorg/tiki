{if $feature_help eq "y"}
<div class="rbox" style="margin-top: 10px;">
<div class="rbox-title" style="background-color: #eeee99; font-weight : bold; display : inline; padding : 0 10px;">{tr}Tip{/tr}</div>  
<div class="rbox-data" style="padding: 2px 10px; background-color: #eeee99;">{tr}Freetags rock!{/tr}</div>
</div>
<br />
{/if}

  <div class="cbox">
    <div class="cbox-title">
    {tr}Freetag Browsing{/tr}
    </div>
    <div class="cbox-data">
    <form action="tiki-admin.php?page=freetags" method="post">
    <table class="admin">
      <tr>
        <td colspan="2" class="heading">General</td>
      </tr>
      <tr>
        <td class="form">{tr}Show Tag Cloud{/tr}: </td>
	<td><input type="checkbox" name="freetags_browse_show_cloud" value="y" {if $freetags_browse_show_cloud eq 'y'}checked{/if} /></td>
      </tr>
      <tr>
        <td class="form">{tr}Number of Tags to show in Cloud{/tr}: </td>
	<td><input type="text" name="freetags_browse_amount_tags_in_cloud" value="{$freetags_browse_amount_tags_in_cloud|escape}" size="3" /></td>
      </tr>
</table>
</div>
</div>

{if $feature_morcego eq "y"}
  <div class="cbox">
    <div class="cbox-title">
    {tr}Freetag 3D Browser Configuration{/tr}
    </div>
    <div class="cbox-data">
    <table class="admin">
      <tr>
        <td colspan="2" class="heading">General</td>
      </tr>
      <tr>
        <td class="form">{tr}Enable freetags 3D browser{/tr}:</td>
        <td><input type="checkbox" name="freetags_feature_3d" {if $freetags_feature_3d eq 'y'}checked="checked"{/if}/></td>
      </tr>
{*      <tr>
        <td class="form">{tr}Load page on navigation{/tr}: </td>
	<td><input type="checkbox" name="freetags_3d_autoload" value="true" {if $freetags_3d_missing_page_color eq 'true'}checked{/if} /></td>
      </tr> *}
      <tr>
        <td class="form">{tr}Browser width{/tr}: </td>
	<td><input type="text" name="freetags_3d_width" value="{$freetags_3d_width|escape}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Browser height{/tr}: </td>
	<td><input type="text" name="freetags_3d_height" value="{$freetags_3d_height|escape}" size="3" /></td>
      </tr>
      <tr>
        <td colspan="2" class="heading">Graph appearance</td>
      </tr>
      <tr>
        <td class="form">{tr}Navigation depth{/tr}: </td>
	<td><input type="text" name="freetags_3d_navigation_depth" value="{$freetags_3d_navigation_depth|escape}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node size{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_size" value="{$freetags_3d_node_size|default:"30"}" size="2" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Text size{/tr}: </td>
	<td><input type="text" name="freetags_3d_text_size" value="{$freetags_3d_text_size|default:"40"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Spring (connection) size{/tr}: </td>
	<td><input type="text" name="freetags_3d_spring_size" value="{$freetags_3d_spring_size|default:"100"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node color{/tr}: </td>
	<td><input type="text" name="freetags_3d_existing_page_color" value="{$freetags_3d_existing_page_color|escape}" size="7" /></td>
      </tr>
      <tr>
{*        <td class="form">{tr}Missing page node color{/tr}: </td>
	<td><input type="text" name="freetags_3d_missing_page_color" value="{$freetags_3d_missing_page_color|escape}" size="7" /></td>
      </tr> *}
      <tr>
        <td colspan="2" class="heading">Camera settings</td>
     </tr>
      <tr>
        <td class="form">{tr}Camera distance adjusted relative to nearest node{/tr}: </td>
	<td><input type="checkbox" name="freetags_3d_adjust_camera" {if $freetags_3d_adjust_camera eq 'true'}checked="checked"{/if} /></td>
      </tr>
      <tr>
        <td class="form">{tr}Camera distance{/tr}: </td>
	<td><input type="text" name="freetags_3d_camera_distance" value="{$freetags_3d_camera_distance|default:"200"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Field of view{/tr}: </td>
	<td><input type="text" name="freetags_3d_fov" value="{$freetags_3d_fov|default:"250"}" size="3" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Feed animation interval (milisecs){/tr}: </td>
	<td><input type="text" name="freetags_3d_feed_animation_interval" value="{$freetags_3d_feed_animation_interval|escape}" size="4" /></td>
      </tr>
      {* new fields *}
      <tr>
        <td colspan="2" class="heading">Physics engine</td>
     </tr>
      <tr>
        <td class="form">{tr}Friction constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_friction_constant" value="{$freetags_3d_friction_constant|default:"0.4f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Elastic constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_elastic_constant" value="{$freetags_3d_elastic_constant|default:"0.5f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Eletrostatic constant{/tr}: </td>
	<td><input type="text" name="freetags_3d_eletrostatic_constant" value="{$freetags_3d_eletrostatic_constant|default:"1000f"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node mass{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_mass" value="{$freetags_3d_node_mass|default:"5"}" size="7" /></td>
      </tr>
      <tr>
        <td class="form">{tr}Node charge{/tr}: </td>
	<td><input type="text" name="freetags_3d_node_charge" value="{$freetags_3d_node_charge|default:"1"}" size="7" /></td>
      </tr>

      <tr>
        <td colspan="2" class="button"><input type="submit" name="freetagsset3d" value="{tr}Change configuration{/tr}" /></td>
      </tr>    
    </table>
    </form>
    </div>
  </div>
{/if}
