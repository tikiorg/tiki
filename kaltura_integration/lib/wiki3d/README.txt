Morcego 3D Network Browser - v0.6.0
http://morcego.arca.ime.usp.br
-----------------------------------

Morcego is a java applet for visualizing and browsing graphs (networks)
in 3D. You can use it to make 3D mind maps. It can be embedded in web
applications and it's goal is to become a visualization framework.

The job of integrating Morcego to your website is becoming easier each version
If this documentation is not enough, don't be afraid to send a message to
developers mailing list at morcego-dev@arca.ime.usp.br. We hope you have as much
fun using Morcego as we do by coding it :-).

Morcego is licensed under GNU LESSER GENERAL PUBLIC LICENSE (LGPL). 

This documentation is divided in three parts: first one is about using applet,
second on how to embbed Morcego in other applications and finally how to upgrade
to v0.6.0.

This document is divided in the following sections:

1 - Using the applet
2 - Embedding applet in application
2.1 - Making a server
2.1.1 - Making a static mindmap
2.1.2 - Integrating to your PHP application
2.1.3 - Developing your own XMLRPC server
2.1.4 - Creating a new transport layer
2.2 - Properties for nodes and links
2.3 - Javascript integration 
2.4 - Applet configuration
3 - Upgrading
3.1 - From v0.5 to v0.6
3.2 - From v0.4 to v0.5
3.3 - From v0.3 to v0.4

1 - USING THE APPLET

Once Morcego applet window opens, you'll see (after loading, that
may take some moments) an animation of little balls connected to each
other in a network getting arranged in space - a cool effect by the way ;-).

The possible actions:

  * ROTATE    - Drag mouse on empty space and you'll be able to see the graph
                from different angles. If you drag fast and relase, like throwing,
                the graph will keep spinning.

  * GET INFO  - Put the mouse cursor over a node, and it will become a hand. If
                there's more info about that node, a description box will appear.
               
  * NAVIGATE  - Click on a node and it will go to center. When you do that, the
                new neighbourhood will appear in a few moments (wait a bit for
                the info to come from server). Clicking on center node will
                open the node URL in browser window.
               
  * MOVE NODE - Click on a node and drag to move it. The graph will balance
                itself as you move. This is useful to see how a node influences
                the whole network.

2 - EMBEDDING APPLET IN APPLICATION

For integrating Morcego applet in your web application, you need an special Morcego server
to feed the applet with graph data. This is usually done with XMLRPC, the only transport
way available until now (check section 2.4 on how to implement other ways).

Implementing this used to be a tough task, but since v0.6 Morcego comes with a demo application
and a set of PHP classes for integration with other softwares. You have several options, depending
on your technical skills and time available. You can:

* Make a static mindmap website by editing some XML data files and templates
* Implement your own server in PHP to make a dynamic map, by extending a PHP class
* Develop a XMLRPC server that implements Morcego's protocol in any language you wish
* Make a new transport layer in java, by extending a java class and creating a server to feed it.

2.1 - MAKING A SERVER
 
2.1.1 - MAKING A STATIC MINDMAP

If you don't have much technical skills, but would like to make an online mind map, you can do one
by editing some XMl files and putting a simple application in a webserver with PHP:

* Copy demo/ to a directory in your web server, let's say /var/www/morcego_demo for example.
* Copy morcego-$VERSION.jar and php/Morcego to /var/www/morcego_demo
* Give permission to your web user to write to /var/www/morcego_demo/templates_c
* Open a browser and check the results
* Take a look at the XML files under templates/data directory. Edit them to customize the mindmap.
* Check also the .tpl files at templates/ dir. Modify those to customize the layout. For help on that, check http://smarty.php.net

2.1.2 - INTEGRATING TO YOUR PHP APPLICATION

