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

import java.applet.Applet;
import java.awt.Color;
import java.awt.Cursor;
import java.awt.Dimension;
import java.awt.Frame;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.RenderingHints;
import java.awt.Toolkit;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.event.MouseMotionListener;
import java.awt.image.BufferedImage;
import java.net.URL;
import java.util.Enumeration;

public class Morcego
	extends Applet
	implements MouseListener, MouseMotionListener {

	private boolean isStandalone = false;
	private boolean focussed = false;
	private Cursor handCursor;

	private int clickX, clickY; // Position where the user clicked;
	private int previousX, previousY; // Previous mouse
	// coordinates
	private int rotateSpeedX, // Rotation speed for spinning
	rotateSpeedY;

	private BufferedImage bi;
	private Graphics2D bg;

	private Graph graph;
	private Feeder feeder;
	private Balancer balancer;
	private Renderer renderer;

	public Morcego() {
		handCursor = new Cursor(Cursor.HAND_CURSOR);
	}

	public void init() {

		initConfig();

		this.setSize(
			((Integer) Config.getValue("windowWidth")).intValue(),
			((Integer) Config.getValue("windowHeight")).intValue());

		resize(
			((Integer) Config.getValue("windowWidth")).intValue(),
			((Integer) Config.getValue("windowHeight")).intValue());

		addMouseListener(this);
		addMouseMotionListener(this);
	}

	public void initConfig() {
		Config.init();

		Enumeration vars = Config.listConfigVars();
		while (vars.hasMoreElements()) {
			String varName = (String) vars.nextElement();

			String value = getParameter(varName);

			if (value != null) {

				Class type = Config.getValue(varName).getClass();

				try {
					if (type.equals(Integer.class)) {
						Config.setValue(varName, Integer.valueOf(value));
					} else if (type.equals(Float.class)) {
						Config.setValue(varName, Float.valueOf(value));
					} else if (type.equals(String.class)) {
						Config.setValue(varName, value);
					} else if (type.equals(Color.class)) {
						Config.setValue(varName, Color.decode(value));
					}
				} catch (Exception e) {
					// Ignore malformed parameters
				}
			}
		}
	}

	public void start() {
		bi =
			new BufferedImage(
				((Integer) Config.getValue("windowWidth")).intValue(),
				((Integer) Config.getValue("windowHeight")).intValue(),
				BufferedImage.TYPE_INT_RGB);

		bg = (Graphics2D) bi.getGraphics();

		bg.setClip(
			((Integer) Config.getValue("viewStartX")).intValue(),
			((Integer) Config.getValue("viewStartY")).intValue(),
			((Integer) Config.getValue("viewWidth")).intValue(),
			((Integer) Config.getValue("viewHeight")).intValue());

		GraphDataRetriever retriever =
			new GraphDataRetriever((String) Config.getValue("serverUrl"));

		Graph firstGraph = null;
		try {
			firstGraph =
				retriever.retrieveData((String) Config.getValue("startNode"));
		} catch (Exception e) {
			e.printStackTrace();
			return;
		}
		graph = new Graph();

		Node centerNode =
			firstGraph.nodeFromName((String) Config.getValue("startNode"));
		graph.add(centerNode);
		graph.navigateTo(centerNode);

		feeder = new Feeder(graph, retriever);

		//graph.navigateTo(graph.nodeFromName(startNodeName));

		renderer = new Renderer(this);
		balancer = new Balancer(graph, renderer);
		Spinner spinner = new Spinner(graph,renderer);

		Thread feedingThread = new Thread(feeder);
		Thread balanceThread = new Thread(balancer);
		Thread renderingThread = new Thread(renderer);
		Thread spinningThread = new Thread(spinner);

		balanceThread.start();
		feedingThread.start();
		renderingThread.start();
		spinningThread.start();

		feeder.feed(firstGraph);
	}

	//Stop the applet

	public void stop() {
	}
	//Destroy the applet

	public void destroy() {
		removeMouseListener(this);
		removeMouseMotionListener(this);
	}

	public void mouseClicked(MouseEvent e) {
		graph.setRotating(false);
		if (graph.contains(e.getX(), e.getY())) {
			if (!graph.getFocus().centered()) {
				graph.navigateTo(graph.getFocus());
				balancer.awake();
				synchronized (feeder) {
					feeder.notify();
				}
			} else {
				URL url = graph.getFocus().getActionUrl();
				if (url != null) {
					getAppletContext().showDocument(
						url,
						(String) Config.getValue("controlWindowName"));

				}
			}
		}
	}

	public void mousePressed(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();

		graph.setRotating(false);

		if (graph.contains(x, y)) {
			balancer.awake();
			balancer.lockBalance();
			setCursor(handCursor);
			graph.getFocus().fixPosition();
			focussed = true;
			clickX = x;
			clickY = y;
		}
		previousX = x;
		previousY = y;
		e.consume();
	}

	public void mouseMoved(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();
		if (graph.contains(x, y)) {
			setCursor(handCursor);
		} else {
			setCursor(Cursor.getDefaultCursor());
		}

		previousX = x;
		previousY = y;
		e.consume();
	}

	public void mouseReleased(MouseEvent e) {
		if (focussed) {
			focussed = false;
			graph.getFocus().releasePosition();
			balancer.awake();
			balancer.unlockBalance();
		} else {
			if (Math.abs(rotateSpeedX) + Math.abs(rotateSpeedY) > 4) {
				graph.setRotating(true);
				graph.rotate(rotateSpeedX / 5, rotateSpeedY / 5);
			}
		}
		setCursor(Cursor.getDefaultCursor());
	}

	public void mouseEntered(MouseEvent e) {

	}

	public void mouseExited(MouseEvent e) {
	}

	public void mouseDragged(MouseEvent e) {

		int x = e.getX();
		int y = e.getY();

		if (!focussed) {

			rotateSpeedX = x - previousX;
			rotateSpeedY = y - previousY;

			graph.rotate(rotateSpeedX, rotateSpeedY);
			synchronized (renderer) {
				renderer.notify();
			}
			
			previousX = x;
			previousY = y;
		} else {
			int dx = x - previousX;
			int dy = y - previousY;
			previousX = x;
			previousY = y;
			// TODO: map screen to graph so node keeps under mouse
			graph.getFocus().moveBy((float) - 2 * dx, (float) - 2 * dy, 0);
			synchronized (renderer) {
				renderer.notify();
			}
		}

		e.consume();
	}

	public void paint(Graphics g1) {

		Graphics2D g = (Graphics2D) g1;

		g.setClip(
			((Integer) Config.getValue("viewStartX")).intValue(),
			((Integer) Config.getValue("viewStartY")).intValue(),
			((Integer) Config.getValue("viewWidth")).intValue(),
			((Integer) Config.getValue("viewHeight")).intValue());

		g.setBackground((Color) Config.getValue("backgroundColor"));

		try {

			RenderingHints qualityHints =
				new RenderingHints(
					RenderingHints.KEY_ANTIALIASING,
					RenderingHints.VALUE_ANTIALIAS_ON);

			qualityHints.put(
				RenderingHints.KEY_RENDERING,
				RenderingHints.VALUE_RENDER_QUALITY);

			qualityHints.put(
				RenderingHints.KEY_RENDERING,
				RenderingHints.VALUE_RENDER_QUALITY);

			g.setRenderingHints(qualityHints);
			if (bg != null)
				bg.setRenderingHints(qualityHints);

		} catch (NullPointerException ne) {
			System.out.println("ne1");
		}

		if (bi != null) {
			//bg.setStroke(new BasicStroke(1.0f));

			bg.setColor((Color) Config.getValue("backgroundColor"));
			bg.fillRect(0, 0, getSize().width, getSize().height);

			graph.paint(bg);

			g.drawImage(bi, 0, 0, this);

		}
	}

	public void update(Graphics g) {
		if (bi == null)
			g.clearRect(0, 0, getSize().width, getSize().height);
		paint(g);
	}

	//Get Applet information

	public String getAppletInfo() {
		return "Morcego rulez!";
	}
	//Get parameter info

	public String[][] getParameterInfo() {
		return null;
	}
	//Main method

	public static void main(String[] args) {
		Morcego applet = new Morcego();
		applet.isStandalone = true;
		Frame frame = new Frame();
		frame.setTitle("Morcego");
		frame.add(applet);
		applet.init();
		applet.start();
		//frame.setSize(Config.windowwidth, Config.windowheight);
		Dimension d = Toolkit.getDefaultToolkit().getScreenSize();
		frame.setLocation(
			(d.width - frame.getSize().width) / 2,
			(d.height - frame.getSize().height) / 2);
		frame.setVisible(true);
	}
	/**
	 * @return Returns the graph.
	 */
	public Graph getGraph() {
		return graph;
	}

}
