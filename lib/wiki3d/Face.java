

package wiki3d;
import java.awt.*;
public class Face {
ObjectVertex v1;
Color c1=Config.colorface1;
Color c2=Config.colorface2;
ObjectVertex v2;
ObjectVertex v3;
ObjectVertex v4;
  public Face(int i) {
  v1=new ObjectVertex(-i,0,i);
  v2=new ObjectVertex(i,0,i);
  v3=new ObjectVertex(i,0,-i);
  v4= new ObjectVertex(-i,0,-i);

  }
 public void setOrigin(int x,int y,int z)
 {
v1.setOrigin(x,y,z);
v2.setOrigin(x,y,z);
v3.setOrigin(x,y,z);
v4.setOrigin(x,y,z);


 }
public void transform(Matrix3D amat)
{
v1.mat.unit();
v1.mat.mult(amat);
v1.mat.translate(v1.origin.x,v1.origin.y,v1.origin.z);
v1.transform();
v1.proj();
//v2.mat.unit();
//v2.mat.mult(amat);
//v2.mat.translate(v2.origin.x,v2.origin.y,v2.origin.z);
v2.transform();
v2.proj();
//v3.mat.unit();
//v3.mat.mult(amat);
//v3.mat.translate(v3.origin.x,v3.origin.y,v3.origin.z);
v3.transform();
v3.proj();
//v4.mat.unit();
//v4.mat.mult(amat);
//v4.mat.translate(v4.origin.x,v4.origin.y,v4.origin.z);
v4.transform();
v4.proj();
}
public void paint(Graphics g)
{
int a1=v1.X-v1.origin.x;
int b1=v1.Y-v1.origin.y;
int a2=v2.X-v2.origin.x;
int b2=v2.Y-v2.origin.y;
int z=a1*b2-a2*b1;



Polygon p=new Polygon();
p.addPoint(v1.u,v1.v);
p.addPoint(v2.u,v2.v);
p.addPoint(v3.u,v3.v);
p.addPoint(v4.u,v4.v);
if(z>0)
g.setColor(c1);
else
g.setColor(c2);
g.fillPolygon(p);

}
public void translate(int x,int y,int z)
{


}


}