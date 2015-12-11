
20 Things I Learned About Browsers and the Web
Created by Fantasy Interactive (f-i.com) for the Google Chrome team


--------------------------------------------------------------------------------
TECHNOLOGY OVERVIEW
--------------------------------------------------------------------------------

Back-end -- the application runs on Google App Engine (GAE) and stores article
data in the App Engine Datastore. Most of the server side code is written in
PHP, but Since GAE only supports Java and Python natively, the app uses Quercus,
a Java implementation of the PHP language
(http://www.caucho.com/resin-3.0/quercus/). Some native Java is also used for
datastore interactions.

Front-end -- the application takes advantage of many of the latest HTML5
technologies, for which Google Chrome has very good support.  These features
include: Canvas element animations (for page flip and animated illustrations),
HTML5 history API, CSS3 transitions, and offline mode.  Much of the JavaScript
code uses native methods, but jQuery is also used for cross-browser
compatibility.


--------------------------------------------------------------------------------
GETTING STARTED
--------------------------------------------------------------------------------

1. Start the app.

The app runs on the App Engine server, so you will need to start the app using
the App Engine SDK.  Download the GAE Java SDK or the Eclipse plugin and start
the development server using the appropriate methods detailed in the GAE
documentation: http://code.google.com/appengine/docs/java/tools/devserver.html

2. Populate the datatore.

Once the app is running, you'll want to first run an a sript to populate the
datastore with article data.  Since 20 Things is now localized, all the content
and configuration files are organized by language.  This version of the code
includes the necessary files for populating the US English content.

To import the US English data, load the following URL in your browser:
http://localhost:8080/populateds?locale=0 (replace the port with the one being
used by your local development server).  When prompted for a username and
password, enter 'testUsername' and 'testPassword' (these can be changed in the
source code).

3. View the app.

Now that you have imported the US English content, you should be able to view
the app on the root of your server (ie. http://localhost:8080).  The app
should redirect you to the /en-US path and display the 20 Things book with US
English content.

4. Edit the content.

The main article content can be edited via a lightweight CMS accessible from the
/cmshome path on your app.  You can add, edit or delete pages and articles from
the locale (en-US) you have imported.

5. Deploy the app.

To deploy the app to App Engine, create an account on the App Engine site and
then insert the name of your app between the <application> tags in this file:
/war/WEB-INF/appengine-web.xml.  Then deploy the app using the appcfg.sh file
in the GAE SDK:
http://code.google.com/appengine/docs/java/tools/uploadinganapp.html

6. Explore!

There's a lot more to the app than what we've just introduced, so explore the
code and see what kinds of cool things you can make from it!


--------------------------------------------------------------------------------
ANT BUILDS
--------------------------------------------------------------------------------

If you update JavaScript or CSS files, you'll want to run the Ant build
sript (/ant/build.xml) to minify your production code.  There is a PHP
variable called 'DEVELOPMENT_HOSTS_EXPRESSION' in the
/war/locale/locale-base-configuration.php that allows you to specify hostnames
in a regular expression that will use the uncompressed JavaScript and CSS files.
Any hosts not specified there will use the minified versions.


--------------------------------------------------------------------------------
QUERCUS
--------------------------------------------------------------------------------

Quercus is pioneering a new mixed Java/PHP approach to web applications and
services. On Quercus, PHP code is interpreted/compiled into Java and 2) Quercus
and its libraries are written entirely in Java. This lets PHP applications and
Java libraries to talk directly with one another at the program level. To
facilitate this new Java/PHP architecture, Quercus provides an API and
interface to expose Java libraries to PHP.

Quercus gives both Java and PHP developers a fast, safe, and powerful
alternative to the standard PHP intepreter. Developers ambitious enough to use
PHP in combination with Java will benefit the most from what Quercus has to
offer.

Setting up Quercus

It really couldn't be much simpler.  The following servlet definitions need to
be added to the deployment descriptor (web.xml) of your project:

	<servlet>
		<servlet-name>Quercus Servlet</servlet-name>
		<servlet-class>com.caucho.quercus.servlet.GoogleQuercusServlet
		</servlet-class>
	</servlet>

	<servlet-mapping>
		<servlet-name>Quercus Servlet</servlet-name>
		<url-pattern>*.php</url-pattern>
	</servlet-mapping>
	
Now your php code is executable on AppEngine.  


--------------------------------------------------------------------------------
OBJECTIFY
--------------------------------------------------------------------------------

20things uses a thin persistence layer as a replacement for the AppEngine
Datastore service.

The Google App Engine/J low-level datastore API is simple and elegant, neatly
reducing your data operations to four simple methods: get, put, delete, and
query. However, it is not designed to be used by the average developer:

DatastoreService persists GAE-specific Entity objects rather than normal POJO
  classes.
DatastoreService Keys are untyped and error-prone.
DatastoreService has a machine-friendly but not human-friendly query interface.
DatastoreService has an unnecessarily complicated transaction API.

Objectify-Appengine provides the thinnest convenient layer which addresses
these issues, yet preserves the elegance of get, put, delete, and query. In
short:

Objectify lets you persist, retrieve, delete, and query your own typed objects. 
Here's a simple example:

	class Car {
      @Id String vin; // Can be Long, long, or String
      String color;
    }
  
 	Objectify ofy = ObjectifyService.begin();
	ofy.put(new Car("123123", "red"));
 	Car c = ofy.get(Car.class, "123123");
 	ofy.delete(c);
