package wiki3d;
import java.util.*;
import java.awt.*;

public class Graph extends Vector {
	Node focus;
	public Graph() {
	}

	//checks whether any of the vertex in the collection contains the
	// given
	// point.
	public boolean contains(int x, int y) {
		int i = 0;

		while (i < size()) {
			if (((Node) elementAt(i)).contains(x, y)) {
				focus = ((Node) elementAt(i));				
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
			Node node = (Node) e.nextElement();
			if (node.visible()) {
				ps[i] = new Pos(node.Z, i);
				i++;				
			}

		}
		count = i;
		//System.out.println("size"+count);
		i = 0;
		//sorting here
		while (i < count - 1) {
			min = i;
			if (ps[i + 1] != null && ps[i] != null) {
				for (int j = i + 1; j < count; j++) {
					if (ps[j].z < ps[min].z) {
						min = j;
					}
				}
			}
			Pos zm = ps[i];
			ps[i] = ps[min];
			ps[min] = zm;
			i++;

		}

		//

		for (i = 0; i < count; i++) {
			try {
				((Node) elementAt(ps[i].pos)).paint(g);
			} catch (Exception ex) {
			}
		}
		//System.out.println("\n "+z[0]+ " "+ z[1]+" "+z[2]);

	}

	public void transform(Matrix3D amat) {
		//transforming each of the vertex in coll.
		//first prepare the transformation matrix which is common and then
		// operate
		//on each points.

		Node.mat.unit();
		Node.mat.mult(amat);
		Node.mat.translate(Node.origin.x, Node.origin.y, Node.origin.z);

		Enumeration e = elements();
		while (e.hasMoreElements()) {
			Node cc = (Node) e.nextElement();
			cc.transform();
			//first change to world coordinate and then projectto 2d
			cc.proj();

		}

	}

	public void removeNode(Node node) {
		for (int i = 0; i < size(); i++) {
			if (this.elementAt(i) == node) {
				this.remove(i);				
			}
		}
		if (focus == node) {
			focus = null;
		}
		node.remove();
	}

}