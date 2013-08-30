<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;
use Aws\S3\Enum\GranteeType;
use Aws\Sts\Exception\MalformedPolicyDocumentException;

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
	));
	
}

//Status Code:403

function fakeAccessputBucketVersioning(){
	global $bucketname;
	global $policy;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
	
		$fakeAccess->putBucketVersioning(array(
			'Bucket' => $bucketname,
			'Status' => 'Enabled'
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretputBucketVersioning(){
	global $bucketname;
	global $policy;
	try
	{

		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$fakeSecret->putBucketVersioning(array(
			'Bucket' => $bucketname,
			'Status' => 'Enabled'
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessgetBucketVersioning(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));

		$result=$fakeAccess->getBucketVersioning(array(
			'Bucket' => $bucketname,
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretgetBucketVersioning(){
	global $bucketname;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		
		$result=$fakeSecret->getBucketVersioning(array(
				'Bucket' => $bucketname,
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

//Status Code:404
function putBucketVersioning_nosuchBucket(){
	global $bucketname;
	global $client;
	global $policy;
	try
	{
		$client->putBucketVersioning(array(
		'Bucket' => 'nosuchbucket',
		'Status' => 'Disabled'
	));
	}catch (S3Exception $e){
		ExpectException($e,404);
	}
	
}

function Cleanup(){
	global $client;
	global $bucketname;
	
	$client->deleteBucket(array(
				'Bucket' => $bucketname
	));
}
	echo "Testing Neg_Versioning Serial...";
	Init();
	fakeAccessputBucketVersioning();
	fakeSecretputBucketVersioning();
	
	fakeAccessgetBucketVersioning();
	fakeSecretgetBucketVersioning();
	
	putBucketVersioning_nosuchBucket();
	
	Cleanup();
	
?>
</pre>

