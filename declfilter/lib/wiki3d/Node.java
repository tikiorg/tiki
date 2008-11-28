/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki3d/Node.java,v 1.14 2006-10-22 03:21:39 mose Exp $
 *
 * Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

package wiki3d;
import java.awt.AlphaComposite;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.font.FontRenderContext;
import java.awt.font.TextLayout;
import java.awt.geom.AffineTransform;
import java.awt.geom.Rectangle2D;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;

public class Node extends Vertex {
	int relativeBallSize, ballSize;
	int relativeTextSize, textSize;

	public boolean mouseover = false;
	boolean focussed = true;
	Rectangle boundRectangle = new Rectangle();
	int U, V;
	int i, j;
	float fixedX, fixedY, fixedZ;
	public int id;
	SpeedVector speed = new SpeedVector(0, 0, 0);

	public Hashtable links;
	private boolean positionFixed;
	private boolean isCentered;

	public String name;

	public boolean passed = false;
	public boolean initialized = false;

	private Graph graph;

	public Node(String name, Graph graph) {
		super();
		links = new Hashtable();

		this.graph = graph;

		this.name = name;
		this.passed = false;

		relativeBallSize = Config.ballsize;
		ballSize = Config.ballsize;

		relativeTextSize = Config.textsize;
		textSize = Config.textsize;

		y = length();
		x = length();
		z = length();

		setBounds();
	}

	public SpeedVector getForceFromNode(Node node) {

		SpeedVector sp = new SpeedVector(x - node.x, y - node.y, z - node.z);

		float distance = 2.5f;
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
			moveBy(speed.x, speed.y, speed.z);
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
		fixedX = x;
		fixedY = y;
		fixedZ = z;

	}

	//function for changing the object space coordinates,
	public void moveBy(float dx, float dy, float dz) {

		x = x + (dx);
		y = y + (dy);
		z = z + (dz);

		adjustPosition();
	}

	public void moveTo(float x, float y, float z) {
		this.x = x;
		this.y = y;
		this.z = z;
		adjustPosition();
	}

	public void adjustPosition() {
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
		moveBy(sp.x, sp.y, sp.z);
	}

	public void changeInProjection(int u, int v) {

	}

	synchronized public void proj() {
		relativeBallSize =
			(int) Math.round((double) ballSize * FOV / (-z + cameraZ));
		relativeTextSize =
			(int) Math.round((double) textSize * FOV / (-z + cameraZ));
		if (this.centered()) {
			relativeTextSize = (int)(relativeTextSize * 1.5);
		}

		//diameter reduced to projection
		//System.out.println("ZC"+ZC+"Z"+Z+"b"+b);
		if (relativeBallSize < Config.minimumBallSize)
			relativeBallSize = Config.minimumBallSize;

		//projection for X,and Y of 3d to u,v of 2d;
		int k = (int) (z - cameraZ);
		int ZZ = (int) (z - cameraZ);
		if (Math.abs(ZZ) < 1)
			ZZ = 1;
		u =
			new Float(graph.origin.x + (FOV * (x - graph.origin.x)) / (ZZ))
				.intValue();
		v =
			new Float(graph.origin.y + (FOV * (y - graph.origin.y)) / (ZZ))
				.intValue();

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
		Node neighbour = graph.nodeFromName(nodeName);
		if (neighbour != null) {
			neighbour.links.put(this.name, new Object());
		}
	}

	public void paint(Graphics g) {

		Graphics2D graphic = (Graphics2D) g;

		double zc = z - cameraZ;
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

		AlphaComposite alphaMemory = (AlphaComposite) graphic.getComposite();

		AlphaComposite al =
			AlphaComposite.getInstance(
				AlphaComposite.SRC_OVER,
				(float) (scale));

		graphic.setComposite(al);

		g.setColor(Config.graphstringcolor);
		Set linkSet = links.keySet();

		for (Iterator it = linkSet.iterator(); it.hasNext();) {
			Node neighbour = graph.nodeFromName((String) it.next());
			if (neighbour != null && neighbour.z > z) {
				g.drawLine(u, v, neighbour.u, neighbour.v);
			}
		}

		graphic.setColor(Config.linkballcolor);
		graphic.setComposite(al);
		graphic.fillOval(U, V, relativeBallSize, relativeBallSize);
		graphic.setColor(Config.ballBorderColor);
		graphic.drawOval(U, V, relativeBallSize, relativeBallSize);
		AffineTransform at = new AffineTransform(40, 0, 0, 4, 00, 0);

		FontRenderContext frc = new FontRenderContext(at, true, true);

		Font font =
			new Font(null, Font.PLAIN, (int) (relativeTextSize / 5) * 5);

		TextLayout l = new TextLayout(name, font, frc);
		//(new AttributedString(name)).getIterator(), font, frc);
		Rectangle2D textBounds = l.getBounds();

		l.draw(graphic, (int) (u - textBounds.getWidth() / 2), V);

		graphic.setComposite(alphaMemory);
	}

	public void remove() {
		graph.nodesFromName.remove((String) name);
	}

}
