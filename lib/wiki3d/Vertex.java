package wiki3d;
public class Vertex {
	int x, y, z;
	int u, v;
	int X, Y, Z;
	public static float FOV;
	public static int XC, YC, ZC;
	public static int xo, yo, zo;

	public static Vertex origin;
	public static Matrix3D mat;

	public Vertex(int x, int y, int z) {
		mat = new Matrix3D();

		this.x = x;
		this.z = z;
		this.y = y;

		//links = new Vector();

	}
	public void paint() {
	}

	public Vertex() {
		//links = new Vector();

	}

	public static void setCamera(int x, int y, int z) {
		XC = x;
		YC = y;
		ZC = z;

	}

	public void setCamerapos(int x, int y, int z) {
		XC = x;
		YC = y;
		ZC = z;

	}
	public static void setFOV(float f) {
		FOV = f;
	}

	public void setOrigin(int x, int y, int z) {
		origin = new Vertex(x, y, z);
		mat.translate(x, y, z);
		transform();

	}
	public void transform() {

		mat.transform(this);

	}
	public void proj() {
		//int xo=X;
		//int yo=origin.Y;
		int k = Z - ZC;
		//System.out.println("Z "+" ");
		// u=X;
		// v=Y;
		u = new Float(origin.x + (FOV * (X - origin.x)) / (-Z + ZC)).intValue();
		v = new Float(origin.y + (FOV * (Y - origin.y)) / (-Z + ZC)).intValue();

	}

}