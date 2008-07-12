<?php


defined('TASKKUN_ACTIVE') or die();

class groups
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $objects;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('groups', 'templates', 'groups_index'));
		$tpl->assign('documenttitle', 'Gruppen anzeigen');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function addgroup($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('groups', 'templates', 'groups_addgroup'));
		$tpl->assign('documenttitle', 'Gruppe erstellen');
		
		$addgroupForm = new zgStaticform();
		$addgroupForm->load('forms/addgroup.form.ini');
		$formvalid = $addgroupForm->process($parameters);

		$groupfunctions = new tkGroupfunctions();
		
		if (!empty($parameters['submit']))
		{

			if ($formvalid)
			{
				$groupdata = $parameters['addgroup'];

				if ($groupfunctions->addGroup($groupdata))
				{
					$this->messages->setMessage('Neue Gruppendaten wurden gespeichert', 'usermessage');
					$tpl = new tkTemplate();
					$tpl->redirect($tpl->createLink('groups', 'index'));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verstndigen Sie einen Administrator', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte berprfen Sie Ihre Angaben sorgfltig.', 'userwarning');
			}
		}

		$formcreated = $addgroupForm->create($tpl);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function editgroup($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['editgroup']['group_id'])) $currentId = $parameters['editgroup']['group_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('groups', 'templates', 'groups_editgroup'));
		$tpl->assign('documenttitle', 'Gruppe bearbeiten');

		$editgroupForm = new zgStaticform();
		$editgroupForm->load('forms/editgroup.form.ini');

		$groupfunctions = new tkGroupfunctions();

		if (!empty($parameters['submit']))
		{
			$formvalid = $editgroupForm->process($parameters);

			if ($formvalid)
			{
				$groupdata = $parameters['editgroup'];

				if ($groupfunctions->updateGroup($groupdata))
				{
					$this->messages->setMessage('Neue Gruppendaten wurden gespeichert', 'usermessage');
					$tpl = new tkTemplate();
					$tpl->redirect($tpl->createLink('groups', 'index'));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verstndigen Sie einen Administrator', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte berprfen Sie Ihre Angaben sorgfltig.', 'userwarning');
			}
		}
		else
		{
			$userinformation = $groupfunctions->getGroupdata($currentId);

			$processData = array();
			$processData['editgroup'] = $userinformation;
			$formvalid = $editgroupForm->process($processData);
		}

		$formcreated = $editgroupForm->create($tpl);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deletegroup($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		if (!empty($parameters['id']))
		{
			$groupfunctions = new tkGroupfunctions();
			if ($groupfunctions->deleteGroup($parameters['id']))
			{
				$this->messages->setMessage('Die Gruppe wurde entfernt', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Die Gruppe konnte nicht gelscht werden. Bitte verstndigen Sie einen Administrator', 'usererror');
			}
		}

		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('groups', 'index'));

		$this->debug->unguard(true);
		return true;
	}
}
?>