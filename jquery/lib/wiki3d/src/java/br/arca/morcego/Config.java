/*
 * Morcego - 3D network browser Copyright (C) 2004 Luis Fagundes - Arca
 * <lfagundes@arca.ime.usp.br>
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

package br.arca.morcego;

import java.awt.Color;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Enumeration;
import java.util.Hashtable;

import br.arca.morcego.structure.Node;


/**
 * @author lfagundes
 * 
 * Configuration of everything
 */
public class Config {

	private static Hashtable config = new Hashtable();

	public static final String backgroundColor = "morcego.backgroundColor";
	public static final String linkColor = "morcego.linkColor";
	public static final String nodeDefaultColor = "morcego.nodeDefaultColor";

	public static final String nodeBorderColor = "morcego.nodeBorderColor";

 	public static final String descriptionColor = "morcego.descriptionColor";
	public static final String descriptionBackground = "morcego.descriptionBackground";

	public static final String descriptionBorder = "morcego.descriptionBorder";
	public static final String descriptionMargin = "morcego.descriptionMargin";

	public static final String nodeSize = "morcego.nodeSize";
	public static final String textSize = "morcego.textSize";

	public static final String cameraDistance = "morcego.cameraDistance";
	public static final String fieldOfView = "morcego.fieldOfView";
	public static String adjustCameraPosition = "morcego.adjustCameraPosition";

	public static final String windowWidth = "morcego.windowWidth";
	public static final String windowHeight = "morcego.windowHeight";

	public static final String viewStartX = "morcego.viewStartX";
	public static final String viewStartY = "morcego.viewStartY";
	public static final String viewHeight = "morcego.viewHeight";

	public static final String viewWidth = "morcego.viewWidth";

	public static final String maxTheta = "morcego.maxTheta";
	public static final String minTheta = "morcego.minTheta";
	//TODO rename to minNodeSize
	public static final String minNodeSize = "morcego.minNodeSize";
	public static final String navigationDepth = "morcego.navigationDepth";
	public static final String feedAnimationInterval = "morcego.feedAnimationInterval";
	public static final String balancingStepInterval = "morcego.balancingStepInterval";
	public static final String fontSizeInterval = "morcego.fontSizeInterval";

	public static final String controlWindowName = "morcego.controlWindowName";

	public static final String renderingFrameInterval = "morcego.renderingFrameInterval";

	public static final String serverUrl = "morcego.serverUrl";

	public static final String transportClass = "morcego.transportClass";

	public static final String startNode = "morcego.startNode";

	public static String _imageLocation = "morcego._imageLocation";
	public static final String _implementsHierarchy = "morcego._implementsHierarchy";

	public static String showMorcegoLogo = "morcego.showMorcegoLogo";
	public static String logoX = "morcego.logoX";
	public static String logoY = "morcego.logoY";

	public static String showArcaLogo = "morcego.showArcaLogo";
	public static String arcaX = "morcego.arcaX";
	public static String arcaY = "morcego.arcaY";

	public static String frictionConstant = "morcego.frictionConstant";
	public static String elasticConstant = "morcego.elasticConstant";
	public static String eletrostaticConstant = "morcego.eletrostaticConstant";
	public static String nodeMass = "morcego.nodeMass";
	public static String nodeCharge = "morcego.nodeCharge";
	public static String springSize = "morcego.springSize";
	public static String punctualElasticConstant = "morcego.punctualElasticConstant";

	public static String loadPageOnCenter = "morcego.loadPageOnCenter";
	
