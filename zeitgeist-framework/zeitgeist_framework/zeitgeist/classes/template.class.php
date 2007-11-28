<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Template class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST TEMPLATE
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgTemplate
{
	protected $debug;
	protected $messages;
	protected $configuration;
	protected $database;
	
	protected $file;
	protected $content;
	protected $blocks;
	protected $variables;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
		
		$this->file = '';
		$this->content = '';
		$this->blocks = array();
		$this->variables = array();
	}
	
	
	/**
	 * Loads a template file
	 * 
	 * @param string $filename name of the file to load
	 * 
	 * @return boolean
	 */	
	public function load($filename)
	{
		$this->debug->guard();

		if (!file_exists($filename))
		{
			$this->debug->write('Could not find the template file: '.$filename, 'error');
			$this->messages->setMessage('Could not find the template file: '.$filename, 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		// try to load the template
		$template = $this->_loadTemplateFromDatabase($filename);
		if ($template !== false)
		{
			$this->debug->write('Template found and successfully loaded: '.$filename);
			
			$this->file = $template['file'];
			$this->content = $template['content'];
			$this->blocks = $template['blocks'];
			$this->variables = $template['variables'];
		}
		else
		{
			$filehandle = fopen($filename, "r");
			$this->content = fread($filehandle, filesize($filename));
			fclose($filehandle);
			
			if (!$this->_loadLinks())
			{
				$this->debug->write('Error while rewriting the links in: '.$filename, 'error');
				$this->messages->setMessage('Error while rewriting the links in: '.$filename, 'error');
				$this->debug->unguard(false);
				return false;
			}
					
			if (!$this->_loadBlocks())
			{
				$this->debug->write('Error while loading the blocks in: '.$filename, 'error');
				$this->messages->setMessage('Error while loading the blocks in: '.$filename, 'error');
				$this->debug->unguard(false);
				return false;
			}
					
			if (!$this->_loadVariables())
			{
				$this->debug->write('Error while loading the variables in: '.$filename, 'error');
				$this->messages->setMessage('Error while loading the variables in: '.$filename, 'error');
				$this->debug->unguard(false);
				return false;
			}
					
			if (!$this->_getBlockParents())
			{
				$this->debug->write('Error while resolving the block tree in: '.$filename, 'error');
				$this->messages->setMessage('Error while resolving the block tree in: '.$filename, 'error');
				$this->debug->unguard(false);
				return false;
			}

			$ret = $this->_saveTemplateToDatabase($filename);
		}
		
		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Shows the template buffer
	 * 
	 * @return boolean 
	 */
	public function show()
	{
		$this->debug->guard();

		if (!$this->_filterTemplateCommands())
		{
			$this->debug->write('Error filtering the template commands', 'error');
			$this->messages->setMessage('Error filtering the template commands', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		echo $this->content;
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Assigns a value to a template variable
	 * 
	 * @param string $name name of the template variable to fill
	 * @param string $value value to fill the variable with
	 * 
	 * @return boolean 
	 */
	public function assign($name, $value)
	{
		$this->debug->guard();

		if (empty($this->variables[$name]))
		{
			$this->debug->write('Could not find the given variable: '.$name, 'error');
			$this->messages->setMessage('Could not find the given variable: '.$name, 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->variables[$name]->currentContent = $value;

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Assigns an array with values to a template variable
	 * The array keys are used as variable names
	 * 
	 * @param array $values values to fill the variables with
	 * 
	 * @return boolean 
	 */
	public function assignDataset($values)
	{
		$this->debug->guard();

		if (is_array($values))
		{
			$this->debug->write('Given dataset is not an array', 'error');
			$this->messages->setMessage('Given dataset is not an array', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		foreach ($values as $variablename => $variablevalue)
		{
			if (!empty($this->variables[$variablename]))
			{
				$this->variables[$variablename]->currentContent = $variablevalue;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Insert a block with its current content into the template buffer
	 * 
	 * @param string $name name of the block to insert
	 * @param boolean $reset flag if the contents of the block and the variables should be reset
	 * 
	 * @return boolean 
	 */
	public function insertBlock($name, $reset=true)
	{
		$this->debug->guard();
				
		if (empty($this->blocks[$name]))
		{
			$this->debug->write('Could not find the given block: '.$name, 'error');
			$this->messages->setMessage('Could not find the given block: '.$name, 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->_insertVariablesIntoBlock($name))
		{
			$this->debug->write('Could not find the given block: '.$name, 'error');
			$this->messages->setMessage('Could not find the given block: '.$name, 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		$blockID = $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin') . $name . $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd');
		$this->content = str_replace($blockID, $this->blocks[$name]->currentContent, $this->content);
		
		if ($reset)
		{
			$this->_resetBlock($name);
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Redirect to a given url
	 * 
	 * @param string $url url to redirect to
	 * 
	 * @return boolean 
	 */
	public function redirect($url)
	{
		$this->debug->guard();
		
		if (strpos($url, 'http://') === false)
		{
			$url = 'http://' . $url;
		}
		
//		if debug redirect to: $url
		
		header('Location: '.$url);

		$this->debug->unguard(true);
	}


	/**
	 * Create a link for a given module and a given action
	 * 
	 * @param string $module module to call
	 * @param string $action action to call
	 * @param array $parameter possible parameters 
	 * 
	 * @return string 
	 */
	public function createLink($module, $action, $parameter=false)
	{
		$this->debug->guard();
		
		$link = 'index.php';
		$link .= '?module='.$module;
		$link .= '&action='.$action;

		if (is_array($parameter))
		{
			foreach ($parameter as $parameterkey => $parametervalue)
			$link .= '&'.$parameterkey.'='.$parametervalue;
		}

		return $link;
		$this->debug->unguard($link);
	}
	

	/**
	 * Loads the internal links of the template and converts them into real links
	 * 
	 * @return boolean 
	 */
	protected function _loadLinks()
	{
		$this->debug->guard();
				
		while ($startPosition = strpos($this->content, $this->configuration->getConfiguration('zeitgeist','template', 'linkBegin')))
		{
			$endPosition = strpos($this->content, $this->configuration->getConfiguration('zeitgeist','template', 'linkEnd'), $startPosition);
			if ($endPosition === false)
			{
				$this->debug->write('Could not extract internal link', 'error');
				$this->messages->setMessage('Could not extract internal link', 'error');
				$this->debug->unguard(false);
				return false;
			}
				
			$completeLink = substr($this->content, $startPosition, ($endPosition - $startPosition + strlen($this->configuration->getConfiguration('zeitgeist','template', 'linkEnd'))));
			$linkContent = substr($completeLink, strlen($this->configuration->getConfiguration('zeitgeist','template', 'linkBegin')), (strlen($completeLink)-strlen($this->configuration->getConfiguration('zeitgeist','template', 'linkBegin'))-strlen($this->configuration->getConfiguration('zeitgeist','template', 'linkEnd'))));

			$linkArray = explode('.', $linkContent);
			
			if ($linkArray[0] == '')
			{
				$linkArray[0] = 'core';
			}
			
			$newLink = $this->createLink($linkArray[0], $linkArray[1]);
			$this->content = str_replace($completeLink, $newLink, $this->content);
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Load all the variables in a template and creates the objects for them
	 * 
	 * @return boolean 
	 */
	protected function _loadVariables()
	{
		$this->debug->guard();

		foreach ($this->blocks as $blockName => $block)
		{
			while ($startPosition = strpos($block->currentContent, $this->configuration->getConfiguration('zeitgeist','template', 'variableBegin')))
			{
				$endPosition = strpos($block->currentContent, $this->configuration->getConfiguration('zeitgeist','template', 'variableEnd'), $startPosition);
				if ($endPosition === false)
				{
					$this->debug->write('Error extracting variable from template', 'error');
					$this->messages->setMessage('Error extracting variable from template', 'error');
					$this->debug->unguard(false);
					return false;
				}

				$completeVariable = substr($block->currentContent, $startPosition, ($endPosition - $startPosition + strlen($this->configuration->getConfiguration('zeitgeist','template', 'variableEnd'))));
				$variableContent = substr($completeVariable, strlen($this->configuration->getConfiguration('zeitgeist','template', 'variableBegin')), (strlen($completeVariable)-strlen($this->configuration->getConfiguration('zeitgeist','template', 'variableBegin'))-strlen($this->configuration->getConfiguration('zeitgeist','template', 'variableEnd'))));
	
				$this->variables[$variableContent] = new zgTemplateVariable;
				$newVariableID = $this->configuration->getConfiguration('zeitgeist','template', 'variableSubstBegin') . $variableContent . $this->configuration->getConfiguration('zeitgeist','template', 'variableSubstEnd');
				$block->currentContent = str_replace($completeVariable, $newVariableID, $block->currentContent);
				$block->blockVariables[$variableContent] = $newVariableID;
				
				echo "variable found & replaced: ".$variableContent." in block: ".$blockName."<br />";
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Load all the blocks in a template and creates the objects for them
	 * 
	 * @return boolean 
	 */
	protected function _loadBlocks()
	{
		$this->debug->guard();
		
		while ($startPosition = strpos($this->content, $this->configuration->getConfiguration('zeitgeist','template', 'blockOpenBegin')))
		{
			// get block contents of next block
			$endPosition = strpos($this->content, $this->configuration->getConfiguration('zeitgeist','template', 'blockClose'), $startPosition);
			if ($endPosition === false)
			{
				$this->debug->write('Error extracting bock from template', 'error');
				$this->messages->setMessage('Error extracting block from template', 'error');
				$this->debug->unguard(false);
				return false;
			}

			$completeBlock = substr($this->content, $startPosition, ($endPosition - $startPosition + strlen($this->configuration->getConfiguration('zeitgeist','template', 'blockClose'))));

			// find nested blocks and parse them inside out
			while ($startPosition = strpos($completeBlock, $this->configuration->getConfiguration('zeitgeist','template', 'blockOpenBegin'), 1))
			{
				$completeBlock = substr($completeBlock, $startPosition);
			}

			// extract the blockname
			$endPosition = strpos($completeBlock, $this->configuration->getConfiguration('zeitgeist','template', 'blockOpenEnd'), 0);
			if ($endPosition === false)
			{
				$this->debug->write('Error extracting blockname from block', 'error');
				$this->messages->setMessage('Error blockname variable from block', 'error');
				$this->debug->unguard(false);
				return false;
			}

			$blockDefinition = substr($completeBlock, 0, $endPosition);

			$startPosition = strpos($blockDefinition, 'name="');
			if ($startPosition === false)
			{
				$this->debug->write('Error extracting blockname from block', 'error');
				$this->messages->setMessage('Error extracting blockname from block', 'error');
				$this->debug->unguard(false);
				return false;
			}

			$blockName = substr($blockDefinition, $startPosition+6);
			$this->blocks[$blockName] = new zgTemplateBlock();

			// extract block content
			$startPosition = strpos($completeBlock, $this->configuration->getConfiguration('zeitgeist','template', 'blockOpenEnd'));
			$blockContent = substr($completeBlock, ($startPosition+strlen($this->configuration->getConfiguration('zeitgeist','template', 'blockOpenEnd'))));
			$endPosition = strpos($blockContent, $this->configuration->getConfiguration('zeitgeist','template', 'blockClose'));
			$blockContent = substr($blockContent, 0, $endPosition);
			$this->blocks[$blockName]->currentContent = $blockContent;
			$this->blocks[$blockName]->originalContent = $blockContent;
			
			$newBlockID = $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin') . $blockName . $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd');
			$this->content = str_replace($completeBlock, $newBlockID, $this->content);

			echo "block found & replaced: ".$blockName."<br />";
		}
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Create the tree of blocks
	 * loops through all blocks in search of child blocks
	 * 
	 * @return boolean 
	 */
	protected function _getBlockParents()
	{
		$this->debug->guard();

		foreach ($this->blocks as $parentName => $block)
		{
			$currentBlock = $block->currentContent;
			while ($startPosition = strpos($currentBlock, $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin')))
			{
				$endPosition = strpos($currentBlock, $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd'));
				if ($endPosition === false)
				{
					$this->debug->write('Error extracting the blockname of a child from block', 'error');
					$this->messages->setMessage('Error extracting the blockname of a child from block', 'error');
					$this->debug->unguard(false);
					return false;
				}
				
				$blockID = substr($currentBlock, $startPosition, ($endPosition-$startPosition+strlen($this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd'))));
				
				$endPosition = strpos($blockID, $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd'));
				if ($endPosition === false)
				{
					$this->debug->write('Error extracting the blockname of a child from block', 'error');
					$this->messages->setMessage('Error extracting the blockname of a child from block', 'error');
					$this->debug->unguard(false);
					return false;
				}
				
				$subblockName = substr($blockID, strlen($this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin')), ($endPosition-strlen($this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin'))));
				$this->blocks[$subblockName]->parent = $parentName;
				$currentBlock = str_replace($blockID, '', $currentBlock);

				echo "subblock ".$subblockName." found in block: ".$parentName."<br />";
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Inserts all variable contents in the given block
	 * 
	 * @param string $blockname name of the block to insert the variables into
	 * 
	 * @return boolean 
	 */
	protected function _insertVariablesIntoBlock($blockname)
	{
		$this->debug->guard();
		
		foreach ($this->blocks[$blockname]->blockVariables as $variableName => $variableID)
		{
			if (empty($this->variables[$variableName]))
			{
				$this->debug->write('Error inserting the variable '.$variableName.' into block '.$blockname, 'error');
				$this->messages->setMessage('Error inserting the variable '.$variableName.' into block '.$blockname, 'error');
				$this->debug->unguard(false);
				return false;
			}

			$this->blocks[$blockname]->currentContent = str_replace($variableID, $this->variables[$variableName]->currentContent, $this->blocks[$blockname]->currentContent);
		}
		
		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Resets a given block or all blocks if no blockname is given
	 * 
	 * @param string $name name of the block to reset
	 * 
	 * @return boolean 
	 */
	protected function _resetBlock($name='')
	{
		$this->debug->guard();
	
		if ($name != '')
		{
			if (empty($this->blocks[$name]))
			{
				$this->debug->write('Error resetting block '.$name, 'error');
				$this->messages->setMessage('Error resetting block '.$name, 'error');
				$this->debug->unguard(false);
				return false;
			}
			
			$this->blocks[$name]->currentContent = $this->blocks[$name]->originalContent;
		}
		else
		{
			foreach ($this->blocks as $block)
			{
				$block->currentContent = $block->originalContent;
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Filter the template commands from the template buffer
	 * 
	 * @return boolean 
	 */
	protected function _filterTemplateCommands()
	{
		foreach ($this->variables as $variablename => $variable)
		{
			$variableID = $this->configuration->getConfiguration('zeitgeist','template', 'variableSubstBegin') . $variablename . $this->configuration->getConfiguration('zeitgeist','template', 'variableSubstEnd');
			$this->content = str_replace($variableID, '', $this->content);
		}

		foreach ($this->blocks as $blockname => $block)
		{
			$blockID = $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstBegin') . $blockname . $this->configuration->getConfiguration('zeitgeist','template', 'blockSubstEnd');
			$this->content = str_replace($blockID, '', $this->content);
		}
		
		$this->debug->unguard(true);
		return true;
	}

	
	/**
	 * Loads a template from the database
	 * 
	 * @param string $filename name of the file/ template to load
	 * 
	 * @return array|boolean 
	 */	
	protected function _loadTemplateFromDatabase($filename)
	{
		$this->debug->guard();

		$templatecacheTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_templatecache');
		$res = $this->database->query("SELECT * FROM " . $templatecacheTablename . " WHERE templatecache_name = '".$filename."'");
	
		if ($this->database->numRows($res) == 1)
		{
			$row = $this->database->fetchArray($res);
			
			if ($row['templatecache_timestamp'] == filemtime($filename))
			{
				$serializedTemplate = $row['templatecache_content'];
				$serializedTemplate = base64_decode($serializedTemplate);
				$template = unserialize($serializedTemplate);

				if ($template === false)
				{
					$this->debug->write('Error unserializing template content from the database', 'error');
					$this->messages->setMessage('Error unserializing template content from the database', 'error');
					$this->debug->unguard(false);
					return false;
				}
			}
			else
			{
				$res = $this->database->query("DELETE FROM " . $templatecacheTablename . " WHERE templatecache_name = '".$filename."'");
				$this->debug->write('Template data in the database is outdated', 'warning');
				$this->messages->setMessage('Template data in the database is outdated', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$this->debug->write('No templatedata is stored in database for this template', 'warning');
			$this->messages->setMessage('No templatedata is stored in database for this template', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($template);
		return $template;		
	}

	
	/**
	 * Save a given template into the database
	 * 
	 * @param string $filename name of the templatefile
	 * 
	 * @return boolean 
	 */	
	protected function _saveTemplateToDatabase($filename)
	{
		$this->debug->guard();
		
		$template = array();
		
		$template['file'] = $filename;
		$template['content'] = $this->content;
		$template['blocks'] = $this->blocks;
		$template['variables'] = $this->variables;
		
		$serializedTemplate = serialize($template);
		if ($serializedTemplate == '')
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$serializedTemplate = base64_encode($serializedTemplate);
		if ($serializedTemplate === false)
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$templatecacheTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_templatecache');
		$res = $this->database->query("INSERT INTO " . $templatecacheTablename . 
		"(templatecache_name, templatecache_content, templatecache_timestamp) " .
		"VALUES('" . $filename . "', '" . $serializedTemplate . "', '" . filemtime($filename) . "')");		
		
		$this->debug->unguard(true);
		return true;
	}

}


class zgTemplateBlock
{
	public $currentContent;
	public $originalContent;
	public $blockParent;
	public $blockVariables;
	
	public function __construct()
	{
		$currentContent = '';
		$originalContent = '';
		$blockParent = '';
		$blockVariables = array();
	}
}


class zgTemplateVariable
{
	public $currentContent;
	public $defaultContent;
	
	public function __construct()
	{
		$currentContent = '';
		$defaultContent = '';
	}
}

?>
