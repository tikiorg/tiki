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

import br.arca.morcego.Config;

public class Matrix3x3 {
	
	private Vector3D x, y, z;
	

	private boolean identity = false;

	//xo,yo,zo position of the camera with respect of world coordinate;
	/** Create a new unit matrix */

	public Matrix3x3() {
		x = new Vector3D();
		y = new Vector3D();
		z = new Vector3D();
		setIdentity();
	}
	
	public Matrix3x3(Vector3D x, Vector3D y, Vector3D z) {
		this.x = new Vector3D(x.x, x.y, x.z);
		this.y = new Vector3D(y.x, y.y, y.z);
		this.z = new Vector3D(z.x, z.y, z.z);
	}
	

	public Matrix3x3(float xx, float xy, float xz, float yx, float yy, float yz, float zx, float zy, float zz) {
		this.x = new Vector3D(xx, xy, xz);
		this.y = new Vector3D(yx, yy, yz);
		this.z = new Vector3D(zx, zy, zz);
	}
	/**
	 * @param theta
	 * @return
	 */
	private static float fitAngle(float theta) {

		float maxTheta = Config.getFloat(Config.maxTheta);
		float minTheta = Config.getFloat(Config.minTheta);
		
		if (Math.abs(theta) > 0) {
			if (Math.abs(theta) > maxTheta)
				theta = maxTheta * Math.abs(theta) / theta;
			else if (Math.abs(theta) < minTheta)
				theta = minTheta * Math.abs(theta) / theta;			
		}
		
		return theta;
	}

	/*
	 * public void translate(float x, float y, float z) { xo += x; yo += y; zo += z;
	 */

	/** rotate theta degrees about the y axis */
	public static Matrix3x3 getXRotation(float theta) {
		theta = fitAngle(theta);
		
		theta *= (Math.PI / 180);

		float cos = (float) Math.cos(theta);
		float sin = (float) Math.sin(theta);

		Vector3D nX = new Vector3D(1, 0, 0);
		Vector3D nY = new Vector3D(0,cos,sin);
		Vector3D nZ = new Vector3D(0,-sin,cos);
		
		return new Matrix3x3(nX, nY, nZ);
	}
	/** rotate theta degrees about the x axis */
	public static Matrix3x3 getYRotation(float theta) {
		
		theta = fitAngle(theta);
		
		theta *= (Math.PI / 180);

		float cos = (float) Math.cos(theta);
		float sin = (float) Math.sin(theta);

		Vector3D nX = new Vector3D(cos,0,-sin);
		Vector3D nY = new Vector3D(0, 1, 0);
		Vector3D nZ = new Vector3D(sin, 0, cos);

		return new Matrix3x3(nX, nY, nZ);
	}

	/** rotate theta degrees about the z axis */
	public static Matrix3x3 getZRotation(float theta) {
		
		theta = fitAngle(theta);

		theta *= (Math.PI / 180);

		float cos = (float) Math.cos(theta);
		float sin = (float) Math.sin(theta);

		Vector3D nX = new Vector3D(cos, sin, 0);
		Vector3D nY = new Vector3D(-sin, cos, 0);
		Vector3D nZ = new Vector3D(0,0,1);

		return new Matrix3x3(nX, nY, nZ);
	}
	
	/*
	public void rotateObject(PositionedObject o, float xtheta, float ytheta) {
		
		setIdentity();
		xrot(xtheta);
		yrot(ytheta);

		o.transform(this);

	}
	*/
		
	public Matrix3x3 multiplyByScalar(float n) {
		identity = false;

		Vector3D X = x.multiplyByScalar(n);
		Vector3D Y = y.multiplyByScalar(n);
		Vector3D Z = z.multiplyByScalar(n);
		
		return new Matrix3x3(X, Y, Z);
	}

	/** Multiply this matrix by a second: M = M*R */
	public Matrix3x3 multiplyByMatrix(Matrix3x3 matrix) {
		Vector3D X,Y,Z;
		
		if (matrix.isIdentity()) {
			return new Matrix3x3(x, y, z);
		}
		
		Matrix3x3 m = matrix.transpose();
		
		X = new Vector3D(x.scalarProduct(m.x),
				x.scalarProduct(m.y),
				x.scalarProduct(m.z));

		Y = new Vector3D(y.scalarProduct(m.x),
				y.scalarProduct(m.y),
				y.scalarProduct(m.z));

		Z = new Vector3D(z.scalarProduct(m.x),
				z.scalarProduct(m.y),
				z.scalarProduct(m.z));
		
		return new Matrix3x3(X, Y, Z);
	}

	public Vector3D multiplyByVector(Vector3D vector) {
		return new Vector3D(
				x.scalarProduct(vector),
				y.scalarProduct(vector),
				z.scalarProduct(vector));	
	}

	/**
	 * @return
	 */
	public Matrix3x3 transpose() {
		Vector3D X, Y, Z;
		
		X = new Vector3D(x.x, y.x, z.x);
		Y = new Vector3D(x.y, y.y, z.y);
		Z = new Vector3D(x.z, y.z, z.z);

		return new Matrix3x3(X, Y, Z);
	}

	/** Reinitialize to the unit matrix */
	public void setIdentity() {
		
		identity = true;

		x = new Vector3D(1,0,0);
		y = new Vector3D(0,1,0);
		z = new Vector3D(0,0,1);

	}

	/**
	 * @return Returns the identity.
	 */
	public boolean isIdentity() {
		if (identity) {
			return true;
		}
		
		return identity = (x.x == 1 && x.y == 0 && x.z == 0 && 
				y.x == 0 && y.y == 1 && y.z == 0 &&
				z.x == 0 && z.y == 0 && z.z == 1);
	}
	
	public String toString() {
		return new String("["
		//+ xo
		+"," + x.x + "," + x.y + "," + x.z + ";"
		//+ yo
		+"," + y.x + "," + y.y + "," + y.z + ";"
		//+ zo
		+"," + z.x + "," + z.y + "," + z.z + "]");
	}

}
