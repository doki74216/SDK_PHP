<?php
 use Aws\S3\S3Client;
 use Guzzle\Plugin\Log\LogPlugin;
 require './aws-autoloader.php';
 $client = S3Client::factory(array(
			'key'    => 'AKIAJSVJQLDTTJ4JATAA',
			'secret' => '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',
));
 //$client->addSubscriber(LogPlugin::getDebugPlugin());
?>