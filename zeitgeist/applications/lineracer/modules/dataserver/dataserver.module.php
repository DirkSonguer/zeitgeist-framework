<?php


defined('LINERACER_ACTIVE') or die();

include_once('includes/open-flash-chart/open-flash-chart.php');

class dataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getlobbydata($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT l.lobby_id, l.lobby_maxplayers, c.circuit_name, c.circuit_description,';
		$sql .= ' u1.user_username as lobby_creator, u2.user_username as lobby_player2, u3.user_username as lobby_player3, u4.user_username as lobby_player4 FROM lobby l';
		$sql .= ' LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id';
		$sql .= ' LEFT JOIN users u1 ON l.lobby_creator = u1.user_id';
		$sql .= ' LEFT JOIN users u2 ON l.lobby_player2 = u2.user_id';
		$sql .= ' LEFT JOIN users u3 ON l.lobby_player3 = u3.user_id';
		$sql .= ' LEFT JOIN users u4 ON l.lobby_player4 = u4.user_id';

		if (!empty($parameters['lobbyid']))
		{
			$sql .= " WHERE l.lobby_id = '" . intval($parameters['lobbyid']) . "'";
		}

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}

/*
	public function getactioninfo($parameters=array())
	{
		$this->debug->guard();

		$xmlData = $this->dataserver->createXMLDatasetFromSQL('SELECT a.*, m.module_name FROM actions a LEFT JOIN modules m ON a.action_module = m.module_id', $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuserinformation($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT u.*, ud.*, ur.* FROM users u ';
		$sql .= 'LEFT JOIN userdata ud ON u.user_id = ud.userdata_user ';
		$sql .= 'LEFT JOIN userroles_to_users uru ON uru.userroleuser_user = u.user_id ';
		$sql .= 'LEFT JOIN userroles ur ON uru.userroleuser_userrole = ur.userrole_id';

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuserroles($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT u.*, COUNT(ur.userroleuser_id) as userrole_usercount FROM userroles u ';
		$sql .= 'LEFT JOIN userroles_to_users ur ON u.userrole_id = ur.userroleuser_userrole GROUP BY u.userrole_id;';

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getmoduleanalytics($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(trafficlog_module) as module_views, trafficlog_module, m.module_name, m.module_description FROM trafficlog tl ";
		$sql .= "LEFT JOIN modules m ON trafficlog_module = m.module_id GROUP BY tl.trafficlog_module ORDER BY module_views DESC";
		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getactionanalytics($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(trafficlog_action) as action_views, trafficlog_action, a.action_name, a.action_description, m.module_name FROM trafficlog tl ";
		$sql .= "LEFT JOIN actions a ON tl.trafficlog_action = a.action_id LEFT JOIN modules m ON tl.trafficlog_module = m.module_id GROUP BY tl.trafficlog_action ORDER BY action_views DESC";
		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuseranalytics($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(tl.trafficlog_userid) as user_views, tl.trafficlog_userid, u.user_username FROM trafficlog tl ";
		$sql .= "LEFT JOIN users u ON tl.trafficlog_userid = u.user_id WHERE tl.trafficlog_userid > 0 GROUP BY tl.trafficlog_userid ORDER BY user_views DESC;";
		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getmodulechartdata($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(trafficlog_module) as module_views, trafficlog_module, m.module_name, m.module_description FROM trafficlog tl ";
		$sql .= "LEFT JOIN modules m ON tl.trafficlog_module = m.module_id GROUP BY tl.trafficlog_module ORDER BY module_views DESC";
		$res = $this->managedDatabase->query($sql);
		$dataArray = array();
		$descArray = array();
		$maxValue = 0;

		$bar = new bar_outline( 50, '#dddddd', '#000000' );

		$tpl = new zgaTemplate();
		$params = array();
		while ($row = $this->managedDatabase->fetchArray($res))
		{
			if ($maxValue < $row['module_views']) $maxValue = $row['module_views'];
			$linkParams['id'] = $row['trafficlog_module'];
			$link = urlencode($tpl->createLink('analyze', 'showmodules', $linkParams));
			$bar->add($row['module_views'], $link);
			$descArray[] = $row['module_name'];
		}

		$g = new graph();
		$g->title('');
		$g->bg_colour = '#fafafa';
		$g->data_sets[] = $bar;
		$g->set_x_labels($descArray);
		$g->set_x_label_style( 0, '#000000', 0, 1 );
		$g->set_x_axis_steps( 1 );
		$g->set_y_max( $maxValue+20 );
		$g->y_label_steps( 0 );
		echo $g->render();
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getactionchartdata($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(trafficlog_action) as action_views, trafficlog_action, a.action_name, a.action_description, m.module_name FROM trafficlog tl ";
		$sql .= "LEFT JOIN actions a ON tl.trafficlog_action = a.action_id LEFT JOIN modules m ON tl.trafficlog_module = m.module_id GROUP BY tl.trafficlog_action ORDER BY action_views DESC";
		$res = $this->managedDatabase->query($sql);
		$dataArray = array();
		$descArray = array();
		$maxValue = 0;
		$bar = new bar_outline( 50, '#dddddd', '#000000' );

		$tpl = new zgaTemplate();
		$params = array();
		while ($row = $this->managedDatabase->fetchArray($res))
		{
			if ($maxValue < $row['action_views']) $maxValue = $row['action_views'];
			$linkParams['id'] = $row['trafficlog_action'];
			$link = urlencode($tpl->createLink('analyze', 'showactions', $linkParams));
			$bar->add($row['action_views'], $link);
			$descArray[] = $row['action_name'];
		}

		$g = new graph();
		$g->title('');
		$g->bg_colour = '#fafafa';
		$g->data_sets[] = $bar;
		$g->set_x_labels($descArray);
		$g->set_x_label_style( 0, '#000000', 0, 1 );
		$g->set_x_axis_steps( 1 );
		$g->set_y_max( $maxValue+20 );
		$g->y_label_steps( 0 );
		echo $g->render();
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuserchartdata($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(tl.trafficlog_userid) as user_views, tl.trafficlog_userid, u.user_username FROM trafficlog tl ";
		$sql .= "LEFT JOIN users u ON tl.trafficlog_userid = u.user_id WHERE tl.trafficlog_userid > 0 GROUP BY tl.trafficlog_userid ORDER BY user_views DESC;";
		$res = $this->managedDatabase->query($sql);
		$dataArray = array();
		$descArray = array();
		$maxValue = 0;
		$bar = new bar_outline( 50, '#dddddd', '#000000' );

		$tpl = new zgaTemplate();
		$params = array();
		while ($row = $this->managedDatabase->fetchArray($res))
		{
			if ($maxValue < $row['user_views']) $maxValue = $row['user_views'];
			$linkParams['id'] = $row['trafficlog_userid'];
			$link = urlencode($tpl->createLink('analyze', 'showusers', $linkParams));
			$bar->add($row['user_views'], $link);
			$descArray[] = $row['user_username'];
		}

		$g = new graph();
		$g->title('');
		$g->bg_colour = '#fafafa';
		$g->data_sets[] = $bar;
		$g->set_x_labels($descArray);
		$g->set_x_label_style( 0, '#000000', 0, 1 );
		$g->set_x_axis_steps( 1 );
		$g->set_y_max( $maxValue+20 );
		$g->y_label_steps( 0 );
		echo $g->render();
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getmoduledetailanalytics($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(trafficlog_action) as action_views, trafficlog_action, a.action_name, a.action_description, a.action_module, m.module_name FROM trafficlog tl ";
		$sql .= "LEFT JOIN actions a ON tl.trafficlog_action = a.action_id LEFT JOIN modules m ON tl.trafficlog_module = m.module_id ";
		$sql .= "GROUP BY tl.trafficlog_action ORDER BY action_views DESC";
		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->managedDatabase);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}
*/
}
?>
