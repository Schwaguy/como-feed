<?php // Default WordPress Feed

$comoFeed .= '<ul id="home-news-list" class="row justify-content-around">';
while ( have_posts() ) {
	the_post();
	$subtitle = (!empty(get_post_meta(get_the_ID(), 'postsubtitle')) ? customExcerpt(get_post_meta(get_the_ID(), 'postsubtitle')[0],$length,'...',true) : '');
	$comoFeed .= '<li class="news-item"><div class="news-wrap">';
	$comoFeed .= '<h4 class="news-date">' . get_the_date('n/j') .'</h4>';
	$comoFeed .= '<div class="news-content"><h5 class="news-title">'. get_the_title() .'</h5>';
	$comoFeed .= '<a href="'. get_the_permalink() .'" title="'.  get_the_title() .'" class="read-more">Read More</a>';
	$comoFeed .= '</div></div></li>';
} 
if (!empty($link)) {
	$comoFeed .= '<li class="news-item news-more-link"><a class="btn" href="'. $link .'" title="'. __($linkTitle,'como-feed') .'">'. __($linkTitle,'como-feed') .'</a></li>';
}
$comoFeed .= '</ul>';