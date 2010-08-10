<html>

  <body style="margin-top: 0px; margin-left: 0px">
    <applet codebase="./lib/wiki3d" archive="morcego-0.6.0.jar" code="br.arca.morcego.Morcego" width="{$prefs.wiki_3d_width}" height="{$prefs.wiki_3d_height}">
      <param name="serverUrl" value="{$base_url}tiki-wiki3d_xmlrpc.php">
      <param name="startNode" value="{$page}">
      <param name="windowWidth" value="{$prefs.wiki_3d_width}">
      <param name="windowHeight" value="{$prefs.wiki_3d_height}">
      <param name="viewWidth" value="{$prefs.wiki_3d_width}">
      <param name="viewHeight" value="{$prefs.wiki_3d_height}">
      <param name="navigationDepth" value="{$prefs.wiki_3d_navigation_depth}">
      <param name="feedAnimationInterval" value="{$prefs.wiki_3d_feed_animation_interval}">
      <param name="controlWindowName" value="tiki">
      
      <param name="showArcaLogo" value="false">
      <param name="showMorcegoLogo" value="false">

      <param name="loadPageOnCenter" value="{$prefs.wiki_3d_autoload|default:"true"}">
      
      <param name="cameraDistance" value="{$prefs.wiki_3d_camera_distance|default:"200"}">
      <param name="adjustCameraPosition" value="{$prefs.wiki_3d_adjust_camera|default:"true"}">

      <param name="fieldOfView" value="{$prefs.wiki_3d_fov|default:"250"}">
      <param name="nodeSize" value="{$prefs.wiki_3d_node_size|default:"30"}">
      <param name="textSize" value="{$prefs.wiki_3d_text_size|default:"40"}">

      <param name="frictionConstant" value="{$prefs.wiki_3d_friction_constant|default:"0.4f"}">
      <param name="elasticConstant" value="{$prefs.wiki_3d_elastic_constant|default:"0.5f"}">
      <param name="eletrostaticConstant" value="{$prefs.wiki_3d_eletrostatic_constant|default:"1000f"}">
      <param name="springSize" value="{$prefs.wiki_3d_spring_size|default:"100"}">
      <param name="nodeMass" value="{$prefs.wiki_3d_node_mass|default:"5"}">
      <param name="nodeCharge" value="{$wiki_3d_node_charde|default:"1"}">

    </applet>

  </body>
</html>
