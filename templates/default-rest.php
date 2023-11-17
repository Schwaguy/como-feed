<?php // Default Rest Feed

$comoFeed .= '<ul id="'. $id .'" class="'. $class .'">'; 
foreach ($posts as $post) {
	$releaseDate = new DateTime($post->{'releaseDate'}->{'date'});
	$releaseYear = date_format($releaseDate, 'Y');
	//$comoFeed = date_format($releaseDate, 'n');
	$releaseMonth = date_format($releaseDate, 'n');
	$releaseDay = date_format($releaseDate, 'j');
	$releaseLink = $cmsDomain .'/node/'. $post->{'id'};
	$releaseTitle = $post->{'title'};
	$releaseExcerpt = (($excerpt) ? customExcerpt($post->{'teaser'},$length,'...',true) : '');
	
	$comoFeed .= '<li class="row news-item">';
	$comoFeed .= '<h3 class="news-date">'. $releaseMonth .'.'. $releaseDay .'.'. $releaseYear .'</h3>';
	$comoFeed .= '<div class="news-content"><h4 class="news-title"><a href="'. $releaseLink .'" title="'.  $releaseTitle .'">'. $releaseTitle .'</a></h4>';
	$comoFeed .= ($excerpt ? $releaseExcerpt : '');
	$comoFeed .= '</div></li>';
} 
if ($link) {
	$comoFeed .= '<li class="news-item news-more-link"><a class="btn" href="'. $link .'" title="'. __($linkTitle,'como-feed') .'">'. __($linkTitle,'como-feed') .'</a></li>';
}
$comoFeed .= '</ul>';