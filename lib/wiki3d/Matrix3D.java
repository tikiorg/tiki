package wiki3d;
public class Matrix3D {
	public float xx = 1.0f, xy, xz, xo = 0;
	public float yx, yy = 1.0f, yz, yo = 0;
	public float zx, zy, zz = 1.0f, zo = 0;
	public float prevx, prevy, prevz;
	int xstep = 1, ystep = 1, zstep = 1;

	public static int FOV = Config.FOV; //field of view
	public static float xf = 1, yf = 1, zf = 1, f = 1;
	public static int xmax = Config.xmax,
		ymax = Config.ymax,
		zmax = Config.zmax,
		xmin = Config.xmin,
		ymin = Config.ymin,
		zmin = Config.zmin;
	boolean makereturnx = false, makereturny = false, makereturnz = false;
	//xo,yo,zo position of the camera with respect of world coordinate
	static final double pi = 3.14159265;
	/** Create a new unit matrix */

	public Matrix3D() {
		xx = 1.0f;
		yy = 1.0f;
		zz = 1.0f;
	}
	/** Scale by f in all dimensions */

	public void setParam(
		int iFOV,
		int initposcamerax,
		int initposcameray,
		int initposcameraz,
		int iymax,
		int iymin,
		int ixmax,
		int ixmin,
		int izmax,
		int izmin,
		int screenwidth,
		int screenheight,
		float scalez,
		float scalex,
		float scaley,
		float scale,
		boolean autoscale) {
		FOV = iFOV;
		xo = initposcamerax;
		yo = initposcameray;
		zo = initposcameraz;
		xmin = ixmin;
		xmax = ixmax;
		ymin = iymin;
		ymax = iymax;
		zmin = izmin;
		zmax = izmax;

		if (autoscale) {
			xf = screenwidth / ((float) (xmax - xmin));
			yf = screenheight / ((float) (ymax - ymin));
			zf = scalez;
			f = Math.min(xf, yf);
		} else {
			f = scale;
			xf = scalex;
			yf = scaley;
			zf = scalez;

		}
		if (f != 0) {
			xf = f;
			yf = f;
			zf = f;
		}
	}

	public void scale() {
		xx *= f;
		xy *= f;
		xz *= f;
		xo *= f;
		yx *= f;
		yy *= f;
		yz *= f;
		yo *= f;
		zx *= f;
		zy *= f;
		zz *= f;
		zo *= f;
	}

	/** Scale along each axis independently */
	public void scale3() {
		xx *= xf;
		xy *= xf;
		xz *= xf;
		xo *= xf;
		yx *= yf;
		yy *= yf;
		yz *= yf;
		yo *= yf;
		zx *= zf;
		zy *= zf;
		zz *= zf;
		zo *= zf;
	}
	/** Translate the origin */
	public void fixpoint() {
		prevx = xo;
		prevy = yo;
		prevz = zo;
		makereturnx = true;
		makereturny = true;
		makereturnz = true;

	}
	public boolean tracepath() {
		int dx, dy, dz;
		if (Math.abs(prevx - xo) < xstep)
			makereturnx = false;
		if (prevx - xo > xstep)
			dx = xstep;
		else
			dx = -xstep;
		if (Math.abs(prevy - yo) < ystep)
			makereturny = false;
		if (prevy - yo > ystep)
			dy = ystep;
		else
			dy = -ystep;
		if (Math.abs(prevz - zo) < zstep)
			makereturnz = false;
		if (prevz - zo > zstep)
			dz = zstep;
		else
			dz = -zstep;
		//if(!makereturnx&&!makereturny&&!makereturnz)
		{
			//System.out.println("removedxoyozo"+xo+"y"+yo+"z"+zo);

			//return false;
		}
		//else
		{
			//x=x+dx;
			//y=y+dy;
			//z=z+dz;

			xo += dx;
			yo += dy;
			zo += dz;
			return true;
			//mat.translate(dx,dy,dz);
			//System.out.println("x"+x+"y"+y+"z"+z);

		}
		//mat.translate(dx,dy,dz);

	}
	public void translate(float x, float y, float z) {
		xo += x;
		yo += y;
		zo += z;
	}
	/** rotate theta degrees about the y axis */
	public void yrot(double theta) {
		theta *= (pi / 180);
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
	public void xrot(double theta) {
		theta *= (pi / 180);
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
	/** rotate theta degrees about the z axis */
	public void zrot(double theta) {
		theta *= (pi / 180);
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

	void mult(Matrix3D rhs) {
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
	public void unit() {
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
		scale3();
	}
	public static float findzcomp(Vertex v1, Vertex v2, Vertex v3) {
		float z = v1.X * v3.X + v1.Y * v3.Y + v1.Z * v3.Z;

		//float z=v1.Y*v2.X-v2.Y*v2.X;

		return z;

	}

	public void transform(Vertex v1) {
		float lxx = xx, lxy = xy, lxz = xz, lxo = xo;
		float lyx = yx, lyy = yy, lyz = yz, lyo = yo;
		float lzx = zx, lzy = zy, lzz = zz, lzo = zo;
		v1.X = (int) (v1.x * lxx + (v1.y) * lxy + v1.z * lxz + lxo);
		v1.Y = (int) (v1.x * lyx + v1.y * lyy + v1.z * lyz + lyo);
		v1.Z = (int) (v1.x * lzx + v1.y * lzy + v1.z * lzz + lzo);
		//  System.out.println(v1.X+" "+v1.Y);
		v1.X = Math.max(xmin, Math.min(v1.X, xmax));
		v1.Y = Math.max(ymin, Math.min(v1.Y, ymax));
		v1.Z = Math.max(zmin, Math.min(v1.Z, zmax));

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
