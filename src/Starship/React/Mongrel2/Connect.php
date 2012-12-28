<?php
namespace Starship\React\Mongrel2;

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use Mongrel2\Request;
use Mongrel2\Tool;

class Connect extends EventEmitter
{
	public $loop;
	public $sender_id;
	public $write_addr;
	public $read_addr;
	public $write_context;
	public $read_context;
	public $read;
	public $write;

	public function __construct($sender_id, $read_addr, $write_addr, LoopInterface $loop)
	{
		$this->sender_id = $sender_id;
		$this->read_addr = $read_addr;
		$this->write_addr = $write_addr;
		$this->loop	= $loop;

		$this->read_context = new \React\ZMQ\Context($loop);
		$this->write_context = new \React\ZMQ\Context($loop);

		$this->initSockets();
		$this->attachListeners();
	}

	public function initSockets()
	{
		$this->read = $this->read_context->getSocket(\ZMQ::SOCKET_UPSTREAM);
		$this->read->connect($this->read_addr);

		$this->write = $this->write_context->getSocket(\ZMQ::SOCKET_PUB);
		$this->write->connect($this->write_addr);
		$this->write->setSockOpt(\ZMQ::SOCKOPT_IDENTITY, $this->sender_id);
	}

	public function attachListeners()
	{
		$that = $this;

		$this->read->on('message', function ($msg) use ($that) {
			$that->emit('read.message', array($msg));
		});

		$this->read->on('error', function ($e) use ($that) {
			$that->emit('read.error', array($e));
		});

		$this->write->on('error', function ($e) use ($that) {
				$that->emit('write.error', array($e));
		});
	}


	public function parseMessage($msg)
	{
		return request::parse($msg);
	}

	public function send($req, $body, $code = 200, $status = "OK", $headers = array("Content-Type"=>"text/html; charset=UTF-8"))
	{
		$req = $this->parseMessage($req);
		$msg = Tool::http_response($body, $code, $status, $headers);
		$uuid = $req->sender;
		$conn_id = $req->conn_id;

		$header = sprintf('%s %d:%s,', $uuid, strlen($conn_id), $conn_id);
		$this->write->send($header . " " . $msg);
	}
}
