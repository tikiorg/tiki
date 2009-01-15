/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Face.java,v 1.8 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;
import java.awt.*;
public class Face {
	Vertex v1;
	Color c1 = Config.colorface1;
	Color c2 = Config.colorface2;
	Vertex v2;
	Vertex v3;
	Vertex v4;
	public Face() {
		int i = Config.facesize;
		v1 = new Vertex(-i, 0, i);
		v2 = new Vertex(i, 0, i);
		v3 = new Vertex(i, 0, -i);
		v4 = new Vertex(-i, 0, -i);

	}

	public void transform(Matrix3D amat) {
		Vertex.mat.unit();
		Vertex.mat.mult(amat);
		Vertex.mat.translate(
			Vertex.origin.x,
			Vertex.origin.y,
			Vertex.origin.z);
		
		v1.transform();
		v1.proj();

		v2.transform();
		v2.proj();

		v3.transform();
		v3.proj();

		v4.transform();
		v4.proj();
	}
	public void paint(Graphics g) {
		int a1 = v1.X - Vertex.origin.x;
		int b1 = v1.Y - Vertex.origin.y;
		int a2 = v2.X - Vertex.origin.x;
		int b2 = v2.Y - Vertex.origin.y;
		int z = a1 * b2 - a2 * b1;

		Polygon p = new Polygon();
		p.addPoint(v1.u, v1.v);
		p.addPoint(v2.u, v2.v);
		p.addPoint(v3.u, v3.v);
		p.addPoint(v4.u, v4.v);
		if (z > 0)
			g.setColor(c1);
		else
			g.setColor(c2);
		g.fillPolygon(p);

	}

}
