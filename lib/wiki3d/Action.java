

package wiki3d;
import java.awt.*;
import java.awt.geom.*;
import java.awt.font.*;
import java.net.*;
import java.text.*;
import java.util.*;
public class Action  extends CanvaxVertex{
String label;
String url;
Font F=new Font("ARIAL",Font.PLAIN,9);
Rectangle2D.Double   r2;
  public Action(String label,String url,Vertex parent) {
  super(parent);

  this.url=url;
  this.label=label;
  System.out.println("action added "+ label+" " + url);
  x=0;
  y=0;
  z=0;
  /*
  x=parent.x+length()/10;
  y=parent.y+length()/10;
  z=parent.z+10;
  setBounds();
  
  */
  }
  public char type()
  {
  return 'a';

  }
 
  public  String getLabel()
  {
  return label;

  }
  public URL getURL()
  {
  try{
  return new URL(url);
   }
   catch(Exception e){
   try{

   return  new URL("http://www.errorpage.com"); }

   catch(Exception ee){return null;}
  }
  }

synchronized public boolean contains(int x,int y)
{
if(r2!=null)
{
if(r2.contains(x,y))
mouseover=true;
else
mouseover=false;

return mouseover;
}
else
return false;
}
synchronized public boolean containsd(int x,int y)//no more used
{
if(super.contains(x,y))
{
mouseover=true;
return true;
}
else
{
mouseover=false;
return false;
}
}
 public Rectangle paint(Graphics g1,int u,int v,FontRenderContext frc,int zone)
{
int u1=0,v11=0,h,w=Config.actionbannerwidth;
float fd=0,fl=0;
Graphics2D g=(Graphics2D)g1;
//if(((CanvaxVertex)parent).mouseover)
//{
Font f1=g.getFont();
g.setFont(F);
//System.out.println("Action "+label);
//FontRenderContext frc=g.getFontRenderContext();
Color c1=g.getColor();
Point2D.Double pen = new Point2D.Double(u,v); 
Vector vl=new Vector();
Vector vt=new Vector();
TextLayout layout;
LineBreakMeasurer measurer = new LineBreakMeasurer((new AttributedString(label)).getIterator(), BreakIterator.getWordInstance(),frc);
double ylast=v;
while (true) {
 layout = measurer.nextLayout(w);
  
if (layout == null) break;
vl.add(layout); 
  pen=new Point2D.Double(pen.x,( pen.y + layout.getAscent())); 
    double dx = 0;

Rectangle2D  bounds = layout.getBounds();
 bounds.setRect(bounds.getX()+pen.x, bounds.getY()+pen.y, bounds.getWidth(), bounds.getHeight());
// g.draw(bounds);
//g.fill(bounds);
//r2.createUnion(bounds);
vt.add(pen);

    //layout.draw(g, (float)(pen.x ), (float)pen.y); 
    pen.y += layout.getDescent() + layout.getLeading();
ylast=pen.y;
fl=layout.getLeading();
fd=layout.getDescent();
}
ylast+=fd;
u-=fl;

Color  c = Config.actionbannercolor;
g.setColor(c);
switch(zone)
{
case 0:
u1=u;
h=(int)ylast-v;

v11=(int)v-h;
break;
case 1:
u1=u;
v11=(int)v;
h=(int)ylast-v;
break;
case 2:
u1=u-w;
v11=v;
h=(int)ylast-v;
break;
case 3:
u1=u-w;
h=(int)ylast-v;
v11=v-h;
break;


}
 r2=new Rectangle2D.Double((double)u1,(double)v11,(double)w,(double)ylast-v);

g.fill(r2);
Rectangle r3=new Rectangle(u1,v11,w,(int)ylast-v); 
g.setColor(Config.actiontextcolor);
//Graphics2D g3=g.clone();
//g.translate(u1-u,v1-v);

Enumeration e=vl.elements();
Enumeration e2=vt.elements();
while(e.hasMoreElements())
{

layout=(TextLayout)e.nextElement();
pen=(Point2D.Double)e2.nextElement();
layout.draw(g,(float)pen.x+u1-u,(float)pen.y+v11-v);

}







 
// layout.draw(g, u,v); 

//draws the line and  ball for the action from the parents projected point to the actions projected point

/*
r2=F.getStringBounds(label,g.getFontRenderContext());
Rectangle r=new Rectangle((int)r2.getX(),(int)r2.getY(),(int)r2.getWidth(),(int)r2.getHeight());
r.setLocation(u,v);

Color c1=g.getColor();
Color  c = new Color(100,100,100);
g.setColor(c);
g.fillRect((int)r.x,(int)r.y,(int)r.width,(int)r.height);

c=new Color(5,5,255);
g.setColor(c);

g.drawString(label,u,v-F.getAscent()-F.getDescent());
*/


g.setFont(f1);
g.setColor(c1);
return r3;
//}

}}
