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
 * Pages represent a single page of data in an Article.  Page information may be modified via the CMS (see: cmseditpage.php).
 */

public class Page {
	
	@Id String id; 
	String stub;
	String locale;
    String pageNumber;
    String template;
    String content;    
    @Transient String doNotPersist;
    
    private Page() {}
    
    public Page(String id, String stub, String locale, String pageNumber, String template, String content) {
    	this.id = id;    	
    	this.stub = stub;
    	this.locale = locale;
    	this.pageNumber = pageNumber;
    	this.template = template;
    	this.content = content;
    }     
        
	public String getStub() {
		return stub;
	}

	public void setStub(String stub) {
		this.stub = stub;
	}

	public String getLocale() {
		return locale;
	}

	public void setLocale(String locale) {
		this.locale = locale;
	}
	
	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getPageNumber() {
		return pageNumber;
	}

	public void setPageNumber(String pageNumber) {
		this.pageNumber = pageNumber;
	}

	public String getTemplate() {
		return template;
	}

	public void setTemplate(String template) {
		this.template = template;
	}	
	
	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}   
    
}
