package wiki3d;
import java.awt.*;
public class Vertex {
	int x, y, z;
	int u, v;
	int X, Y, Z;
	public static float FOV;
	public static int XC, YC, ZC;
	public static int xo, yo, zo;
	public Vertex(int x, int y, int z) {
		this.x = x;
		this.z = z;
		this.y = y;

	}
	public Vertex() {
	}
	public void setOrigin(int x, int y, int z) {

		xo = x;
		yo = y;
		zo = z;

	}
	public static void setCamera(int x, int y, int z) {
		XC = x;
		YC = y;
		ZC = z;

	}
	
	public void paint(Graphics g) {
		g.drawOval(u, v, 13, 13);
	}
	
	public void setCamerapos(int x, int y, int z) {
		XC = x;
		YC = y;
		ZC = z;

	}
	public static void setFOV(float f) {
		FOV = f;
	}
	public void proj() {
		//int xo=X;
		//int yo=origin.Y;
		int k = Z - ZC;
		System.out.println("Z " + "  ");
		// u=X;
		// v=Y;
		u = new Float((FOV * (X)) / (-Z + ZC)).intValue();
		v = new Float((FOV * (Y)) / (-Z + ZC)).intValue();

	}
	public char type() {
		return 'v'; //returns type of vertex ie graph/node...

	}

}