	/*
	 * Sets all configuration, edit here to modify config
	 */
	public static void init() {

		setValue(backgroundColor, new Color(255, 255, 255));

		setValue(linkColor, new Color(120, 120, 120));
		setValue(nodeDefaultColor, new Color(255, 0, 0));
		
		setValue(_imageLocation, new String("br/arca/morcego/image/"));
		
		setValue(nodeBorderColor, new Color(0,0,0));
		setValue(descriptionColor, new Color(40,40,40));
		setValue(descriptionBackground, new Color(200,200,200));
		setValue(descriptionBorder, new Color(0,0,0));
		setValue(descriptionMargin, new Integer(4));
		
		setValue(showMorcegoLogo, new Boolean("true"));
		setValue(showArcaLogo, new Boolean("true"));
		setValue(logoX, new Integer(10));
		setValue(logoY, new Integer(10));
		setValue(arcaX, new Integer(380));
		setValue(arcaY, new Integer(460));
		
		// Window definition
		setValue(windowWidth, new Integer(500));
		setValue(windowHeight, new Integer(500));

		// View area definition
		setValue(viewStartX, new Integer(0));
		setValue(viewStartY, new Integer(0));
		setValue(viewHeight, new Integer(500));
		setValue(viewWidth, new Integer(500));

		// rotation angle limits
		setValue(maxTheta, new Float(20.0f));
		setValue(minTheta, new Float(1.0f));

		setValue(minNodeSize, new Integer(0));
		setValue(navigationDepth, new Integer(3));
		setValue(feedAnimationInterval, new Integer(100));
		setValue(balancingStepInterval, new Integer(50));
		setValue(fontSizeInterval, new Integer(5));

		// Name of window in which URLs should be loaded
		setValue(controlWindowName, "morcegoController");

		// Delay between each frame of animation
		setValue(renderingFrameInterval, new Integer(50));

		setValue(transportClass,"XmlrpcTransport");

		// Position of the camera
		setValue(cameraDistance, new Integer(200));
		setValue(adjustCameraPosition, new Boolean(true));

		setValue(fieldOfView, new Integer(250));
		setValue(nodeSize, new Integer(30));
		setValue(textSize, new Integer(40));

		setValue(frictionConstant, new Float(0.4f));
		setValue(elasticConstant, new Float(0.5f));
		setValue(punctualElasticConstant, new Float(1f));
		setValue(eletrostaticConstant, new Float(1000f));
		setValue(springSize, new Float(100));
		setValue(nodeMass, new Float(5));
		setValue(nodeCharge, new Float(1));

		setValue(loadPageOnCenter, new Boolean(true));

		// Private configuration vars, set by application
		setValue(_implementsHierarchy, new Boolean(false));

		// This should be set by application, from applet parameters
		// They're set here for developing purposes
		//setValue(serverUrl, "http://localhost/estudiolivre/tiki-wiki3d_xmlrpc.php");
		setValue(serverUrl,	"http://localhost/estudiolivre/tiki-wiki3d_xmlrpc.php");
		//setValue(startNode, new Node("CulturaDigital"));
		setValue(startNode, new Node("CulturaDigital"));
	}

	public static void setValue(String var, Object value) {
		config.put(var, value);
	}

	public static Object getValue(String var) {
		return config.get(var);
	}
	
	public static int getInteger(String var) {
		return ((Integer)getValue(var)).intValue();
	}
	
	public static Color getColor(String var) {
		return (Color)getValue(var);
	}
	
	public static String getString(String var) {
		return (String)getValue(var);
	}
	
	public static boolean getBoolean(String var) {
		return ((Boolean)getValue(var)).booleanValue();
	}
	

	public static Class getClass(String var) {
		Class transportClass = null;
		try {
			String transportClassName = Config.getString(var);
			transportClassName = (new String("br.arca.morcego.transport."))
			.concat(transportClassName);
			transportClass = Class.forName(transportClassName);
		} catch (Exception e) {
			e.printStackTrace();
		}
		return transportClass;
	}

	
	public static float getFloat(String var) {
		return ((Float)getValue(var)).floatValue();
	}
	
	public static Node getNode(String var) {
		return (Node)getValue(var);
	}

	public static Enumeration listConfigVars() {
		return config.keys();
	}

	public static Object decode(String value, Class type) {

		if (type.equals(Integer.class)) {
			return Integer.valueOf(value);
		} else if (type.equals(Node.class)) {
			return new Node(value);
		} else if (type.equals(Float.class)) {
			return Float.valueOf(value);
		} else if (type.equals(String.class)) {
			return value;
		} else if (type.equals(Boolean.class)) {
			return new Boolean(value);
		} else if (type.equals(Color.class)) {
			return Color.decode(value);
		} else if (type.equals(URL.class)) {
			try {
				return new URL(value);
			} catch (MalformedURLException e) {
				// ignore
			}
		}
		return value;
	}
}
