--TEST--
PHPC-349: APM Specification
--SKIPIF--
<?php require __DIR__ . "/../utils/basic-skipif.inc"; CLEANUP(STANDALONE); ?>
--FILE--
<?php
require_once __DIR__ . "/../utils/basic.inc";

$m = new MongoDB\Driver\Manager(STANDALONE);

class MySubscriber implements MongoDB\Driver\Monitoring\CommandSubscriber
{
	public function commandStarted( \MongoDB\Driver\Monitoring\CommandStartedEvent $event )
	{
		echo "started:\n";
		var_dump( $event );
	}

	public function commandSucceeded( \MongoDB\Driver\Monitoring\CommandSucceededEvent $event )
	{
		echo "succeeded:\n";
		var_dump( $event );
	}

	public function commandFailed( \MongoDB\Driver\Monitoring\CommandFailedEvent $event )
	{
		echo "failed:\n";
		var_dump( $event );
	}
}

MongoDB\Monitoring\addSubscriber( new MySubscriber() );
CLEANUP(STANDALONE);

$d = 12345678;

$bw = new MongoDB\Driver\BulkWrite( [ 'ordered' => false ] );
$_id = $bw->insert( [ 'decimal' => $d ] );
$r = $m->executeBulkWrite( DATABASE_NAME . '.' . COLLECTION_NAME, $bw );

$query = new MongoDB\Driver\Query( [] );
$cursor = $m->executeQuery( DATABASE_NAME . '.' . COLLECTION_NAME, $query );
var_dump( $cursor->toArray() );
?>
--EXPECTF--
started:
object(MongoDB\Driver\Monitoring\CommandStartedEvent)#%d (%d) {
  ["command"]=>
  object(stdClass)#%d (%d) {
    ["drop"]=>
    string(12) "apm_overview"
  }
  ["commandName"]=>
  string(4) "drop"
  ["databaseName"]=>
  string(6) "phongo"
  ["operationId"]=>
  string(%d) "%s"
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
failed:
object(MongoDB\Driver\Monitoring\CommandFailedEvent)#%d (%d) {
  ["commandName"]=>
  string(4) "drop"
  ["durationMicros"]=>
  int(%d)
  ["error"]=>
  object(MongoDB\Driver\Exception\RuntimeException)#%d (%d) {
    ["message":protected]=>
    string(12) "ns not found"
    ["string":"Exception":private]=>
    string(0) ""
    ["code":protected]=>
    int(26)
    ["file":protected]=>
    string(%d) "%stests/%s"
    ["line":protected]=>
    int(%d)
    ["trace":"Exception":private]=>
    %a
    ["previous":"Exception":private]=>
    NULL
  }
  ["operationId"]=>
  string(%d) "%s"
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
started:
object(MongoDB\Driver\Monitoring\CommandStartedEvent)#%d (%d) {
  ["command"]=>
  object(stdClass)#%d (%d) {
    ["insert"]=>
    string(12) "apm_overview"
    ["writeConcern"]=>
    object(stdClass)#%d (%d) {
    }
    ["ordered"]=>
    bool(false)
    ["documents"]=>
    array(%d) {
      [0]=>
      object(stdClass)#%d (%d) {
        ["decimal"]=>
        int(12345678)
        ["_id"]=>
        object(MongoDB\BSON\ObjectID)#%d (%d) {
          ["oid"]=>
          string(24) "%s"
        }
      }
    }
  }
  ["commandName"]=>
  string(6) "insert"
  ["databaseName"]=>
  string(6) "phongo"
  ["operationId"]=>
  string(%d) "%s"
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
succeeded:
object(MongoDB\Driver\Monitoring\CommandSucceededEvent)#%d (%d) {
  ["commandName"]=>
  string(6) "insert"
  ["durationMicros"]=>
  int(%d)
  ["operationId"]=>
  string(%d) "%s"
  ["reply"]=>
  object(stdClass)#%d (%d) {
    ["n"]=>
    int(1)
    ["ok"]=>
    float(1)
  }
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
started:
object(MongoDB\Driver\Monitoring\CommandStartedEvent)#%d (%d) {
  ["command"]=>
  object(stdClass)#%d (%d) {
    ["find"]=>
    string(12) "apm_overview"
    ["filter"]=>
    object(stdClass)#%d (%d) {
    }
  }
  ["commandName"]=>
  string(4) "find"
  ["databaseName"]=>
  string(6) "phongo"
  ["operationId"]=>
  string(%d) "%s"
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
succeeded:
object(MongoDB\Driver\Monitoring\CommandSucceededEvent)#%d (%d) {
  ["commandName"]=>
  string(4) "find"
  ["durationMicros"]=>
  int(%d)
  ["operationId"]=>
  string(%d) "%s"
  ["reply"]=>
  object(stdClass)#%d (%d) {
    ["cursor"]=>
    object(stdClass)#%d (%d) {
      ["firstBatch"]=>
      array(%d) {
        [0]=>
        object(stdClass)#%d (%d) {
          ["_id"]=>
          object(MongoDB\BSON\ObjectID)#%d (%d) {
            ["oid"]=>
            string(24) "%s"
          }
          ["decimal"]=>
          int(12345678)
        }
      }
      ["id"]=>
      int(0)
      ["ns"]=>
      string(%d) "%s.%s"
    }
    ["ok"]=>
    float(1)
  }
  ["requestId"]=>
  string(%d) "%s"
  ["server"]=>
  object(MongoDB\Driver\Server)#%d (%d) {
    %a
  }
}
array(%d) {
  [0]=>
  object(stdClass)#%d (%d) {
    ["_id"]=>
    object(MongoDB\BSON\ObjectID)#%d (%d) {
      ["oid"]=>
      string(24) "%s"
    }
    ["decimal"]=>
    int(12345678)
  }
}
