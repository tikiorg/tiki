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

import java.awt.Point;
import br.arca.morcego.Config;
import br.arca.morcego.Morcego;

/**
 * @author lfagundes
 * 
 * Vertex is any point in 3D space
 */

public class Vector3D implements PositionedObject {
	// Coordinates in 3D space
	public float x, y, z;

	// Projected coordinates in 2D
	public Point projection = new Point(0, 0);

	// scale of size according to depth
	private float scale;

	// Field of view
	public static float FOV = Config.getInteger(Config.fieldOfView);
	
	private float maxModule = 0;

	public Vector3D(float x, float y, float z) {
		this.x = x;
		this.z = z;
		this.y = y;
	}

	public void moveBy(Vector3D v) {
		moveBy(v.x, v.y, v.z);
	}

	public void moveBy(float dx, float dy, float dz) {

		x = x + (dx);
		y = y + (dy);
		z = z + (dz);

		//setBounds();
		proj();
	}

	public void moveTo(Vector3D v) {
		moveTo(v.x, v.y, v.z);
	}

	public void moveTo(float x, float y, float z) {
		this.x = x;
		this.y = y;
		this.z = z;
		//setBounds();
		proj();
	}

	public Vector3D() {
	}

	public void proj() {
		Camera camera = Morcego.getCamera();
		
		//scale = FOV / Morcego.getCamera().getDistanceTo(this);

		scale = camera.getDistanceTo(new Vector3D(0,0,z));
		if (scale > 0) {
			scale = FOV / scale;

			int depth = (int) (camera.getDistanceTo(this));

			if (Math.abs(depth) < 1) 
				depth = 1;
	
			float u = Morcego.getOrigin().x + FOV * x / depth;
			float v = Morcego.getOrigin().y + FOV * y / depth;
	
			projection.x = new Float(u).intValue();
			projection.y = new Float(v).intValue();

		} else {
			scale = 0;
		}
	}

	public Vector3D unproj(float dx, float dy) {
		int depth = (int) (Morcego.getCamera().getDistanceTo(this));

		float xi = (dx - Morcego.getOrigin().x) * depth / FOV;
		float yi = (dy - Morcego.getOrigin().y) * depth / FOV;
		float zi = z;

		return new Vector3D(xi, yi, zi);

	}

	/**
	 * @return Returns the scale.
	 */
	public float getScale() {
		return scale;
	}

	public float getX() {
		return x;
	}

	public float getY() {
		return y;
	}

	public float getZ() {
		return z;
	}

	public void rotate(float xTheta, float yTheta) {

		Matrix3x3 matrix = Matrix3x3.getXRotation(xTheta).multiplyByMatrix(
				Matrix3x3.getYRotation(yTheta));

		Vector3D v = matrix.multiplyByVector(this);

		x = v.x;
		y = v.y;
		z = v.z;

		proj();
	}

	public float getDistanceTo(Vector3D one) {
		Vector3D diff = new Vector3D(x - one.getX(), y - one.getY(), z
				- one.getZ());
		return diff.module();
	}

	public float getDistanceFromOrigin() {
		return getDistanceTo(new Vector3D(0, 0, 0));
	}

	public Vector3D getVectorFrom(Vector3D v) {
		return new Vector3D(x - v.x, y - v.y, z - v.z);
	}

	public void add(Vector3D s) {
		this.x += s.x;
		this.y += s.y;
		this.z += s.z;
		//this.makeInteger();
	}

	public void clear() {
		//this.ix = this.iy = this.iz = 0;
		this.x = this.y = this.z = 0;
	}

	public Vector3D multiplyByScalar(float module) {
		return new Vector3D(x * module, y * module, z * module);
	}

	public void resize(float module) {
		Vector3D v = multiplyByScalar(module);
		x = v.x;
		y = v.y;
		z = v.z;
	}

	public Vector3D opposite() {
		return new Vector3D(-x, -y, -z);
	}

	public float module() {
		return (float) Math.sqrt(x * x + y * y + z * z);
	}

	public Vector3D fromVertex(Vector3D v) {
		return new Vector3D(x + v.x, y + v.y, z + v.z);
	}

	public float scalarProduct(Vector3D s) {
		return x * s.x + y * s.y + z * s.z;
	}

	/**
	 * @return
	 */
	public Vector3D getUnit() {
		Vector3D unit = new Vector3D(x, y, z);
		return unit.multiplyByScalar(1/unit.module());
	}
}