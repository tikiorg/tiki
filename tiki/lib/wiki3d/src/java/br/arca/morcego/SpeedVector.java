/*
 * Morcego - 3D network browser
 * Copyright (C) 2004 Luis Fagundes - Arca <lfagundes@arca.ime.usp.br> 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
package br.arca.morcego;

/**
 * @author lfagundes
 * 
 * Vector representin node speed on 3D space
 */
public class SpeedVector {
	public int x, y, z;
	private float fx, fy, fz;

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
	
	/*
	 * Verifies if another vector's projection over this has opposed
	 * orientation
	 */
	public boolean opposed(SpeedVector s) {
		return x*s.x + y*s.y + z*s.z < 0;
	}

	/**
	 * @return
	 */
	public boolean isTooLow() {
		int distance = ((Integer)Config.getValue("linkedNodesDistance")).intValue();
		return module() < Math.pow(distance,0.33f);
	}
}
