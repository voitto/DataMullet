<?php


// Data-mullet
// HTTP/REST API inspired by Mongo, Couch & Sleepy Mongoose
// https://datamullet.com

// Brian Hendrickson <brian@megapump.com>
// Version: 0.2 
// June 16, 2011

// IRC: irc.freenode.net #datamullet
// Facebook: http://rp.ly/4i
// Twitter: http://rp.ly/4h


require 'kelvinj-Traffic/lib/Fu/Traffic.php';
require 'Mullet.php';



// Data-mullet config

define( 'DATABASE_ENGINE', 		'mysql');
//      pgsql OR mysql OR sqlite

define( 'DATABASE_NAME', 			'test');
//      NAME OF SQL DATABASE

define( 'DATABASE_USER', 			'root');
//      NAME OF SQL DATABASE USER

define( 'DATABASE_PASSWORD', 	'');
//      PASSWORD OF SQL DATABASE USER

define( 'DATABASE_HOST', 			'');
//      NAME ("localhost") OR IP ADDR OF SQL DATABASE SERVER

define( 'DATABASE_PORT', 			3306);
//      PORT NUMBER 3306/MySQL, 5432/PostgreSQL

define( 'TIME_ZONE', 					'America/Los Angeles');
//      DEFAULT TIME ZONE




$conn = new Mullet();

use Fu\Traffic as t;



/*

// HTTP AUTHENTICATION EXAMPLE

if (isset($_SERVER['PHP_AUTH_USER'])) {
	$conn = new Mullet;
	$coll = $conn->user->profiles;
	$cursor = $coll->find(array(
	  'username' => $_SERVER['PHP_AUTH_USER'],
	  'password' => md5($_SERVER['PHP_AUTH_PW'])
	));
	$user = $cursor->getNext();
  if ($user) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'BAD LOGIN';
    exit;
  }
}

*/



// REST API

t::rel('/_hello', function () {
	t::get(function(){
		echo "Luke, I am your cousin.\n";
	});
});

t::rel('/_all_dbs', function(){
	$conn = new Mullet();
	json_emit($conn->all_dbs());
});

t::rel('/_connect', function () {
	t::post(function(){
	});
});

t::put('*',function(){
	$conn = new Mullet();
	json_emit(array('ok',$conn->create_database()));
});

t::post("/*/*/_insert", function(){
	$args = Fu\Traffic::params();
	$args = explode('/',$args[1]);
	if (!(count($args) == 2))
	  json_error('Must include database and collection');
	$conn = new Mullet();
	$db = $args[0];
	$co = $args[1];
	$coll = $conn->$db->$co;
	$docs = json_decode($_POST['docs']);
	foreach($docs as $doc)
	  $result = $coll->insert( $doc );
	json_emit(array(
		'ok'=>$result
	));
});

t::post("/*/*/_update", function(){
	$args = Fu\Traffic::params();
	$args = explode('/',$args[1]);
	if (!(count($args) == 2))
	  json_error('Must include database and collection');
	$conn = new Mullet();
	$db = $args[0];
	$co = $args[1];
	$coll = $conn->$db->$co;
	json_emit(array(
		'ok'=>$coll->update( json_decode($_POST['criteria']), json_decode($_POST['newobj']) )
	));
});

t::get("/*/*/_find", function(){
	$args = Fu\Traffic::params();
	$args = explode('/',$args[1]);
	if (!(count($args) == 2))
	  json_error('Must include database and collection');
	$conn = new Mullet();
	$db = $args[0];
	$co = $args[1];
	$coll = $conn->$db->$co;
	$doc = $coll->findOne();
	if ($doc) $result = true;
	else $result = false;
	json_emit(array(
		'ok'=>$result,
		'results'=>array($doc),
		'id'=>0
	));
});

t::post("/*/*/_remove", function(){
	$args = Fu\Traffic::params();
	$args = explode('/',$args[1]);
	if (!(count($args) == 2))
	  json_error('Must include database and collection');
	$conn = new Mullet();
	$db = $args[0];
	$co = $args[1];
	$coll = $conn->$db->$co;
	json_emit(array(
		'ok'=>$coll->remove( json_decode($_POST['criteria']) )
	));
});

t::get("/*/*/_more", function(){
});








