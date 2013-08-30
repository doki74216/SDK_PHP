<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;

$bucketname=$argv[1];

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


function Init(){
	global $client;
	global $bucketname;
	$client->createBucket(array(
			'Bucket' => $bucketname,
			'ACL' 	 => 'public-read'
	));
	
	$client->putObject(array(
			'Body'   => '404testchttl<br><title>chttl</title>',
			'Bucket' => $bucketname,
			'Key'	 => 'error.html',
			'ACL' 	 => 'public-read'
	));
	
	$client->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'index.html',
			'ACL' 	 => 'public-read'
	));
	$client->putBucketWebsite(array(
			'Bucket' => $bucketname,
			'ErrorDocument' => array(
					'Key' => 'error.html'
			),
			'IndexDocument' => array(
					'Suffix' => 'index.html'
			),
	));
}

function fakeAccessputWebsite(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
		'key'    => '1234567',
		'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		
		$fakeAccess->putBucketWebsite(array(
				'Bucket' => $bucketname,
				'ErrorDocument' => array(
						'Key' => 'error.html'
				),
				'IndexDocument' => array(
						'Suffix' => 'index.html'
				),
				'RoutingRules' => array(
						'RoutingRule' =>array(
								'Redirect' =>array(
										'HostName' => 'www.google.com'
								),
						),
				),
		));
		
		$result=$fakeAccess->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
	
}

function fakeSecretputWebsite(){
	global $bucketname;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		

		$fakeSecret->putBucketWebsite(array(
				'Bucket' => $bucketname,
				'ErrorDocument' => array(
						'Key' => 'error.html'
				),
				'IndexDocument' => array(
						'Suffix' => 'index.html'
				),
				'RoutingRules' => array(
						'RoutingRule' =>array(
								'Redirect' =>array(
										'HostName' => 'www.google.com'
								),
						),
				),
		));
		
		$result=$fakeSecret->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;
		

	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}


//if the bucket name exist, error code will be 403 AccessDenied
function putnosuchBucket(){
	try{
		global $bucketname;
		global $client;
		$client->putBucketWebsite(array(
				'Bucket' => 'tesaweaweat',
				'ErrorDocument' => array(
						'Key' => 'error.html'
				),
				'IndexDocument' => array(
						'Suffix' => 'index.html'
				),
		));
		
		$result=$client->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;
		
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}

function getnosuchBucket(){
	try{
		global $bucketname;
		global $client;
		$client->putBucketWebsite(array(
				'Bucket' => $bucketname,
				'ErrorDocument' => array(
						'Key' => 'error.html'
				),
				'IndexDocument' => array(
						'Suffix' => 'index.html'
				),
		));
	
		$result=$client->getBucketWebsite( array(
				'Bucket' => 'testestsesets'
		));
		echo $result;
	
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}


function Cleanup(){
	global $client;
	global $bucketname;
	$client->deleteObjects(array(
			'Bucket' => $bucketname,
			'Objects' => array(
					array(
							'Key' => 'error.html'
					),
					array(
							'Key' => 'index.html'
					)
			),
	));
	
	$client->deleteBucket(array(
			'Bucket' => $bucketname
	));
}
echo "Testing Neg_Website Serial...";
Init();
fakeAccessputWebsite();
fakeSecretputWebsite();
putnosuchBucket();
getnosuchBucket();
Cleanup();

?>
</pre>