package wiki3d;
import java.applet.Applet;
import java.awt.BasicStroke;
import java.awt.Cursor;
import java.awt.Dimension;
import java.awt.Frame;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Rectangle;
import java.awt.RenderingHints;
import java.awt.Toolkit;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.event.MouseMotionListener;
import java.awt.image.BufferedImage;
public class Can3d
	extends Applet
	implements MouseListener, MouseMotionListener {
	boolean isStandalone = false;
	boolean painted = true;
	Thread t;
	float xfac;
	BalanceThread balancing;
	boolean focussed = false;
	Face face;
	ObjectVertex ob;
	Cursor cur;
	boolean rotate = true; //used to track if in rotation mode or
	// not
	Rectangle r = new Rectangle(); //
	String ps;
	Matrix3D imat = new Matrix3D();
	Matrix3D amat = new Matrix3D();
	//matrix used to acculumate rotations and use for transforming the face.
	Matrix3D tmat = new Matrix3D();
	//temporary matrix used to hold the present rotation of the object.
	Matrix3D mmat = new Matrix3D();
	//temporary to hold present translation in x,y,z direction
	Matrix3D tmmat = new Matrix3D();
	//matrix used to accumulate movement of the graph like amat for the face
	Camera c;
	boolean animate = true;
	int prevx, prevy;
	float xtheta, ytheta; //the angle of the ratation for each
	// mouseevent
	float scalefudge = 1;
	BufferedImage bi;
	int XO, YO;
	//Graph cv;
	Graph graph;
	//stores all the nodes links actions which are latter used for display
	XmlReader xr;
	Graphics2D bg;
	Dimension bd;

	String url = null;
	String message = null;
	
	String startNodeName = "Krico";
	
	int clickX, clickY; // Position where the user clicked;

	//Construct the applet

	public Can3d() {
		cur = new Cursor(Cursor.HAND_CURSOR);
		Node.origin =
			new Vertex(Config.originx, Config.originy, Config.originz);
		//origin for the graph
		ob =
			new ObjectVertex(Config.facesize, Config.facesize, Config.facesize);

		ob.setOrigin(Config.faceoriginx, Config.faceoriginy, 0);

		ObjectVertex.setCamera(
			Config.faceoriginx,
			Config.faceoriginy,
			Config.camposz);

		// ObjectVertex.origin=new Vertex(400,450,0);

		graph = new Graph();

		ps =
			"<graph node=\"My Graph\"><link name=\"vt\"><action label=\"heard about that\" url=\"www.visualthearus.com\"/><action label=\"my thearasus\" url= \"www.myhome.com\"/></link><link name=\"hi click me for fun\"><action label=\"The last action\" url=\"www.thelast.com\"/></link><link name=\"see these also\"><action label=\"Me first\" url=\"www.greaturl.com\"/><action label=\"Me second\" url=\"www.greaturl.com\"/></link></graph>";

		face = new Face(Config.facesize * 2);

		tmat.xrot(Config.initrotx); //initial rotation of the face
		// and graph

		tmat.yrot(Config.initroty);

		amat.mult(tmat); //accumulated in face

		mmat.mult(tmat); //accumulated in graph

		Node.setFOV(Config.fieldOfView);

		ObjectVertex.setFOV(Config.fieldOfView);

		Camera.ZC = Config.camposz;

		Camera.XC = Config.faceoriginx;

		Camera.YC = Config.faceoriginy;

		r.setBounds(
			ObjectVertex.origin.x - Config.faceadjust,
			ObjectVertex.origin.y - Config.faceadjust,
			Config.controlwidth,
			Config.controlwidth);

	}

	public void init() {
		try {
			jbInit();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	private void jbInit() throws Exception {
		this.setSize(Config.windowwidth, Config.windowheight);
		try {
			url = getParameter("url"); //reads from
			// http://vt.php?node=someword
			startNodeName = getParameter("node");
			//url = url + "?page=" + startNodeName;

		} catch (Exception e) {
			url = "http://c3po.kriconet.com.br/tiki-dev/tikiwiki/tiki-wiki3d_xml.php";
		};

		resize(
			getSize().width <= 20 ? Config.windowwidth : getSize().width,
			getSize().height <= 20 ? Config.windowheight : getSize().height);
		addMouseListener(this);
		addMouseMotionListener(this);

	}
	//Start the applet

	public void start() {

		xr = new XmlReader(url);
		graph.setXmlReader(xr);
		xr.getNodeData(startNodeName,graph);
		graph.navigateTo(Graph.centerNode);
		
		bi =
			new BufferedImage(
				Config.windowwidth,
				Config.windowheight,
				BufferedImage.TYPE_INT_RGB);
		bg = (Graphics2D) bi.getGraphics();
		bg.setClip(
			Config.viewstartx,
			Config.viewstarty,
			Config.viewwidth,
			Config.viewheight);
		balancing = new BalanceThread(this);
		Thread t = new Thread(balancing);
		t.start();

		//CanvaxVertex.setanimator(a);

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

		if (graph.contains(e.getX(), e.getY())) {
			graph.focus = graph.focus;

			/*
			 * This code is here as an example on how to open an url and how to
			 * catch a node click, to be used later if (vertexes.focus.type() ==
			 * 'a') { this.getAppletContext().showDocument( ((Action)
			 * vertexes.focus).getURL(), ((Action) vertexes.focus).getLabel()); }
			 * else { // here goes navigation through nodes, later }
			 * System.out.println("mouse clicked");
			 *  
			 */

		}
	}

	public void mousePressed(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();
		System.out.println("mouse pressed");

		if (graph.contains(x, y)) {
			setCursor(cur);
			graph.focus.fixPosition();
			focussed = true;
			clickX = x;
			clickY = y;
		}
		prevx = x;
		prevy = y;
		e.consume();
	}

	public void mouseMoved(MouseEvent e) {

		int x = e.getX();
		int y = e.getY();
		if (graph.contains(x, y)) {
			setCursor(cur);
			if (painted) {
				painted = false;
			}
		} else {
			setCursor(Cursor.getDefaultCursor());
		}

		prevx = x;
		prevy = y;
		e.consume();

	}

	public void mouseReleased(MouseEvent e) {
        if (e.getX() == clickX && e.getY() == clickY) {
        	graph.navigateTo(graph.focus);
        }
		if (focussed) {
			focussed = false;
			graph.focus.releasePosition();
		}
		setCursor(Cursor.getDefaultCursor());
		if (painted) {
			painted = false;
			repaint();
		}

	}

	public void mouseEntered(MouseEvent e) {

		if (painted) {
			painted = false;
			repaint();

		}

	}

	public void mouseExited(MouseEvent e) {
		//a.animate();
	}

	public void mouseDragged(MouseEvent e) {
		
		float scalex = Config.scalex;
		float scaley = Config.scaley;
		int x = e.getX();
		int y = e.getY();
		if (!focussed) {
			float xtheta = (prevy - y) * 360.0f / getSize().width * scalex;
			float ytheta = (x - prevx) * 360.0f / getSize().height * scaley;

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

			tmat.unit();
			tmat.xrot(-xtheta);
			tmat.yrot(-ytheta);
			amat.mult(tmat);
			tmat.unit();
			tmat.xrot(-xtheta / Config.gearing);
			tmat.yrot(-ytheta / Config.gearing);

			tmmat.mult(tmat);
			if (painted) {
				painted = false;
				//setCursor(Cursor.getDefaultCursor());
				//   focussed=false;
				repaint();

			}
			prevx = x;
			prevy = y;
			e.consume();

		} else {
			int dy = y - prevy;
			int dx = x - prevx;

			if (e.isAltDown()) {
				Node.mat.translate(0, 0, dx + dy);
			} else
				graph.focus.change(-dx, -dy, 0);

			if (painted) {
				painted = false;
				repaint();
			}
			prevx = x;
			prevy = y;
			e.consume();

		}
	}
	private synchronized void setPainted() {
		painted = true;
		notifyAll();
	}
	private synchronized void waitPainted() {
		while (!painted) {
			try {
				wait();
			} catch (InterruptedException e) {
			}
		}
		painted = false;
	}

	public void paint(Graphics g1) {
		Graphics2D g = (Graphics2D) g1;
		g.setClip(
			Config.viewstartx,
			Config.viewstarty,
			Config.viewwidth,
			Config.viewheight);
		if (face != null) {

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
			if (!focussed) {
				graph.transform(tmmat);
				face.transform(amat);
			} else {
				graph.focus.transform();
				graph.focus.proj();
				//cv.focus.transform();
				//cv.focus.transform(imat);
				imat.unit();
			}

			if (bi != null) {
				bg.setStroke(new BasicStroke(1.0f));

				bg.setColor(getBackground());
				bg.fillRect(0, 0, getSize().width, getSize().height);
				graph.paint(bg);
				//if(rotate)
				face.paint(bg);

				bg.draw3DRect(
					ObjectVertex.origin.x - Config.faceadjust,
					ObjectVertex.origin.y - Config.faceadjust,
					Config.controlwidth,
					Config.controlwidth,
					true);
				bg.setColor(Config.facecolorblue);
				bg.setColor(Config.facecolorblack);

				g.drawImage(bi, 0, 0, this);
			} else {
				graph.paint(g);
				face.paint(g);
				g.setColor(Config.facecolorblue);
				g.draw3DRect(
					ObjectVertex.origin.x - Config.faceadjust,
					ObjectVertex.origin.y - Config.faceadjust,
					Config.controlwidth,
					Config.controlwidth,
					true);

			}

			setPainted();

		}
	}
	synchronized public boolean painted() {
		return painted;

	}

	public void update(Graphics g) {
		if (bi == null)
			g.clearRect(0, 0, getSize().width, getSize().height);
		paint(g);
	}

	//Get Applet information

	public String getAppletInfo() {
		return "Applet Information";
	}
	//Get parameter info

	public String[][] getParameterInfo() {
		return null;
	}
	//Main method

	public static void main(String[] args) {
		Can3d applet = new Can3d();
		applet.isStandalone = true;
		Frame frame = new Frame();
		frame.setTitle("Morcego");
		frame.add(applet);
		applet.init();
		applet.start();
		frame.setSize(Config.windowwidth, Config.windowheight);
		Dimension d = Toolkit.getDefaultToolkit().getScreenSize();
		frame.setLocation(
			(d.width - frame.getSize().width) / 2,
			(d.height - frame.getSize().height) / 2);
		frame.setVisible(true);
	}
}
