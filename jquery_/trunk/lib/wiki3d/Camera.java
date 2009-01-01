/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Camera.java,v 1.7 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;

//Camera class determines from where the view is taken.
//currently only used to determine the projection by using the zc variable ie.
// the distance from the screen to eye
//
public class  Camera extends Matrix3D {
	static int XC, YC, ZC;
	public Camera(int x, int y, int z) {
		XC = x;
		YC = y;
		ZC = z;
	}
}
