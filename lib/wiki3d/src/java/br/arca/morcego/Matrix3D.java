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

public class Matrix3D {
	private float xx = 1.0f, xy, xz;
	private float yx, yy = 1.0f, yz;
	private float zx, zy, zz = 1.0f;

	private static float xf = 1, yf = 1, zf = 1, f = 1;
	
	private boolean identity = false;

	//xo,yo,zo position of the camera with respect of world coordinate

	/** Create a new unit matrix */

	public Matrix3D() {

		xx = 1.0f;
		yy = 1.0f;
		zz = 1.0f;
	}

	/*
	 * public void translate(float x, float y, float z) { xo += x; yo += y; zo += z;
	 */

	/** rotate theta degrees about the y axis */
	public void xrot(double theta) {

		identity = false;
		
		theta *= (Math.PI / 180);

		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nxx = (float) (xx * ct + zx * st);
		float Nxy = (float) (xy * ct + zy * st);
		float Nxz = (float) (xz * ct + zz * st);
		//float Nxo = (float) (xo * ct + zo * st);

		float Nzx = (float) (zx * ct - xx * st);
		float Nzy = (float) (zy * ct - xy * st);
		float Nzz = (float) (zz * ct - xz * st);
		//float Nzo = (float) (zo * ct - xo * st);

		//xo = Nxo;
		xx = Nxx;
		xy = Nxy;
		xz = Nxz;
		//zo = Nzo;
		zx = Nzx;
		zy = Nzy;
		zz = Nzz;
	}
	/** rotate theta degrees about the x axis */
	public void yrot(double theta) {

		identity = false;
		
		theta *= (Math.PI / 180);

		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nyx = (float) (yx * ct + zx * st);
		float Nyy = (float) (yy * ct + zy * st);
		float Nyz = (float) (yz * ct + zz * st);
		//float Nyo = (float) (yo * ct + zo * st);

		float Nzx = (float) (zx * ct - yx * st);
		float Nzy = (float) (zy * ct - yy * st);
		float Nzz = (float) (zz * ct - yz * st);
		//float Nzo = (float) (zo * ct - yo * st);

		//yo = Nyo;
		yx = Nyx;
		yy = Nyy;
		yz = Nyz;
		//zo = Nzo;
		zx = Nzx;
		zy = Nzy;
		zz = Nzz;
	}

	/** rotate theta degrees about the z axis */
	public void zrot(double theta) {
		identity = false;
		
		theta *= (Math.PI / 180);

		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nyx = (float) (yx * ct + xx * st);
		float Nyy = (float) (yy * ct + xy * st);
		float Nyz = (float) (yz * ct + xz * st);
		//float Nyo = (float) (yo * ct + xo * st);

		float Nxx = (float) (xx * ct - yx * st);
		float Nxy = (float) (xy * ct - yy * st);
		float Nxz = (float) (xz * ct - yz * st);
		//float Nxo = (float) (xo * ct - yo * st);

		//yo = Nyo;
		yx = Nyx;
		yy = Nyy;
		yz = Nyz;
		//xo = Nxo;
		xx = Nxx;
		xy = Nxy;
		xz = Nxz;
	}
	
	void mul(float n) {
		identity = false;
		
		xx *= n;
		xy *= n;
		xz *= n;
		yx *= n;
		yy *= n;
		yz *= n;
		zx *= n;
		zy *= n;
		zz *= n;
	}

	/** Multiply this matrix by a second: M = M*R */
	void mul(Matrix3D rhs) {

		identity = false;
		
		float lxx = xx * rhs.xx + yx * rhs.xy + zx * rhs.xz;
		float lxy = xy * rhs.xx + yy * rhs.xy + zy * rhs.xz;
		float lxz = xz * rhs.xx + yz * rhs.xy + zz * rhs.xz;
		//float lxo = xo * rhs.xx + yo * rhs.xy + zo * rhs.xz + rhs.xo;

		float lyx = xx * rhs.yx + yx * rhs.yy + zx * rhs.yz;
		float lyy = xy * rhs.yx + yy * rhs.yy + zy * rhs.yz;
		float lyz = xz * rhs.yx + yz * rhs.yy + zz * rhs.yz;
		//float lyo = xo * rhs.yx + yo * rhs.yy + zo * rhs.yz + rhs.yo;

		float lzx = xx * rhs.zx + yx * rhs.zy + zx * rhs.zz;
		float lzy = xy * rhs.zx + yy * rhs.zy + zy * rhs.zz;
		float lzz = xz * rhs.zx + yz * rhs.zy + zz * rhs.zz;
		//float lzo = xo * rhs.zx + yo * rhs.zy + zo * rhs.zz + rhs.zo;

		xx = lxx;
		xy = lxy;
		xz = lxz;
		//xo = lxo;

		yx = lyx;
		yy = lyy;
		yz = lyz;
		//yo = lyo;

		zx = lzx;
		zy = lzy;
		zz = lzz;
		//zo = lzo;

	}

	/** Reinitialize to the unit matrix */
	public void setIdentity() {
		
		identity = true;

		//xo = 0;
		xx = 1;
		xy = 0;
		xz = 0;
		//yo = 0;
		yx = 0;
		yy = 1;
		yz = 0;
		//zo = 0;
		zx = 0;
		zy = 0;
		zz = 1;

	}
	
	public void transform(Vertex v1) {
		float X, Y, Z;
		X = v1.x * xx + (v1.y) * xy + v1.z * xz; // + xo;
		Y = v1.x * yx + v1.y * yy + v1.z * yz; // + yo;
		Z = v1.x * zx + v1.y * zy + v1.z * zz; // + zo;

		int radius = ((Integer)Config.getValue("universeRadius")).intValue();

		X = Math.max(-radius, Math.min(X, radius));
		Y = Math.max(-radius, Math.min(Y, radius));
		Z = Math.max(-radius, Math.min(Z, radius));

		v1.x = X;
		v1.y = Y;
		v1.z = Z;
	}

	public String toString() {
		return ("["
		//+ xo
		+"," + xx + "," + xy + "," + xz + ";"
		//+ yo
		+"," + yx + "," + yy + "," + yz + ";"
		//+ zo
		+"," + zx + "," + zy + "," + zz + "]");
	}

	/**
	 * @return Returns the identity.
	 */
	public boolean isIdentity() {
		return identity;
	}

}
