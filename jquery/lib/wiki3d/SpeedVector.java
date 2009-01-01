/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/SpeedVector.java,v 1.7 2006-10-22 03:21:40 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

/*
 * Created on Apr 11, 2004
 * 
 * To change the template for this generated file go to Window - Preferences -
 * Java - Code Generation - Code and Comments
 */
package wiki3d;

/**
 * @author lfagundes
 * 
 * To change the template for this generated type comment go to Window -
 * Preferences - Java - Code Generation - Code and Comments
 */
public class SpeedVector {
	public int x, y, z;
	public float fx, fy, fz;

	public SpeedVector(int xi, int yi, int zi) {
		this.x = xi;
		this.y = yi;
		this.z = zi;
		
		this.fx = (float) this.x;
		this.fy = (float) this.y;
		this.fz = (float) this.z;
		
	}
	
	public SpeedVector(float xi, float yi, float zi) {
		fx=xi;
		fy=yi;
		fz=zi;
		makeInteger();
	}

	public SpeedVector(double xi, double yi, double zi) {
		fx=(float)xi;
		fy=(float)yi;
		fz=(float)zi;
		makeInteger();
	}

	public void add(SpeedVector s) {
		this.fx += s.fx;
		this.fy += s.fy;
		this.fz += s.fz;
		this.makeInteger();
	}

	public void clear() {
		this.x = this.y = this.z = 0;
		this.fx = this.fy = this.fz = 0;
	}

	public void resize(float module) {
		fx = fx * module;
		fy = fy * module;
		fz = fz * module;
		makeInteger();
	}
	
	private void makeInteger() {
		this.x = (int)Math.ceil(this.fx);
		this.y = (int)Math.ceil(this.fy);
		this.z = (int)Math.ceil(this.fz);
	}

	public SpeedVector reverse() {
		return new SpeedVector(-fx, -fy, -fz);
	}

	public float module() {
		return (float) Math.sqrt(fx*fx  + fy*fy + fz*fz);
	}

	public void print(String str) {
//		System.out.println(str + ' ' + x + ',' + y + ',' + z);
	}
}
