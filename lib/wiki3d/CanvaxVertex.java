package wiki3d;
import java.awt.*;
public class CanvaxVertex extends Vertex {
	String name;
	int b, B;
	static AnimationThread animator;
	public static Vertex origin;
	public static Matrix3D mat;
	public boolean mouseover = false;
	public static int zmax = Config.zmax,
		zmin = Config.zmin,
		xmax = Config.xmax,
		xmin = Config.xmin,
		ymax = Config.ymax,
		ymin = Config.xmin;
	public static int pbias = 3, nbias = 8, biax = 8, biay = 8, biaz = 8;
	boolean transformed = false;
	boolean focussed = true;
	boolean makereturn = false,
		makereturnx = false,
		makereturnz = false,
		makereturny = false;
	Rectangle r = new Rectangle();
	int U, V;
	int i, j;
	int xpos, ypos, zpos;

	SpeedVector speed = new SpeedVector(0, 0, 0);

	public Vertex parent;
	public CanvaxVertex(int x, int y, int z) {

	}
	public static void setanimator(AnimationThread a) {
		animator = a;
	}
	public void initpos(float theta) {
		int l = (int) Math.abs(length());

		x += Math.cos(theta) * l;
		y += Math.sin(theta) * l;

		setBounds();

	}

	public SpeedVector getForceFromNode(CanvaxVertex node) {

		SpeedVector sp = new SpeedVector(x - node.x, y - node.y, z - node.z);

		float distance = 2;
		float module = sp.module();
		float d;
		
		sp.resize(1/module);
		
		module /= 50;		

		if (this.type() == 'g' || node.type() == 'g') {
			module -= distance;
			if (module > 0) {				
				sp.resize(-1*module*module);
			} else {
				sp.resize(1*module*module);
			}
		} else {			
			sp.resize(10*(float)(Math.pow(Math.E,1/module)-1));
		}
		//sp.clear();
		return sp;
	}

	public void clearSpeed() {
		speed.clear();
	}
	
	
	public void addSpeed(SpeedVector s) {
		if (this.type() == 'l') {
			speed.add(s);
			//speed.print(this.name);
		}

	}

	public void balance() {
		if (this.type() == 'l') {
			//speed.print(name);
			change(speed.x, speed.y, speed.z);
		}
	}

	public void tracepath() {
		int dx = 0, dy = 0, dz = 0;

		if (Math.abs(xpos - x) <= Config.xstep)
			makereturnx = false;
		else if (xpos - x > Config.xstep)
			dx = Config.xstep;
		else
			dx = -Config.xstep;
		if (Math.abs(y - ypos) <= Config.ystep) {
			makereturny = false;
		} else if (ypos - y > Config.ystep)
			dy = Config.ystep;
		else
			dy = -Config.ystep;
		if (Math.abs(z - zpos) <= Config.zstep) {
			makereturnz = false;

		} else if (zpos - z > Config.zstep)
			dz = Config.zstep;
		else
			dz = -Config.zstep;

		if (!makereturnx && !makereturny && !makereturnz) {
			makereturn = false;
			animator.remove(this);
		} else {
			change(dx, dy, dz);
		}

	}

	public void addParent(Vertex p) {
		parent = p;

		double ii = Math.random();
		if (ii > 0.5)
			ii = -ii + 0.5;
		z = p.z + (int) (length() * ii) / 2;
		ii = Math.random();
		if (ii > 0.5)
			ii = -ii + 0.5;
		z = max(min(z, zmax), zmin);

		y = p.y + new Double(length() * ii).intValue();
		y = max(min(y, ymax), ymin);

		ii = Math.random();
		if (ii > 0.5)
			ii = -ii + 0.5;
		x = p.x + new Double(length() * ii).intValue();
		x = max(min(x, xmax), xmin);

	}
	public CanvaxVertex() {

		super();
		mat = new Matrix3D();

		parent = new Vertex(origin.x, origin.y, origin.z);

		b = Config.ballsize;
		B = Config.ballsize;

		y = length();
		x = length();
		z = Config.zshift;

	}
	public char type() {
		return 'c';

	}
	public CanvaxVertex(Vertex parent) {
		super();
		mat = new Matrix3D();
		this.parent = parent;
		b = Config.ballsize;
		B = Config.ballsize;
		//y=length();
		//x=length();
		//z=0;
	}

	public CanvaxVertex(int size) {
		super();
		mat = new Matrix3D();

		b = size;
		B = size;
		//y=length();
		//x=length();
		//z=0;

	}

