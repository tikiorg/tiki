package wiki3d;
import java.util.*;
import java.awt.*;

public class Graph extends Vector {
	Node focus;
	public static HashMap nodeNameMap;
	public static Node centerNode;
	static Node lastNode;
	
	XmlReader xmlReader;
	private int maxDistance = 2;

	public Graph() {
		nodeNameMap = new HashMap();
		
	}

	public void setXmlReader(XmlReader xr) {
		xmlReader = xr;
	}

	public Node add(String nodeName) {
		Node node;
		if (nodeNameMap.containsKey(nodeName)) {
			node = (Node) nodeNameMap.get(nodeName);
		} else {
			node = new Node(nodeName, this);
			nodeNameMap.put(nodeName, node);
			addElement(node);
			
		}

		if (centerNode == null) {
			node.center();
		}

		return node;
	}

	public void addLink(String nodeName1, String nodeName2) {
		Node node1 = add(nodeName1);
		Node node2 = add(nodeName2);
		node1.addLink(node2);
		node2.addLink(node1);
	}

	//checks whether any of the vertex in the collection contains the
	// given
	// point.
	public boolean contains(int x, int y) {
		int i = 0;

		while (i < size()) {
			if (((Node) elementAt(i)).contains(x, y)) {
				focus = ((Node) elementAt(i));
				lastNode = (Node) focus;
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
			ps[i] = new Pos(((Node) e.nextElement()).Z, i);
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
			((Node) elementAt(ps[i].pos)).paint(g);
			//System.out.print(" "+ps[i].pos);

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

	/**
	 * @param node
	 */
	public void navigateTo(Node node) {
		node.center();
		populateGraph();
		removeFarNodes();
	}

	public void removeNode(Node node) {
		for (int i = 0; i < size(); i++) {
			if (this.elementAt(i) == node) {
				this.remove(i);
				
				return;
			}
		}
	}

	public void mapDistances(Node node) {
		
		if (node == null) {
			for (int i = 0; i < size(); i++) {
				((Node) elementAt(i)).passed = false;
				((Node) elementAt(i)).distanceFromCenter = -1;
			}
			node = centerNode;
			node.distanceFromCenter = 0;
			node.passed = true;
		}

		Set linkSet = node.links.keySet();
		for (Iterator it = linkSet.iterator(); it.hasNext();) {
			Node neighbour = (Node) it.next();
			if (!neighbour.passed) {
				neighbour.distanceFromCenter = node.distanceFromCenter+1;
				neighbour.passed = true;
				mapDistances(neighbour);
			}
		}
	}
	
	public void removeFarNodes() {		
		mapDistances(null);
		for (int i=0; i<size(); i++) {
			Node node = ((Node) elementAt(i));
			int d = node.distanceFromCenter;
			if (d > maxDistance || d < 0) {
				node.clean();
			}
		}
	}
	
	public void populateGraph() {
		mapDistances(null);
		for (int i=0; i<size(); i++) {
			Node node = ((Node) elementAt(i));
			int d = node.distanceFromCenter;
			if (d <= maxDistance && d >= 0) { 
				xmlReader.getNodeData(node.name, this);
				mapDistances(null);
			}
		}
		
	}

}