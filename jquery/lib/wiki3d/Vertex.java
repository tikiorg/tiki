/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Vertex.java,v 1.12 2006-10-22 03:21:41 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;

import javax.vecmath.Tuple3f;

public class Vertex extends Tuple3f {
	public float x, y, z;
	int u, v;
	public static float FOV;
	public static int cameraX, cameraY, cameraZ;	

	public static Vertex origin;
	
	public Vertex(int x, int y, int z) {
		this.x = x;
		this.z = z;
		this.y = y;
	}
	
	public Vertex() {
	}
	
	public static void setCamera(int x, int y, int z) {
		cameraX = x;
		cameraY = y;
		cameraZ = z;
	}

	public void setCamerapos(int x, int y, int z) {
		cameraX = x;
		cameraY = y;
		cameraZ = z;
	}

	public static void setFOV(float f) {
		FOV = f;
	}

}
