package wiki3d;
import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.Stroke;
import java.awt.font.FontRenderContext;
import java.awt.font.TextLayout;
import java.awt.geom.AffineTransform;
import java.text.AttributedString;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;

public class Node extends Vertex {
	int relativeBallSize, ballSize;

	static Balancer balancer;
	public static Vertex origin;
	public static Matrix3D mat;
	public boolean mouseover = false;
	boolean focussed = true;
	Rectangle boundRectangle = new Rectangle();
	int U, V;
	int i, j;
	int xpos, ypos, zpos;
	public int id;
	SpeedVector speed = new SpeedVector(0, 0, 0);

	public Hashtable links;
	private boolean positionFixed;
	private boolean isCentered;

	public String name;

	public int distanceFromCenter = 0;
	public boolean passed = false;
	public boolean initialized = false;

	private static Hashtable nodesFromName = new Hashtable();

	public Node(String name) {
		super();
		links = new Hashtable();
		mat = new Matrix3D();

		this.name = name;
		this.passed = false;
		distanceFromCenter = -1;

		relativeBallSize = Config.ballsize;
		ballSize = Config.ballsize;

		y = length();
		x = length();
		z = length();
		setBounds();

		//System.out.println("Creating " + name);
		nodesFromName.put(name, this);

	}

	public static void setBalancer(Balancer a) {
		balancer = a;
	}

	public SpeedVector getForceFromNode(Node node) {

		SpeedVector sp = new SpeedVector(x - node.x, y - node.y, z - node.z);

		float distance = 2f;
		float module = sp.module();
		float d;

		sp.resize(1 / module);

		module /= 50;

		if (this.isLinkedTo(node)) {
			module -= distance;
			if (module > 0) {
				sp.resize((float) (-1 * module * module));
			} else {
				sp.resize(1 * module * module);
			}
		} else {
			sp.resize(
				3 * (float) (Math.pow(Math.E, 1 / Math.sqrt(module)) - 1));

		}

		return sp;
	}

	public SpeedVector getForceToCenter() {
		SpeedVector sp = new SpeedVector(0, 0, 0);
		if (!this.centered()) {
			return new SpeedVector(0, 0, 0);
		} else {
			return new SpeedVector(-x / 4, -y / 4, -z / 4);
		}
	}

	/**
	 * @param node
	 * @return
	 */
	private boolean isLinkedTo(Node node) {
		return links.containsKey(node.name);
	}

	/**
	 * @return
	 */
	public boolean centered() {
		return isCentered;
	}

	public void center() {
		isCentered = true;
	}

	public void unCenter() {
		isCentered = false;
		x++;
		y++;
		z++;
	}

	public void clearSpeed() {
		speed.clear();
	}

	public void addSpeed(SpeedVector s) {
		speed.add(s);
	}

	public void balance() {
		if (centered()) {
			clearSpeed();
			addSpeed(getForceToCenter());
			change(speed.x, speed.y, speed.z);
		} else if (!this.positionFixed()) {
			change(speed);
		}
	}

	public boolean positionFixed() {
		return positionFixed || centered();
	}

	public void releasePosition() {
		positionFixed = false;

	}

	public void fixPosition() {

		positionFixed = true;
		xpos = x;
		ypos = y;
		zpos = z;

	}

	//function for changing the object space coordinates,
	public void change(int dx, int dy, int dz) {

		x = x + (dx);
		y = y + (dy);
		z = z + (dz);

		if (x > Config.xmax) {
			x = Config.xmax;
		}
		if (x < Config.xmin) {
			x = Config.xmin;
		}
		if (z > Config.zmax) {
			z = Config.zmax;
		}
		if (z < Config.zmin) {
			z = Config.zmin;
		}
		if (y > Config.ymax) {
			y = Config.ymax;
		}
		if (y < Config.ymin) {
			y = Config.ymin;
		}

	}

	public void change(SpeedVector sp) {
		if (sp.module() > Config.maxSpeed) {
			sp.resize(Config.maxSpeed / sp.module());
		}
		change(sp.x, sp.y, sp.z);
	}

	synchronized public void proj() {
		relativeBallSize =
			(int) Math.round((double) ballSize * FOV / (-Z + ZC));
		//diameter reduced to projection
		//System.out.println("ZC"+ZC+"Z"+Z+"b"+b);
		if (relativeBallSize < Config.minimumBallSize)
			relativeBallSize = Config.minimumBallSize;

		//projection for X,and Y of 3d to u,v of 2d;
		int k = Z - ZC;
		int ZZ = Z - ZC;
		if (Math.abs(ZZ) < 1)
			ZZ = 1;
		u = new Float(origin.x + (FOV * (X - origin.x)) / (ZZ)).intValue();
		v = new Float(origin.y + (FOV * (Y - origin.y)) / (ZZ)).intValue();

		int c = relativeBallSize / 2;

		U = u - c;
		V = v - c;
		boundRectangle =
			new Rectangle(U, V, relativeBallSize, relativeBallSize);

	}

	public boolean contains(int x, int y) {
		if (isCentered) {
			return false;
		}
		mouseover = false;

		if (boundRectangle.contains(x, y)) {
			mouseover = true;
		}

		return mouseover;

	}

	synchronized public void transform() {
		mat.transform(this);
	}

	public int length() {
		return (int) (Math.random() * Config.randomlength)
			- Config.lengthmedian;

	}

	void setBounds() {
		x = Math.min(Math.max(x, Config.xmin), Config.xmax);
		y = Math.min(Math.max(y, Config.ymin), Config.ymax);
		z = Math.min(Math.max(z, Config.zmin), Config.zmax);
	}

	public void addLink(String nodeName) {
		links.put(nodeName, new Object());
		Node neighbour = Node.fromString(nodeName);
		if (neighbour != null) {
			neighbour.links.put(this.name, new Object());
		}
	}

	public static Node fromString(String nodeName) {
		Node node = (Node) nodesFromName.get(nodeName);
		return node;
	}

	public void paint(Graphics g) {

		Graphics2D graphic = (Graphics2D) g;

		double zc = Z - ZC;
		double scale;
		if (Math.abs(zc) > 1)
			scale = Math.abs(FOV * 1 / zc);
		else
			scale = 1;
		scale *= 2;

		if (scale > 1)
			scale = 1;
		if (scale < 0.1)
			scale = 0.1;

		g.setColor(Config.graphstringcolor);
		Set linkSet = links.keySet();

		for (Iterator it = linkSet.iterator(); it.hasNext();) {
			Node neighbour = fromString((String) it.next());
			if (neighbour != null) {
				g.drawLine(u, v, neighbour.u, neighbour.v);
			}
		}

		AffineTransform at = graphic.getTransform();

		AlphaComposite al2 = (AlphaComposite) graphic.getComposite();

		AlphaComposite al =
			AlphaComposite.getInstance(
				AlphaComposite.SRC_OVER,
				(float) (scale));

		graphic.setComposite(al);

		Color cl = graphic.getColor();

		Stroke s = graphic.getStroke();

		at.setToScale(scale, scale);

		graphic.setColor(Config.linkballcolor);
		graphic.setComposite(al);
		graphic.fillOval(U, V, relativeBallSize, relativeBallSize);
		AffineTransform at2 = new AffineTransform();
		at2.setToScale(scale, scale);
		FontRenderContext frc = new FontRenderContext(at2, true, true);

		TextLayout l =
			new TextLayout((new AttributedString(name)).getIterator(), frc);
		l.draw(graphic, U, V);
		graphic.setComposite(al2);
	}

	public void remove() {
		nodesFromName.remove((String) name);
	}

}