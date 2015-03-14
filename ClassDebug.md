# Debug / Debugdummy (Core Class) #

Debug & tracing functionality. Only called if debug functionality is activated. If not, the debugdummy class is invoked which is just an empty skeleton class.

Part of the [Core Classes](ZeitgeistClasses.md).

## Examples ##

```
// The guarding method is part of the function guarding / tracing
// mechanism. It automatically adds the function to the log with 
// all parameters it was called with

// You should use guarding with all methods, functions etc to get
// a complete trace of your application
$this->debug->guard();

// Send a simple debug message
// This will be shown in the normal debug message log notice
// "my_type" as type
$this->debug->write('Hello Debug', 'my_type');

// There are several kinds of types, which define the style of
// a debug message in the log
// Special types defined per default are:
$this->debug->write('Hello Message', 'message');
$this->debug->write('Hello Warning', 'warning');
$this->debug->write('Hello Error', 'error');

// The style of each individual type is defined via css in
// zeitgeist/configuration/debug.css

// Note that you have to define "DEBUGMODE" _before_ you load the
// framework, otherwise debugging will be deactivated for performance
// reasons

// To show the debug messages, place the following code at the end of
// your application:
/*
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();
*/

// If you want to store the debug output to a file instead of showing
// it use this method:
/*
	$debug->saveToFile('path_to_file/debug_file.html', 'debug.css');
*/


// All database calls through the db abstraction class will be logged
// automatically
$database = new zgDatabase();
$database->connect();

// Normal query
$database->query("SELECT 'Hello Database'");

// Errors will be highlighted
$database->query("'Hello Database Error'");

// At the start and end of the exaple functions ou see the guarding calls.
// Function Guarding acts as tracing through the entire application as long
// as you use the calls.
// "$this->debug->guard();" automatically pulls out all relevant information:
// class, method, parameters and so on.
// "$this->debug->unguard(true);" should be called with the return value and
// marks the end of a method call.

$this->debug->unguard(true);
```