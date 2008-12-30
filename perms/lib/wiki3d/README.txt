Morcego 3D Network Browser - v0.4.0
-----------------------------------

Morcego is a java applet for visualizing and browsing graphs (networks)
in 3D. It's goal is to be easily embedded in web (by now) applications and,
for future releases, to become a framework. This documentation is divided
in two parts: first one is about using applet, second on how to embbed
Morcego in other applications and how to upgrade to v0.4.0. 


1 - USING THE APPLET

1.1 - Note for integrators

Having usage instructions only here in the README doesn't help much final users,
at least while Morcego is only available as applet and this documentation
is not accessible by them. So, if you plan to put Morcego in your site,
please paste the instructions below somewhere. Future releases will have a
help system.

1.2 - Instructions

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


EMBEDDING APPLET IN APPLICATION

For integrating Morcego applet in your web application, a server must be implemented
to feed the applet with graph data. Although v0.4.0 has a modular transport layer to
communicate with server, currently only XmlRpc is supported. If you don't know XMLRPC
check http://www.xmlrpc.com. If you don't like using XmlRpc you're not alone in this
world, your help in implementing new transport layer is welcome.

The XMLRPC server must implement the method:

struct getSubGraph(string nodeName, int depth)

This method returns a part of the graph containing the node named
nodeName and all nodes with distance lesser or equal than depth.
The returned struct must be as follow, {} indicates structs and []
arrays, fields marked with * are optional

{
  graph => {
                    nodeId => {
                                            * neighbours => [ nodeId1, nodeId2, ... ],
                                            * color => "#FF0000",
                                            * actionUrl => "http://...",
                                            * description => "node description, shown in box",
                                           }
                    node1Id => {...}
                    node2Id => {...}
                  }
}

CONFIGURATION

Together with morcego-VERSION.jar, you must provide a copy of xmlrpc-1.2-b1.jar,
which can be downloaded from http://www.apache.org/dyn/closer.cgi/ws/xmlrpc/.

There are configuration variables on Config class, every var can be overrided
by an applet param with same name. The only ones that you must override
are serverUrl and startNode. All variables:

     - serverUrl (string): Full URL of XMLRPC server. MANDATORY
     - startNode (string): ID of the starting node. MANDATORY

  Colors and layout settings

     - showMorcegoLogo (boolean): show software's logo on position below, default true
     - logoX (integer): default 10
     - logoY (integer): default 10

     - showArcaLogo (boolean): show deverlopers' logo on position below, default true
     - arcaX (integer): default 380
     - arcaY (integer): default 460

     - backgroundColor (color): default #FFFFFF

     - linkColor (color): default #787878

     - nodeDefaultColor (color): default #FF0000

     - nodeBorderColor (color): default #000000

     - nodeSize (integer): default 30

     - textSize (integer): default 40

     - minNodeSize (integer): default 0

     - windowWidth (integer): default 500

     - windowHeight (integer): default 500

     - viewStartX (integer): default 0

     - viewStartY (integer): default 0

     - viewHeight (integer): default 500

     - viewWidth (integer): default 500

     - descriptionColor (color): color of text in node's description box, 
                                 default #282828

     - descriptionBackground (color): default #c8c8c8

     - descriptionBorder (color): default #000000

     - descriptionMargin (integer): margin around text in description box,
                                    default 4


  Camera configuration

     - adjustCameraPosition (boolean): if set to true, the distance from camera (below)
                                       will refer to nearest node, instead of center node.
                                       default true.

     - cameraDistance (integer): distance from camera to center node or nearest node, depending
                                 on adjustCameraPosition. default 200.

     - fieldOfView (integer): The greater fieldOfView is, bigger is everything. default 200


  Physical constants used to balance the graph. For best position, Morcego uses a physical
  simulation in which each node is an eletrical charge with mass and no dimension, each
  connection is a spring and whole system is in a viscose environment.

     - frictionConstant (float): small values increases the time for graph to get
                                 balanced, while very big values will make the whole
                                 process very slow. default 0.4

     - elasticConstant (float): default 0.5

     - punctualElasticConstant (float): default 1

     - eletrostaticConstant (float): default 1000

     - springSize (float): default 100

     - nodeMass (float): default 5

     - nodeCharge (float): default 1


  Rotation angle limits

     - maxTheta (float): default 20.0f

     - minTheta (float): default 1.0f
     

   General configuration

     - transportClass (string): Kind of transport layer used, defaults to "XmlrpcTransport",
                                only option available at moment.

     - loadPageOnCenter (boolean): If set to true, target page will be loaded on browser
                                   when user navigates to node. default true.


     - navigationDepth (integer): The distance from farest node to center. The bigger it
                                  is, more nodes will be fetched around center. default 2.
     
     - feedAnimationInterval (integer): Time between appearance of each node, in milisecs.
                                        default 500.
		
     - controlWindowName (string): Name of window in which URLs should be loaded. 
                                   This only has any effect if the nodes have an actionUrl
				   set by server. default "morcegoController".

     - balancingStepInterval (integer): Interval between each calculation of forces to
                                        balance the graph, in miliseconds. Increase will 
					raise performance and lower animation quality.
					default 50.

     - fontSizeInterval (integer): Looks like font rendering is cached, so that if a lower
                                   number of font sizes are rendered the performance is
				   better. Increasing this number will increase performance
				   but lower text depth impression. default 5.

    - renderingFrameInterval (integer): Delay between each frame of animation, in miliseconds.
                                        default 50.



UPGRADING

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

     - originX, originY: now it's always the center os screen
     - originZ: no sense being different from zero.

     - universeRadius, maxNodeSpeed: the new physical model don't need limits to avoid
                                     weird behaviour on high values.

     - linkedNodesDistance: also obsoleted by physical model, check now springSize
