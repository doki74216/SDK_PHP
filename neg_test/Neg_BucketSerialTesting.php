<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;

$bucketname=$argv[3];

function ExpectException($e,$statuscode){
	if($e->getStatusCode()!=$statuscode){
		echo "<font color=red>¡I</font>Expected Status Code : $statuscode , get another Exception...<br>";
		echo "Error Message:    " . $e->getMessage()."<br>";
		echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
		echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
		echo "Error Type:       " . $e->getExceptionType()."<br>";
		echo "Request ID:       " . $e->getRequestId()."<br>";
	}
}

function ErrorHandler($errno, $errstr, $errfile, $errline) {
	if ( E_RECOVERABLE_ERROR===4096 ) {
		echo "$errstr\n";
		echo "$errno\n";
		echo "$errfile\n";
		echo "$errline\n";
		return false;
	}
	return true;
}
set_error_handler('ErrorHandler');


function put100Bucket(){
	
	try 
	{
		
		global $client;
		global $bucketname;
	
		for($i=0;$i<105;$i++)
		{
			$client->createBucket(array(
				'Bucket' => $bucketname.$i
			));
		}
		
		$result = $client->listBuckets();
		echo "Bucket List...\n";
		foreach ($result['Buckets'] as $bucket) {
			echo " - ".$bucket['Name']."\n";
		}
		
		
	}catch(S3Exception $e){
		ExpectException($e, 400);
	}
}

function nosuchBucket(){
	try
	{

		global $client;
		global $bucketname;
		for($i=0;$i<105;$i++)
		{
			$client->deleteBucket(array(
					'Bucket' => $bucketname.$i
			));
		}
	}catch(S3Exception $e){
		ExpectException($e,404);
	}
}


echo "Testing Neg_Bucket Serial ...";
//Create over 100 Buckets: 400-TooManyBuckets
put100Bucket();
//Delete over 100 Buckets: 404-NoSuchBucket
nosuchBucket();


?>
</pre>