/*
 * Created on May 23, 2004
 *
 * To change the template for this generated file go to
 * Window - Preferences - Java - Code Generation - Code and Comments
 */
package br.arca.morcego;

import br.arca.morcego.physics.Vector3D;
import junit.framework.TestCase;

/**
 * @author alex
 * 
 */
public class Vertex3DTest extends TestCase {

	private Vector3D origin, one, lost;
	
	protected void setUp() throws Exception {
		Config.init();
		
		origin = new Vector3D(0,0,0);
		one = new Vector3D(1,0,0);
		lost = new Vector3D(1,1,1);
	}

	public void testVertexConstructor() {
		assertEquals("x should be what we constructed the vertex with", 0, origin.getX(), 0);
		assertEquals("y should be what we constructed the vertex with", 0, origin.getY(), 0);
		assertEquals("z should be what we constructed the vertex with", 0, origin.getZ(), 0);

	}
	
	public void testGetDistanceTo(){
		assertEquals("distance from origin to one should be one", 1, origin.getDistanceTo(one), 0);
		assertEquals("distance from one to origin should also be one", 1, one.getDistanceTo(origin), 0);
		assertEquals("distance from origin to lost should be sqrt(3)", (float)Math.sqrt(3), origin.getDistanceTo(lost), 0);
	}

	public void testProj() {
		//TODO: setar o POV e o UV no config e dar proj e ver se o getScale era oq esperavamos
	}

	public void testConstructor()
	{
		Vector3D v = new Vector3D(1.1f, 2.2f, 3.3f);
		assertSpeedVector(v, 2, 3, 4);
	}

	public void testAdd()
	{
		Vector3D v1 = new Vector3D(1, 2, 3);
		Vector3D v2 = new Vector3D(3, 2, 1);
		v1.add(v2);
		assertSpeedVector(v1, 4, 4, 4);
		assertSpeedVector(v2, 3, 2, 1);
	}
	
	public void testClear()
	{
		Vector3D v = new Vector3D(9, 8, 7);
		v.clear();
		assertSpeedVector(v, 0, 0, 0);
	}

	public void testResize()
	{
		Vector3D v = new Vector3D(9, 8, 7);
		v.resize(10);
		assertSpeedVector(v, 90, 80, 70);
	}

	public void testReverse()
	{
		Vector3D v = new Vector3D(9, 8, 7);		
		assertSpeedVector(v.opposite(), -9, -8, -7);
	}

	public void testModule()
	{
		Vector3D v = new Vector3D(9, 8, 7);
		float expected = (float) Math.sqrt(v.x*v.x  + v.y*v.y + v.z*v.z); 
		assertTrue(expected == v.module());
	}
	
	/*
	public void testStabilize()
	{
		Vector3D v = new Vector3D(8,9,10);
		v.stabilize(new Vector3D(1,2,3));
		assertSpeedVector(v, 8, 9, 10);
		v.stabilize(v.reverse());
		assertTrue(v.ix < 8);
		assertTrue(v.iy < 9);
		assertTrue(v.iz < 10);
	}
	*/

	/**
	 * @param v
	 */
	private void assertSpeedVector(Vector3D v, float x, float y, float z)
	{
		// TODO
		/*
		assertEquals(x, v.x);
		assertEquals(y, v.y);
		assertEquals(z, v.z);
		*/
	}
}
