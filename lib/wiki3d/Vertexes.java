package wiki3d;
import java.util.*;
import java.awt.*;

public class Vertexes extends Vector {
	CanvaxVertex focus;
	static Node lastlink;
	int count;
	public Vertexes() {
	}
	public void change(int dx, int dy, int dz) {
		int i = 0;
		while (i < count)
			 ((CanvaxVertex) elementAt(i++)).change(dx, dy, dz);
	}

	public void add(CanvaxVertex v) {
		addElement(v);
		count = size();

	}

	public void initpos() {

		float theta = (float) Math.PI * 2 / size();
		float ttheta = (float) Math.random();
		Enumeration e = elements();
		while (e.hasMoreElements()) {
			((CanvaxVertex) e.nextElement()).initpos(ttheta);
			ttheta += theta;

		}

	}

	//checks whether any of the vertex in the collection contains the given
	// point.
	public boolean contains(int x, int y) {
		int i = 0;

		while (i < count) {
			if (((CanvaxVertex) elementAt(i)).contains(x, y)) {
				focus = ((CanvaxVertex) elementAt(i)).getElement();
				if (focus.type() == 'l') {
					lastlink = (Node) focus;
				}
				return true;
			}
			i++;
		}

		return false;

	}

	synchronized public void paint(Graphics g) {
		//orders the vertexes in increasing order of Z (world coordinate)and
		// paints
		//which ensures that the vertex in the backside doesnot obscures the
		// one above

		Enumeration e = elements();
		int min = 0;
		Pos ps[] = new Pos[this.size()];
		int count = size();
		int i = 0;
		//object ps contains the value of Z for each vertex and the position
		// of the vertex in the collection.
		while (e.hasMoreElements()) {
			ps[i] = new Pos(((CanvaxVertex) e.nextElement()).Z, i);
			i++;

		}
		//System.out.println("size"+count);
		i = 0;
		//sorting here
		while (i < count - 1) {
			min = i;
			for (int j = i + 1; j < count; j++) {
				if (ps[j].z < ps[min].z) {
					min = j;
				}
			}
			Pos zm = ps[i];
			ps[i] = ps[min];
			ps[min] = zm;
			i++;

		}

		//

		for (i = 0; i < count; i++) {
			((CanvaxVertex) elementAt(ps[i].pos)).paint(g);
			// System.out.print(" "+ps[i].pos);

		}
		//System.out.println("\n "+z[0]+ " "+ z[1]+" "+z[2]);

	}

	public void transform(Matrix3D amat) {
		//transforming each of the vertex in coll.
		//first prepare the transformation matrix which is common and then
		// operate
		//on each points.

		CanvaxVertex.mat.unit();
		CanvaxVertex.mat.mult(amat);
		CanvaxVertex.mat.translate(
			CanvaxVertex.origin.x,
			CanvaxVertex.origin.y,
			CanvaxVertex.origin.z);

		Enumeration e = elements();
		while (e.hasMoreElements()) {
			CanvaxVertex cc = (CanvaxVertex) e.nextElement();
			cc.transform();
			//first change to world coordinate and then projectto 2d
			cc.proj();

		}

	}

}