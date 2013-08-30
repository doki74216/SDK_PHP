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
	));
	
	$client->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'test.txt',
			'ACL' 	 => 'public-read'
	));
}

function fakeAccessputObject(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
		'key'    => '1234567',
		'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		
		$fakeAccess->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'test.txt',
			'ACL' 	 => 'public-read'
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
	
}

function fakeSecretputObject(){
	global $bucketname;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		

		$fakeSecret->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'test.txt',
			'ACL' 	 => 'public-read'
		));
	
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessgetObject(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->getObject(array(
   			 'Bucket' => $bucketname,
   			 'Key'    => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretgetObject(){
	global $bucketname;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		
		$result=$fakeSecret->getObject(array(
   			 'Bucket' => $bucketname,
   			 'Key'    => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeAccessdelObject(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->deleteObject(array(
			'Bucket' => $bucketname,
			'Key' => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretdelObject(){
	global $bucketname;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$result=$fakeSecret->deleteObject(array(
			'Bucket' => $bucketname,
			'Key' => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeAccessheadObject(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->headObject(array(
				'Bucket' => $bucketname,
				'Key' => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretheadObject(){
	global $bucketname;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$result=$fakeSecret->headObject(array(
				'Bucket' => $bucketname,
				'Key' => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeAccess_copyObject(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		$result=$fakeAccess->copyObject(array(
				'CopySource' => $bucketname."/"."test.txt",
				'Bucket' => $bucketname,
				'Key' => 'copied-test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecret_copyObject(){
	global $bucketname;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));

		$result=$fakeSecret->copyObject(array(
				'CopySource' => $bucketname."/"."test.txt",
				'Bucket' => $bucketname,
				'Key' => 'copied-test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function copyObject_nosuchBucket(){
	try{
		global $bucketname;
		global $client;

		$client->copyObject(array(
				'CopySource' => 'nosuchbucket'."/"."test.txt",
				'Bucket' => $bucketname,
				'Key' => 'copied-test.txt'
		));
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}

function copyObject_nosuchKey(){
	try{
		global $bucketname;
		global $client;

		$client->copyObject(array(
				'CopySource' => $bucketname."/"."nosuchkey",
				'Bucket' => $bucketname,
				'Key' => 'copied-test.txt'
		));
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}

function putObject_nosuchBucket(){
	try{
		global $bucketname;
		global $client;
		
		$client->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => 'nosuchbucket',
			'Key'	 => 'test.txt',
			'ACL' 	 => 'public-read'
		));
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}

function getObject_nosuchKey(){
	try{
		global $bucketname;
		global $client;
		$result=$client->getObject(array(
				'Bucket' => $bucketname,
				'Key'    => 'nosuchkey'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}

function getObject_nosuchBucket(){
	try{
		global $bucketname;
		global $client;
		$result=$client->getObject(array(
   			 'Bucket' => 'nosuchbucket',
   			 'Key'    => 'test.txt'
		));
		echo $result;
	
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
	
}

function delObject_nosuchKey(){
	try{
		global $bucketname;
		global $client;
		$result=$client->deleteObject(array(
				'Bucket' => $bucketname,
				'Key'    => 'nosuchkey'
		));
		

	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}

function delObject_nosuchBucket(){
	try{
		global $bucketname;
		global $client;
		$result=$client->deleteObject(array(
				'Bucket' => 'nosuchbuckssssset',
				'Key'    => 'test.txt'
		));
		echo $result;
	
	}catch (S3Exception $e){
		ExpectException($e, 404);
	}
}

function headObject_nosuchKey(){
	try{
		global $bucketname;
		global $client;
		$result=$client->headObject(array(
				'Bucket' => $bucketname,
				'Key'    => 'nosuchkey'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}

function headObject_nosuchBucket(){
	try{
		global $bucketname;
		global $client;
		$result=$client->headObject(array(
				'Bucket' => 'nosuchbucket',
				'Key'    => 'test.txt'
		));
		echo $result;

	}catch (S3Exception $e){
		ExpectException($e, 404);
	}

}


function Cleanup(){
	global $client;
	global $bucketname;
	$client->deleteObject(array(
			'Bucket' => $bucketname,
			'Key' => 'test.txt'
	));
	
	$client->deleteBucket(array(
			'Bucket' => $bucketname
	));
}


echo "Testing Neg_Object Serial...";
Init();

fakeAccessputObject();
fakeSecretputObject();
putObject_nosuchBucket();

fakeAccessgetObject();
fakeSecretgetObject();
getObject_nosuchKey();
getObject_nosuchBucket();

fakeAccessdelObject();
fakeSecretdelObject();
delObject_nosuchKey();
delObject_nosuchBucket();

fakeAccessheadObject();
fakeSecretheadObject();
headObject_nosuchKey();
headObject_nosuchBucket();

fakeAccess_copyObject();
fakeSecret_copyObject();
copyObject_nosuchBucket();
copyObject_nosuchKey();

Cleanup();

?>
</pre>