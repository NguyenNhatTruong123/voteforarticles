<?php

// Set flag that this is a parent file
$myfile = fopen("C:\\newfile1.txt", "w") or die("Unable to open file!");
define('_JEXEC', 1);
define('JPATH_BASE', __DIR__ . '/../../..');
require JPATH_BASE . '/includes/defines.php';
require JPATH_BASE . '/includes/framework.php';

// use Joomla\CMS\Factory;



// fwrite($myfile, "1\n");
$container = \Joomla\CMS\Factory::getContainer();

$container->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

// Get the application.
$app      = $container->get(\Joomla\CMS\Application\SiteApplication::class);
// fwrite($myfile, "2\n");
// $app->initialise();
// fwrite($myfile, "3");

$user = JFactory::getUser();
// fwrite($myfile,"4\n");

$plugin	= JPluginHelper::getPlugin('content', 'extravote');
// fwrite($myfile, $params);

$params = new JRegistry;
$params->loadString($plugin->params);

if(!empty($_POST['ratingPoints'])){
	// fwrite($myfile, "1");
	$article_id   = $_POST['postID'];
    $ratingPoints = $_POST['ratingPoints'];	
    $alreadyvoted = $_POST['alreadyvoted'];	
    $firstvote = $_POST['firstvote'];	
    $thanksmessage = $_POST['thanksmessage'];
	// fwrite($myfile, $article_id."\n");
	// fwrite($myfile, $ratingPoints."\n");
    // $average_rating = $_POST['average_rating'];	
    // $numberofvotes = $_POST['numberofvotes'];	
    // $average_rating = $_POST['average_rating'];	
    // $rating_count = $_POST['rating_count'];	
	$current_ip   = $_SERVER['REMOTE_ADDR'];
	$id_rating = $article_id . "&" . $current_ip;
	fwrite($myfile, $id_rating."\n");
	fwrite($myfile, $current_ip."\n".$ratingPoints."\n".$article_id."\n");

// 	echo "articles id ".$article_id;

	$db       = JFactory::getDbo();
	// fwrite($myfile, "get db \n");
	$query = $db->getQuery(true);
	fwrite($myfile, "query\n");			

	$query = "SELECT content_id, rating_sum, rating_count, ip_address, id_rating FROM star_rating"
			."\n WHERE id_rating = '" . $id_rating ."'";

	$db->setQuery($query);
	fwrite($myfile, "select\n");
	$result = $db->loadObject();
	// fwrite($myfile, $result);

	if(!empty($result)){
		$query5 = $db->getQuery(true);			
		$query5 = "UPDATE star_rating"
				. "\n SET rating_count = rating_count + 1, rating_sum = rating_sum + " .   $ratingPoints . ", ip_address = " . $db->Quote( $current_ip )
				. "\n WHERE id_rating = '" . $id_rating ."'";
		$db->setQuery($query5);
		$db->execute();
	}
	else
	{
		fwrite($myfile, "insert new row\n");
		$query2 = $db->getQuery(true);			
		$query2 = "INSERT INTO star_rating (`content_id`, `rating_sum`, `rating_count`, `ip_address`, `id_rating`)"
				."\n VALUES(".$article_id.",'".$ratingPoints."','1','".$current_ip."','".$id_rating."')";
		$db->setQuery($query2);		
		$db->execute();
		fwrite($myfile, "execute insert \n");
	    $mail = $firstvote;
	}
    
	$query = "SELECT content_id, rating_sum, rating_count FROM star_rating WHERE content_id = ".$article_id;

	$db->setQuery($query);
	
	$result = $db->loadObjectList();
	$rating_count = 0;
	$rating_sum = 0;
	foreach($result as $re){
		// fwrite($myfile, "summm ".$re->rating_sum."\n");
		$rating_count += $re->rating_count;
		$rating_sum += $re->rating_sum;
	}

	$average_rating = round($rating_sum / $rating_count, 2);
}
        
echo json_encode(array("db"=>$mail));

// function debug_to_console($data) {
//     $output = $data;
//     if (is_array($output))
//         $output = implode(',', $output);

//     echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
// }
?>