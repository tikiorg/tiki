package wiki3d;
import java.util.*;
import java.awt.*;

public class Graph extends Vector {
	Node focus;
	public Matrix3D transformationMatrix, rotationMatrix;
	public Face face;
	
	public Graph() {
		transformationMatrix = new Matrix3D();
		rotationMatrix = new Matrix3D();
		face = new Face();
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
			ps[i] = new Pos(node.Z, i);
			i++;

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

		for (i = 0; i < count; i++) {
			try {
				((Node) elementAt(ps[i].pos)).paint(g);
			} catch (Exception ex) {
			}
		}

	}

	public void transform() {
		//transforming each of the vertex in coll.
		//first prepare the transformation matrix which is common and then
		// operate
		//on each points.

		Node.mat.unit();
		Node.mat.mult(rotationMatrix);
		Node.mat.translate(Node.origin.x, Node.origin.y, Node.origin.z);

		Enumeration e = elements();
		while (e.hasMoreElements()) {
			Node cc = (Node) e.nextElement();
			cc.transform();
			//first change to world coordinate and then projectto 2d
			cc.proj();

		}
		
		face.transform(rotationMatrix);

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

	public void rotate(double xtheta, double ytheta) {
		if (xtheta > Config.thetamax)
			xtheta = Config.thetamax;
		else if (xtheta < -Config.thetamax)
			xtheta = -Config.thetamax;
		else if (xtheta > 0 && xtheta < Config.thetamin)
			xtheta = Config.thetamin;
		else if (xtheta < 0 && xtheta > -Config.thetamin)
			xtheta = -Config.thetamin;

		if (ytheta > Config.thetamax)
			ytheta = Config.thetamax;
		else if (ytheta < -Config.thetamax)
			ytheta = -Config.thetamax;
		else if (ytheta > 0 && ytheta < Config.thetamin)
			ytheta = Config.thetamin;
		else if (ytheta < 0 && ytheta > Config.thetamin)
			ytheta = -Config.thetamin;

		transformationMatrix.unit();
		transformationMatrix.xrot(-xtheta);
		transformationMatrix.yrot(-ytheta);

		rotationMatrix.mult(transformationMatrix);
	}

}