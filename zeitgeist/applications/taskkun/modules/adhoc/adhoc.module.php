<?php

defined('TASKKUN_ACTIVE') or die();

class adhoc
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('adhoc', 'templates', 'adhoc_index'));
		$tpl->assign('documenttitle', 'Ad-Hoc-Tätigkeit eintragen');

		$addadhocForm = new zgStaticform();
		$addadhocForm->load('forms/addadhoc.form.ini');
		$formvalid = $addadhocForm->process($parameters);

		$taskfunctions = new tkTaskfunctions();
		if (!empty($parameters['submit']))
		{
			$formcontent = $parameters['addadhoc'];

			if ($formvalid)
			{
				if (empty($formcontent['task_hoursworked'])) $formcontent['task_hoursworked'] = '0';

				if (!$taskfunctions->addAdhoc($formcontent))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Ad-Hoc-Tätigkeitsbeschreibung wurde gespeichert', 'usermessage');
				}

				$this->debug->unguard(true);
				$tpl->redirect($tpl->createLink('tasks', 'index'));
				return true;
			}
		}

		$formcreated = $addadhocForm->create($tpl);

		if (empty($parameters['addadhoc']['task_date']))
		{
			$tpl->assign('task_date:value', date('d.m.Y'));
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
