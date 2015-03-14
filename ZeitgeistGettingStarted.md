# Getting Started with Zeitgeist #

This page acts as a quick guide to get you started. There are links to more in depth information on the [overview page](ZeitgeistOverview.md).

## Obtaining Zeitgeist ##

If you are new to the Zeitgeist Framework you should first [take a look](http://code.google.com/p/zeitgeist-framework/source/browse/) at the repository. As you can see it contains 4 sections:

  * **applications**: tools that help you develop with Zeitgeist
  * **examples**: examples of the ZG classes as well as an empty ZG project template
  * **framework**: the framework itself
  * **wiki**: copy of the documentation (in Wiki format)

Just check out the entire repository to get the newest version of everything or use the [downloads](http://code.google.com/p/zeitgeist-framework/downloads/list) to download specific versions:

`svn checkout http://zeitgeist-framework.googlecode.com/svn/ zeitgeist-framework-read-only`

Note that the framework and tools have their own branching and tagging directories, so you might get some unstable work-in-prograss stuff as well. For now, just ignore them and concentrate on the trunk-directories.

## Installing the Zeitgeist examples ##

### Use the download ###

It is advised that you use the [download version](http://code.google.com/p/zeitgeist-framework/downloads/list). It contains everything you need and is easier than copying some files manually.

Just download a version of the ZG Examples and follow the directions inside the readme.

### Do it manually ###

Follow these steps to get the ZG examples up and running. You will need some kind of webserver and a MySQL database.

  * Create a new folder on you webserver, for example "_yourwebroot/zgexamples_".
  * Copy the contents of "_zeitgeist-repository/examples/zeitgeist`_`examples/_" into the new  folder
  * Create a new subfolder called "zeitgeist" and copy the framework itself there ("_zeitgeist-repository/framework/trunk_")
  * Import the SQL file for the examples ("_/`_`additional\_material/zeitgeist\_examples.sql_") into a MySQL database
  * Edit the config file ("_/configuration/application.configuration.php_") and enter your database credentials
  * Now try calling the examples in your webserver, for example http:://localhost/zgexamples

Most of the projects have a [readme](http://code.google.com/p/zeitgeist-framework/source/browse/examples/zeitgeist_examples/readme.txt) in the root directory describing this in a bit more detail.

## Framework and Application Structure ##

If you downloaded the example project as recommended, you will see the following directory structure:

  * '''`_`additional\_material''': contains additional files not directly needed for the application, e.g. an sql dump etc.
  * '''classes''': contains the classes for the application
  * '''configuration''': contains the configuration for the application
  * '''modules''': contains the modules of the application
  * '''templates''': contains templates / the presentation layers
  * '''zeitgeist''': contains the Zeitgeist framework files

The framework itself (inside the /zeitgeist folder) looks like this:

  * '''`_`additional\_material''': contains additional files not directly needed for the framework, e.g. an sql dump etc.
  * '''classes''': contains the classes of the framework
  * '''configuration''': contains the configuration for the framework as well as the global constants
  * '''includes''': contains additional includes for the framework (methods)
  * '''modules''': contains additional modules for the framework (classes)
  * '''tests''': contains the unit tests for the Zeitgeist framework

## How Does an Application Work? ##

All calls go through the '''index.php'''. It is called with a module and action. Example: http://www.zeitgeistapplication.com/index.php?module=main&action=index

From there the '''[controller](ClassController.md)''' of Zeitgeist will route the call accordingly:

  * The '''[module](ZeitgeistModules.md)''' is the name of the class. In our example this would be "class main" in "/modules/main/main.php". It also loads the accordig configuration for the module.

  * The '''[action](ZeitgeistActions.md)''' is the name of the method inside the module class. In our example it would be "function index" in "/modules/main/main.php" that does all the magic

## A Hello World Application ##

It is advised that you use the [download version](http://code.google.com/p/zeitgeist-framework/downloads/list). It contains everything you need and is easier than copying some files manually.

Just download a version of the ZG Example Project and follow the directions inside the readme.

  * Create a new folder on you webserver, for example "_yourwebroot/zghelloworld_".
  * Copy the contents of "_zeitgeist-repository/examples/zeitgeist`_`example-project/_" OR the download contents (see above) into the new  folder
  * Copy "_zeitgeist-repository/framework/trunk_" (the ZG framework itself) into the new folder (NOTE: this step is only necessary if you did not use the downloaded version and are using the repository directly)
  * Import the SQL file for the examples ("_/`_`additional\_material/zeitgeist\_project.sql_") into a MySQL database
  * Edit the config file ("_/configuration/application.configuration.php_") and enter your database credentials
  * Now try calling the examples in your webserver, for example http:://localhost/zghelloworld
  * The first call should actually display nothing
  * Open "_/modules/main/main.php_" in an editor
  * Locate the index-method (''public function index($parameters=array())'')
  * Enter an echo with some text (''echo "Hello World!";'')
  * Reload the page and you should see your new content

## Adding more actions ##

An action is represented by a method inside the module class. However just adding a method and calling it by it's name as action parameter does not work. The reason is that all actions need to be registered in the application database. For more details about this, read the [controller documentation](ClassController.md).

Step by step:

  * Add the method to the module class (for example "''public function test()''")
  * Add some content to it (''echo "test"; // or something'')
  * Add the method to the application database (''INSERT INTO actions(action\_module, action\_name, action\_description, action\_requiresuserright) VALUES('1', 'test', 'Some Test', '0');'')
  * Please note that the first parameter of the query is the id of the module the action belongs to. The modules and their respective ids are located in the modules table (''SELECT `*` FROM modules'')
  * Now call the index.php with the new action parameter (''http://127.0.0.1/zeitgeist_app/index.php?module=main&action=test'')

## Adding more modules ##

A module is a class, located in a folder in /modules/. The folder, file and class have to have the name of the module. The class need not extend any other class. However you need to register the module in the database, much like an action. For more details about this, read the [controller documentation](ClassController.md).

Step by step:

  * Create a new folder (for example ''/modules/test/'')
  * Create a new file in the folder with the same name (''/modules/test/test.php'')
  * Add module to the application database (''INSERT INTO modules(module\_name, module\_description, module\_active) VALUES('test', 'Some Test', '1');'')
  * Start adding actions to the new module

## Convinience ##

Both actions and modules can be created and configured with the [Zeitgeist Administrator](http://code.google.com/p/zeitgeist-framework/source/browse/#svn/applications/zeitgeist_administrator_v2). It saves you time as it creates the database entries and so on and basically does most of the steps automatically.

See [ZeitgeistAdministrator](ZeitgeistAdministrator.md) for more details.

## Architectural Layout ##

Zeitgeist does not dictate any architectural pattern besides the module / action layout. This gives you the freedom to utilize the framework in a way that fits your style and solves the application in an efficient way.

Here are some examples:

  * Use [MVC](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller) by using actions as controllers, special application classes as models and templates as views.
  * Use the [Controller](ClassController.md) as an eventhandler for an [Event Driven](http://en.wikipedia.org/wiki/Event_driven) approach (use the [message class](ClassMessages.md) as internal control path).
  * Put everything in the actions and be done with it (linear approach).

Of course you can do pretty much anything and even mix up the different approaches to suit your needs. For more on this see [Application Architecture](ZeitgeistApplicationArchitecture.md).