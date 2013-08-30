<pre>
<?php
/*
 * test 1. Basic putBucket
* 		2. put BucketACL
* 		3. put BucketLogging (put log Native & to target bucket)
* 		4. get BucketLogging
* 		5. Delete Bucket
*/

use Aws\S3\Enum\GranteeType;
use Guzzle\Plugin\Log\LogPlugin;
use Aws\S3\Enum\Group;
use Aws\S3\Model\AcpBuilder;
use Aws\S3\Enum\Permission;
use Aws\S3\Exception\MalformedXMLException;
require './aws-autoloader.php';
require 'client.php';
// Add a debug log plugin
$bucketname=$argv[1];
$bucketname2=$argv[2];
try{
	echo "Bucket Logging Serial Testing...";
	$client->createBucket(array(
		'Bucket' => $bucketname,
	));
	$client->createBucket(array(
		'Bucket' => $bucketname2,
	));
	//log_delievery group must have WRITE & READ_ACP Permission
	$acp = AcpBuilder::newInstance();
	$acp->setOwner("201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54","comedy912"); //OwnerID must be correct
	$acp->addGrantForUser('FULL_CONTROL', "201721dd4688a6cdebd36b47d96ffe3f2d7d4d1374f36df3e6ada12a139d1c54");
	$acp->addGrantForGroup(Permission::WRITE, Group::LOG_DELIVERY);
	$acp->addGrantForGroup(Permission::READ_ACP, Group::LOG_DELIVERY);
	$acp2 = $acp->build();
	
	$client->putBucketAcl(array(
			'Bucket' => $bucketname,
			'ACP'	 => $acp2
	));
	
	$client->putBucketAcl(array(
			'Bucket' => $bucketname2,
			'ACP'	 => $acp2
	));
	
	$client->putBucketLogging(array(
			'Bucket' => $bucketname,
			'ContentMD5'=> 'false',
			'LoggingEnabled' => array(
            'TargetBucket' => $bucketname2,
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
	
	$result=$client->getBucketLogging(array(
		'Bucket' => $bucketname, 
	));
	
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
	
	$result=$client->getBucketLogging(array(
			'Bucket' => $bucketname,
	));
	
	$client->deleteBucket(array(
			'Bucket' => $bucketname
	));
	$client->deleteBucket(array(
			'Bucket' => $bucketname2
	));
	
}catch (S3Exception $e) {
	echo "<font color=red>¡I</font>Caught an AmazonServiceException.<br>";
	echo "Error Message:    " . $e->getMessage()."<br>";
	echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
	echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
	echo "Error Type:       " . $e->getExceptionType()."<br>";
	echo "Request ID:       " . $e->getRequestId()."<br>";
} catch (MalformedXMLException $e) {
	echo $e->__toString();
}

?>
</pre>