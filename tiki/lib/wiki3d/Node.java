package wiki3d;
import java.util.*;
import java.awt.*;
import java.awt.geom.*;
import java.awt.font.*;
import java.text.*;
public class Node extends CanvaxVertex {
	Vector actions;
	boolean lastfocus = false;
	Rectangle ri;
	boolean mouseover = false;

	int count = 0;
	String name;
	CanvaxVertex focus;
	Action selectedaction, aa;
	int i;
	public Node(String name, Vertex parent) {
		super(parent);
		actions = new Vector();
		this.name = name;
		x = parent.x;
		y = parent.y;
		z = parent.z + length();
		setBounds();
		focus = this;
	}

	public boolean nextAction() {

		if (i < count) {
			i++;
			return true;
		} else {
			i = 0;
			return false;

		}

	}
	public char type() {

		return 'l';

	}
	public Action getCurrentAction() {

		return (Action) actions.elementAt(i);

	}
	public boolean contains(int x, int y) {
		mouseover = false;
		focus = this;
		if (boundRectangle.contains(x, y)) {
			mouseover = true;
		} else {
			i = 0;
			if (Vertexes.lastlink == this) {
				while ((aa = getNextAction()) != null) {
					if (aa.contains(x, y)) {
						mouseover = true;
						focus = aa;
						return true;
					}

				}
			}
		}
		return mouseover;

	}
	public CanvaxVertex getElement() {

		return focus;

	}

	public Action getNextAction() {
		if (i < count)
			return (Action) actions.elementAt(i++);
		else {
			i = 0;
			return null;

		}
	}
	public String getName() {

		return name;

	}
	public void addAction(Action a) {
		actions.addElement(a);
		count++;

	}
	public void paint(Graphics g1) {
		Graphics2D g = (Graphics2D) g1;
		i = 0;

		AffineTransform at = g.getTransform();

		GeneralPath gpt = new GeneralPath();
		gpt.moveTo((float) parent.v, (float) parent.u);
		gpt.lineTo((float) u, (float) v);
		gpt.lineTo((float) parent.u - 1, (float) parent.v - 1);
		gpt.lineTo((float) parent.u + 1, (float) parent.v + 1);

		Rectangle bound = new Rectangle();
		Rectangle r = new Rectangle(U, V, relativeBallSize, relativeBallSize);
		Rectangle r3 = new Rectangle(r);
	
		gpt.closePath();
		GeneralPath gpt2 = new GeneralPath();
		gpt2.moveTo(parent.u, parent.v);
		gpt2.lineTo(u - 1, v - 1);
		gpt2.lineTo(u + 1, v + 1);
		gpt2.closePath();
		gpt2.closePath();
		//gpt.append(gpt2,true);
		gpt.transform(at);
		gpt2.transform(at);
		GradientPaint gp; //=new
								  // GradientPaint(parent.u,parent.v,c1,u,v,c2);
		AlphaComposite al2 = (AlphaComposite) g.getComposite();
		double zc = Z - ZC;
		double scale, scale2;
		if (Math.abs(zc) > 1)
			scale = Math.abs(FOV * 1 / zc);
		else
			scale = 1;
		scale2 = scale * 2;
		if (scale2 > 1)
			scale2 = 1;
		if (scale2 < 0.1)
			scale2 = 0.1;

		AlphaComposite al =
			AlphaComposite.getInstance(
				AlphaComposite.SRC_OVER,
				(float) (scale2));
		Color cb = Config.stringcolorend;

		g.setComposite(al);

		Color cl = g.getColor();
//		g.setPaint(new GradientPaint(parent.u, parent.v, cb, u, v, cl));
		Stroke s = g.getStroke();
		g.setStroke(new BasicStroke(9.0f));

		g.fill(gpt);
		g.fill(gpt2);
		g.setStroke(s);
		at.setToScale(scale2, scale2);

		g.setColor(Config.linkballcolor);
		g.setComposite(al);
		g.fillOval(U, V, relativeBallSize, relativeBallSize);
		AffineTransform at2 = new AffineTransform();
		at2.setToScale(scale2, scale2);
		FontRenderContext frc = new FontRenderContext(at2, true, true);
		TextLayout l =
			new TextLayout((new AttributedString(name)).getIterator(), frc);
		l.draw(g, U, V);
		g.setComposite(al2);
	}

}
