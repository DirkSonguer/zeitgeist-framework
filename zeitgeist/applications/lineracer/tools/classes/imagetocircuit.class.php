<?php

defined('LINERACER_ACTIVE') or die();

class imagetocircuit
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $objects;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjectcache::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		// load circuit definitions
		$this->configuration->loadConfiguration('circuit_definitions', 'configuration/circuit_definitions.ini');

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * imports the map data of a given circuit
	 *
	 * @param string $circuitname name of the circuit as defined in the circuit table
	 * @param string $filename filename of the image
	 * @param int $scale scale of reduction. 1 = original size
	 *
	 * @return boolean
	 */
	public function import($circuitname, $filename, $scale=1)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM circuits WHERE circuit_name='". $circuitname ."'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) <> 1)
		{
			$this->debug->write('Problem importing circuit: could not read out circuit database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->database->fetchArray($res);
		$circuit_id = $ret['circuit_id'];

		$circuit_data = $this->_createLinearbufferFromBitmap($filename, $scale);

		$sql = "SELECT * FROM circuit_data WHERE circuitdata_circuit='". $circuit_id ."'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$sql = "UPDATE circuit_data SET circuitdata_data='" . $circuit_data . "', circuitdata_scale='" . $scale . "' WHERE circuitdata_circuit='" . $circuit_id . "'";
			$this->database->query($sql);
		}
		else
		{
			$sql = "INSERT INTO circuit_data(circuitdata_circuit, circuitdata_data, circuitdata_scale) VALUES('" . $circuit_id . "', '" . $circuit_data . "', '" . $scale . "')";
			$this->database->query($sql);
		}
				
		$this->debug->unguard(true);
		return true;
	}
	
	/**
	 * creates a linear buffer with states of the given map
	 *
	 * @param string $filename filename of the image
	 * @param int $scale scale of reduction. 1 = original size
	 *
	 * @return boolean
	 */	
	private function _createLinearbufferFromBitmap($filename, $scale=1)
	{
		$this->debug->guard();

		$imagedata = getimagesize($filename);
		if (!$imagedata)
		{
			$this->debug->write('Problem creating buffer from circuit: could not read out image data ('.$filename.')', 'warning');
			return false;	
		}

		$circuit_width = $imagedata[0];
		$circuit_height = $imagedata[1];
		
		$circuit = imagecreatefromjpeg($filename);
		if (!$circuit)
		{
			$this->debug->write('Problem creating buffer from circuit: circuit file not found ('.$filename.')', 'warning');
			return false;	
		}
		
		$circuit_data = '';
		
		$surface['unpassable'] = explode(',', $this->configuration->getConfiguration('circuit_definitions', 'surfaces', 'unpassable'));

		for ($y=1; $y<$circuit_height; $y+=$scale)
		{
			for ($x=0; $x<$circuit_width; $x+=$scale)
			{
				$surfacestate = 1;
				
				$pixelcolor = imagecolorat($circuit, $x, $y);
				$colorarray = imagecolorsforindex($circuit, $pixelcolor);
				
				// check for unpassable surface
				if ( ($colorarray['red'] == $surface['unpassable'][0]) && ($colorarray['green'] == $surface['unpassable'][1]) && ($colorarray['blue'] == $surface['unpassable'][0]) )
				{
					$surfacestate = 0;
				}
				
				$circuit_data .= $surfacestate;
			}
		}
		
		imagedestroy($circuit);
		$this->debug->unguard($circuit_data);
		return $circuit_data;
	}

}
?>