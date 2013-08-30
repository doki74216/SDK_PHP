<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;
use Aws\S3\Enum\GranteeType;

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

function fakeAccessputLogging(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
	
		$fakeAccess->putBucketLogging(array(
				'Bucket' => $bucketname,
				'LoggingEnabled' => array(
						'TargetBucket' => $bucketname,
						'TargetGrants' => array(
								'Grant' => array(
										'Grantee' => array(
												'Type' => GranteeType::USER,
												'ID' => '201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54',
										),
										'Permission' => 'FULL_CONTROL',
								),
						),
						'TargetPrefix' => 'log-',
				),
		));
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretputLogging(){
	global $bucketname;
	try
	{

		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));


		$fakeSecret->putBucketLogging(array(
				'Bucket' => $bucketname,
				'LoggingEnabled' => array(
						'TargetBucket' => $bucketname,
						'TargetGrants' => array(
								'Grant' => array(
										'Grantee' => array(
												'Type' => GranteeType::USER,
												'ID' => '201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54',
										),
										'Permission' => 'FULL_CONTROL',
								),
						),
						'TargetPrefix' => 'log-',
				),
		));

	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeAccessgetLogging(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
				'key'    => '1234567',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));

		$result=$fakeAccess->getBucketLogging(array(
			'Bucket' => $bucketname,
		));
	
		echo $result;
	}catch (S3Exception $e){
		ExpectException($e,403);
	}

}

function fakeSecretgetLogging(){
	global $bucketname;
	try
	{
	
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		
		$result=$fakeSecret->getBucketLogging(array(
				'Bucket' => $bucketname,
		));
		
		echo $result;
	
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function log_group_need_permission(){
	try {
		global $client;
		global $bucketname;
		$client->putBucketLogging(array(
				'Bucket' => $bucketname,
				'LoggingEnabled' => array(
						'TargetBucket' => $bucketname,
						'TargetGrants' => array(
								'Grant' => array(
										'Grantee' => array(
												'Type' => GranteeType::USER,
												'ID' => '201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54',
										),
										'Permission' => 'FULL_CONTROL',
								),
						),
						'TargetPrefix' => 'log-',
				),
		));
		
	} catch (S3Exception $e) {
		ExpectException($e, 400);
	}
}

function putLogging_nosuchBucket(){
	global $bucketname;
	global $client;
	try
	{
		$client->putBucketLogging(array(
				'Bucket' => 'nosuchbucket',
				'LoggingEnabled' => array(
						'TargetBucket' => $bucketname,
						'TargetGrants' => array(
								'Grant' => array(
										'Grantee' => array(
												'Type' => GranteeType::USER,
												'ID' => '201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54',
										),
										'Permission' => 'FULL_CONTROL',
								),
						),
						'TargetPrefix' => 'log-',
				),
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
	
	echo "Testing Neg_Logging Serial ...";
	Init();
	fakeAccessputLogging();
	fakeSecretputLogging();
	
	fakeAccessgetLogging();
	fakeSecretgetLogging();
	
	//log-delivery group need WRITE & READ_ACP
	log_group_need_permission();
	putLogging_nosuchBucket();
	
	Cleanup();
	
?>
</pre>

