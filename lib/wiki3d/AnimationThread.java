package wiki3d;
import java.util.*;
class AnimationThread implements Runnable {
	boolean animating = false;

	Vector v = new Vector();
	Can3d c;
	AnimationThread(Can3d c) {
		//v=ver;
		this.c = c;

	}
	public void stopanimate() {

		animating = false;
		//notify();
	}
	public void animate() {
		animating = true;

	}
	public void add(CanvaxVertex vr) {

		v.addElement(vr);
		vr.setanimator(this);
	}
	public void remove(CanvaxVertex vr) {

		v.removeElement(vr);

	}

	public void run() {

		Thread.currentThread().setPriority(Thread.MIN_PRIORITY);

		int i = 0;

		try {

			while (true) {

				Enumeration e;
				if (i++ % 5 == 0) {
					
					e = this.c.cv.elements();
					while (e.hasMoreElements()) {
						CanvaxVertex node = (CanvaxVertex) e.nextElement();
						node.clearSpeed();
					}

					for (int j = 0; j < this.c.cv.count; j++) {
						CanvaxVertex node1 =
							(CanvaxVertex) this.c.cv.elementAt(j);
						for (int k = j + 1; k < this.c.cv.count; k++) {
							CanvaxVertex node2 =
								(CanvaxVertex) this.c.cv.elementAt(k);
							SpeedVector sp = node1.getForceFromNode(node2);
							node1.addSpeed(sp);
							node2.addSpeed(sp.reverse());
						}
					}
				}
				Enumeration en = this.c.cv.elements();
				while (en.hasMoreElements()) {
					CanvaxVertex node = (CanvaxVertex) en.nextElement();
					node.balance();
				}

				animating = false;
				if (animating) {
					//Enumeration e;
					e = v.elements();

					if (i++ > 300) {
						animating = false;
						i = 0;
					}
					int count = 0;
					while (e.hasMoreElements()) {
						count++;
						((CanvaxVertex) e.nextElement()).tracepath();

						/*
						 * //System.out.println("done"); 10; 10; 10;
						 * 
						 * 
						 * 360.0f / getSize().width*scalex; 360.0f /
						 * getSize().height*scaley; c.tmat.unit();
						 * c.tmat.xrot(-xtheta); c.tmat.yrot(-ytheta);
						 * c.amat.mult(c.tmat); c.tmat.unit();
						 * c.tmat.xrot(-xtheta/10); c.tmat.yrot(-ytheta/10);
						 * c.tmmat.mult(c.tmat);
						 */

					}
					//if(count==0)
					//break;
					if (c.painted) {
						c.painted = false;
						c.repaint();
						//c.painted=false;
					}
					//}
				}

				/* e = c.cv.elements();
				while (e.hasMoreElements()) {
					CanvaxVertex v = (CanvaxVertex) e.nextElement();
					if (v.type() == 'v') {
						v.change(10, -10, 10);
					}

				} */

				Thread.currentThread().sleep(40);
				this.c.repaint();

			}
		} catch (InterruptedException e) {
		}

	}
}