	public CanvaxVertex(String name, int x, int y, int z, int size) {
		super();

		mat = new Matrix3D();

		this.x = x;
		this.y = y;
		this.z = z;

		b = size;
		B = b;

		this.name = name;
	}
	public CanvaxVertex(
		String name,
		int x,
		int y,
		int z,
		int size,
		Vertex parent) {
		super();
		this.parent = parent;

		mat = new Matrix3D();
		this.x = x;
		this.y = y;
		this.z = z;
		b = size;
		B = b;
		this.name = name;
	}
	public void fixpos() {
		xpos = x;
		ypos = y;
		zpos = z;
		//System.out.println("posfixed"+xpos+" "+ypos+" " +zpos );

		makereturn = true;
		makereturnx = true;
		makereturny = true;
		makereturnz = true;
	}

	//function for changing the object space coordinates,
	public void change(int dx, int dy, int dz) {
		x = x + (dx); //-biax);
		y = y + (dy); //-biay);
		z = z + (dz); //-biaz);
		if (x > Config.xmax) //minmax checks
			{
			x = Config.xmax;
			biax = nbias;
		}
		if (x < Config.xmin) {
			x = Config.xmin;
			biax = pbias;
		}
		if (z > Config.zmax) {
			z = Config.zmax;
			biaz = nbias;
		}
		if (z < Config.zmin) {
			z = Config.zmin;
			biaz = pbias;
		}
		if (y > Config.ymax) {
			y = 500;
			biay = nbias;
		}
		if (y < Config.ymin) {
			y = Config.ymin;
			biay = pbias;
		}
		//System.out.println("x"+x+"y"+y+"z"+z);

	}

	synchronized public void paint(Graphics g) {
		int i;
		int j;
		try {
			/*
			 * if(u-this.parent.x>0) i=-1; else i=1; if(v-this.parent.y
			 * <0) j=-1; else j=1; int c = b/2; U=u-c; V=v-c; r=new
			 * Rectangle(U,V,b,b); //g.drawString("this is where",u,v);
			 */
			//g.drawArc((int)U,(int)V,b,b,0,360);
			//g.drawLine(this.parent.x,this.parent.y,u,v);
			//g.drawArc(u,v,10,10);
			//System.out.println("X "+X+" Y "+Y);

		} catch (Exception ne) {
		}
	}

	synchronized public void proj() {
		b = (int) Math.round((double) B * FOV / (-Z + ZC));
		//diameter reduced to projection
		//System.out.println("ZC"+ZC+"Z"+Z+"b"+b);
		if (b < 2)
			b = 2;
		//minimum
		proj2();
		/*
		 * if(u-this.parent.x>0) i=-1; else i=1; if(v-this.parent.y
		 * <0) j=-1; else j=1;
		 */
		int c = b / 2;

		U = u - c;
		V = v - c;
		r = new Rectangle(U, V, b, b); //bounds for the
		// circle

	}
	public void setOrigin(int x, int y, int z) {
		origin = new Vertex(x, y, z);
		mat.translate(x, y, z); //origin changed and transformed
		transform();

	}
	public boolean contains(int x, int y) {
		if (r.contains(x, y)) {
			mouseover = true;
			return true;
		} else {
			mouseover = false;
			return false;
		}
	}
	public CanvaxVertex getElement() {

		return this;

	}
	public boolean containsd(int x, int y) {

		if (r.contains(x, y)) {
			mouseover = true;
			if (!focussed) {
				focussed = true;
			}
		} else {
			mouseover = false;
			if (focussed) {
				focussed = false;
			}
		}
		return focussed;

	}
	public void transform(Matrix3D m) {
		//if(makereturn)
		//tracepath();
		m.transform(this);
	}
	synchronized public void transform() {
		//System.out.println("x "+x+" y "+Y+" z"+z);
		//if(makereturn)
		//tracepath();
		mat.transform(this);
		//change the object coordinates to cameracodinates by operating
		// with
		// the change accumulation matrix

	}

	public void proj2() //projection for X,and Y of 3d to u,v of
	// 2d;
	{
		//int xo=X;
		//int yo=origin.Y;
		int k = Z - ZC;
		// System.out.println("x "+x+"y "+y);
		// u=X;
		// v=Y;
		int ZZ = Z - ZC;
		if (Math.abs(ZZ) < 1)
			ZZ = 1;
		u = new Float(origin.x + (FOV * (X - origin.x)) / (ZZ)).intValue();
		v = new Float(origin.y + (FOV * (Y - origin.y)) / (ZZ)).intValue();

	}

	public int length() {
		return (int) (Math.random() * Config.randomlength)
			- Config.lengthmedian;

	}
	void setBounds() {
		x = min(max(x, xmin), xmax);
		y = min(max(y, ymin), ymax);
		z = min(max(z, zmin), zmax);

	}

	int max(int x, int y) {
		return Math.max(x, y);

	}
	int min(int x, int y) {
		return Math.min(x, y);

	}

}