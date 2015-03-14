# Part 7 - User Creation (user login 3) #

This tutorial is about creating a working persistent browser based game with the Zeitgeist Framework. To keep it simple, we're just doing a simple Tic Tac Toe game. However we will use a much Zeitgeist specific functionalities as possible to show how they work.

If you have specific questions about the tutorial, please mail us or open an [issue](http://code.google.com/p/zeitgeist-framework/issues/list).

## The Goal ##

In the last two tutorial we created actions for the general user handling and we added a test user in the database with the name "testuser" and password "testuser". Now we're adding an action so that a new user can be created by the frontend itself.

### User creation ###

First we need to create an actual action for the creation of the user. We start again by creating the action in the database.

```
	INSERT INTO actions ( action_id, action_module, action_name, action_description, action_active )
	VALUES ( '5', '2', 'create', 'Create a new user', '1' );
```

However if you remember [part 5](TicTacTutorial5.md) of this tutorial (User Handling Basics) - a user that is not logged in can only access one page. We defined it that way by adding a check to the index.php:

```
	// check if user is alreay logged in
	// if not, only show the not logged in page / action
	if (!$userLoggedIn)
	{
		$module = 'user';
		$action = 'login';
	}
```

So how can we make sure a user can log in and register? We could add the functionality for user registration directly into the user / login action but that would mean we'd process two functionalities with one action, which goes against the purpose of using a controller. Or we use the rights and roles management functionalities of Zeitgeist, as already hinted in part 5 but this would mean adding a bunch of additional code.

However there is a very simple solution if you want to make additional pages available for users that are not logged in: you just add the rights.

The right to access certain actions can be defined on a user basis. So in this case we'd just need to add the right to access the user creation action to a user that is not logged in. This might seem confusing as a user that is not logged in can't be a user user in the normal sense. While that is true, an unknown user can simply be mapped by definiing that such user has the id "0" (as in: "not available in the table"). So if we add actions for the user with the ID "0", we're really adding actions for all users that are currently not logged in.

So to define that an unknown user can also access the user/login (Action ID: 3) and user/create (Action ID: 5), we add the actions to his rights:

```
	INSERT INTO userrights ( userright_action, userright_user )
	VALUES ( '3', '0');

	INSERT INTO userrights ( userright_action, userright_user )
	VALUES ( '5', '0' );
```

Now we need to change the index.php as we're still only testing if the user is logged in or not. We now change it to check if the user has the right to call the action instead:

```
	// check if user is alreay logged in
	// if he is logged in, check if he has the right to call this action
	// if not the action is changed to the user login action
	if ( ( $user->isLoggedIn( ) ) && ( !$user->hasUserright( $controller->getActionID( $module, $action ) ) ) )
	{
		$module = 'user';
		$action = 'login';
	}
```

Now any user that is not logged in is able to call the login and create user actions.

Of course now we need a simple page to create a new user. To keep it simple we just modify the login form a bit. To do so, copy the user\_login.tpl.html and rename the dopy to user\_create.tpl.html. The text and form should be changed to the new action:

```
    <form method="post" action="@@{[user.create]}@@" name="create">
        <table border="0">
            <tr>
                <td align="right" nowrap width="110"><p>Username</p></td>
                <td align="left" valign="middle" nowrap><input type="text" size="35" maxlength="20" name="username" style="width:250px;" /></td>
            </tr>
            <tr>
                <td align="right" nowrap><p>Password</p></td>
                <td align="left" valign="middle" nowrap><input type="password" size="35" maxlength="32" name="password" style="width:250px;" /></td>
            </tr>
            <tr>
                <td align="left" nowrap>&nbsp;</td>
                <td align="right" valign="middle" nowrap><input type="submit" name="create" value="Create User" /></td>
            </tr>
        </table>
    </form>
```

Now we add a new action to the module file (/modules/user/user.module.php).

First we check if the user is already logged in. If so, he obviously already has a user.

```
		// check if the user is already logged in
		if ( $this->user->isLoggedIn( ) )
		{
			// the user is already logged in
			// no need to create another user
			// redirect the user to the main page
			$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'main', 'index' ) );
			$this->debug->unguard( true );
			return true;
		}
```

Creating a new user is rather simple: you just call the according method, hand over the designated username and password and the user class should do everythig for you. If all went well the method returns the user ID of the new user.

```
		$newUserId = $userfunctions->createUser( $parameters[ 'username' ], $parameters[ 'password' ] );
		if ( !$newUserId )
		{
			$creationerror = true;
		}
```

Note that initially a created user account is not active. Normally the account would be activated by a double opt in process, but we don't care about this for the sake of simplicity. We just activate the user account by calling the activation method directly:

```
		if ( !$userfunctions->activateUser( $newUserId ) )
		{
			$creationerror = true;
		}
```

And there you go: you can now create new users through the frontend.

## Closing ##

In this part of the tutorial we now created a new action for creating a new user through the frontend. In the next tutorial part we'll create a lobby, which we'll use as a simple matchmaking pattern.

[Back to the tutorial overview page](http://code.google.com/p/zeitgeist-framework/wiki/TicTacTutorialOverview)