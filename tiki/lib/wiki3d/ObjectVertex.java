package wiki3d;

public class ObjectVertex extends Vertex {
	public static Vertex origin;
	public static Matrix3D mat;
	boolean transformed = false;

	public ObjectVertex(int x, int y, int z) {
		mat = new Matrix3D();
		this.x = x;
		this.y = y;
		this.z = z;

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