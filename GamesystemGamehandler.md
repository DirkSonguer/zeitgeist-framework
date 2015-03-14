# Gamehandler #

The Gamehandler has two main components: An _Eventhandler_ that processes all incoming game events and a _Gamehandler_ that handles / dispatches them at the right time.

# Game Events #

A game event consists of the following information:

  * Action: An id that maps to a specific piece of code
  * Parameter: The parameter/s the action is called with
  * Time: This is the game time when the event should be handled
  * Player: Id of the player the event applies to
  * Game: The id of the game or shard the action resides in
  * Timestamp: The time the event was issued

The game parameter allows that multiple games can reside in the same database. While some persistens PBBGs (for example the bigger ones with one huge game world) might have only one running game at a time, other games might have multiple games played by different players at the same time (for example a Tic Tac Toe platform with many players playing many rounds against each other at the same time).

The events are sent by the client (game interface) to the server are routed to the Eventhandler, which stores them into the database.

# Gamehandler #

The Gamehandler takes the currently relevant game events (stored by the Eventhandler) and maps them to specific class objects. Every event is represented by an external class that contains the implementation of the game logic behind an event.

Executed game events are deleted from the event list and stored in the archive.