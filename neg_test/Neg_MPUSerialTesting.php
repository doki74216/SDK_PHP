<pre>
<?php
require 'client.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Group;
use Guzzle\Service\Exception\ValidationException;

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
			'Bucket' => $bucketname
	));
}
//fakeclient with Invalid Access Key
function fakeAccessInit(){
	try
	{
		global $bucketname;
		$client = S3Client::factory(array(
				'key'    => '123',
				'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
		));
		
		$result=$client->createMultipartUpload(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg'
		));
		$uploadID=$result['UploadId'];
	}catch(S3Exception $e){
		ExpectException($e,403);
	}
	
}
function fakeSecretInit(){
	try
	{
		global $bucketname;
		$client = S3Client::factory(array(
				'key'    => 'AKIAJSVJQLDTTJ4JATAA',
				'secret' => '123',
		));
		
		$result=$client->createMultipartUpload(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg'
		));
		$uploadID=$result['UploadId'];
	}catch(S3Exception $e){
		ExpectException($e,403);
	}

}

function UploadPartnoUploadID(){
	try
	{	
	global $client;
	global $bucketname;
	
	$result=$client->createMultipartUpload(array(
			'Bucket' => $bucketname,
			'Key'	 => 'castle.jpg'
	));
	$uploadID=$result['UploadId'];
	
	$result=$client->listMultipartUploads(array(
			'Bucket' => $bucketname,
	));
	
	$client->uploadPart(array(
			'Body'	 => fopen('D:/castle.jpg.001','r+'),
			'Bucket' => $bucketname,
			'Key'	 => 'castle.jpg',
			'PartNumber' => '1',
			//'UploadId'=> $uploadID
	));
	
	$client->abortMultipartUpload(array(
			'Bucket' => $bucketname,
			'Key'	 => 'castle.jpg',
			'UploadId'=> $uploadID
	));
	
	$client->deleteBucket(array(
			'Bucket' => $bucketname
	));
	}catch(ValidationException $e){
		if($e->getMessage()!='Validation errors: [UploadId] is a required string: Upload ID identifying the multipart upload whose part is being uploaded.')
			{
				echo "<font color=red>¡I</font>Expected Status Code : [UploadId] is a required string , get another Exception...<br>";
				echo $e->getMessage();
			}
	}
}
function CompletenoUploadID(){
	try{
		global $client;
		global $bucketname;
		
		$result=$client->createMultipartUpload(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg'
		));
		$uploadID=$result['UploadId'];
		
		$result=$client->listMultipartUploads(array(
				'Bucket' => $bucketname,
		));
		$client->uploadPart(array(
				'Body'	 => fopen('D:/castle.jpg.001','r+'),
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg',
				'PartNumber' => '1',
				//'UploadId'=> $uploadID
		));
		$client->uploadPart(array(
				'Body'	 => fopen('D:/castle.jpg.002','r+'),
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg',
				'PartNumber' => '2',
				'UploadId'=> $uploadID
		));
		$client->uploadPart(array(
				'Body'	 => fopen('D:/castle.jpg.003','r+'),
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg',
				'PartNumber' => '3',
				'UploadId'=> $uploadID
		));
		
		
		$result=$client->listParts(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg',
				'UploadId'=> $uploadID
		));
		
		$parts=$result['Parts'];
		
		$result=$client->completeMultipartUpload(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg',
				'Parts'	 => $parts
		));
		
	}catch(ValidationException $e){
		if($e->getMessage()!='Validation errors: [UploadId] is a required string: Upload ID identifying the multipart upload whose part is being uploaded.')
		{
			echo "<font color=red>¡I</font>Expected Status Code : [UploadId] is a required string , get another Exception...<br>";
			echo $e->getMessage();
		}
	}
}
function AbortnoUploadID(){
	try{
		global $client;
		global $bucketname;
		$result=$client->createMultipartUpload(array(
				'Bucket' => $bucketname,
				'Key'	 => 'castle.jpg'
		));
		$uploadID=$result['UploadId'];
		
		$result=$client->listMultipartUploads(array(
				'Bucket' => $bucketname,
		));
		
		$client->abortMultipartUpload(array(
			'Bucket' => $bucketname,
			'Key'	 => 'castle.jpg',
			//'UploadId'=> $uploadID
		));
	}catch(ValidationException $e){
		if($e->getMessage()!='Validation errors: [UploadId] is a required string')
		{
			echo "<font color=red>¡I</font>Expected Status Code : [UploadId] is a required string , get another Exception...<br>";
			echo $e->getMessage();
		}
	}
}

echo "Testing Neg_MPUSerial Test ...";
Init();
fakeAccessInit();
fakeSecretInit();
UploadPartnoUploadID();
CompletenoUploadID();
AbortnoUploadID();
$client->deleteBucket(array(
	'Bucket'=>$bucketname
));

?>
</pre>