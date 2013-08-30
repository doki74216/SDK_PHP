<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;

$bucketname=$argv[1];
$prefix ='index';
$id='testLC';

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
	global $prefix;
	global $id;
	$client->createBucket(array(
			'Bucket' => $bucketname,
	));
	
	$client->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'index.html',
			'ACL' 	 => 'public-read'
	));
	sleep(10);
	$client->putBucketLifecycle(array(
			'Bucket' => $bucketname ,
			'Rules' => array(
					array(
							'Expiration'=> array(
									'Days' => 1,
							),
							'ID' => $id ,
							'Prefix' => $prefix ,
							'Status' => 'Enabled',
					),
			)
	));
	
}

function fakeAccessputLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
		$fakeAccess = S3Client::factory(array(
		'key'    => '1234567',
		'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		
		$fakeAccess->putBucketLifecycle(array(
			'Bucket' => $bucketname ,
			'Rules' => array(
					array(
							'Expiration'=> array(
									'Days' => 1,
							),
							'ID' => $id ,
							'Prefix' => $prefix ,
							'Status' => 'Enabled',
					),
			)
		));
		
		$result=$fakeAccess->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
	
}

function fakeSecretputLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		

		$fakeSecret->putBucketLifecycle(array(
			'Bucket' => $bucketname ,
			'Rules' => array(
					array(
							'Expiration'=> array(
									'Days' => 1,
							),
							'ID' => $id ,
							'Prefix' => $prefix ,
							'Status' => 'Enabled',
					),
			)
		));
		
		$result=$fakeSecret->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;
		

	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessgetLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretgetLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		
		$result=$fakeSecret->getBucketWebsite( array(
				'Bucket' => $bucketname
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeAccessdelLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->deleteBucketLifecycle(array(
		'Bucket' => $bucketname,
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretdelLifecycle(){
	global $bucketname;
	global $prefix;
	global $id;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$result=$fakeSecret->deleteBucketLifecycle(array(
		'Bucket' => $bucketname,
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function putnosuchBucket(){
	try{
		global $bucketname;
		global $client;
		global $prefix;
		global $id;
		$client->putBucketLifecycle(array(
			'Bucket' => 'nosuchbucket' ,
			'Rules' => array(
					array(
							'Expiration'=> array(
									'Days' => 1,
							),
							'ID' => $id ,
							'Prefix' => $prefix ,
							'Status' => 'Enabled',
					),
			)
		));
		
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}

function getnosuchBucket(){
	try{
		global $bucketname;
		global $client;
		global $prefix;
		global $id;
		
		$result=$client->getBucketWebsite( array(
				'Bucket' => 'nosuchbucket'
		));
		
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}

function prefixOverlapping(){
	try{
		global $client;
		global $bucketname;
		global $prefix;
		global $id;
	
		$client->putBucketLifecycle(array(
				'Bucket' => $bucketname ,
				'Rules' => array(
						array(
								'Expiration'=> array(
										'Days' => 1,
								),
								'ID' => 'test1' ,
								'Prefix' => 'gg' ,
								'Status' => 'Enabled',
						),
				)
		));
		
		$client->putBucketLifecycle(array(
				'Bucket' => $bucketname ,
				'Rules' => array(
						array(
								'Expiration'=> array(
										'Days' => 1,
								),
								'ID' => 'test2' ,
								'Prefix' => 'gghh' ,
								'Status' => 'Enabled',
						),
				)
		));
	}catch(S3Exception $e){
		ExpectException($e, 400);
	}
}

function Cleanup(){
	global $client;
	global $bucketname;
	$client->deleteObject(array(
			'Bucket' => $bucketname,
			'Key' => 'index.html'
	));
	
	$client->deleteBucket(array(
			'Bucket' => $bucketname
	));
}

echo "Testing Neg_lifecycle Serial....";
Init();
fakeAccessputLifecycle();
fakeSecretputLifecycle();
fakeAccessgetLifecycle();
fakeSecretgetLifecycle();
fakeAccessdelLifecycle();
fakeSecretdelLifecycle();
putnosuchBucket();
getnosuchBucket();

//rules will be overwrited if prefix overlapping occured... 
prefixOverlapping();

Cleanup();

?>
</pre>