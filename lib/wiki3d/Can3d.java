package wiki3d;
import java.awt.*;
import java.awt.event.*;
import java.applet.*;
import java.awt.image.*;

import java.io.*;
import java.net.*;
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
	Vertexes vertexes;
	//stores all the nodes links actions which are latter used for display
	XmlReader xr;
	Graphics2D bg;
	Dimension bd;

	String ips = null;
	String message = null;

	//Construct the applet

	public Can3d() {
		cur = new Cursor(Cursor.HAND_CURSOR);
		CanvaxVertex.origin =
			new Vertex(Config.originx, Config.originy, Config.originz);
		//origin for the graph
		ob =
			new ObjectVertex(Config.facesize, Config.facesize, Config.facesize);

		ob.setOrigin(Config.faceoriginx, Config.faceoriginy, 0);

		ObjectVertex.setCamera(Config.faceoriginx, Config.faceoriginy, Config.camposz);

		// ObjectVertex.origin=new Vertex(400,450,0);

		vertexes = new Vertexes();

		ps =
			"<graph node=\"My Graph\"><link name=\"vt\"><action label=\"heard about that\" url=\"www.visualthearus.com\"/><action label=\"my thearasus\" url= \"www.myhome.com\"/></link><link name=\"hi click me for fun\"><action label=\"The last action\" url=\"www.thelast.com\"/></link><link name=\"see these also\"><action label=\"Me first\" url=\"www.greaturl.com\"/><action label=\"Me second\" url=\"www.greaturl.com\"/></link></graph>";

		face = new Face(Config.facesize * 2);

		tmat.xrot(Config.initrotx); //initial rotation of the face
												// and graph

		tmat.yrot(Config.initroty);

		amat.mult(tmat); //accumulated in face

		mmat.mult(tmat); //accumulated in graph

		CanvaxVertex.setFOV(Config.fieldOfView);

		ObjectVertex.setFOV(Config.fieldOfView);

		Camera.ZC = Config.camposz;

		Camera.XC = Config.faceoriginx;

		Camera.YC = Config.faceoriginy;

		r.setBounds(
			ObjectVertex.origin.x - Config.faceadjust,
			ObjectVertex.origin.y - Config.faceadjust,
			Config.controlwidth,
			Config.controlwidth);

		//  xr=new XmlReader(ps,cv);

	}
	//Initialize the applet

	public void init() {
		try {
			jbInit();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	//static {
	//  try {
	//    //UIManager.setLookAndFeel(new
	// com.sun.java.swing.plaf.metal.MetalLookAndFeel());
	//    //UIManager.setLookAndFeel(new
	// com.sun.java.swing.plaf.motif.MotifLookAndFeel());
	//    UIManager.setLookAndFeel(new
	// com.sun.java.swing.plaf.windows.WindowsLookAndFeel());
	//  }
	//  catch (Exception e) {}
	//}
	//Component initialization

	private void jbInit() throws Exception {
		this.setSize(Config.windowwidth, Config.windowheight);
		try {
			ips = getParameter("url"); //reads from
													// http://vt.php?node=someword
			String node = getParameter("node");
			ips = ips + "?page=" + node;

		} catch (Exception e) {
		};

		resize(
			getSize().width <= 20 ? Config.windowwidth : getSize().width,
			getSize().height <= 20 ? Config.windowheight : getSize().height);
		addMouseListener(this);
		addMouseMotionListener(this);

	}
	//Start the applet

	public void start() {
		URL u;
		StringBuffer st = new StringBuffer();
		if (ips != null) {

			try {
				String ps = " ";
				u = new URL(ips);
				DataInputStream b1 = new DataInputStream(u.openStream());
				int j;
				while (true) {

					j = b1.read();
					if (j == -1)
						break;
					System.out.print((char) j);
					st.append((char) j);

				}
			} catch (Exception ee) {
				System.out.println("other exception");
			}
			ps = st.toString();
		}

		xr = new XmlReader(ps, vertexes);
		vertexes.initpos();
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
		
		if (vertexes.contains(e.getX(), e.getY())) {
			vertexes.focus = vertexes.focus.getElement();
			if (vertexes.focus.type() == 'a') {
				this.getAppletContext().showDocument(
					((Action) vertexes.focus).getURL(),
					((Action) vertexes.focus).getLabel());

			} else {
			   // here goes navigation through nodes, later
			}
			System.out.println("mouse clicked");
			
			

		}
	}

	public void mousePressed(MouseEvent e) {
		int x = e.getX();
		int y = e.getY();
		System.out.println("mouse pressed");
		
		if (vertexes.contains(x, y)) 
			{
			setCursor(cur);
			vertexes.focus.fixPosition();
			focussed = true;
		}
		prevx = x;
		prevy = y;
		e.consume();
	}
	
	public void mouseMoved(MouseEvent e) {

		int x = e.getX();
		int y = e.getY();
		if (vertexes.contains(x, y)) {
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

		if (focussed) {
			focussed = false;
			vertexes.focus.releasePosition();
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
		balancing.stopanimate();
		//notifyAll();

		//EventQueue aq=Toolkit.getDefaultToolkit().getSystemEventQueue();
		//try{
		//while(aq.peekEvent()!=null)
		//aq.getNextEvent();
		//}catch(Exception exe){};
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

		} else 
			{
			int dy = y - prevy;
			int dx = x - prevx;
			
			if (e.isAltDown()) {
				CanvaxVertex.mat.translate(0, 0, dx + dy);
			} else
				vertexes.focus.change(-dx, -dy, 0);
			
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
				vertexes.transform(tmmat);				
				face.transform(amat);
			} else {
				vertexes.focus.transform();
				vertexes.focus.proj();
				//cv.focus.transform();
				//cv.focus.transform(imat);
				imat.unit();
			}
			try {
				if (bi != null) {
					bg.setStroke(new BasicStroke(1.0f));

					bg.setColor(getBackground());
					bg.fillRect(0, 0, getSize().width, getSize().height);
					vertexes.paint(bg);
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
					vertexes.paint(g);
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

			} catch (NullPointerException eb) {
				System.out.println("ee");
			}

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
