<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userrights class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERRIGHTS
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class handles the userrights
 * Although it is a stand alone class, it is most likely called directly from the userhandler class
 */
class zgUserrights
{
	protected $debug;
	protected $messages;
	protected $session;
	protected $configuration;
	protected $database;
	
	public $userrights;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->session = zgSession::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}
	

	/**
	 * Load all userrights for a given user
	 * 
	 * @param integer $userid id of the user
	 * 
	 * @return boolean 
	 */
	public function loadUserrights($userid)
	{
		$this->debug->guard();
		
		$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
		$sql = "SELECT * FROM " . $userrightsTablename . " WHERE userright_user = '" . $userid . "'";
		
	    if ($res = $this->database->query($sql))
	    {
	        while ($row = $this->database->fetchArray($res))
	        {
	        	$this->userrights[$row['userright_action']] = true;
	        }

	        if (count($this->userrights) == 0)
	        {
				$this->debug->write('Possible problem getting userrights for a user: the user seems to habe no assigned rights', 'warning');
				$this->messages->setMessage('Possible problem getting userrights for a user: the user seems to habe no assigned rights', 'warning');
				
				$this->debug->unguard(false);
				return false;
	        }
	        
	        $this->debug->unguard(true);
			return true;
	    }
		else
	    {
			$this->debug->write('Error getting userrights for a user: could not find the userrights', 'error');
			$this->messages->setMessage('Error getting userrights for a user: could not find the userrights', 'error');
			$this->debug->unguard(false);
			return false;
	    }		
		
		$this->debug->unguard(false);
		return false;
	}
	
	
	/**
	 * Reloads all userrights from the session and stores it back into the structures/ class
	 * 
	 * @return boolean
	 */	
	public function reloadUserrights()
	{
		$this->debug->guard();
		
		$sessiondata = $this->session->getSessionVariable('userrights');
	
        if (is_array($sessiondata))
        {
        	$this->userrights = $sessiondata;
        }
        else
        {
			$this->debug->write('Problem getting userrights from session: session does not contain the userrights', 'warning');
			$this->messages->setMessage('Problem getting userrights from session: session does not contain the userrights', 'warning');
			
			$this->debug->unguard(false);
			return false;
        }
			
		$this->debug->unguard(true);
		return true;
	}	
	
	
	/**
	 * Save all userrights to the session for later use
	 * Also updates the according userright table with the current data
	 * 
	 * @return boolean 
	 */
	public function saveUserrights()
	{
		$this->debug->guard();
		
		$this->session->setSessionVariable('userrights', $this->userrights);
		
		$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
		$userid = $this->session->getSessionVariable('user_userid');
		
		$sql = 'DELETE FROM ' . $userrightsTablename . " WHERE userright_user='" . $userid . "'";
		$res = $this->database->query($sql);

		foreach ($this->userrights as $key => $value)
		{
			$sql = 'INSERT INTO ' . $userrightsTablename . "(userright_action, userright_user) VALUES('" . $key . "', '" . $userid . "')";
			$res = $this->database->query($sql);
		}

		$this->debug->unguard(true);
		return true;		
	}
	
	
	/**
	 * Check if the user has a given userright
	 * 
	 * @param integer $actionid id of the action
	 * 
	 * @return boolean 
	 */	
	public function hasUserright($actionid)
	{
		$this->debug->guard();
		
		if (!empty($this->userrights[$actionid]))
		{
			$this->debug->unguard(true);
			return true;		
		}
		
		$this->debug->write('User does not have the requested right for action (' . $actionid . ')', 'warning');
		$this->messages->setMessage('User does not have the requested right for action (' . $actionid . ')', 'warning');
		
		$this->debug->unguard(false);
		return false;		
	}
	
	
	/**
	 * Adds rights for the user for a given action
	 * 
	 * @param integer $userright id of the action to add rights to
	 * 
	 * @return boolean 
	 */		
	public function addUserright($userright)
	{
		$this->debug->guard();
		
		$this->userrights[$userright] = true;
		$this->saveUserrights();

		$this->debug->unguard(true);
		return true;		
	}
	
	
	/**
	 * Deletes a userright for an action
	 * 
	 * @param integer $userright id of the action to delete rights for
	 * 
	 * @return boolean 
	 */		
	public function deleteUserright($userright)
	{
		$this->debug->guard();
		
		if (isset($this->userrights[$userright]))
		{
			unset($this->userrights[$userright]);
			$this->saveUserrights();
		}

		$this->debug->unguard(true);
		return true;		
	}	
		
}
?>
