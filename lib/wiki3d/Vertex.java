package wiki3d;

import javax.vecmath.Tuple3f;

public class Vertex extends Tuple3f {
	public float x, y, z;
	int u, v;
	public static float FOV;
	public static int cameraX, cameraY, cameraZ;	

	public static Vertex origin;
	
	public Vertex(int x, int y, int z) {
		this.x = x;
		this.z = z;
		this.y = y;
	}
	
	public Vertex() {
	}
	
	public static void setCamera(int x, int y, int z) {
		cameraX = x;
		cameraY = y;
		cameraZ = z;
	}

	public void setCamerapos(int x, int y, int z) {
		cameraX = x;
		cameraY = y;
		cameraZ = z;
	}

	public static void setFOV(float f) {
		FOV = f;
	}

}