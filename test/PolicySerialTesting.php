<pre>
<?php
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Exception\NoSuchBucketPolicyException;
require './aws-autoloader.php';
require 'client.php';
/*
 * test 1. Put bucket policy
 * 		2. Get bucket policy
 * 		3. Delete bucket policy
 * 
 */
try
{
	echo "Policy Serial Testing...";
	$bucketname=$argv[1];

	$client->createBucket(array(
			'Bucket' => $bucketname
	));
	//policy to deny all user to GetObject() 
	$policy='{"Version":"2012-10-17","Statement":[{"Sid":"DenyPublicREAD","Effect":"Deny","Principal":{"AWS":"*"},"Action":"s3:GetObject","Resource":"arn:aws:s3:::'.$bucketname.'/*"}]}';
	
	$client->putBucketPolicy(array(
		'Bucket' => $bucketname,
		'Policy' => $policy
	));
	
	$result=$client->getBucketPolicy(array(
			'Bucket' => $bucketname
	));
	
	$client->deleteBucketPolicy(array(
			'Bucket' => $bucketname
	)); 
	
	if(! $client->doesBucketPolicyExist($bucketname)){
		$client->deleteBucket(array(
				'Bucket' => $bucketname
		));
	}
	
}catch(S3Exception $e)
{
	echo "<font color=red>¡I</font>Caught an AmazonServiceException.<br>";
	echo "Error Message:    " . $e->getMessage()."<br>";
	echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
	echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
	echo "Error Type:       " . $e->getExceptionType()."<br>";
	echo "Request ID:       " . $e->getRequestId()."<br>";
}catch(ValidationException $e)
{
	echo "<font color=red>¡I</font>Caught an ClientException, check your inputs";
	echo "Error Message:    " . $e->getMessage()."<br>";
	echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
	echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
	echo "Error Type:       " . $e->getExceptionType()."<br>";
	echo "Request ID:       " . $e->getRequestId()."<br>";
}
?>
</pre>