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
import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Container;
import java.awt.Cursor;
import java.awt.Dimension;
import java.awt.Frame;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Point;
import java.awt.RenderingHints;
import java.awt.Toolkit;
import java.awt.event.MouseEvent;
import java.awt.image.BufferedImage;
import java.net.URL;
import java.util.Enumeration;

import br.arca.morcego.component.LocalImage;
import br.arca.morcego.physics.Camera;
import br.arca.morcego.run.Renderer;
import br.arca.morcego.structure.Graph;

public class Morcego extends Applet {

	private boolean isStandalone = false;
	private Cursor handCursor;

	private BufferedImage bi;
	private Graphics2D bg;
	private Frame rootFrame;

	private Graph graph;
	private Renderer renderer;
	private static Camera camera;
	
	//TODO create 2D vertex type
	private static Point origin;

	private static Morcego application;
	
	private Container container;

	public Morcego() {
		handCursor = new Cursor(Cursor.HAND_CURSOR);
	}

	/**
	 * 
	 */
	public static void setUp() {
		setCamera(new Camera());

		int originX = Config.getInteger(Config.viewStartX)
				+ (Config.getInteger(Config.viewWidth) / 2);
		int originY = Config.getInteger(Config.viewStartY)
			+ (Config.getInteger(Config.viewHeight) / 2);

		setOrigin(new Point(originX, originY));
	}

	
	public void init() {

		application = this;

		initConfig();

		this.setSize(
			Config.getInteger(Config.windowWidth),
			Config.getInteger(Config.windowHeight));

		resize(Config.getInteger(Config.windowWidth),
				Config.getInteger(Config.windowHeight));
	}

	public void initConfig() {
		Config.init();
		
		if (!isStandalone) {
	
			Enumeration vars = Config.listConfigVars();
			while (vars.hasMoreElements()) {
				String varName = (String) vars.nextElement();
				
				//backward compatible with applet params
				String appletVarName = varName.substring(8, varName.length());
	
				String value = getParameter(appletVarName);
				
				if ( value == null ) {
					value = getParameter(varName);
				}
	
				if (value != null) {
	
					Class type = Config.getValue(varName).getClass();
	
					try {
						Config.setValue(varName, Config.decode(value, type));
					} catch (Exception e) {
						// Ignore malformed parameters
					}
				}
			}
		}
	}

	public void start() {		
		bi =
			new BufferedImage(
					Config.getInteger(Config.windowWidth),
					Config.getInteger(Config.windowHeight),
				BufferedImage.TYPE_INT_RGB);

		bg = bi.createGraphics();

		bg.setClip(
			Config.getInteger(Config.viewStartX),
			Config.getInteger(Config.viewStartY),
			Config.getInteger(Config.viewWidth),
			Config.getInteger(Config.viewHeight));
		

		
		setUp();
		
		graph = new Graph();
		(new Thread(graph)).start();

		renderer = new Renderer(this);
		(new Thread(renderer)).start();
		
		container = new Container();
		
		container.setLayout(new BorderLayout());

		if (Config.getBoolean(Config.showMorcegoLogo)) {
			int logoX = Config.getInteger(Config.logoX);
			int logoY = Config.getInteger(Config.logoY);
			LocalImage logo = new LocalImage("Logo.jpg", logoX, logoY);
			logo.setVisible(true);
			addMouseListener(logo);
			addMouseMotionListener(logo);
			container.add(logo,"Center");
		}
		
		if (Config.getBoolean(Config.showArcaLogo)) {
			int logoX = Config.getInteger(Config.arcaX);
			int logoY = Config.getInteger(Config.arcaY);
			LocalImage arca = new LocalImage("Arca.jpg", logoX, logoY);
			arca.setVisible(true);
			addMouseListener(arca);
			addMouseMotionListener(arca);
			container.add(arca,"Center");
		}
		
		graph.setVisible(true);
		addMouseListener(graph);
		addMouseMotionListener(graph);
		container.add(graph,"Center");
		
		container.setVisible(true);
		container.addNotify();

	}

	/**
	 * @param point
	 */
	public static void setOrigin(Point point) {
		origin = point;
	}

	//Stop the applet

	public void stop() {
	}
	//Destroy the applet
	
	public Component add(Component c) {
		container.add(c);
		return c;
	}
	
	public void remove(Component c) {
		container.remove(c);
	}

	public void destroy() {
		removeMouseListener(graph);
		removeMouseMotionListener(graph);
	}

	public void mouseClicked(MouseEvent e) {
		graph.mouseClicked(e);
		e.consume();
	}

	public void mousePressed(MouseEvent e) {
		graph.mousePressed(e);
		e.consume();
	}

	public void mouseMoved(MouseEvent e) {
		graph.mouseMoved(e);
		e.consume();
	}

	public void mouseReleased(MouseEvent e) {
		graph.mouseReleased(e);
		e.consume();
	}

	public void mouseEntered(MouseEvent e) {
		graph.mouseEntered(e);
		e.consume();
	}

	public void mouseExited(MouseEvent e) {
		graph.mouseExited(e);
		e.consume();
	}

	public void mouseDragged(MouseEvent e) {
		graph.mouseDragged(e);
		e.consume();
	}

	public void paint(Graphics g1) {
		super.paint(g1);

		Graphics2D g = (Graphics2D) g1;

		g.setClip(
			Config.getInteger(Config.viewStartX),
			Config.getInteger(Config.viewStartY),
			Config.getInteger(Config.viewWidth),
			Config.getInteger(Config.viewHeight));

		g.setBackground(Config.getColor(Config.backgroundColor));

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

			bg.setColor(Config.getColor(Config.backgroundColor));
			bg.fillRect(0, 0, getSize().width, getSize().height);


			
			synchronized (container) {
				for (int i=0; i < container.getComponentCount(); i++) {
					Component c = container.getComponent(i);
					c.paint(bg);
				}
			}

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
		int width = Config.getInteger(Config.windowWidth);
		int height = Config.getInteger(Config.windowHeight);
		frame.setSize(width, height);
		Dimension d = Toolkit.getDefaultToolkit().getScreenSize();
		frame.setLocation(
			(d.width - frame.getSize().width) / 2,
			(d.height - frame.getSize().height) / 2);
		frame.setVisible(true);
	}

	public static void notifyRenderer() {
		synchronized (application.renderer) {
			application.renderer.render();
		}
	}
	
	public static Morcego getApplication() {
		return application;
	}
	
	

	/**
	 * @param url
	 * @param string
	 */
	public static void showDocument(URL url, String string) {
		application.getAppletContext().showDocument(url, string);
	}

	public static void setHandCursor() {
		application.setCursor(application.handCursor);
	}

	public static void setDefaultCursor() {
		application.setCursor(Cursor.getDefaultCursor());
	}
	
	public static Camera getCamera() {
		return camera;
	}
	
	public static void setCamera(Camera cam) {
		camera = cam;
	}
	
	/**
	 * @return Returns the origin.
	 */
	public static Point getOrigin() {
		return origin;
	}



}
