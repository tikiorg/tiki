/*
 * Morcego - 3D network browser Copyright (C) 2005 Luis Fagundes - Arca
 * <lfagundes@arca.ime.usp.br>
 * 
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
package br.arca.morcego;

import junit.framework.TestCase;
import br.arca.morcego.physics.Matrix3x3;
import br.arca.morcego.physics.Vector3D;

/**
 * @author lfagundes
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class Matrix3DTest extends TestCase {
	
	public void setUp() throws Exception {
		super.setUp();
		Config.init();
		Morcego.setUp();
	}
	
	public void testConstructor() {
		Matrix3x3 m = new Matrix3x3();
		assertTrue("New matrix must be identity", m.isIdentity());
	}
	
	public void testIdentity() {
		Matrix3x3 identity = new Matrix3x3();
		Matrix3x3 n = new Matrix3x3();
		
		n = n.multiplyByScalar(17);
		n = n.multiplyByMatrix(Matrix3x3.getXRotation(79));
		
	    String nDescription = n.toString();
		
		n.multiplyByMatrix(identity);

		assertTrue("Matrix multiplied by identity must remain same", nDescription.equals(n.multiplyByMatrix(identity).toString()));
	}
	
	public void testMultiply() {
		Matrix3x3 m = new Matrix3x3();
		
		m.multiplyByScalar(20);
		m.multiplyByScalar(1/20);
		
		//assertTrue("Matrix must remain identity after multiplication of n and 1/n", m.isIdentity());

		//TODO Test multiply by other matrix, that's not being used yet
	}
	
	// TODO implement new test
	/*
	public void testRotateObject() {
		int x=10;
		int y=20;
		int z=30;
		
		Vector3D obj = new Vector3D(x,y,z);
		
		Matrix3x3 m = new Matrix3x3();
		
		float xTheta = (float) 30;
		float yTheta = (float) 70;
	
		m.rotateObject(obj, xTheta, yTheta);
		assertFalse("Object must move with rotation",
				x == obj.x && y == obj.y && z == obj.z);
		
		int newX, newY, newZ;
		
		m.rotateObject(obj, -xTheta, -yTheta);
		newX = Math.round(obj.x);
		newY = Math.round(obj.y);
		newZ = Math.round(obj.z);
		assertTrue("Object should get back to original position after rotation and inverse rotation",
				x == newX && y == newY && z == newZ);
		
		obj = new Vector3D(x,y,z);
		m.rotateObject(obj, (float)Math.PI, (float) Math.PI);
		newX = Math.round(obj.x);
		newY = Math.round(obj.y);
		newZ = Math.round(obj.z);
		assertTrue("Object should get back to original position after 360 degree rotations",
				x == newX && y == newY && z == newZ);
		
	}
	*/
	
	public void testSetIdentity() {
		Matrix3x3 m = new Matrix3x3();
		
		m = m.multiplyByScalar(23);
		
		assertFalse("Matrix should not be identity after multiplication",m.isIdentity());
		
		m.setIdentity();
		
		assertTrue("Matrix should be identity",m.isIdentity());
	}
	
	public void testTransform() {
		Matrix3x3 m = new Matrix3x3();
		
		float x = 10;
		float y = 20;
		float z = 30;
		
		Vector3D v = new Vector3D(x,y,z);
		
		m.multiplyByVector(v);
		
		assertTrue("Identity matrix should not modify vertex", 
				x == v.x && y == v.y && z == v.z);
	}
	
	
	public void testRotations() {
		Matrix3x3 m = new Matrix3x3();
		
		Vector3D v = new Vector3D(1,0,0);
		
		// TODO...
		
	}
	
	
	
}
