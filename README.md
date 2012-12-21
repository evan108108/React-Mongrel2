# React/Mongrel2

Mongrel2 bindings for React. Provides a simple rapper for easy connection to Mongrel2 web-server.

## Install

The recommended way to install react/mongrel2 is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "react/mongrel2": "dev-master"
    }
}
```

## Example

Hello World; Connecting a React process to Mongrel2:
### helloMongrel2.php
```php
require __DIR__.'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create(); //Create The React Loop

	//Mongrel2 Setup ------------------------------------------
	//---------------------------------------------------------
	$m2 = new React\Mongrel2\Connect(
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
```


### mongrel2.conf
```py
hello_handler = Handler(
    send_spec='tcp://*:9997', 
    send_ident='ab206881-6f49-4276-9db1-1676bfae18b0',
    recv_spec='tcp://*:9996', recv_ident='',
)

main = Server(
    uuid="9e71cabf-6afb-4ee1-b550-7972245f7e0a",
    access_log="/logs/access.log",
    error_log="/logs/error.log",
    chroot="./",
    default_host="localhost",
    name="codebanger",
    pid_file="/run/mongre2.pid",
    port=6767,
    hosts = [
        Host(name="localhost", routes={'/hello':hello_handler})
    ]
)

servers = [main]
```

