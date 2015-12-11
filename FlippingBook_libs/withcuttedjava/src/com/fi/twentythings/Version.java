package com.fi.twentythings;

import javax.persistence.Id;
import javax.persistence.Transient;

/**
 * Copyright 2011 Google Inc.
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * @author jonathan.gray
 * This class is used to store the version number of the app - the version number is incremented on any write to the datastore.  
 * see objectify.php to see incrementing code
 */

public class Version {
	
	@Id String id; 
	String version;
    @Transient String doNotPersist;
    
    private Version() {}
    
    public Version(String id, String version) {
    	this.id = id;    	
    	this.version = version;    	
    }

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getVersion() {
		return version;
	}

	public void setVersion(String version) {
		this.version = version;
	}
  
    

}
