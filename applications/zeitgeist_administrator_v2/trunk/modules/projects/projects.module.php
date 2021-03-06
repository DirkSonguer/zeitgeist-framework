<?php

defined('ZGADMIN_ACTIVE') or die();

class projects
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $projectfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->projectfunctions = new zgaProjectfunctions();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('projects', 'templates', 'projects_index'));

		$projectdata = $this->projectfunctions->getAllProjects();
		$activeproject = $this->projectfunctions->getActiveProject();
		
		foreach ($projectdata as $project)
		{
			$tpl->assignDataset($project);

			if ($activeproject['project_name'] !== $project['project_name'])
			{
				$tpl->insertBlock('inactiveproject');
			}
			else
			{
				$tpl->assign('activeprojectid', $project['project_id']);
				$tpl->insertBlock('activeproject');
			}

			$tpl->insertBlock('zgaproject');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function activateproject($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('projects', 'index'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->projectfunctions->setActiveProject($parameters['id']);
		$activeproject = $this->projectfunctions->getActiveProject();

		if (!$ret)
		{
			$this->messages->setMessage('Could not activate the project. Project "' . $activeproject['project_name'] . '" is still active', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Active project changed. You are now working on project "' . $activeproject['project_name'] . '"', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('projects', 'index'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function deleteproject($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('projects', 'index'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->projectfunctions->deleteProject($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete the project. The project is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Project deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('projects', 'index'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function createproject($parameters=array())
	{
		$this->debug->guard();

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('projects', 'templates', 'projects_createproject'));

		$createprojectForm = new zgForm();
		$createprojectForm->load('forms/projectdata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $createprojectForm->validate($parameters);

			if ($formvalid)
			{
				$creationProblems = false;
				
				// check database connection
				$projectdatabase = new zgDatabase();
				$ret = $projectdatabase->connect($parameters['projectdata']['project_dbserver'], $parameters['projectdata']['project_dbuser'], $parameters['projectdata']['project_dbpassword'], $parameters['projectdata']['project_dbdatabase'], false, true);
				if (!$ret)
				{
					$creationProblems = true;
					$this->messages->setMessage('Could not connect to database. Please check user credentials carefully', 'userwarning');
				}

				if (!$creationProblems)
				{				
					$ret = $projectdatabase->query('SELECT * FROM actions a LEFT JOIN modules m ON a.action_module = m.module_id');
					if (!$ret)
					{
						$creationProblems = true;
						$this->messages->setMessage('Database layout is not in a valid Zeitgeist format', 'userwarning');
					}
				}
				
				$projectdatabase->close();

				if (!$creationProblems)
				{
					$ret = $this->projectfunctions->saveProject($parameters['projectdata']);
					if (!$ret)
					{
						$this->messages->setMessage('Could not save project data to database', 'userwarning');
					}
					else
					{
						$this->messages->setMessage('Project was created', 'usermessage');
					}

					$tpl->redirect($tpl->createLink('projects', 'index'));
					$this->debug->unguard(true);
					return true;
				}
			}
			
			$formcreated = $createprojectForm->insert($tpl);
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function editproject($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['projectdata']['project_id'])) $currentId = $parameters['projectdata']['project_id'];

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('projects', 'templates', 'projects_editproject'));

		$editprojectForm = new zgForm();
		$editprojectForm->load('forms/projectdata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $editprojectForm->validate($parameters);

			if ($formvalid)
			{
				$updateProblems = false;
				
				// check database connection
				$projectdatabase = new zgDatabase();
				$ret = $projectdatabase->connect($parameters['projectdata']['project_dbserver'], $parameters['projectdata']['project_dbuser'], $parameters['projectdata']['project_dbpassword'], $parameters['projectdata']['project_dbdatabase']);
				if (!$ret)
				{
					$updateProblems = true;
					$this->messages->setMessage('Could not connect to database. Please check user credentials carefully', 'userwarning');
				}

				if (!$updateProblems)
				{				
					$ret = $projectdatabase->query('SELECT * FROM actions a LEFT JOIN modules m ON a.action_module = m.module_id');
					if (!$ret)
					{
						$updateProblems = true;
						$this->messages->setMessage('Database layout is not in a valid Zeitgeist format', 'userwarning');
					}
				}

				$projectdatabase->close();

				if (!$updateProblems)
				{
					$ret = $this->projectfunctions->saveProject($parameters['projectdata']);
					if (!$ret)
					{
						$this->messages->setMessage('Could not save project data to database', 'userwarning');
					}
					else
					{
						$this->messages->setMessage('Project data was changed', 'usermessage');
					}

					$tpl->redirect($tpl->createLink('projects', 'index'));
					$this->debug->unguard(true);
					return true;
				}
			}
		}
		else
		{
			$projectdata = $this->projectfunctions->getProject($currentId);

			$processData = array();
			$processData['projectdata'] = $projectdata;
			$formvalid = $editprojectForm->validate($processData);
		}

		$formcreated = $editprojectForm->insert($tpl);

		$tpl->assign('project_id:value', $currentId);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

}
?>
