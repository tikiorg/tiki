
package wiki3d;

//Camera class determines from where the view is taken.
//currently only used to determine the projection by using the zc variable ie. the distance from the screen to eye
//
public class Camera  extends Matrix3D{
static int XC,YC,ZC;
  public Camera(int x,int y,int z) {
  XC=x;YC=y;ZC=z;
  }
} 