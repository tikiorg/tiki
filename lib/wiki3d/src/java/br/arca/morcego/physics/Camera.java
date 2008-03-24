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
package br.arca.morcego.physics;

import java.util.Enumeration;

import br.arca.morcego.Config;
import br.arca.morcego.structure.Graph;
import br.arca.morcego.structure.Node;

/**
 * @author lfagundes
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class Camera extends Vector3D {

	/**
	 * @param x
	 * @param y
	 * @param z
	 */
	public Camera(int x, int y, int z) {
		super(x, y, z);
	}

	/**
	 * 
	 */
	public Camera() {
		super(0, 0, Config.getInteger(Config.cameraDistance));				
	}
	
	public void adjustPosition(Graph graph) {
		if (Config.getBoolean(Config.adjustCameraPosition)) {
			synchronized (this) {
				int distance = (Config.getInteger(Config.cameraDistance));
				z = distance;
			
				Enumeration en = graph.getNodes().elements();
				while (en.hasMoreElements()) {
					Node node = (Node) en.nextElement();
					if (node.getBody().z + distance > z) {
						z = node.getBody().z + distance;
					}
				}
			}
		}
	}
	

}
