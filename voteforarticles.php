<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.voteforarticles
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$myfile = fopen("C:\\newfile2.txt", "w") or die("Unable to open file!");

/**
 * Voteforarticles plugin class.
 *
 */
class PlgContentVoteforarticles extends JPlugin
{
	/**
	 * Displays the voting area if in an article
	 *
	 * @param   string   $context  The context of the content being passed to the plugin
	 * @param   object   &$row     The article object
	 * @param   object   &$params  The article params
	 * @param   integer  $page     The 'page' number
	 *
	 * @return  mixed  html string containing code for the votes if in com_content else boolean false
	 *
	 * @since   1.6
	 */
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;


    public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
	{
		$app   = JFactory::getApplication();
		$view  = $app->input->get('view');
		$print = $app->input->getBool('print');
		
		if(empty($this->params["firstvote"]))	 {$firstvote 		 = 'You are the first to vote';}else{$firstvote 	= $this->params["firstvote"];}
		if(empty($this->params["alreadyvoted"])) {$alreadyvoted 	 = 'You already vote !';}       else{$alreadyvoted  = $this->params["alreadyvoted"];}
		if(empty($this->params["thanksmessage"])){$thanksmessage = 'Thanks to vote';}          		else{$thanksmessage = $this->params["thanksmessage"];}
		if(empty($this->params["averagerating"])){$averagerating = 'Average rating';}           	else{$averagerating = $this->params["averagerating"];}
		if(empty($this->params["on"]))           {$on = 'on';}										else{$on			= $this->params["on"];}				
		if(empty($this->params["numberofvotes"])){$numberofvotes = 'rating';}                   	else{$numberofvotes = $this->params["numberofvotes"];}
		if ($print)
		{
			return false;
		}

		if (($context == 'com_content.article') && ($view == 'article'))
		{
			$article_id = JFactory::getApplication()->input->get('id');
			// echo "Articles id ".$article_id;

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT content_id, rating_sum, rating_count, ip_address FROM star_rating"
					."\n WHERE content_id = " . $article_id;
			// $query->select('`content_id`, `SUM(rating_sum)`, `rating_count`, `lastip`');
			// $query->from($db->quoteName('#__content_rating'));
			// $query->where($db->quoteName('content_id')." = ".$db->quote($article_id));
			 
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$result = $db->loadObjectList();
			$rating_count = 0;
			$rating_sum = 0;
			if (empty($result)) {
				$average_rating = 0;
				$average0 = 0;
			} else {
				foreach ($result as $re){
					$rating_sum += $re->rating_sum;
					$rating_count += $re->rating_count;
				}

				$average_rating = round($rating_sum / $rating_count , 1);
				$average0 = round($rating_sum / $rating_count , 0);
			}
			
			// $currentURL = $this->params->def('websiteBaseURL');
			// if ($currentURL == ''){
				// $currentURL = 'http://'.$_SERVER['HTTP_HOST'];
			// }
			
			$currentURL = JURI::base() . 'plugins/content/voteforarticles/';
			$images 	= JURI::base() . 'plugins/content/voteforarticles/images/';
			$myvote 	= JURI::base() . 'plugins/content/voteforarticles/myvote.php';
			
			$declareJS = "";
			// $declareJS  = "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>";
			
			$declareJS .= "<script type='text/javascript' src=\"".$currentURL."js/rating.js\"></script>";
			$declareJS .= "<link href=\"".$currentURL."css/rating.css\" rel=\"stylesheet\" />";
			
			$declareJS .= "<script type='text/javascript'>var jq=jQuery.noConflict();jq(function() {
			var images = jq('#images').val();
			jq('#rating_star').rating_actia({
				starLength: '5',
				initialValue: '$average0',
				callbackFunctionName: 'processRating',
				imageDirectory: '$images',
				inputAttr: 'postID'
			});
			});
			</script>";
			$declareJS .= "<script type='text/javascript'>
				function processRating(val, attrVal, alreadyvoted, firstvote, thanksmessage){
				var alreadyvoted 	= jq('#alreadyvoted').val();
				var firstvote 	 	= jq('#firstvote').val();
				var thanksmessage 	= jq('#thanksmessage').val();
				jq.ajax({
					type: 'POST',
					url : '$myvote',
					data: 'postID='+attrVal+'&ratingPoints='+val+'&alreadyvoted='+alreadyvoted+'&firstvote='+firstvote+'&thanksmessage='+thanksmessage,
					dataType: 'json',
					success : function(data) {
							jq('#voteforarticles').html(
							  data['db']
							);
				}
				});
			}
			</script>";

			// echo "declare ".$declareJS;

			if(!$rating_count){
				$rating_count = 0;
			}
			$input = JFactory::getApplication()->input;
			$id = $input->getInt('id'); //get the article ID
			$articlem = JTable::getInstance('content');
			$articlem->load($id);
			$title = $articlem->get('title');
			// echo "title ".$title;

						
			$html = '<div itempscope itemptype="http://schema.org/Article">
								<span itemprop="name">' . $title . '</span></div>';

			$html .= '<div><input type="hidden" name="alreadyvoted" id="alreadyvoted" value="' . $alreadyvoted . '"/>
						 <input type="hidden" name="firstvote" id="firstvote" value="' . $firstvote . '"/>
						 <input type="hidden" name="thanksmessage" id="thanksmessage" value="' . $thanksmessage . '"/>
						 <input name="rating" value="0" id="rating_star" type="hidden" postID="' . $article_id . '" />
					</div>';

			// echo $alreadyvoted;
			$html .= '<div id="voteforarticles" class="overall-rating" itemprop="aggregateRating" itemscope itemptype="http://schema.org/AggregateRating">
							Note <span itemprop="ratingValue">' . $average_rating . '</span> sur <span itemprop="bestRating">5</span>
							Pour <span itemprop="reviewCount">' . $rating_count  . ' votes</span>
							<meta itemprop="worstRating" content="1" />							
					 </div>';			

			
					 
			// $html .= JText::_("PLG_VOTEFORARTICLES_FIRST_VOTE");

			return $declareJS . $html;
		}
	}
}
