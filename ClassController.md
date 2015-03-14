# Controller (Core Class) #

This class controls the flow of the application. It's basically a [front controller pattern](http://en.wikipedia.org/wiki/Front_Controller_pattern) that acts as a central controller and routes your application to the right action as well as doing all kinds of security and application related tasks in between.

## How the controller works ##

## Usage ##

The controller code can be seen [source:/framework/trunk/classes/controller.class.php here]. Notice that the class has only one public method named [source:/framework/trunk/classes/controller.class.php#L150 callEvent] which you call with the module and action you want to execute. Everything else is done by the controller.

The controller will return the value from your action method, so if you want to do more complex routing pattern, you can implement them yourself. A typical example would be reroute a user to a login page if he needs to be logged in for a specific action.

In the workflow of Zeitgeist the action would return false regardless of the nature of the problem. This would be sent to the [message system](ClassMessages.md) by the component encountering the problem, evaluated in the index.php and handled accordingly.

## What the controller does when called ##

  * If the message handling is set to persistent in the [source:/framework/trunk/configuration/zeitgeist.ini Zeitgeist configuration], it loads the message data for the current session. This is part of the [message class](ClassMessages.md).
  * Check if the given [module](ZeitgeistModules.md) exists in the module registry and loads its data.
  * Check from the module data if the module is active.
  * Make sure that the module class is not already loaded before invoking it so that there are no collisions.
  * Check if Zeitgeist can load the module, basically if a class with the module name is available.
  * Load the module class through the [source:/framework/trunk/includes/autoloader.include.php autoloader] and execute the class constructor.
  * Check if the given [action](ZeitgeistActions.md) exists in the action registry and load the action data.
  * Check if action method really exists in module and is available to the system at this point.
  * Check if user has the [rights](ClassUserrights.md) to call the given action through either rights table or associated user roles.
  * Load the [configuration](ClassConfiguration.md) of the module.
  * [Filter input parameters](ClassParameters.md) and test them against the defined parameters in the module configuration. Safe ones will be handed over to the action method as a parameter.
  * [Log](ClassActionlog.md) the pageview if logging is active in the [source:/framework/trunk/configuration/zeitgeist.ini Zeitgeist configuration].
  * Execute the action method in the module class.
  * Save the [message](ClassMessages.md) data existing now for the session if messages should be persistent.