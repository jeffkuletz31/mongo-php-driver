--TEST--
APM: Manager::removeSubscriber()
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
		echo "- started: ", $event->getCommandName(), "\n";
	}

	public function commandSucceeded( \MongoDB\Driver\Monitoring\CommandSucceededEvent $event )
	{
	}

	public function commandFailed( \MongoDB\Driver\Monitoring\CommandFailedEvent $event )
	{
	}
}

$query = new MongoDB\Driver\Query( [] );
$subscriber = new MySubscriber;

echo "Before addSubscriber\n";
$cursor = $m->executeQuery( "demo.test", $query );

MongoDB\Monitoring\addSubscriber( $subscriber );

echo "After addSubscriber\n";
$cursor = $m->executeQuery( "demo.test", $query );

MongoDB\Monitoring\removeSubscriber( $subscriber );

echo "After removeSubscriber\n";
$cursor = $m->executeQuery( "demo.test", $query );
?>
--EXPECT--
Before addSubscriber
After addSubscriber
- started: find
After removeSubscriber
