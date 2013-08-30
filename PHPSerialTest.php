<pre>
<?php
//php.ini max_execution_time = 600 ...

//Change test buckets' name here
$bucket = array("allentest1","allentest2","allentest");

echo "S3 PHP SDK Serial Test-\nbucketname1: ".$bucket[0]." ,bucketname2: ".$bucket[1]." ,bucketname3: ".$bucket[2];
echo "\n-----------------------------------------------------------------------\n";

//Bucketname 傳進去之後array index 是從 1 開始 ,$argv[1],$argv[2]...
system("php test/ACLSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/BucketLoggingSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/BucketSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/LifecycleSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/ObjectSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/PolicySerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/VersioningSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/WebsiteSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php test/MPUSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);


system("php neg_test/Neg_ACLSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
//Neg_BucketSerialTesting會因為要建很多bucket跑很久
system("php neg_test/Neg_BucketSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/Neg_MPUSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/Neg_WebsiteSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/S_Neg_LoggingSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/S_Neg_LifecycleSerial.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/S_Neg_ObjectSerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/S_Neg_PolicySerialTesting.php $bucket[0] $bucket[1] $bucket[2]");
sleep(5);
system("php neg_test/S_Neg_VersioningSerialTesting.php $bucket[0] $bucket[1] $bucket[2]"); 

echo "\nS3 Python SDK Serial Test Done!";
?>
</pre>