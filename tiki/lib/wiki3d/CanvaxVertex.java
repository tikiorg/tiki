package wiki3d;
import java.awt.*;
public class CanvaxVertex extends Vertex {
	String name;

	int relativeBallSize, ballSize;

	static BalanceThread animator;
	public static Vertex origin;
	public static Matrix3D mat;
	public boolean mouseover = false;
	private static int zmax = Config.zmax,
		zmin = Config.zmin,
		xmax = Config.xmax,
		xmin = Config.xmin,
		ymax = Config.ymax,
		ymin = Config.xmin;
	
	boolean transformed = false;
	boolean focussed = true;
	boolean makereturn = false,
		makereturnx = false,
		makereturnz = false,
		makereturny = false;
	Rectangle boundRectangle = new Rectangle();
	int U, V;
	int i, j;
	int xpos, ypos, zpos;
	public int id;
	public static int idCounter = 0;

	SpeedVector speed = new SpeedVector(0, 0, 0);

	public Vertex parent;

	private boolean positionFixed;
	public CanvaxVertex(int x, int y, int z) {
		id = ++idCounter;
	}
	public static void setanimator(BalanceThread a) {
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

		sp.resize(1 / module);

		module /= 50;

		if (this.type() == 'g' || node.type() == 'g') {
			module -= distance;
			if (module > 0) {
				sp.resize(-1 * module * module);
			} else {
				sp.resize(1 * module * module);
			}
		} else {
			sp.resize(10 * (float) (Math.pow(Math.E, 1 / Math.sqrt(module)) - 1));
		}
		
		return sp;
	}

	public void clearSpeed() {
		speed.clear();
	}

	public void addSpeed(SpeedVector s) {
			speed.add(s);			
	}

	public void balance() {
		if (!this.positionFixed()) {			
			change(speed.x, speed.y, speed.z);
		}
	}

	public boolean positionFixed() {
		return positionFixed || this.type() == 'g';
	}
	
	public void releasePosition() {
		positionFixed = false;
	}
	
	
	public CanvaxVertex() {

		super();
		mat = new Matrix3D();

		parent = new Vertex(origin.x, origin.y, origin.z);

		relativeBallSize = Config.ballsize;
		ballSize = Config.ballsize;

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
		relativeBallSize = Config.ballsize;
		ballSize = Config.ballsize;
		//y=length();
		//x=length();
		//z=0;
	}

	public CanvaxVertex(int size) {
		super();
		mat = new Matrix3D();

		relativeBallSize = size;
		ballSize = size;
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

		relativeBallSize = size;
		ballSize = relativeBallSize;

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
		relativeBallSize = size;
		ballSize = relativeBallSize;
		this.name = name;
	}
	
	public void fixPosition() {
		positionFixed = true;
		xpos = x;
		ypos = y;
		zpos = z;
		
		makereturn = true;
		makereturnx = true;
		makereturny = true;
		makereturnz = true;
	}

	//function for changing the object space coordinates,
	public void change(int dx, int dy, int dz) {
		x = x + (dx);
		y = y + (dy);
		z = z + (dz);
		
		if (x > Config.xmax) 
			{
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
			y = 500;
		}
		if (y < Config.ymin) {
			y = Config.ymin;
		}
		
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
		boundRectangle = new Rectangle(U, V, relativeBallSize, relativeBallSize);
		
	}
	
	public void setOrigin(int x, int y, int z) {
		origin = new Vertex(x, y, z);
		mat.translate(x, y, z); //origin changed and transformed
		transform();
	}
	
	public boolean contains(int x, int y) {
		if (boundRectangle.contains(x, y)) {
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
	
	public void transform(Matrix3D m) {
		//if(makereturn)
		//tracepath();
		m.transform(this);
	}
	synchronized public void transform() {
		mat.transform(this);
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