# Objects (Core Class) #

Implementation of an object system. With it you can share objects throughout the application.

Part of the [Core Classes](ZeitgeistClasses.md).

## Examples ##

```
// The object system is similar to the message system. However
// instead of messages, objects are stored. Think of it as a
// dumb stack where you can put your data, objects or whatever.

// The idea is to separate messages (that should be strings only)
// and functional objects.

// Another difference is that the object system is not persistent.
// All messages are lost as soon as the program ends. To store objects
// per user / session, use the session handler.

// Basically the object system replaces the global keyword with the
// advantage that you have full guard / trace support and a decent
// object structure.

// Create handler for object system
$objects = zgObjects::init();

// Create some test object
$tpl = new zgTemplate();

// Store it in the object system
$objects->storeObject('template', $tpl);

// Note that names for objects should be unique. By default the object
// system checks if a name is already set and catches collisions.
// If you want to update / force overwrite an existing object, set the 
// third parameter to true
$tpl->load('_additional_files/example_template.tpl.html');
$objects->storeObject('template', $tpl, true);

unset($tpl);

// Get the object again
$tpl = $objects->getObject('template');
$tpl->show();

// If you're done you can delete the object from the system at any time
$objects->deleteObject('template');

// Or delete all objects from the system
$objects->deleteAllObjects();		
```