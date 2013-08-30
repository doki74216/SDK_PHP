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


function fakeAccessClient(){
	global $bucketname;
	try
	{
		$fakeAccess = S3Client::factory(array(
		'key'    => '1234567',
		'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		
		$fakeAccess->createBucket(array(
			'Bucket' =>$bucketname
		));
		
	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function fakeSecretClient(){
	global $bucketname;
	try
	{
		$fakeSecret = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '1234567',
		));
		$fakeSecret->createBucket(array(
				'Bucket' =>$bucketname
		));

	}catch (S3Exception $e){
		ExpectException($e,403);
	}
}

function ACLnoOwner(){
	global $client;
	global $bucketname;
	try {
		
	$acp = AcpBuilder::newInstance();
	$acp->addGrantForEmail('FULL_CONTROL', 'w84miracle@gmail.com'); //must be another registered user's mail
	$acp->setOwner("201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54","comedy912"); //OwnerID must be correct
	$acp->addGrantForUser('FULL_CONTROL', "201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54");
	$acp->addGrantForGroup('FULL_CONTROL', Group	::ALL_USERS);
	$acp2 = $acp->build();
	
	$client->createBucket(array('Bucket'=>$bucketname));
	
	$client->putBucketAcl(array(
			'Bucket' => $bucketname ,
			'ACP'	 => $acp2
	));
	$result=$client->getBucketAcl(array(
			'Bucket' => $bucketname
	));
	
	}catch (S3Exception $e) {
		ExpectException($e,403);
	}
	
}

function ACLinvalidID(){
	global $client;
	global $bucketname;
	try {

		$acp = AcpBuilder::newInstance();
		$acp->addGrantForEmail('FULL_CONTROL', 'w84miracle@gmail.com');
		$acp->setOwner("1234567","comedy912"); //fake OwnerID 
		$acp->addGrantForUser('FULL_CONTROL', "201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54");
		$acp->addGrantForGroup('FULL_CONTROL', Group	::ALL_USERS);
		$acp2 = $acp->build();

		$client->createBucket(array('Bucket'=>$bucketname));

		$client->putBucketAcl(array(
				'Bucket' => $bucketname ,
				'ACP'	 => $acp2
		));
		$result=$client->getBucketAcl(array(
				'Bucket' => $bucketname
		));

	}catch (S3Exception $e) {
		ExpectException($e,400);
	}

}

function ACLinvalidMail(){
	global $client;
	global $bucketname;
	try {
	
		$acp = AcpBuilder::newInstance();
		$acp->addGrantForEmail('FULL_CONTROL', 'abc@cbaa.com'); //must be another registered user's mail
		$acp->setOwner("201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54","comedy912"); //OwnerID must be correct
		$acp->addGrantForUser('FULL_CONTROL', "201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54");
		$acp->addGrantForGroup('FULL_CONTROL', Group	::ALL_USERS);
		$acp2 = $acp->build();
	
		$client->createBucket(array('Bucket'=>$bucketname));
	
		$client->putBucketAcl(array(
				'Bucket' => $bucketname ,
				'ACP'	 => $acp2
		));
		$result=$client->getBucketAcl(array(
				'Bucket' => $bucketname
		));
	
	}catch (S3Exception $e) {
		ExpectException($e,400);
	}
	
}

fakeAccessClient();
fakeSecretClient();
ACLnoOwner();
ACLinvalidID();
ACLinvalidMail();
echo "Testing Neg_ACLSerial ..."
?>
</pre>