<?php

class testPdcards extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$pdCards = new pdCards();
		$this->assertNotNull($pdCards);
    }

	function test_getAllTags()
	{
		$pdCards = new pdCards();

		$this->database->query('TRUNCATE TABLE tags');

		$tag1 = rand(100,500);
		$this->database->query("INSERT INTO tags(tag_id, tag_text) VALUES('".$tag1."', 'test1')");
		$tag2 = rand(501,1000);
		$this->database->query("INSERT INTO tags(tag_id, tag_text) VALUES('".$tag2."', 'test2')");

		$ret = $pdCards->getAllTags();
		$this->assertTrue(count($ret), 2);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
	}
	
	
	function test_getTags()
	{
		$pdCards = new pdCards();

		$this->database->query('TRUNCATE TABLE tags');
		$this->database->query('TRUNCATE TABLE tags_to_cards');

		$tag1 = rand(100,500);
		$this->database->query("INSERT INTO tags(tag_id, tag_text) VALUES('".$tag1."', 'test1')");
		$tag2 = rand(501,1000);
		$this->database->query("INSERT INTO tags(tag_id, tag_text) VALUES('".$tag2."', 'test2')");

		$card = rand(100,500);
		$this->database->query("INSERT INTO tags_to_cards(cardtag_card, cardtag_tag) VALUES('".$card."', '".$tag1."')");
		$this->database->query("INSERT INTO tags_to_cards(cardtag_card, cardtag_tag) VALUES('".$card."', '".$tag2."')");

		$ret = $pdCards->getTags(($card-1));
		$this->assertFalse($ret);

		$ret = $pdCards->getTags($card);
		$this->assertTrue(count($ret), 2);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
	}


	function test_addTags()
	{
		$pdCards = new pdCards();

		$this->database->query('TRUNCATE TABLE tags');
		$this->database->query('TRUNCATE TABLE tags_to_cards');
		
		$card = rand(100,500);
		$ret = $pdCards->addTags('test1, test2', $card);
		$this->assertTrue($ret);

		$ret = $pdCards->getTags($card);
		$this->assertTrue(count($ret), 2);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
				
		$ret = $pdCards->addTags('test1, test3', $card);
		$this->assertTrue($ret);
		$ret = $pdCards->getTags($card);
		$this->assertTrue(count($ret), 3);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
		$this->assertTrue(in_array('test3',$ret));

		$tag1 = rand(501,1000);
		$this->database->query("INSERT INTO tags(tag_id, tag_text) VALUES('".$tag1."', 'test4')");

		$ret = $pdCards->addTags('test4', $card);
		$this->assertTrue($ret);
		$ret = $pdCards->getTags($card);
		$this->assertTrue(count($ret), 4);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
		$this->assertTrue(in_array('test3',$ret));
		$this->assertTrue(in_array('test4',$ret));
		
		$ret = $pdCards->addTags('test1, test2', $card, true);
		$this->assertTrue($ret);
		$ret = $pdCards->getTags($card);
		$this->assertTrue(count($ret), 2);
		$this->assertTrue(in_array('test1',$ret));
		$this->assertTrue(in_array('test2',$ret));
	}

}

?>
