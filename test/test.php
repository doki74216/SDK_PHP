<?php
// Include the SDK using the Composer autoloader
//	$client->registerStreamWrapper();
//	putObject("/photo/test1.jpg", createSampleFile());
require './aws-autoloader.php';

use Aws\Common\Aws;
use Aws\S3\S3Client;
use Aws\S3\Exception\BucketAlreadyExistsException;

// Instantiate the S3 client with your AWS credentials and desired AWS region
$amzClient = S3Client::factory(array(
		'key'    => 'AKIAJSVJQLDTTJ4JATAA',
		'secret' => 'KsfrFsMZpRnNEDa6XTcimwzdc9/qpyobSDCWc/Ft',
));

$hiClient = S3Client::factory(array(
	    'key'    => 'edc98059ceb7f848d819e3da1400ab00',
	    'secret' => '8ca94ece8b03b8f44210ef31d0e8e41eae6cd554bf48557581fdd47685dbe799',
		'base_url' => 'http://s3.hicloud.net.tw'
));

$bucketname ='chttest5';
$client = $amzClient;
//$client->registerStreamWrapper();

/*$client->getConfig()->set('curl.options', array(
		CURLOPT_PROXY=>"10.160.3.88",
		CURLOPT_PROXYPORT=>"8080"
));*/
//$client->createBucket(array('Bucket' => $bucketname));



//createBucket();
//putObject('321.txt');
//delObject();
//deleteBucketList();
showBucketList();
//createSampleFile();
//$dir = "s3://testyaya/";


/*
if (is_dir($dir) && ($dh = opendir($dir))) {
    while (($file = readdir($dh)) !== false) {
        echo "filename: {$file} : filetype: " . filetype($dir . $file) . "\n";
    }
    closedir($dh);
}
*/
function createBucket()
{
	global $bucketname;
	global $client;
	try {
		$result = $client->createBucket(array('Bucket' => $bucketname));
	} catch (BucketAlreadyExistsException $e) {
		echo 'Bucket "'. $bucketname .'" already exists! <br>' .$e->getMessage()."<br><br>" ;
	}
}

function deleteBucketList()
{
	global $bucketname;
	global $client;
	$client->deleteBucket(array('Bucket' => $bucketname));
	
	// Wait until the bucket is not accessible
	$client->waitUntilBucketNotExists(array('Bucket' => $bucketname));
}


function showBucketList()

{	
	global $client;
	$result = $client->listBuckets();
	foreach ($result['Buckets'] as $bucket) {
		// Each Bucket value will contain a Name and CreationDate
		print_r("{$bucket['Name']} - {$bucket['CreationDate']} <br>");
	}
}

function putObject($objName)
{
	global $bucketname;
	global $client;
	$result = $client->putObject(array(
		'Bucket' => $bucketname,
		'Key'    => $objName,
		'Body'   => createSampleFile()
	));
	echo $result['ObjectURL'] . "<br>";
}

function delObject()
{
	global $bucketname;
	global $client;
	$result = $client->deleteObject(array(
			'Bucket' => $bucketname ,
			'Key' => 'data2.txt'
	));
	echo $result['Key'];
}

/* 
		 $result = $client->getObject(array(
		'Bucket' => $bucketname ,
		'Key'    => '123.txt'
));
echo $result['Body'];

*/

/*list file version
$iterator = $client->getIterator('listObjectVersions', array(
		'Bucket' => $bucket
));

foreach ($iterator as $object) {
	echo $object['Key'] . "\n";
}
*/	
function createSampleFile()
{
	$temp = tmpfile();
	$content = "abcdefghijklmnopqrstuvwxyz<br>01234567890112345678901234<br>!@#$%^&*()-=[]{};':',.<>/?<br>01234567890112345678901234<br>abcdefghijklmnopqrstuvwxyz<br>";
	fwrite($temp, $content);
	fseek($temp, 0);
	return $temp;
	fclose($temp); // this removes the file
}

/*$client->putBucketAcl(array(
		'Bucket' => $bucketname,
		'ACL'	 => CannedAcl::PUBLIC_READ
));
*/


?>