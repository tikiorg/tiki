<html>
  <body style="margin-top: 0px; margin-left: 0px">
    <applet codebase="./lib/wiki3d" archive="morcego-0.0.1.jar" code="br.arca.morcego.Morcego" width="{$wiki_3d_width}" height="{$wiki_3d_height}">
      <param name="serverUrl" value="{$base_url}/tiki-wiki3d_xmlrpc.php">
      <param name="startNode" value="{$page}">
      <param name="windowWidth" value="{$wiki_3d_width}">
      <param name="windowHeight" value="{$wiki_3d_height}">
      <param name="viewWidth" value="{$wiki_3d_width}">
      <param name="viewHeight" value="{$wiki_3d_height}">
      <param name="navigationDepth" value="{$wiki_3d_navigation_depth}">

    </applet>
      
  </body>
</html>
