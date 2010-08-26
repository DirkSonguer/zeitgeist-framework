<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Template class
 *
 * A simple template class based on Dreamweaver templates
 * The idea is to let the designer work in Dreamweaver and define
 * the dynamic parts as DW template markup supported by the IDE
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST TEMPLATE
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgTemplate
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $locale;
	protected $file;
	protected $content;
	protected $blocks;
	protected $variables;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );
		$this->locale = new zgLocalisation( );

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );

		$this->file = '';
		$this->content = '';
		$this->blocks = array( );
		$this->variables = array( );
	}


	/**
	 * Loads a template file
	 * If the template is cached in the database, the cached
	 * version will be used
	 * The template will be cached into the database if it
	 * is not already cached there
	 *
	 * @param string $filename name of the file to load
	 *
	 * @return boolean
	 */
	public function load( $filename )
	{
		$this->debug->guard( );

		if ( !file_exists( $filename ) )
		{
			$this->debug->write( 'Problem loading the template: could not find the template file: ' . $filename, 'warning' );
			$this->messages->setMessage( 'Problem loading the template: could not find the template file: ' . $filename, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// try to load the template
		$gotTemplateFromDatabase = false;
		$template = $this->_loadTemplateFromDatabase( $filename );
		if ( $template !== false )
		{
			$this->debug->write( 'Template found and successfully loaded: ' . $filename );

			$this->file = $template[ 'file' ];
			$this->content = $template[ 'content' ];
			$this->blocks = $template[ 'blocks' ];
			$this->variables = $template[ 'variables' ];
			$gotTemplateFromDatabase = true;
		}
		else
		{
			$filehandle = fopen( $filename, "r" );
			$this->content = fread( $filehandle, filesize( $filename ) );
			fclose( $filehandle );

			if ( !$this->_loadLinks( ) )
			{
				$this->debug->write( 'Problem loading the template: could not rewrite links in : ' . $filename, 'warning' );
				$this->messages->setMessage( 'Problem loading the template: could not rewrite links in : ' . $filename, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			if ( !$this->_loadBlocks( ) )
			{
				$this->debug->write( 'Problem loading the template: could not load the blocks in: ' . $filename, 'warning' );
				$this->messages->setMessage( 'Problem loading the template: could not load the blocks in: ' . $filename, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			if ( !$this->_loadVariables( ) )
			{
				$this->debug->write( 'Problem loading the template: could not load the variables in: ' . $filename, 'warning' );
				$this->messages->setMessage( 'Problem loading the template: could not load the variables in: ' . $filename, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			if ( !$this->_getBlockParents( ) )
			{
				$this->debug->write( 'Problem loading the template: could not resolve the block tree in: ' . $filename, 'warning' );
				$this->messages->setMessage( 'Problem loading the template: could not resolve the block tree in: ' . $filename, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			if ( !$this->_loadRootVariables( ) )
			{
				$this->debug->write( 'Problem loading the template: could not load the root variables in: ' . $filename, 'warning' );
				$this->messages->setMessage( 'Problem loading the template: could not load the root variables in: ' . $filename, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			if ( !$gotTemplateFromDatabase )
			{
				if ( !$this->_saveTemplateToDatabase( $filename ) )
				{
					$this->debug->write( 'Problem loading the template: could not save the template in database', 'warning' );
					$this->messages->setMessage( 'Problem loading the template: could not save the template in database', 'warning' );
					$this->debug->unguard( false );
					return false;
				}
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Shows the template buffer
	 * This just prints out all the current contents of the
	 * template buffer
	 * All template commands, blocks etc. will be removed
	 *
	 * @return boolean
	 */
	public function show( )
	{
		$this->debug->guard( );

		if ( !$this->_insertRootVariables( ) )
		{
			$this->debug->write( 'Problem showing the template: could not insertg the root variables', 'warning' );
			$this->messages->setMessage( 'Problem showing the template: could not insertg the root variables', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( !$this->_filterTemplateCommands( ) )
		{
			$this->debug->write( 'Problem showing the template: could not filter the template commands', 'warning' );
			$this->messages->setMessage( 'Problem showing the template: could not filter the template commands', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		echo $this->content;

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Returns the template buffer as string
	 * All template commands, blocks etc. will be removed
	 * if the parameter is set to true (default)
	 *
	 * @param boolean $filterTemplateCommands set false to leave template commands intact
	 *
	 * @return string
	 */
	public function getContent( $filterTemplateCommands = true )
	{
		$this->debug->guard( );

		if ( !$this->_insertRootVariables( ) )
		{
			$this->debug->write( 'Problem inserting the root variables', 'warning' );
			$this->messages->setMessage( 'Problem inserting the root variables', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( $filterTemplateCommands )
		{
			if ( !$this->_filterTemplateCommands( ) )
			{
				$this->debug->write( 'Problem filtering the template commands', 'warning' );
				$this->messages->setMessage( 'Problem filtering the template commands', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}

		$ret = $this->content;

		$this->debug->unguard( true );
		return $ret;
	}


	/**
	 * Get the contents of a block
	 * The variables will be inserted and the blockdata will be cleared
	 *
	 * @param string $blockname name of the block to get the contents of
	 * @param boolean $reset flag if the contents of the block and the variables should be reset
	 *
	 * @return string
	 */
	public function getBlockContent( $blockname, $reset = true )
	{
		$this->debug->guard( );

		if ( empty( $this->blocks[ $blockname ] ) )
		{
			$this->debug->write( 'Problem getting the block content: could not find the given block: ' . $blockname, 'warning' );
			$this->messages->setMessage( 'Problem getting the block content: could not find the given block: ' . $blockname, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( !$this->_insertVariablesIntoBlock( $blockname ) )
		{
			$this->debug->write( 'Problem getting the block content: could not insert variables into the given block: ' . $blockname, 'warning' );
			$this->messages->setMessage( 'Problem getting the block content: could not insert variables into the given block: ' . $blockname, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$ret = $this->blocks[ $blockname ]->currentContent;

		if ( $reset )
		{
			$this->_resetBlock( $blockname );
		}

		$this->debug->unguard( true );
		return $ret;
	}


	/**
	 * Assigns a value to a template variable
	 *
	 * @param string $variablename name of the template variable to fill
	 * @param string $value value to fill the variable with
	 *
	 * @return boolean
	 */
	public function assign( $variablename, $value )
	{
		$this->debug->guard( );

		if ( empty( $this->variables[ $variablename ] ) )
		{
			$this->debug->write( 'Problem assigning a variable: could not find the given variable: ' . $variablename, 'warning' );
			$this->messages->setMessage( 'Problem assigning a variable: could not find the given variable: ' . $variablename, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->variables[ $variablename ]->currentContent = $value;

		$this->debug->unguard( true );
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
	public function assignDataset( $values )
	{
		$this->debug->guard( );

		if ( !is_array( $values ) )
		{
			$this->debug->write( 'Problem assigning a dataset: given dataset is not an array', 'warning' );
			$this->messages->setMessage( 'Problem assigning a dataset: given dataset is not an array', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		foreach ( $values as $variablename => $variablevalue )
		{
			if ( !empty( $this->variables[ $variablename ] ) )
			{
				$this->variables[ $variablename ]->currentContent = $variablevalue;
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Insert a block with its current content into the template buffer
	 *
	 * @param string $blockname name of the block to insert
	 * @param boolean $reset flag if the contents of the block and the variables should be reset
	 *
	 * @return boolean
	 */
	public function insertBlock( $blockname, $reset = true )
	{
		$this->debug->guard( );

		if ( empty( $this->blocks[ $blockname ] ) )
		{
			$this->debug->write( 'Problem inserting a block: could not find the given block: ' . $blockname, 'warning' );
			$this->messages->setMessage( 'Problem inserting a block: could not find the given block: ' . $blockname, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( !$this->_insertVariablesIntoBlock( $blockname ) )
		{
			$this->debug->write( 'Could not insert variables into the given block: ' . $blockname, 'error' );
			$this->messages->setMessage( 'Could not insert variables into the given block: ' . $blockname, 'error' );
			$this->debug->unguard( false );
			return false;
		}

		$blockID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) . $blockname . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' );
		if ( empty( $this->blocks[ $blockname ]->blockParent ) )
		{
			$this->content = str_replace( $blockID, $this->blocks[ $blockname ]->currentContent . "\n" . $blockID, $this->content );
		}
		else
		{
			$this->blocks[ $this->blocks[ $blockname ]->blockParent ]->currentContent = str_replace( $blockID, $this->blocks[ $blockname ]->currentContent . "\n" . $blockID, $this->blocks[ $this->blocks[ $blockname ]->blockParent ]->currentContent );
		}

		if ( $reset )
		{
			$this->_resetBlock( $blockname );
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Insert all usermessages to the default block
	 *
	 * @return boolean
	 */
	public function insertUsermessages( )
	{
		$this->debug->guard( );

		$messageblock = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'UsermessageMessages' );
		$currentUsermessages = $this->messages->getMessagesByType( 'usermessage' );
		if ( is_array( $currentUsermessages ) )
		{
			foreach ( $currentUsermessages as $message )
			{
				$this->assign( 'usermessage', $this->locale->write( $message->message ) );
				$this->insertBlock( $messageblock );
			}
		}

		$warningblock = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'UsermessageWarnings' );
		$currentUserwarnings = $this->messages->getMessagesByType( 'userwarning' );
		if ( is_array( $currentUserwarnings ) )
		{
			foreach ( $currentUserwarnings as $warning )
			{
				$this->assign( 'userwarning', $this->locale->write( $warning->message ) );
				$this->insertBlock( $warningblock );
			}
		}

		$errorblock = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'UsermessageErrors' );
		$currentUsererrors = $this->messages->getMessagesByType( 'usererror' );
		if ( is_array( $currentUsererrors ) )
		{
			foreach ( $currentUsererrors as $error )
			{
				$this->assign( 'usererror', $this->locale->write( $error->message ) );
				$this->insertBlock( $errorblock );
			}
		}

		$this->messages->clearAllMessages( );

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Redirect to a given url
	 *
	 * @param string $url url to redirect to
	 *
	 * @return boolean
	 */
	public function redirect( $url )
	{
		$this->debug->guard( );

		if ( strpos( $url, 'http://' ) === false )
		{
			$url = 'http://' . $url;
		}

		if ( !defined( 'DEBUGMODE' ) )
		{
			$this->debug->unguard( true );
			header( 'Location: ' . $url );
		}
		else
		{
			echo '<p style="background:#00ff00;">Should redirect now to: <a href="' . $url . '">' . $url . '</a></p>';
		}

		$this->debug->unguard( true );
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
	public function createLink( $module, $action, $parameter = false )
	{
		$this->debug->guard( );

		/*
				 *  Using $linkurl this way, we prevent Zeitgeist applications to be run
				 *  in any other place than the webservers documentroot.
				 */
		$linkurl = 'index.php';

		$link = array( );
		if ( $module != 'main' )
		{
			$link[ 0 ] = 'module=' . $module;
		}
		if ( $action != 'index' )
		{
			$link[ 1 ] = 'action=' . $action;
		}
		if ( count( $link ) > 0 )
		{
			$linkparameters = implode( $link, '&' );
			$linkurl = $linkurl . '?' . $linkparameters;
		}

		if ( is_array( $parameter ) )
		{
			foreach ( $parameter as $parameterkey => $parametervalue )
				$linkurl .= '&' . $parameterkey . '=' . $parametervalue;
		}

		return $linkurl;
		$this->debug->unguard( $linkurl );
	}


	/**
	 * Loads the internal links of the template and converts them into real links
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadLinks( )
	{
		$this->debug->guard( );

		while ( $startPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkBegin' ) ) )
		{
			$endPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkEnd' ), $startPosition );
			if ( $endPosition === false )
			{
				$this->debug->write( 'Problem loading links: could not extract internal link', 'warning' );
				$this->messages->setMessage( 'Problem loading links: could not extract internal link', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$completeLink = substr( $this->content, $startPosition, ( $endPosition - $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkEnd' ) ) ) );
			$linkContent = substr( $completeLink, strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkBegin' ) ), ( strlen( $completeLink ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkBegin' ) ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'linkEnd' ) ) ) );

			$linkArray = explode( '.', $linkContent );

			if ( $linkArray[ 0 ] == '' )
			{
				$linkArray[ 0 ] = 'main';
			}

			$newLink = $this->createLink( $linkArray[ 0 ], $linkArray[ 1 ] );
			$this->content = str_replace( $completeLink, $newLink, $this->content );
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Load all the variables in a template and creates the objects for them
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadVariables( )
	{
		$this->debug->guard( );

		foreach ( $this->blocks as $blockName => $block )
		{
			while ( $startPosition = strpos( $block->currentContent, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ) )
			{
				$endPosition = strpos( $block->currentContent, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ), $startPosition );
				if ( $endPosition === false )
				{
					$this->debug->write( 'Problem loading the template variables: could not extract variables from template', 'warning' );
					$this->messages->setMessage( 'Problem loading the template variables: could not extract variables from template', 'warning' );
					$this->debug->unguard( false );
					return false;
				}

				$completeVariable = substr( $block->currentContent, $startPosition, ( $endPosition - $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ) ) ) );
				$variableContent = substr( $completeVariable, strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ), ( strlen( $completeVariable ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ) ) ) );

				$this->variables[ $variableContent ] = new zgTemplateVariable( );
				$newVariableID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstBegin' ) . $variableContent . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstEnd' );
				$block->currentContent = str_replace( $completeVariable, $newVariableID, $block->currentContent );
				$block->originalContent = $block->currentContent;
				$block->blockVariables[ $variableContent ] = $newVariableID;
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Load all the variables in the root segment of a template and creates the objects for them
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadRootVariables( )
	{
		$this->debug->guard( );

		$this->blocks[ 'root' ] = new zgTemplateBlock( );

		while ( $startPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ) )
		{
			$endPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ), $startPosition );
			if ( $endPosition === false )
			{
				$this->debug->write( 'Problem loading root variables: could not extract root variables from template', 'warning' );
				$this->messages->setMessage( 'Problem loading root variables: could not extract root variables from template', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$completeVariable = substr( $this->content, $startPosition, ( $endPosition - $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ) ) ) );
			$variableContent = substr( $completeVariable, strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ), ( strlen( $completeVariable ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableBegin' ) ) - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableEnd' ) ) ) );

			$this->variables[ $variableContent ] = new zgTemplateVariable( );
			$newVariableID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstBegin' ) . $variableContent . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstEnd' );
			$this->content = str_replace( $completeVariable, $newVariableID, $this->content );
			$this->blocks[ 'root' ]->blockVariables[ $variableContent ] = $newVariableID;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Load all the blocks in a template and creates the objects for them
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadBlocks( )
	{
		$this->debug->guard( );

		while ( $startPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockOpenBegin' ) ) )
		{
			// get block contents of next block
			$endPosition = strpos( $this->content, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockClose' ), $startPosition );
			if ( $endPosition === false )
			{
				$this->debug->write( 'Problem loading template blocks: could not extract blocks from template', 'warning' );
				$this->messages->setMessage( 'Problem loading template blocks: could not extract blocks from template', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$completeBlock = substr( $this->content, $startPosition, ( $endPosition - $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockClose' ) ) ) );

			// find nested blocks and parse them inside out
			while ( $startPosition = strpos( $completeBlock, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockOpenBegin' ), 1 ) )
			{
				$completeBlock = substr( $completeBlock, $startPosition );
			}

			// extract the blockname
			$endPosition = strpos( $completeBlock, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockOpenEnd' ), 0 );
			if ( $endPosition === false )
			{
				$this->debug->write( 'Problem loading template blocks: could not extract blockname from block', 'warning' );
				$this->messages->setMessage( 'Problem loading template blocks: could not extract blockname from block', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$blockDefinition = substr( $completeBlock, 0, $endPosition );

			$startPosition = strpos( $blockDefinition, 'name="' );
			if ( $startPosition === false )
			{
				$this->debug->write( 'Problem loading template blocks: could not extract blockname from block', 'warning' );
				$this->messages->setMessage( 'Problem loading template blocks: could not extract blockname from block', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$blockName = substr( $blockDefinition, $startPosition + 6 );
			$this->blocks[ $blockName ] = new zgTemplateBlock( );

			// extract block content
			$startPosition = strpos( $completeBlock, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockOpenEnd' ) );
			$blockContent = substr( $completeBlock, ( $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockOpenEnd' ) ) ) );
			$endPosition = strpos( $blockContent, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockClose' ) );
			$blockContent = substr( $blockContent, 0, $endPosition );
			$this->blocks[ $blockName ]->currentContent = $blockContent;
			$this->blocks[ $blockName ]->originalContent = $blockContent;

			$newBlockID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) . $blockName . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' );
			$this->content = str_replace( $completeBlock, $newBlockID, $this->content );
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Create the tree of blocks
	 * loops through all blocks in search of child blocks
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _getBlockParents( )
	{
		$this->debug->guard( );

		foreach ( $this->blocks as $parentName => $block )
		{
			$currentBlock = $block->currentContent;
			while ( $startPosition = strpos( $currentBlock, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) ) )
			{
				$endPosition = strpos( $currentBlock, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' ) );
				if ( $endPosition === false )
				{
					$this->debug->write( 'Problem getting template block parents: could not extract the blockname of a child from block', 'warning' );
					$this->messages->setMessage( 'Problem getting template block parents: could not extract the blockname of a child from block', 'warning' );
					$this->debug->unguard( false );
					return false;
				}

				$blockID = substr( $currentBlock, $startPosition, ( $endPosition - $startPosition + strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' ) ) ) );

				$endPosition = strpos( $blockID, $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' ) );
				if ( $endPosition === false )
				{
					$this->debug->write( 'Problem getting template block parents: could not extract the blockname of a child from block', 'warning' );
					$this->messages->setMessage( 'Problem getting template block parents: could not extract the blockname of a child from block', 'warning' );
					$this->debug->unguard( false );
					return false;
				}

				$subblockName = substr( $blockID, strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) ), ( $endPosition - strlen( $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) ) ) );
				$this->blocks[ $subblockName ]->blockParent = $parentName;
				$currentBlock = str_replace( $blockID, '', $currentBlock );
			}

			$this->_resetBlock( $parentName );
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Inserts all variable contents in the given block
	 *
	 * @access protected
	 *
	 * @param string $blockname name of the block to insert the variables into
	 *
	 * @return boolean
	 */
	protected function _insertVariablesIntoBlock( $blockname )
	{
		$this->debug->guard( );

		if ( !empty( $this->blocks[ $blockname ]->blockVariables ) )
		{
			foreach ( $this->blocks[ $blockname ]->blockVariables as $variableName => $variableID )
			{
				if ( empty( $this->variables[ $variableName ] ) )
				{
					$this->debug->write( 'Problem inserting variables into block: could not insert the variable ' . $variableName . ' into block ' . $blockname, 'warning' );
					$this->messages->setMessage( 'Problem inserting variables into block: could not insert the variable ' . $variableName . ' into block ' . $blockname, 'warning' );
					$this->debug->unguard( false );
					return false;
				}

				$this->blocks[ $blockname ]->currentContent = str_replace( $variableID, $this->variables[ $variableName ]->currentContent, $this->blocks[ $blockname ]->currentContent );
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Inserts all variable contents in the the root element of the template
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _insertRootVariables( )
	{
		$this->debug->guard( );

		if ( !empty( $this->blocks[ 'root' ]->blockVariables ) )
		{
			foreach ( $this->blocks[ 'root' ]->blockVariables as $variableName => $variableID )
			{
				if ( empty( $this->variables[ $variableName ] ) )
				{
					$this->debug->write( 'Problem inserting root variables: could not insert the variable ' . $variableName . ' into the outer template node', 'warning' );
					$this->messages->setMessage( 'Problem inserting root variables: could not insert the variable ' . $variableName . ' into the outer template node', 'warning' );
					$this->debug->unguard( false );
					return false;
				}

				$this->content = str_replace( $variableID, $this->variables[ $variableName ]->currentContent, $this->content );
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Resets a given block or all blocks if no blockname is given
	 *
	 * @access protected
	 *
	 * @param string $name name of the block to reset
	 *
	 * @return boolean
	 */
	protected function _resetBlock( $name = '' )
	{
		$this->debug->guard( );

		if ( $name != '' )
		{
			if ( empty( $this->blocks[ $name ] ) )
			{
				$this->debug->write( 'Problem resetting block: could not find block: ' . $name, 'warning' );
				$this->messages->setMessage( 'Problem resetting block: could not find block: ' . $name, 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$this->blocks[ $name ]->currentContent = $this->blocks[ $name ]->originalContent;
		}
		else
		{
			foreach ( $this->blocks as $block )
			{
				$block->currentContent = $block->originalContent;
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Filter the template commands from the template buffer
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _filterTemplateCommands( )
	{
		foreach ( $this->variables as $variablename => $variable )
		{
			$variableID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstBegin' ) . $variablename . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'variableSubstEnd' );
			$this->content = str_replace( $variableID, '', $this->content );
		}

		foreach ( $this->blocks as $blockname => $block )
		{
			$blockID = $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstBegin' ) . $blockname . $this->configuration->getConfiguration( 'zeitgeist', 'template', 'blockSubstEnd' );
			$this->content = str_replace( $blockID, '', $this->content );
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Loads a template from the database
	 *
	 * @access protected
	 *
	 * @param string $filename name of the file/ template to load
	 *
	 * @return array|boolean
	 */
	protected function _loadTemplateFromDatabase( $filename )
	{
		$this->debug->guard( );

		$templatecacheTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_templatecache' );
		$sql = $this->database->prepare( "SELECT templatecache_content, templatecache_timestamp FROM " . $templatecacheTablename . " WHERE templatecache_name = ?" );
		$sql->bindParam( 1, $filename );
		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem loading the template from the database: could not read from template table', 'warning' );
			$this->messages->setMessage( 'Problem loading the template from the database: could not read from template table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) == 1 )
		{
			$row = $sql->fetch( PDO::FETCH_ASSOC );

			if ( $row[ 'templatecache_timestamp' ] == filemtime( $filename ) )
			{
				$serializedTemplate = $row[ 'templatecache_content' ];
				$serializedTemplate = base64_decode( $serializedTemplate );
				$template = unserialize( $serializedTemplate );

				if ( $template === false )
				{
					$this->debug->write( 'Problem loading the template from the database: could not unserialize template content from the database', 'warning' );
					$this->messages->setMessage( 'Problem loading the template from the database: could not unserialize template content from the database', 'warning' );
					$this->debug->unguard( false );
					return false;
				}
			}
			else
			{
				$this->debug->write( 'Template data in the database is outdated', 'message' );
				$this->messages->setMessage( 'Template data in the database is outdated', 'message' );

				$sql = $this->database->prepare( "DELETE FROM " . $templatecacheTablename . " WHERE templatecache_name = ?" );
				$sql->bindParam( 1, $filename );
				if ( !$sql->execute( ) )
				{
					$this->debug->write( 'Problem loading the template from the database: could not write to template table', 'warning' );
					$this->messages->setMessage( 'Problem loading the template from the database: could not write to template table', 'warning' );

					$this->debug->unguard( false );
					return false;
				}

				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'No templatedata is stored in database for this template', 'warning' );
			$this->messages->setMessage( 'No templatedata is stored in database for this template', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( $template );
		return $template;
	}


	/**
	 * Save a given template into the database
	 *
	 * @access protected
	 *
	 * @param string $filename name of the templatefile
	 *
	 * @return boolean
	 */
	protected function _saveTemplateToDatabase( $filename )
	{
		$this->debug->guard( );

		$template = array( );

		$template[ 'file' ] = $filename;
		$template[ 'content' ] = $this->content;
		$template[ 'blocks' ] = $this->blocks;
		$template[ 'variables' ] = $this->variables;

		$serializedTemplate = serialize( $template );
		if ( $serializedTemplate == '' )
		{
			$this->debug->unguard( false );
			return false;
		}

		$serializedTemplate = base64_encode( $serializedTemplate );
		if ( $serializedTemplate === false )
		{
			$this->debug->unguard( false );
			return false;
		}

		$templatecacheTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_templatecache' );

		$sql = $this->database->prepare( "INSERT INTO " . $templatecacheTablename . "(templatecache_name, templatecache_content, templatecache_timestamp) " . "VALUES(?, ?, ?)" );
		$sql->bindParam( 1, $filename );
		$sql->bindParam( 2, $serializedTemplate );
		$sql->bindParam( 3, filemtime( $filename ) );
		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem saving the template to the database: could not write to template table', 'warning' );
			$this->messages->setMessage( 'Problem saving the template to the database: could not write to template table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}
}


class zgTemplateBlock
{
	public $currentContent;
	public $originalContent;
	public $blockParent;
	public $blockVariables;


	public function __construct( )
	{
		$this->currentContent = '';
		$this->originalContent = '';
		$this->blockParent = '';
		$this->blockVariables = array( );
	}
}


class zgTemplateVariable
{
	public $currentContent;
	public $defaultContent;


	public function __construct( )
	{
		$this->currentContent = '';
		$this->defaultContent = '';
	}
}

?>
