<pre>
<?php
/*
 * test 1. Basic putBucket
 * 		2. put Object
 * 		3. put BucketWebsite (Basic & with redirect rules)
 * 		4. get BucketWebsite
 * 		5. Delete BucketWebsite
 */

use Aws\S3\Exception\S3Exception;
require 'client.php';
$bucketname=$argv[1];

try{
	echo "Website Serial Testing...<br>";
	$client->createBucket(array(
		'Bucket' => $bucketname,
		'ACL' 	 => 'public-read'	
	));
	
	$client->putObject(array(
			'Body'   => '404testchttl<br><title>chttl</title>',
			'Bucket' => $bucketname,
			'Key'	 => 'error.html',
			'ACL' 	 => 'public-read',
			'WebsiteRedirectLocation' => 'http://google.com'
	));
	
	$client->putObject(array(
			'Body'   => 'Hello world!',
			'Bucket' => $bucketname,
			'Key'	 => 'index.html',
			'ACL' 	 => 'public-read'
	));
	$client->putBucketWebsite(array(
			'Bucket' => $bucketname,
			'ContentMD5' => 'false',
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
	
	//apply routing rules, may take some time for applying new rules  
	$client->putBucketWebsite(array(
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
	
	$result=$client->getBucketWebsite( array(
			'Bucket' => $bucketname
	));
	
	$client->deleteBucketWebsite(array(
			'Bucket' => $bucketname
	));
	
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
}catch (S3Exception $e) {
		echo "<font color=red>¡I</font>Caught an AmazonServiceException<br>";
		echo "Error Message:    " . $e->getMessage()."<br>";
		echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
		echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
		echo "Error Type:       " . $e->getExceptionType()."<br>";
		echo "Request ID:       " . $e->getRequestId()."<br>";
}
?>
</pre>
