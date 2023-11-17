<?php // Default XML Feed

$irFeedArray = xml2array($feedURL, $get_attributes=1, $priority='tag');

// Take out first three array items 
$releases = $irFeedArray['releases'];
unset($releases['matching_count']);
unset($releases['returned_count']);
unset($releases['latestModified']);

$comoFeed .= '<ul'. (($id) ? ' id="'. $id .'"' : '') . (($class) ? ' class="'. $class .'"' : '') .'>'; 
for ($pr=0;$pr<$limit;$pr++) {
	$release = $releases['release'][$pr];
	$releaseID = $release['id'];
	$releaseDate = new DateTime();
	$releaseDate->setTimestamp($release['released']);
	$releaseYear = date_format($releaseDate, 'Y');
	$releaseMonth = date_format($releaseDate, 'n');
	$releaseDay = date_format($releaseDate, 'j');
	$releaseLink = '#';
	$releaseTitle = $release['headline'];
	
	$comoFeed .= '<li class="row news-item">';
	$comoFeed .= '<h3 class="news-date">'. $releaseMonth .'.'. $releaseDay .'.'. $releaseYear .'</h3>';
	$comoFeed .= '<div class="news-content"><h4 class="news-title"><a href="'. $releaseLink .'" title="'.  $releaseTitle .'">'. $releaseTitle .'</a></h4>';
	$comoFeed .= '</div></li>';
}
if ($link) {
	$comoFeed .= '<li class="news-item news-more-link"><a class="btn" href="'. $link .'" title="'. __($linkTitle,'como-feed') .'">'. __($linkTitle,'como-feed') .'</a></li>';
}
$comoFeed .= '</ul>';

