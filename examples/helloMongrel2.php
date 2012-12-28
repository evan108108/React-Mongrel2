<?php
require __DIR__.'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create(); //Create The React Loop

	//Mongrel2 Setup ------------------------------------------
	//---------------------------------------------------------
	$m2 = new Starship\ReactMongrel2\Connect(
		'82209006-86FF-4982-B5EA-D1E29E55D481', //Sender ID 
		'tcp://127.0.0.1:9997', //recv_spec
		'tcp://127.0.0.1:9996', //send_spec
		$loop //React Event LoopInterface 
	);
	//---------------------------------------------------------
	//---------------------------------------------------------
	
	//Handel incoming and outgoing requests -------------------
	//---------------------------------------------------------
	$m2->on('read.message', function ($msg) use ($m2) {	
		echo $msg . "\n\n";
		$m2->send($msg, '<h1>Hello From React PHP</h1>');
	});
	//---------------------------------------------------------
	//---------------------------------------------------------

	//Handle comm errors --------------------------------------
	//---------------------------------------------------------
	$m2->on('read.error', function ($e) {
			var_dump($e->getMessage());
	});

	$m2->on('write.error', function ($e) {
			var_dump($e->getMessage());
	});
	//---------------------------------------------------------
	//---------------------------------------------------------

$loop->run(); //Start the loop


