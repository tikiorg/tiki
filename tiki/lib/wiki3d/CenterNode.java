package wiki3d;
import java.util.*;
import java.awt.*;
public class CenterNode extends CanvaxVertex {
	String node;

	Vector links = new Vector();
	int i = 0;
	int count = 0;

	public CenterNode(String node) {
		super();
		this.node = node;
		x = 0;
		y = 0;
		z = 0;
	}
	public boolean contains(int x, int y) {
		return false;

	}
	public void initpos(float theta) {
	}
	public char type() {
		return 'g';

	}
	public void addLink(Node link) {
		links.addElement(link);
		count++;
	}
	public Node getNextLink() {
		if (i < count)
			return (Node) links.elementAt(i++);
		else {
			i = 0;
			return null;
		}
	}
	public int getLinkCount() {
		return count;

	}
	public Node getCurrentLInk() {
		return (Node) links.elementAt(i);

	}
	public boolean nextLink() {
		if (i < count) {
			i++;
			return true;
		} else {
			i = 0;
			return false;
		}
	}
	public String getNode() {
		return node;

	}

	public void paint(Graphics g) {

		g.setColor(new Color(255, 255, 0));
		g.fillArc(U, V, b, b, 0, 360);

		g.setColor(new Color(10, 10, 10));
		g.drawString(node, u, v);

	}

}
