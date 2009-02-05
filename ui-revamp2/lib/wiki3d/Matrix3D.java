/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Matrix3D.java,v 1.9 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;

import javax.vecmath.Matrix3f;


public class Matrix3D  {
	 public float xx = 1.0f, xy, xz, xo = 0;
	public float yx, yy = 1.0f, yz, yo = 0;
	public float zx, zy, zz = 1.0f, zo = 0; 

	public static int fieldOfView = Config.fieldOfView;
	
	public static float xf = 1, yf = 1, zf = 1, f = 1;
	
	public static int xmax = Config.xmax,
		ymax = Config.ymax,
		zmax = Config.zmax,
		xmin = Config.xmin,
		ymin = Config.ymin,
		zmin = Config.zmin;
	
	//xo,yo,zo position of the camera with respect of world coordinate
	
	/** Create a new unit matrix */

	public Matrix3D() {	
				
		xx = 1.0f;
		yy = 1.0f;
		zz = 1.0f;
	}
	
	public void translate(float x, float y, float z) {			
		xo += x;
		yo += y;
		zo += z;
	}
	/** rotate theta degrees about the y axis */
	public void xrot(double theta) {
		
		theta *= (Math.PI / 180);
		
		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nxx = (float) (xx * ct + zx * st);
		float Nxy = (float) (xy * ct + zy * st);
		float Nxz = (float) (xz * ct + zz * st);
		float Nxo = (float) (xo * ct + zo * st);

		float Nzx = (float) (zx * ct - xx * st);
		float Nzy = (float) (zy * ct - xy * st);
		float Nzz = (float) (zz * ct - xz * st);
		float Nzo = (float) (zo * ct - xo * st);

		xo = Nxo;
		xx = Nxx;
		xy = Nxy;
		xz = Nxz;
		zo = Nzo;
		zx = Nzx;
		zy = Nzy;
		zz = Nzz;
	}
	/** rotate theta degrees about the x axis */
	public void yrot(double theta) {
		
		theta *= (Math.PI / 180);
		
		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nyx = (float) (yx * ct + zx * st);
		float Nyy = (float) (yy * ct + zy * st);
		float Nyz = (float) (yz * ct + zz * st);
		float Nyo = (float) (yo * ct + zo * st);

		float Nzx = (float) (zx * ct - yx * st);
		float Nzy = (float) (zy * ct - yy * st);
		float Nzz = (float) (zz * ct - yz * st);
		float Nzo = (float) (zo * ct - yo * st);

		yo = Nyo;
		yx = Nyx;
		yy = Nyy;
		yz = Nyz;
		zo = Nzo;
		zx = Nzx;
		zy = Nzy;
		zz = Nzz;
	}
	/** rotate theta degrees about the z axis
	 * Is this necessary?? */
	public void zrot(double theta) {
		theta *= (Math.PI / 180);
		
		double ct = Math.cos(theta);
		double st = Math.sin(theta);

		float Nyx = (float) (yx * ct + xx * st);
		float Nyy = (float) (yy * ct + xy * st);
		float Nyz = (float) (yz * ct + xz * st);
		float Nyo = (float) (yo * ct + xo * st);

		float Nxx = (float) (xx * ct - yx * st);
		float Nxy = (float) (xy * ct - yy * st);
		float Nxz = (float) (xz * ct - yz * st);
		float Nxo = (float) (xo * ct - yo * st);

		yo = Nyo;
		yx = Nyx;
		yy = Nyy;
		yz = Nyz;
		xo = Nxo;
		xx = Nxx;
		xy = Nxy;
		xz = Nxz;
	}
	/** Multiply this matrix by a second: M = M*R */

	void mul(Matrix3D rhs) {
		
		float lxx = xx * rhs.xx + yx * rhs.xy + zx * rhs.xz;
		float lxy = xy * rhs.xx + yy * rhs.xy + zy * rhs.xz;
		float lxz = xz * rhs.xx + yz * rhs.xy + zz * rhs.xz;
		float lxo = xo * rhs.xx + yo * rhs.xy + zo * rhs.xz + rhs.xo;

		float lyx = xx * rhs.yx + yx * rhs.yy + zx * rhs.yz;
		float lyy = xy * rhs.yx + yy * rhs.yy + zy * rhs.yz;
		float lyz = xz * rhs.yx + yz * rhs.yy + zz * rhs.yz;
		float lyo = xo * rhs.yx + yo * rhs.yy + zo * rhs.yz + rhs.yo;

		float lzx = xx * rhs.zx + yx * rhs.zy + zx * rhs.zz;
		float lzy = xy * rhs.zx + yy * rhs.zy + zy * rhs.zz;
		float lzz = xz * rhs.zx + yz * rhs.zy + zz * rhs.zz;
		float lzo = xo * rhs.zx + yo * rhs.zy + zo * rhs.zz + rhs.zo;

		xx = lxx;
		xy = lxy;
		xz = lxz;
		xo = lxo;

		yx = lyx;
		yy = lyy;
		yz = lyz;
		yo = lyo;

		zx = lzx;
		zy = lzy;
		zz = lzz;
		zo = lzo;

	}

	/** Reinitialize to the unit matrix */
	public void setIdentity() {
		
		xo = 0;
		xx = 1;
		xy = 0;
		xz = 0;
		yo = 0;
		yx = 0;
		yy = 1;
		yz = 0;
		zo = 0;
		zx = 0;
		zy = 0;
		zz = 1;
		
	}
	public void transformReal(Vertex v1) {	
		float X, Y, Z;
		X = v1.x * xx + (v1.y) * xy + v1.z * xz + xo;
		Y = v1.x * yx + v1.y * yy + v1.z * yz + yo;
		Z = v1.x * zx + v1.y * zy + v1.z * zz + zo;

		X = Math.max(xmin, Math.min(X, xmax));
		Y = Math.max(ymin, Math.min(Y, ymax));
		Z = Math.max(zmin, Math.min(Z, zmax));
		
		v1.x=X;
		v1.y=Y;
		v1.z=Z;		
	}

	public Matrix3D invert() {
		Matrix3f m = new Matrix3f(xx, xy, xz, yx, yy, yz, zx, zy, zz);
		Matrix3D inverted = new Matrix3D();
		
		inverted.xx = (float)m.m00;
		inverted.xy = (float)m.m01;
		inverted.xz = (float)m.m02;

		inverted.yx = (float)m.m10;
		inverted.yy = (float)m.m11;
		inverted.yz = (float)m.m12;
		
		inverted.zx = (float)m.m20;
		inverted.zy = (float)m.m21;
		inverted.zz = (float)m.m22;
		
		m.invert();

		return inverted;		
	}

	public String toString() {
		return (
			"["
				+ xo
				+ ","
				+ xx
				+ ","
				+ xy
				+ ","
				+ xz
				+ ";"
				+ yo
				+ ","
				+ yx
				+ ","
				+ yy
				+ ","
				+ yz
				+ ";"
				+ zo
				+ ","
				+ zx
				+ ","
				+ zy
				+ ","
				+ zz
				+ "]");
	}

}
