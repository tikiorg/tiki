/*
 * Morcego - 3D network browser Copyright (C) 2004 Luis Fagundes - Arca
 * <lfagundes@arca.ime.usp.br>
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

package br.arca.morcego;
import java.awt.Color;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.font.FontRenderContext;
import java.awt.font.TextLayout;
import java.awt.geom.AffineTransform;
import java.awt.geom.Rectangle2D;
import java.net.URL;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;

public class Node extends Vertex {

	// relative size of ball and text
	private int ballSize, textSize;

	// this node is focussed
	private boolean focussed = true;

	// Used to check if mouse is over here
	private Rectangle boundRectangle = new Rectangle();

	// Screen coordinates of upper left corner of node
	private int cornerU, cornerV;

	// For fixing node position
	private boolean positionFixed;

	// Current node speed
	private SpeedVector speed = new SpeedVector(1, 1, 1);

	// Linked nodes' names as keys in hash for fast searching,
	// value is not used
	private Hashtable links;

	private boolean isCentered;

	private String name;

	// Graph containing node
	private Graph graph;

	private URL actionUrl;
	private Color color = (Color) Config.getValue("nodeDefaultColor");

	private SpeedVector lastSpeed;

	public Node(String name, Graph graph) {
		super();
		links = new Hashtable();

		this.graph = graph;

		this.name = name;

		this.graph.add(this);

		y = length();
		x = length();
		z = length();

		setBounds();
	}

	// Note for physicians: force and speed are the same thing in this
	// universe and I don't care :-)
	public SpeedVector getForceFromNode(Node node) {

		SpeedVector sp = new SpeedVector(x - node.x, y - node.y, z - node.z);

		int distance =
			((Integer) Config.getValue("linkedNodesDistance")).intValue();
		float module = sp.module();
		int radius = ((Integer) Config.getValue("universeRadius")).intValue();

		sp.resize(1 / module);
		if (this.isLinkedTo(node)) {
			module -= distance;
			if (module > 0) {
				sp.resize(- (float) Math.pow(module / Math.sqrt(radius), 2));
			} else {
				sp.resize((float) Math.pow(module / Math.sqrt(radius), 2));
			}
		} else {
			sp.resize((float) (Math.pow(Math.E, 2 * radius / module)));
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

	public void balance(Balancer balancer) {
		if (centered()) {
			clearSpeed();
			addSpeed(getForceToCenter());
			moveBy(speed.x, speed.y, speed.z);
		} else if (!this.positionFixed()) {
			speed.resize(0.5f);
			if (lastSpeed != null && speed.opposed(lastSpeed)) {
				float module = speed.module();
				float lastModule = lastSpeed.module();
				speed.resize(0.75f * Math.min(lastModule, module) / module);
			}			
			change(speed);
		}

		if (lastSpeed != null && speed.isTooLow() && lastSpeed.isTooLow()) {
			balancer.notifyBalanced(this);
		}
		lastSpeed = new SpeedVector(speed.x, speed.y, speed.z);
	}

	public boolean positionFixed() {
		return positionFixed || centered();
	}

	public void releasePosition() {
		positionFixed = false;

	}

	public void fixPosition() {
		positionFixed = true;
	}

	//function for changing the object space coordinates,
	public void moveBy(float dx, float dy, float dz) {

		x = x + (dx);
		y = y + (dy);
		z = z + (dz);

		setBounds();
		proj();
	}

	public void moveTo(float x, float y, float z) {
		this.x = x;
		this.y = y;
		this.z = z;
		setBounds();
		proj();
	}

	public void change(SpeedVector sp) {		
		if (sp.module()
			> ((Integer) Config.getValue("maxNodeSpeed")).intValue()) {
			sp.resize(
				((Integer) Config.getValue("maxNodeSpeed")).intValue()
					/ sp.module());
		}
		moveBy(sp.x/2, sp.y/2, sp.z/2);
	}

	synchronized public void proj() {
		ballSize =
			(int) Math.round(
				(double) ((Integer) Config.getValue("nodeSize")).intValue()
					* FOV
					/ (-z + ((Integer) Config.getValue("cameraZ")).intValue()));
		textSize =
			(int) Math.round(
				(double) ((Integer) Config.getValue("textSize")).intValue()
					* FOV
					/ (-z + ((Integer) Config.getValue("cameraZ")).intValue()));
		if (this.centered()) {
			textSize = (int) (textSize * 1.5);
		}

		//diameter reduced to projection
		//System.out.println("ZC"+ZC+"Z"+Z+"b"+b);
		if (ballSize < ((Integer) Config.getValue("minBallSize")).intValue())
			ballSize = ((Integer) Config.getValue("minBallSize")).intValue();

		//projection for X,and Y of 3d to u,v of 2d;
		int k = (int) (z - ((Integer) Config.getValue("cameraZ")).intValue());
		int ZZ = (int) (z - ((Integer) Config.getValue("cameraZ")).intValue());
		if (Math.abs(ZZ) < 1)
			ZZ = 1;
		u =
			new Float(
				graph.getOrigin().x + (FOV * (x - graph.getOrigin().x)) / (ZZ))
				.intValue();
		v =
			new Float(
				graph.getOrigin().y + (FOV * (y - graph.getOrigin().y)) / (ZZ))
				.intValue();

		int c = ballSize / 2;

		cornerU = u - c;
		cornerV = v - c;
		boundRectangle = new Rectangle(cornerU, cornerV, ballSize, ballSize);

	}

	public boolean contains(int x, int y) {
		return boundRectangle.contains(x, y);
	}

	public int length() {
		return (int) ((Math.random() * 2 - 1) * 500);
		// Config.universeRadius);
	}

	void setBounds() {
		int radius = ((Integer) Config.getValue("universeRadius")).intValue();
		x = Math.min(Math.max(x, -radius), radius);
		y = Math.min(Math.max(y, -radius), radius);
		z = Math.min(Math.max(z, -radius), radius);
	}

	public void addLink(String nodeName) {
		links.put(nodeName, new Object());
		Node neighbour = graph.nodeFromName(nodeName);
		if (neighbour != null) {
			neighbour.links.put(this.name, new Object());
		}
	}

	public Color fadeColor(Color color, float scale) {
		int red =
			(int) (scale * color.getRed()
				+ (1 - scale)
					* ((Color) Config.getValue("backgroundColor")).getRed());
		int green =
			(int) (scale * color.getGreen()
				+ (1 - scale)
					* ((Color) Config.getValue("backgroundColor")).getGreen());
		int blue =
			(int) (scale * color.getBlue()
				+ (1 - scale)
					* ((Color) Config.getValue("backgroundColor")).getBlue());
		return new Color(red, green, blue);
	}

	public void paint(Graphics g) {

		Graphics2D graphic = (Graphics2D) g;

		float zc = z - ((Integer) Config.getValue("cameraZ")).intValue();
		float scale;
		if (Math.abs(zc) > 1)
			scale = Math.abs(FOV * 1 / zc);
		else
			scale = 1;
		scale *= 1.5;

		if (scale > 1)
			scale = 1;
		if (scale < 0.1)
			scale = 0.1f;

		g.setColor(fadeColor(((Color) Config.getValue("linkColor")), scale));
		Set linkSet = links.keySet();

		for (Iterator it = linkSet.iterator(); it.hasNext();) {
			Node neighbour = graph.nodeFromName((String) it.next());
			if (neighbour != null && neighbour.z > z) {
				g.drawLine(u, v, neighbour.u, neighbour.v);
			}
		}

		graphic.setColor(fadeColor(color, scale));

		graphic.fillOval(cornerU, cornerV, ballSize, ballSize);
		graphic.setColor(
			fadeColor(((Color) Config.getValue("ballBorderColor")), scale));
		graphic.drawOval(cornerU, cornerV, ballSize, ballSize);
		AffineTransform at = new AffineTransform(40, 0, 0, 4, 00, 0);

		FontRenderContext frc = new FontRenderContext(at, false, false);

		int interval =
			((Integer) Config.getValue("fontSizeInterval")).intValue();
		Font font =
			new Font(null, Font.PLAIN, (int) (textSize / interval) * interval);

		TextLayout l = new TextLayout(name, font, frc);

		Rectangle2D textBounds = l.getBounds();

		l.draw(graphic, (int) (u - textBounds.getWidth() / 2), cornerV);
	}

	public void setGraph(Graph g) {
		graph = g;
	}

	/**
	 * @return Returns the graph.
	 */
	public Graph getGraph() {
		return graph;
	}

	/**
	 * @return Returns the name.
	 */
	public String getName() {
		return name;
	}

	/**
	 * @return Returns the actionUrl.
	 */
	public URL getActionUrl() {
		return actionUrl;
	}

	/**
	 * @param actionUrl
	 *                  The actionUrl to set.
	 */
	public void setActionUrl(URL actionUrl) {
		this.actionUrl = actionUrl;
	}

	/**
	 * @param color
	 *                  The color to set.
	 */
	public void setColor(Color color) {
		this.color = color;
	}

	/**
	 * @return Returns the speed.
	 */
	public SpeedVector getSpeed() {
		return speed;
	}

	/**
	 *  
	 */
	public void awake() {
		lastSpeed = null;
	}

	/**
	 * @return Returns the lastSpeed.
	 */
	public SpeedVector getLastSpeed() {
		return lastSpeed;
	}

	public Enumeration getLinkList() {
		return links.keys();
	}

}