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
import java.util.Enumeration;
import java.util.Hashtable;

/**
 * @author lfagundes
 * 
 * Configuration of everything
 */
class Config {

	private static Hashtable config = new Hashtable();

	/*
	 * Sets all configuration, edit here to modify config
	 */
	public static void init() {
		setValue("backgroundColor", new Color(255, 255, 255));
		setValue("linkColor", new Color(120, 120, 120));
		setValue("nodeDefaultColor", new Color(255, 0, 0));
		setValue("ballBorderColor", new Color(0, 0, 0));

		setValue("nodeSize", new Integer(30));
		setValue("textSize", new Integer(40));

		// Position of center node
		setValue("originX", new Integer(200));
		setValue("originY", new Integer(200));
		setValue("originZ", new Integer(0));

		// Position of the camera
		setValue("cameraX", new Integer(400));
		setValue("cameraY", new Integer(400));
		setValue("cameraZ", new Integer(700));

		setValue("fieldOfView", new Integer(200));

		// Window definition
		setValue("windowWidth", new Integer(500));
		setValue("windowHeight", new Integer(500));

		// View area definition
		setValue("viewStartX", new Integer(0));
		setValue("viewStartY", new Integer(0));
		setValue("viewHeight", new Integer(500));
		setValue("viewWidth", new Integer(500));

		setValue("universeRadius", new Integer(500));

		// rotation angle limits
		setValue("maxTheta", new Float(20.0f));
		setValue("minTheta", new Float(1.0f));

		setValue("minBallSize", new Integer(0));
		setValue("maxNodeSpeed", new Integer(50));
		setValue("navigationDepth", new Integer(2));
		setValue("feedAnimationInterval", new Integer(500));
		setValue("balancingStepInterval", new Integer(50));		
		setValue("fontSizeInterval", new Integer(5));
		setValue("linkedNodesDistance", new Integer(100));
		
		// Name of window in which URLs should be loaded
		setValue("controlWindowName", "morcegoController");

		// This should be set by application, from applet parameters
		// They're set here for developing purposes
		setValue(
			"serverUrl", 
			"http://c3po.kriconet.com.br/tiki-dev/tikiwiki/tiki-wiki3d_xmlrpc.php");
		
		setValue("startNode", "HomePage");

	}

	public static void setValue(String var, Object value) {
		config.put(var, value);
	}

	public static Object getValue(String var) {
		return config.get(var);
	}

	public static Enumeration listConfigVars() {
		return config.keys();
	}

}