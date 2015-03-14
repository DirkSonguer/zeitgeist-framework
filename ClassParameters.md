# Parameters (Core Class) #

The parameters class acts as an input filter for all kinds of incoming data.

Part of the [Core Classes](ZeitgeistClasses.md).

## Examples ##

```
// This class filters user input that arrives via REQUEST
// methods (GET, POST, COOKIE). It is executed transparently by the 
// controller. So if you got to this point via the normal pipeline
// you will have the filtered input in the "parameters" array.

// You can define the allowed input for each action in the according
// .ini file of the module. If you open the parameters.ini
// file you'll notice that this action has 4 allowed input variables:
// teststring, testnumber, testescape and teststripslashes.
// Each input has a pattern that defines its possible content.


// The first parameter is defined as GET parameter and the definition
// the input data has to follow is defined as "/^.{4,5}$/".
// Notice that you can use any regexp to define the incoming data.
echo '<a href="./index.php?module=parameters&teststring=abc">Test first parameter with 3 chars (fail)</a><br />';
echo '<a href="./index.php?module=parameters&teststring=abcde">Test first parameter with 5 chars (success)</a><br /><br />';


// The second parameter is also defined as GET parameter.
// However it does not use a regexp but one of Zeitgeists predefined
// input types.
// The possible input types as well as their regexp can be seen in
// /zeitgeist/configuration/zeitgeist.ini.
echo '<a href="./index.php?module=parameters&testnumber=abc">Test second parameter with string (fail)</a><br />';
echo '<a href="./index.php?module=parameters&testnumber=123">Test second parameter with number (success)</a><br /><br />';


// The third parameter is defined as pretty open string (/^.{1,50}$/). Notice that escape=true
// for this input. This means it will be routed through a mysql_real_escape.
echo '<a href="./index.php?module=parameters&testescape=escap\'d">Test third parameter with special chars (escaped)</a><br /><br />';


// The fourth parameter is defined as pretty open string as well (/^.{1,50}$/). Notice that stripslashes=true
// for this input. This means it will be routed through the php stripslashes() function to clean up html form escapes
echo '<a href="./index.php?module=parameters&teststripslashes=Strip\'/"">Test fourth parameter with special chars (stripped)</a><br /><br />';

// Teststring content
if (!empty($parameters['teststring']))
{
	echo '<span style="background:#33cc33;">teststring: '.$parameters['teststring'].'</span><br />';
}
else
{
	echo '<span style="background:#ff8888;">teststring is empty</span><br />';
}
	
// Testnumber content
if (!empty($parameters['testnumber']))
{
	echo '<span style="background:#33cc33;">testnumber: '.$parameters['testnumber'].'</span><br />';
}
else
{
	echo '<span style="background:#ff8888;">testnumber is empty</span><br />';
}

// Testescape content
if (!empty($parameters['testescape']))
{
	echo '<span style="background:#33cc33;">testescape: '.$parameters['testescape'].'</span><br />';
}
else
{
	echo '<span style="background:#ff8888;">testescape is empty</span><br />';
}

// Teststripslashes content
if (!empty($parameters['teststripslashes']))
{
	echo '<span style="background:#33cc33;">teststripslashes: '.$parameters['teststripslashes'].'</span><br />';
}
else
{
	echo '<span style="background:#ff8888;">teststripslashes is empty</span><br />';
}
```