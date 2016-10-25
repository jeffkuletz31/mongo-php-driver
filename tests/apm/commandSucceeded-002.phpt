--TEST--
APM: commandSucceeded callback (requestId and operationId match)
--SKIPIF--
<?php require __DIR__ . "/../utils/basic-skipif.inc"; CLEANUP(STANDALONE) ?>
--FILE--
<?php
require_once __DIR__ . "/../utils/basic.inc";

$m = new MongoDB\Driver\Manager(STANDALONE);

class MySubscriber implements MongoDB\Driver\Monitoring\CommandSubscriber
{
	public function commandStarted( \MongoDB\Driver\Monitoring\CommandStartedEvent $event )
	{
		echo "started: ", $event->getCommandName(), "\n";
		$this->startRequestId = $event->getRequestId();
		$this->startOperationId = $event->getOperationId();
	}

	public function commandSucceeded( \MongoDB\Driver\Monitoring\CommandSucceededEvent $event )
	{
		echo "succeeded: ", $event->getCommandName(), "\n";
		echo "- requestId matches: ", $this->startRequestId == $event->getRequestId() ? 'yes' : 'no', " \n";
		echo "- operationId matches: ", $this->startOperationId == $event->getOperationId() ? 'yes' : 'no', " \n";
	}

	public function commandFailed( \MongoDB\Driver\Monitoring\CommandFailedEvent $event )
	{
	}
}

$query = new MongoDB\Driver\Query( [] );
$subscriber = new MySubscriber;

MongoDB\Monitoring\addSubscriber( $subscriber );

$cursor = $m->executeQuery( "demo.test", $query );
?>
--EXPECT--
started: find
succeeded: find
- requestId matches: yes 
- operationId matches: yes