Morcego also comes with a set of PHP classes to make your own server. You'll need two files: your server (let's call it server.php),
a php file available through HTTP, and a subclass of Morcego_Graph (let's call it MyGraph)

* server.php

Very simple, check demo/server.php on how to do that. All you need to do is create a Morcego_Graph object (note that Morcego_Graph is an
abstract class and must be subclassed) and use it to instantiate a Morcego_Server object.

* MyGraph

This is the core of your server. You must implement the method getNode($nodeId), that takes the nodeId and returns a data structure
containing all relevant data about node with id $nodeId and the links it has to/from other nodes.

The data structure has the exact same format of the getSubGraph() method (explained below at 2.3), but you usually want to return
only one node in "nodes" section. I say usually, because it might be useful to return a bigger set of nodes and links, for example
in raw XMl server used by the demo: you might want to defined many nodes in one XML files to make your job easier (note that in the
demo you can't start your navigation in some of the nodes that doesn't have it's own XMl file). 

2.1.3 - DEVELOPING YOUR OWN XMLRPC SERVER

The XMLRPC server should implement two methods:

 * integer getVersion()

This method returns the XMLRPC protocol version. Current version is 2, so if you're
implementing the server according to instructions below, this method should return "2".
In case this method is not present, version 1 is assumed, and so data structure for 
older versions of Morcego (0.4 and below) will be expected.

 * struct getSubGraph(string nodeId, int depth)

This method returns a part of the graph containing the node with id
nodeId and all nodes with distance lesser or equal than depth.
The returned struct must be as follow, {} indicates structs and []
arrays, fields marked with * are optional (see section below for complete
list of properties)

{
  nodes => {
                    nodeId => {
                                            * type => "Round",
                                            * color => "#FF0000",
                                            * actionUrl => "http://...",
                                            * description => "node description, shown in box",
                                },
                    node1Id => {},
                    node2Id => {...}
                  }
  links => [
                {
                    from => "nodeId",
                    to => "node2Id",
                  * type => "Solid"
                },
                
                {
                    from => "nodeId",
                    to => "node1Id",
 				  * type => "Dashed"
 				}                 
                
           ]
}

2.1.4 - CREATING A NEW TRANSPORT LAYER

You can do so by creating a new java class in package br.arca.morcego.transport package that implements Transport.
Your class must have the word "Transport" at end, because it will be appended to the parameter "transport" to
obtain the class name.

You have to implement two methods (check interface):

* setup: will be called once

* retrieveData: will be called once on start and everytime user navigates. The format of hash returned is the same described
                above in getSubGraph() from xmlrpc server.

2.2 - PROPERTIES FOR NODES AND LINKS

  Each NODE can have the following properties:
  
   * type: "Round", "Square", "Triangle", "Text" or "Image".
           Round nodes are represented by balls with a title above (that can be empty).
           Square and Triangle have similar behavior, but with other forms
           Text nodes have just the title.
           Image nodes are represented by image defined by "image" property, an url.
           
   * title: Name of the node, that will appear above Round, Square and Triangle nodes, or will
            be the text in Text nodes.

   * actionUrl: The URL that will be loaded by this node, in window defined by applet param
                "controlWindowName". 
   
   * description: Text that will show when mouse passes over the node

   * onClick: Javascript code to be executed when user clicks the node. This can substitute
     (NEW)    actionUrl with an ajax call, for example (but both can be used).
   
   * onMouseover and onMouseout: Javascript code to be executed when user passes mouse over and
     (NEW)                       get out the node. This can substitute "description" above (but
                                 can be used together). 
   
   * color: Overrides nodeDefaultColor and defines the color of node if it's Round, Square or Triangle.
   
   * charge: Overrides nodeCharge configuration and sets the eletrostatical charge of
             the node. Bigger values will put this node far from others.
                 
   * mass: Overrides nodeMass configuration and sets the mass of the node. Bigger
           values will give more inerce to the node and make it move slower.
           
     ** NOTE: in v0.4 "charge" and "mass" were called "bodyCharge" and "bodyMass". v0.5 README file
              lacked documentation about the renaming.
                 
   * level: If your graph is an hierarchical tree, then setting the level will force nodes of higher levels
     (NEW)  to be positioned in far from lower level nodes. Since this implies using conflicting forces to position
            nodes, you need very fine tunning of physical constants to avoid extreme graph instability (see windIntensity
            applet parameter).
              
  Each LINK can have the following ones:
  
   * type: "Solid", "Directional", "Bidirectional", "Dashed", "DashedDirectional" and "Invisible" 
           Solid links are solid lines from one node to another.
           Directional is solid with an arrow from one to other.
           Bidirectional has arrows in both ends.
           Dashed is a dashed line.
     (NEW) DashedDirectional is a dashed line with an arrow.
     (NEW) Invisible do not appear, just influences the balancing.
           
   * from (mandatory): The id of the node at one end.
   
   * to (mandatory): The id of the node at other end.
   
     ** NOTE: the order of "from" and "to" only matters for "Directional" and "DashedDirectional" type of link.
     
   * description: Text that will show when mouse passes over the link.
   
   * color: Overrides linkDefaultColor and defines color of link.
   
   * onClick, onMouseover and onMouseout: same as node's properties with same name, execute js code on these events.
     (NEW)
   
   * springSize: Overrides springSize configuration option for this link and sets its size.
                 Bigger values will put nodes far from each other.
                 
   * springElasticConstant: Overrides elasticConstant configuration option for this link.
                            Bigger values will lower variation between springSize and final size.
                            Smaller values will make animation smoother.
                            
2.3 - JAVASCRIPT INTEGRATION

Besides the JS calls from applet shown above, you can use javascript to call the following applet's methods:

* navigateTo(nodeId) - navigate to node with id nodeId, simulates user click on node.

* refresh() - reloads applet data from server

* changeParam(varName, value) - dinamically changes one of applet's param (APPARENTLY NOT WORKING YET,
                                ask the developers to give special attention to this if you need it ;-) )
                            
