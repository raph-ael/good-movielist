<?php
class Db {
	public static $db;
	
	public static function init() 
	{
		$dbhost = 'localhost';
		$dbname = 'movies';
		
		// Connect to test database
		//$m = new Mongo ( "mongodb://$dbhost" );
		$m = new MongoClient($dbhost);
		Db::$db = $m->$dbname;
	}
	
	/**
	 * default mongo query on specific collection
	 *
	 * @param String $collection        	
	 * @param array $query        	
	 * @param array $fields        	
	 * @return multitype:array |boolean
	 */
	public static function q($collection, $query, $fields = array()) {
		try {
			if ($cursor = Db::$db->$collection->find ( $query, $fields )) {
				if (count ( $cursor ) > 0) {
					$out = array ();
					$i = 0;
					foreach ( $cursor as $doc ) {
						$out [$doc ['_id']->{'$id'}] = $doc;
						$out [$doc ['_id']->{'$id'}] ['id'] = $doc ['_id']->{'$id'};
						$i ++;
					}
					return $out;
				}
			}
		} catch ( Exception $e ) {
			return false;
		}
		return false;
	}
	
	public static function getMovie($id)
	{
		return Db::findOne('movie',array(
			'_id' => new MongoId($id)
		));
	}
	
	public static function insert($collection, $data, $safe = true) {
		try {
			Db::$db->$collection->insert ( $data, array (
					'w' => $safe 
			) );
			return $data ['_id'];
		} catch ( Exception $e ) {
		}
		return false;
	}
	
	public static function listAll ($collection, $fields = array('name')) {
		$fields = array_merge($fields, array(
				'_id'
		));
		$pr = Db::$db->$collection->find(array(), $fields);
	
		if (count($pr) > 0) {
			$out = array();
			$i = 0;
			foreach ($pr as $p) {
				$out[$i] = array();
				foreach ($fields as $f) {
					if (isset($p[$f])) {
						$out[$i][$f] = $p[$f];
					} else {
						$out[$i][$f] = false;
					}
				}
				$out[$i]['id'] = $p['_id']->{'$id'};
				$i ++;
			}
			return $out;
		}
		return false;
	}
	
	public static function findOne($collection, $query) {
		try {
			if ($doc = Db::$db->$collection->findOne ( $query )) {
				$doc ['id'] = $doc ['_id']->{'$id'};
				return $doc;
			}
		} catch ( Exception $e ) {
			return false;
		}
		return false;
	}
	public static function update($collection, $id, $doc) {
		try {
			Db::$db->$collection->update ( array (
					'_id' => new MongoId ( $id ) 
			), array (
					'$set' => $doc 
			) );
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}
	public static function delete($collection, $id) {
		try {
			Db::$db->$collection->remove ( array (
					'_id' => new MongoId ( $id ) 
			), array (
					'justOne' => true 
			) );
		} catch ( Exception $e ) {
			T::debug ( $e );
		}
		return false;
	}
}