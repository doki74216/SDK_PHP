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
$policy='{"Version":"2012-10-17","Statement":[{"Sid":"DenyPublicREAD","Effect":"Deny","Principal":{"AWS":"*"},"Action":"s3:GetObject","Resource":"arn:aws:s3:::'.$bucketname.'/*"}]}';
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

function fakeAccessputBucketPolicy(){
	global $bucketname;
	global $policy;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
	
		$fakeAccess->putBucketPolicy(array(
		'Bucket' => $bucketname,
		'Policy' => $policy
		));
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretputBucketPolicy(){
	global $bucketname;
	global $policy;
	try
	{

		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));


		$fakeSecret->putBucketPolicy(array(
		'Bucket' => $bucketname,
		'Policy' => $policy
		));
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessgetBucketPolicy(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));

		$result=$fakeAccess->getBucketPolicy(array(
			'Bucket' => $bucketname,
		));
	
		echo $result;
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretgetBucketPolicy(){
	global $bucketname;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		
		$result=$fakeSecret->getBucketPolicy(array(
				'Bucket' => $bucketname,
		));
		
		echo $result;
	
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessdelBucketPolicy(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));

		$fakeAccess->deleteBucketPolicy(array(
			'Bucket' => $bucketname
		)); 
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretdelBucketPolicy(){
	global $bucketname;
	try
	{

		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$fakeSecret->deleteBucketPolicy(array(
			'Bucket' => $bucketname
		)); 

	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

//Status Code:400
function MalformedPolicy(){
	try {
		global $client;
		global $bucketname;
		//'Wersion'
		$policy='{"Wersion":"2012-10-17","Statement":[{"Sid":"DenyPublicREAD","Effect":"Deny","Principal":{"AWS":"*"},"Action":"s3:GetObject","Resource":"arn:aws:s3:::chttest5/*"}]}';
		$client->putBucketPolicy(array(
		'Bucket' => $bucketname,
		'Policy' => $policy
		));
		
	} catch (S3Exception $e) {
		ExpectException($e, 400);
	}
}

//Status Code:404
function putBucketPolicy_nosuchBucket(){
	global $bucketname;
	global $client;
	global $policy;
	try
	{
		$client->putBucketPolicy(array(
		'Bucket' => 'nosuchbucket',
		'Policy' => $policy
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
	
	echo "Testing Neg_Policy Serial...";
	Init();
	fakeAccessputBucketPolicy();
	fakeSecretputBucketPolicy();
	
	fakeAccessgetBucketPolicy();
	fakeSecretgetBucketPolicy();
	
	fakeAccessdelBucketPolicy();
	fakeSecretdelBucketPolicy();
	
	MalformedPolicy();
	putBucketPolicy_nosuchBucket();
	
	Cleanup();
	
?>
</pre>