2.4 - APPLET CONFIGURATION

There are configuration variables on Config class, every var can be overrided
by an applet param with same name. The only ones that you must override
are serverUrl and startNode. All variables:

     - serverUrl (string): Full URL of XMLRPC server. MANDATORY
     - startNode (string): ID of the starting node. MANDATORY

  Colors and layout settings

     - showMorcegoLogo (boolean): default true
                                  Show software's logo on position below
     - logoX (integer): default 10
     - logoY (integer): default 10

     - showArcaLogo (boolean): default true
                               Show deverlopers' logo on position below, bottom right corner by default
     - arcaX (integer): default width - 127
     - arcaY (integer): default height - 40
     
     - originX (integer): default width/2
     - originY (integer): default height/2
       (NEW)              Origin's coordinates had to be configured manually in v0.3, then in v0.4 these
                          parameters were removed and since v0.6 they're again available for configuration.
                          It might be useful to change the center of the graph in case graphIsTree is set to true.  
       
     - backgroundColor (color): default #FFFFFF

     - linkDefaultType (string): default "Solid"
                                 Can also be any of link types defined above.
                                 Overrided by property "type" of each link.

     - linkDefaultColor (color): default #787878
     

     - nodeDefaultType (string): default "Round"
                                 Can also be any of node types defined above.
                                 Overrided by property "type" of each node.

     - nodeDefaultColor (color): default #FF0000
     
     - nodeDefaultImage (string or url): default "default.gif"
                                         This is used for nodes of type "Image".
                                         default.gif is packaged with applet, you should put an url here.
                                         Be careful, images take a lot of cpu, use very simple ones.

     - nodeBorderColor (color): default #000000

     - textSize (integer): default 25
                           Size of text in node
     - nodeSize (integer): default 15
     					   Size of the node
     - minNodeSize (integer): default 0
                              Minimum size of node, 0 will make far nodes disappear
     - centerNodeScale (float): default 2
                                Proportion of center node, to put it in more evidence than others.

     - descriptionColor (color): default #282828
								 Color of text in node's description box.

     - descriptionBackground (color): default #c8c8c8

     - descriptionBorder (color): default #000000

     - descriptionMargin (integer): default 4
                                    Margin around text in description box.

     - width (integer): default is to automatic get from browser
     - height (integer): default is to automatic get from browser
     
       (NEW - width and height substitute viewWidth, viewHeight,
              windowWidth, windowHeight, viewStartX and viewStartY)

  Camera configuration

     - cameraDistance (integer): default 500
                                 Distance from camera to center node.
     
     - cameraDepth (integer): default 250
       (NEW)                  Distance from camera screen to its focus
                              
     - minCameraDistance (integer): default 150
                                    Minimum distance from camera to nearest node.
                                    In case a node get too near, cameraDistance is increased.

     - fieldOfView (integer): default 250
                              The greater fieldOfView is, bigger is everything.


  Physical constants used to balance the graph. For best position, Morcego uses a physical
  simulation in which each node is an eletrical charge with mass and no dimension, each
  connection is a spring and whole system is in a viscose environment. Nodes are leveled
  by a simulated wind in case graphIsTree is set to true.

     - frictionConstant (float): default 0.4
     							 Small values increases the time for graph to get
                                 balanced, while very big values will make the whole
                                 process very slow. 

     - elasticConstant (float): default 0.3
                                This is the default elastic constant for links, smaller values will
                                make movements smoother but graph will take more time to balance.
                                Overrided by property with same name for each link.

     - punctualElasticConstant (float): default 0.8
                                        Elastic constant of spring that will move some node to center
                                        when it's clicked. The smaller this value, the smoother is the
                                        navigation.
                                        
     - eletrostaticConstant (float): default 1000
     								 Bigger values will put nodes far from each other.

     - springSize (float): default 100
                           Bigger values increase the distance of nodes

     - nodeMass (float): default 5
                         Bigger values increase the inerce of system

     - nodeCharge (float): default 1
                         Bigger values increase the strengh of repulsion of nodes

     - windIntensity (float): default 10
       (NEW)                  Intensity of wind that will level nodes in case graphIsTree is
                              true. Low values won't give the desired impression, high values
                              will cause extreme system instability. You'll need fine adjustment
                              of all physical constants to use this. 

  Rotation angle limits. These configure mouse sensitiveness for rotating the graph.

     - maxTheta (float): default 20.0f

     - minTheta (float): default 1.0f

   Performance X Quality options

     - renderingFrameInterval (integer): Delay between each frame of animation, in miliseconds.
                                         default 50.
     
     - balancingStepInterval (integer): Interval between each calculation of forces to
                                        balance the graph, in miliseconds. Increase will 
					raise performance and lower animation quality.
					default 50.

     - fontSizeInterval (integer): Looks like font rendering is cached, so that if a lower
                                   number of font sizes are rendered the performance is
								   better. Increasing this number will increase performance
								   but lower text depth impression. default 5.

   General configuration
   
     - graphIsTree (boolean): default false.
       (NEW)                  This determines if a wind should blow the nodes to in one direction
                              according to its "level" property. You'll need fine adjustment of
                              physical constants to get a good visual tree disposition.

     - transport (string): default "Xmlrpc"
     					   Kind of transport layer used, xmlrpc is the only one at moment.

     - feedAnimationInterval (integer): default 100
                                        Time between appearance of each node, in milisecs.

     - loadPageOnCenter (boolean): default false.
								   If set to true, target page will be loaded on browser
                                   when user navigates to node. Since v0.6 you'd probably
                                   prefer to use node's onClick property.

     - navigationDepth (integer): default 3
                                  The distance from farest node to center. The bigger it
                                  is, more nodes will be fetched around center.
     
     - controlWindowName (string): default "morcegoController"
     							   Name of window in which URLs should be loaded. 
                                   This only has any effect if the nodes have an actionUrl
								   set by server.

		
