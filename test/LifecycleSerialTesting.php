<pre>
<?php
/*
 * test 1. putBucket
 * 		2. put Object
 * 		3. put BucketLifecycle (expire on date & expire in days)
 * 		4. get BucketLifecycle
 * 		5. Delete BucketLifecycle
 */

use Aws\S3\Exception\S3Exception;
use Guzzle\Service\Exception\ValidationException;

require 'client.php';

$bucketname=$argv[2];
$id='testLC';
$id2='testLC2';
$prefix='test.txt';
$datetime = new DateTime('17 Oct 2020');

function createSampleFile()
{
	$temp = tmpfile();
	$content = "abcdefghijklmnopqrstuvwxyz<br>01234567890112345678901234<br>!@#$%^&*()-=[]{};':',.<>/?<br>01234567890112345678901234<br>abcdefghijklmnopqrstuvwxyz<br>";
	fwrite($temp, $content);
	fseek($temp, 0);
	return $temp;
	fclose($temp); // this removes the file
}
try
{
echo "Lifecycle Serial testing...<br>";
	
$client->createBucket(array(
	'Bucket' => $bucketname
));
$result = $client->putObject(array(
		'Bucket' => $bucketname,
		'Key'    => $prefix,
		'Body'   => createSampleFile()
	));


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

$client->putBucketLifecycle(array(
		'Bucket' => $bucketname ,
		'Rules' => array(
				array(
						'Expiration'=> array(
								'Date' => "GMT ".$datetime->format('c'),
						),
						'ID' => $id2 ,
						'Prefix' => $prefix ,
						'Status' => 'Enabled',
				),
		)
));


$result = $client->getBucketLifecycle(array(
	'Bucket' => $bucketname,
));
/*
echo "Listing Rules...\n";
			
foreach($result['Rules'] as $rule){
	echo "--------------------------\n";
	echo "[ID]" . $rule['ID']." \n".
         "[Prefix]".$rule['Prefix']." \n".
         "[Status]".$rule['Status']." \n";
    	if(empty($rule['Expiration']['Date']))     
			echo"[Expiration] in ".$rule['Expiration']['Days']." days \n";
    	else 
    		echo"[Expiration] on ".$rule['Expiration']['Date']." \n";
}
*/
$client->putBucketLifecycle(array(
		'Bucket' => $bucketname ,
		'Rules' => array(
				array(
						'Expiration'=> array(
								'Days' => 1,
						),
						'ID' => $id ,
						'Prefix' => $prefix ,
						'Status' => 'Disabled',
				),
		)
));

$result = $client->getBucketLifecycle(array(
		'Bucket' => $bucketname,
));

$client->deleteBucketLifecycle(array(
		'Bucket' => $bucketname,
));

$client->deleteObject(array(
		'Bucket' => $bucketname,
		'Key'	=> $prefix 
));
$client->deleteBucket(array(
		'Bucket' => $bucketname
));





}catch(ValidationException $e){
		echo $e->getMessage();
} catch (S3Exception $e) {
	echo "<font color=red>¡I</font>Caught an AmazonServiceException.<br>";
	echo "Error Message:    " . $e->getMessage()."<br>";
	echo "HTTP Status Code: " . $e->getStatusCode()."<br>";
	echo "AWS Error Code:   " . $e->getExceptionCode()."<br>";
	echo "Error Type:       " . $e->getExceptionType()."<br>";
	echo "Request ID:       " . $e->getRequestId()."<br>";
}

?>
</pre>