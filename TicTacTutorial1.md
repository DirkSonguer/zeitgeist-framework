# Part 1 - Introduction #

This tutorial is about creating a working persistent browser based game with the Zeitgeist Framework. To keep it simple, we're just doing a simple Tic Tac Toe game. However we will use a much Zeitgeist specific functionalities as possible to show how they work.

If you have specific questions about the tutorial, please mail us or open an [issue](http://code.google.com/p/zeitgeist-framework/issues/list).

## The Goal ##

In this tutorial, we're setting up the project and the framework. As a result of this tutorial we should be ready to start developing an application.

## Basic Setup ##

To develop something like a PBBG, it's always a good idea to have a local development environment. This will enable you to develop and test locally instead of testing on an external webserver. For a Zeitgeist based project, this would include a webserver (Apache, IIS, ..), PHP5 and MySQL5. If you don't have these already running on your machine, [XAMPP](http://www.apachefriends.org/en/xampp.html) will help you set all this up on your Windows / Mac / Linux machine. For installation, please refer to the XAMPP website.

After setting up your servers, you will need some kind of client to access your database with. If you choose XAMPP you'll already have the phpMyAdmin installed and ready to use (try calling your localhost in your browser and look for it). Otherwise I'd recommend [downloading it](http://www.phpmyadmin.net/home_page/index.php) or use something like the MySQL Gui Tools.

You should also use some kind of versioning. It makes development a lot easier and safer and you will also be able to use the current versions of the framework and example project instead of the download versions.

As versioning tool we would recommend [Apache SVN](http://subversion.apache.org/). Also, there are tools for a lot of systems. [Tortoise SVN](http://tortoisesvn.tigris.org/) for example is a simple SVN client for Windows that provides a very simple interface and is easy to set up.

Also, you need some kind of editor or IDE to write the actual code in. There are many IDE specialized on PHP development, but the choice of an IDE is a pretty subjective one. Here is a list of the better known IDEs for PHP development:

  * Zend Studio
  * Eclipse PDT
  * Netbeans
  * PHPStorm

However you can use pretty much everything you want, even a simple notepad will do.


## Project Setup ##

By this point you should have the following components running on your development machine:

  * Some webserver
  * PHP5
  * MySQL5
  * Some SVN client
  * Some kind of editor / IDE

### Checkout Example Project ###

After that, the first thing you'll need is the [Zeitgeist Example Project](http://code.google.com/p/zeitgeist-framework/source/browse/#svn/examples/zeitgeist_example-project). This is basically an empty project that provides the right file and directory structure for a new project as well as a default database.

Use your SVN client to check out the current version: "svn checkout http://zeitgeist-framework.googlecode.com/svn/examples/zeitgeist_example-project". Put the files into the directory _"tictactutorial"_. However this name is just a convention for the ongoing tutorial, you may of course put the files into any directory somewhere on your webservers document root. The versions in the [Download section](http://code.google.com/p/zeitgeist-framework/downloads/list) will work as well, but SVN checkouts are preferred.

### Checkout Zeitgeist Framework ###

While the example project contains the typical file and directory structures it does not contain the framework itself.

Use your SVN client to check out the current version of the framework files: "svn checkout http://zeitgeist-framework.googlecode.com/svn/framework/trunk". Make sure you use the trink version. Put it into the same directory as the example project into a folder named _"tictactutorial/zeitgeist"_. The versions in the [Download section](http://code.google.com/p/zeitgeist-framework/downloads/list) will work as well, but SVN checkouts are preferred.


## Closing ##

That's it for the setup process. You now have everything you need to start developing. Unfortunately you can't see anything at the moment as the whole project needs to be configured first. We'll cover that in the next part of the tutorial.

[Back to the tutorial overview page](http://code.google.com/p/zeitgeist-framework/wiki/TicTacTutorialOverview)