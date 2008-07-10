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

public class Matrix2x3 {
	
	private Vector3D x, y;
	
	/** Create a new matrix */
	public Matrix2x3() {
		x = new Vector3D();
		y = new Vector3D();
	}
	
	public Matrix2x3(Vector3D x, Vector3D y) {
		this.x = new Vector3D(x.x, x.y, x.z);
		this.y = new Vector3D(y.x, y.y, y.z);
	}
	

	public Matrix2x3(float xx, float xy, float xz, float yx, float yy, float yz) {
		this.x = new Vector3D(xx, xy, xz);
		this.y = new Vector3D(yx, yy, yz);
	}

	public Matrix2x3 multiplyByScalar(float n) {

		Vector3D X = x.multiplyByScalar(n);
		Vector3D Y = y.multiplyByScalar(n);
		
		return new Matrix2x3(X, Y);
	}

	//TODO Matrix2x3 times Matrix2x3 
	/* Multiply this matrix by a second: M = M*R yet to do 
	public Matrix2x3 multiplyByMatrix(Matrix2x3 matrix) {
		Vector3D X,Y,Z;
		
		if (matrix.isIdentity()) {
			return new Matrix2x3(x, y, z);
		}
		
		Matrix2x3 m = matrix.transpose();
		
		X = new Vector3D(x.scalarProduct(m.x),
				x.scalarProduct(m.y),
				x.scalarProduct(m.z));

		Y = new Vector3D(y.scalarProduct(m.x),
				y.scalarProduct(m.y),
				y.scalarProduct(m.z));

		Z = new Vector3D(z.scalarProduct(m.x),
				z.scalarProduct(m.y),
				z.scalarProduct(m.z));
		
		return new Matrix2x3(X, Y, Z);
	}*/

	public Point multiplyByVector(Vector3D vector) {
		return new Point(
				new Float(x.scalarProduct(vector)).intValue(),
				new Float(y.scalarProduct(vector)).intValue());	
	}

	//TODO como transposar Matrix2x3????
	/**public Matrix2x3 transpose() {
		Vector3D X, Y, Z;
		
		X = new Vector3D(x.x, y.x, z.x);
		Y = new Vector3D(x.y, y.y, z.y);
		Z = new Vector3D(x.z, y.z, z.z);

		return new Matrix2x3(X, Y, Z);
	}*/

	public String toString() {
		return new String("["
		+"(" + x.x + "," + x.y + "," + x.z + ")\n"
		+"(" + y.x + "," + y.y + "," + y.z + ")]");
	}

}