3 - UPGRADING

3.1 - Upgrading 0.5 -> 0.6

To upgrade from v0.5 to v0.6, put morcego-0.6.0.jar in place of morcego-0.5.0.jar and check below
the changes made in applet that may affect your application:

  - Configuration variables changed
  
    * viewWidth, viewHeight, windowWidth, windowHeight, viewStartX and viewStartY got obsolete.
      Now you should use just width and height, starting point is always at (0,0) and the default
      values are obtained from browser. Be aware that keeping the default might not work correctly
      in some MacOS versions.
      
  - New default values
  
    * nodeSize: 15 instead of 30
    * textSize: 25 instead of 40. NOTE: nodeSize and textSize default values were changed in v0.5,
                                        but the changed was not documented.
                                        
    * centerNodeScale: 1 instead of 2 (this scale was messing with depth perception)
    * fieldOfView: 250 instead of 200
    * elasticConstant: 0.3 instead of 0.5 (more smooth)
    * loadPageOnCenter: false instead of true (now it's better to use node's onClick property)
    
  - New configuration options
  
    * graphIsTree
    * windIntensity
    * cameraDepth
    * originX
    * originY
        

3.2 - Upgrading 0.4 -> 0.5

To upgrade from v0.4 to v0.5, put morcego-0.5.0.jar in place of morcego-0.4.jar and check below
the changes made in applet that may affect your application. You can remove xmlrpc jar file, it's
now packaged in morcego's jar. If you want to benefit from new features like link properties,
dashed and directional links, you have to implement in your server the new version of XMLRPC
transport protocol. For that, check "EMBEDDING APPLET IN APPLICATION" section above.

  - Configuration variables renamed

     * linkColor: renamed to linkDefaultColor
     * transportClass: renamed to transport.
                       The suffix "Transport" in this configuration is not used anymore.
     
  - New default values
       
     * windowWidth: 600 instead of 500
     * elasticConstant: 0.3 instead of 0.5
     * punctualElasticConstant: 0.8 instead of 1
     * feedAnimationInterval: 100 instead of 500
     
  - Removed options
  
     * adjustCameraPosition: this got obsolete with new minCameraDistance, that makes camera adjustment
                             more flexible. If you want behaviour of adjustCameraPosition, just set
                             minCameraDistance to same value as cameraDistance.
  
     * viewHeight, viewWidth: these are not really gone, but they now default to windowHeight and windowWidth, so
                              you don't have to configure them anymore. Right now it's useless to define a view
                              smaller than window.
                              
  - New configuration options
  
     * minCameraDistance: defines the minimum distance of the camera from any node. Use this instead of
                          adjustCameraPosition.
     * centerNodeScale: see above

3.3 - Upgrading 0.3 -> 0.4

To upgrade from v0.3 to v0.4.0 you basically have to:

  1- put morcego-0.4.0.jar in place of morcego-0.3.jar

  2- Check if you have configured any of these variables:

     * ballBorderColor: renamed to nodeBorderColor

     * minBallSize: renamed to minNodeSize

     * cameraX, cameraY, cameraZ: all three substituted by cameraDistance. Note the new
                                  variable adjustCameraPosition.

  3- Check if any of these new behaviour bothers you and change them as desired:

     * Having software logo and developer's logo in screen. You might have to configure
       arcaX and arcaY if your window is not default size.

     * Now camera distance is relative to plane of nearest node instead of fixed origin.
       Disable adjustCameraPosition to fix camera.

     * Now node's target page is loaded every time user navigates on graph, instead of
       having to click center node. Disable that by setting loadPageOnCenter to false.

  4- Remove any of the following configuration vars that are not used anymore:

     - originX, originY: now it's always the center of screen
     - originZ: no sense being different from zero.

     - universeRadius, maxNodeSpeed: the new physical model don't need limits to avoid
                                     weird behaviour on high values.

     - linkedNodesDistance: also obsoleted by physical model, check now springSize
