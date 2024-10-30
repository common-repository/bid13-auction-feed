<?php
/*
 * Plugin Name: Bid13 Auction Feed
 * Version: 1.26
 * Plugin URI: http://wp-demo.bid13.com
 * Description: Uses the bid13 API to generate a feed of auctions.
 * Author: Bid13 Storage Auctions
 * Author URI: http://www.bid13.com
 * Requires at least: 4.0
 * Tested up to: 5.6.1
 *
 * Text Domain: bid13-auction-feed
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Bid13 Storage Auctions
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-bid13-auction-feed.php' );
require_once( 'includes/class-bid13-auction-feed-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-bid13-auction-feed-admin-api.php' );
require_once( 'includes/lib/class-bid13-auction-feed-post-type.php' );
require_once( 'includes/lib/class-bid13-auction-feed-taxonomy.php' );

/**
 * Returns the main instance of Bid13_Auction_Feed to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Bid13_Auction_Feed
 */
function Bid13_Auction_Feed () {
	$instance = Bid13_Auction_Feed::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Bid13_Auction_Feed_Settings::instance( $instance );
	}

	return $instance;
}

Bid13_Auction_Feed();

/**
 * Make an API call and get the latest auction feed.
 * @since  1.0.1
 * @return decoded json object containing auctions from bid13 api.
 */
function bid13_auction_feed_get_auctions(){
	$request_parameters = array("facilities" => get_option('wpt_facilitynids'), "expired" => ! get_option('wpt_hide_expired'));
	return bid13_auction_feed_api_request('auctions', $request_parameters);
}

function bid13_auction_feed_get_facilities(){
	return bid13_auction_feed_api_request('facilities');
}

function bid13_auction_feed_api_request($endpoint, $args = array()){
	$baseUrl = 'https://bid13.com';
	$request_parameters = array_merge($args, array("api_key"=>get_option('wpt_api_key')));
	$response = wp_remote_get($baseUrl.'/api/v2/'.$endpoint.'?'.http_build_query($request_parameters));
	if(is_wp_error($response)) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );
	return json_decode($body);	
}

function bid13_auction_feed_state_short_code_to_full_name($short_code){
	$region_code_map = [
		'AL'=>'Alabama',
		'AK'=>'Alaska',
		'AS'=>'American Samoa',
		'AZ'=>'Arizona',
		'AR'=>'Arkansas',
		'AF'=>'Armed Forces Africa',
		'AA'=>'Armed Forces Americas',
		'AC'=>'Armed Forces Canada',
		'AE'=>'Armed Forces Europe',
		'AM'=>'Armed Forces Middle East',
		'AP'=>'Armed Forces Pacific',
		'CA'=>'California',
		'CO'=>'Colorado',
		'CT'=>'Connecticut',
		'DE'=>'Delaware',
		'DC'=>'District of Columbia',
		'FM'=>'Federated States Of Micronesia',
		'FL'=>'Florida',
		'GA'=>'Georgia',
		'GU'=>'Guam',
		'HI'=>'Hawaii',
		'ID'=>'Idaho',
		'IL'=>'Illinois',
		'IN'=>'Indiana',
		'IA'=>'Iowa',
		'KS'=>'Kansas',
		'KY'=>'Kentucky',
		'LA'=>'Louisiana',
		'ME'=>'Maine',
		'MH'=>'Marshall Islands',
		'MD'=>'Maryland',
		'MA'=>'Massachusetts',
		'MI'=>'Michigan',
		'MN'=>'Minnesota',
		'MS'=>'Mississippi',
		'MO'=>'Missouri',
		'MT'=>'Montana',
		'NE'=>'Nebraska',
		'NV'=>'Nevada',
		'NH'=>'New Hampshire',
		'NJ'=>'New Jersey',
		'NM'=>'New Mexico',
		'NY'=>'New York',
		'NC'=>'North Carolina',
		'ND'=>'North Dakota',
		'MP'=>'Northern Mariana Islands',
		'OH'=>'Ohio',
		'OK'=>'Oklahoma',
		'OR'=>'Oregon',
		'PW'=>'Palau',
		'PA'=>'Pennsylvania',
		'PR'=>'Puerto Rico',
		'RI'=>'Rhode Island',
		'SC'=>'South Carolina',
		'SD'=>'South Dakota',
		'TN'=>'Tennessee',
		'TX'=>'Texas',
		'UT'=>'Utah',
		'VT'=>'Vermont',
		'VI'=>'Virgin Islands',
		'VA'=>'Virginia',
		'WA'=>'Washington',
		'WV'=>'West Virginia',
		'WI'=>'Wisconsin',
		'WY'=>'Wyoming',
		'AB'=>'Alberta',
		'BC'=>'British Columbia',
		'MB'=>'Manitoba',
		'NL'=>'Newfoundland and Labrador',
		'NB'=>'New Brunswick',
		'NS'=>'Nova Scotia',
		'NT'=>'Northwest Territories',
		'NU'=>'Nunavut',
		'ON'=>'Ontario',
		'PE'=>'Prince Edward Island',
		'QC'=>'Quebec',
		'SK'=>'Saskatchewan',
		'YT'=>'Yukon Territory'
	];
	return $region_code_map[$short_code];
}
	
/**
 * Helper function to rewrite an array of urls to an array of html img embeds.
 * @since  1.0.1
 */
function bid13_auction_feed_url_to_img(&$item, $key){
	$item = "<img src='$item'>";
}

/**
 * Make an API call and get the latest auction feed.
 * TODO: add caching, more granular options, for selecting just a single facility and not just all of one companies auctions
 * @since  1.0.0
 * @return nothing, it prints to screen.
 */
function bid13_auction_feed_display( $atts ){
	ob_start();
	$sortedView = get_option('wpt_geo_sorted_view');  
  $results = bid13_auction_feed_get_auctions();
	if(is_object($results) && property_exists($results, 'error')){
		print $results->error;
	} else if(is_array($results) && array_key_exists('error', $results)){
		print $result['error'];
	} else { 
		$facilities_in_these_results = array_column($results, 'location_id');
		// user custom settings
		$linkColor = get_option('wpt_link_color');
		$textColor = get_option('wpt_text_color');
		$headlineColor = get_option('wpt_headline_color');
	?>
		<style>
			<?php if($linkColor) { ?>
				#bid13-auction-feed a{
					color: <?php echo $linkColor; ?>;
				}				
			<?php } ?>
			<?php if($textColor) { ?>
				#bid13-auction-feed div{
					color: <?php echo $textColor; ?>;
				}
			<?php } ?>
			<?php if($headlineColor) { ?>
				#bid13-auction-feed h1,
				#bid13-auction-feed h2,
				#bid13-auction-feed h3{
					color: <?php echo $headlineColor; ?>;
				}
			<?php } ?>

		</style>
		<div id="bid13-auction-feed" class="<?php echo $sortedView ? 'sortedview':''; ?>">
			
			<?php
				// get the facilicies and reorganize the results by [state][city] for printing out larger batches of auctions
				
				if( get_option("bid13_auctions_cached_facilities") === false ) {
					$facilities = bid13_auction_feed_get_facilities();
					add_option("bid13_auctions_cached_facilities", $facilities);
				} else {
					$facilities = get_option("bid13_auctions_cached_facilities");
				}		
				
				$facilities_reindexed = array();
				foreach($facilities as $facility){
					if(!in_array($facility->nid, $facilities_in_these_results)){
						continue;
					}
					if(!$facilities_reindexed[$facility->state]){
						$facilities_reindexed[$facility->state] = array();
					}
					if(!$facilities_reindexed[$facility->state][$facility->city]){
						$facilities_reindexed[$facility->state][$facility->city] = array();
					}
					$facilities_reindexed[$facility->state][$facility->city][] = $facility;
				}
				ksort($facilities_reindexed);
				foreach($facilities_reindexed as $key => $state){
					ksort($facilities_reindexed[$key]);
				}
			?>
			<?php if($sortedView && $results){ ?>
				<div class="filter-wrap">
					<span class="filter-text"><?php _e("Filter by Region","bid13-auction-feed");?>:</span>
					<select name="state" id="state-select">
							<option value="all"><?php _e("Show all","bid13-auction-feed");?></option>
						<?php foreach($facilities_reindexed as $state => $cities){ ?>
							<option value="<?php echo $state; ?>"><?php echo bid13_auction_feed_state_short_code_to_full_name($state); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="filter-wrap">
					<span class="filter-text"><?php _e("Filter by City","bid13-auction-feed");?>:</span>
					<select name="city" id="city-select">
							<option value="all" data-region="all"><?php _e("Show all","bid13-auction-feed");?></option>
						<?php foreach($facilities_reindexed as $state => $cities){ ?>
							<?php foreach($cities as $cityname => $city) { ?>
								<option value="<?php echo $cityname; ?>" data-region="<?php echo $state; ?>"><?php echo ucwords($cityname); ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				</div>	
				<?php foreach($facilities_reindexed as $state => $cities){ ?>
					<div class="region-wrap" data-region="<?php echo $state; ?>">
						<h2><?php echo bid13_auction_feed_state_short_code_to_full_name($state); ?> <span class="collapse"></span></h2>
						<?php foreach($cities as $cityname => $city) { ?>
							<div class="city-wrap" data-city="<?php echo $cityname; ?>" data-region="<?php echo $state; ?>">
								<h3><?php echo ucwords($cityname); ?> <span class="collapse"></span></h3>
								<?php foreach($city as $facility){ ?>
									<?php bid13_auction_feed_print_auctions($results, $facility->nid); ?>	
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } else { ?>
				<?php bid13_auction_feed_print_auctions($results, false); ?>
			<?php } ?>
			<?php if(!$results) { ?>
				<div id="no-auctions">
					<span><?php _e("There are no units currently available for auction. Please check back in the future...","bid13-auction-feed"); ?></span>
					<?php if(!get_option('wpt_newsletter_link')) { ?>
						<?php _e("If you would like to receive email updates about our future auctions please sign up for the ","bid13-auction-feed");?><a <?php echo $openLinksInNewTab ? 'target="_blank"':''; ?> href="https://bid13.com"><?php _e("storage auction newsletter","bid13-auction-feed");?></a>.
					<?php } ?>
				</div><br />
			<?php } ?>
		</div>
<?php	} 
	return ob_get_clean();
}

/* this is a dirty hack that's only going to work for english and french, other languages have more complex plurals */
function bid13_auction_feed_pluralizer($count){
	if($count == 0 || $count > 1){
		return 's';
	} else {
		return '';
	}
}
function bid13_auction_feed_time_left($timestamp){
	$seconds = $timestamp - time();
	$output = '';
	$weeks = floor($seconds / 604800);
	$seconds %= 604800;
	if($weeks){
		$output = $weeks.' '.__("week","bid13-auction-feed").bid13_auction_feed_pluralizer($weeks);
	}

	$days = floor($seconds / 86400);
	$seconds %= 86400;
	if($days){
		$output .= ($output ? ', ':'').$days.' '.__("day","bid13-auction-feed").bid13_auction_feed_pluralizer($days);
	}
		
	$hours = floor($seconds / 3600);
	$seconds %= 3600;
	if($hours){
		$output .= ($output ? ', ':'').$hours.' '.__("hour","bid13-auction-feed").bid13_auction_feed_pluralizer($hours);
	}
		
	$minutes = floor($seconds / 60);
	$seconds %= 60;
	if($minutes){
		$output .= ($output ? ', ':'').$minutes.' '.__("minute","bid13-auction-feed").bid13_auction_feed_pluralizer($minutes);
	}
	return $output;
}

function bid13_auction_feed_print_auctions($auctions, $facilityID){ ?>
	<?php 
		$sortedView = get_option('wpt_geo_sorted_view');
		$thumbnails = get_option('wpt_use_thumbnails');
		$width = intval(get_option('wpt_video_max_width'));
		if(!$width){ $width = 853; }
		$height = round($width * 9/16); 
		$openLinksInNewTab = (bool)get_option('wpt_open_links_in_new_tab');
	?>
	<?php foreach($auctions as $auction){ ?>
		<?php //echo "<pre>".print_r($auction,1)."</pre>"; ?>
		<?php if($sortedView && $facilityID && $auction->location_id != $facilityID ){ continue; } ?>
		<?php if($hideExpired && !$auction->active) { continue; } ?>
		<div class="bid13-auction">
			<h4 class="bid13-unit-number"><a <?php echo $openLinksInNewTab ? 'target="_blank"':''; ?> href="<?php echo $auction->url; ?>"><?php _e("Unit", "bid13-auction-feed"); ?>&nbsp;<?php echo str_ireplace('Unit ', '', $auction->title); ?></a></h4>
			<?php if(get_option('wpt_use_video') && $auction->video){ ?>
				<div class="videoWrapper">
					<?php
						if(ctype_digit($auction->video_hash)){
							echo '<iframe src="https://player.vimeo.com/video/'.$auction->video_hash.'" width="'.$width.'" height="'.$height.'" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
						} else {
							echo '<iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$auction->video_hash.'?html5=1&rel=0" frameborder="0" allowfullscreen></iframe>';
						}
					?>
					
				</div>
			<?php } else { ?> 
				<a class="image-wrap" <?php echo $openLinksInNewTab ? 'target="_blank"':''; ?> href="<?php echo $auction->url; ?>">
					<?php
						$photo_url = $auction->photos[0]; // default
						$preferred_image = get_option('wpt_preferred_image-'.$auction->unit_id);
						// Generate the requested thumbnail size
						$thumbPrefix = "https://uccdn.bid13.com/thumbnails/".$width."x".$height;
						$photo_url = $thumbPrefix.preg_replace('/https:\/\/uccdn.bid13.com\/original_images/', '', $photo_url);
						if($preferred_image){
							if($preferred_image == 'custom'){
								$alternate_image = get_option('wpt_alternate_image-'.$auction->unit_id);
								$photo_obj = wp_get_attachment_image_src($alternate_image, 'full');
								$photo_url = $photo_obj[0];
							} else {
								$photo_url = $auction->photos[$preferred_image];
								// Generate the requested thumbnail size
								$thumbPrefix = "https://uccdn.bid13.com/thumbnails/".$width."x".$height;
								$photo_url = $thumbPrefix.preg_replace('/https:\/\/uccdn.bid13.com\/original_images/', '', $photo_url);
							}
						}
					?>
					<img class="unit-image" src="<?php echo $photo_url;?>" alt="<?php echo $auction->title; ?>">
				</a>
			<?php } ?>
			<div class="description-wrap">
				<?php if(get_option('wpt_show_unit_descriptions')){ ?>
					<?php
						$unit_description = get_option('wpt_unit_description-'.$auction->unit_id);
						if(!$unit_description){
							$unit_description = str_replace('_', ' ', $auction->description);
						}
					?>
					<?php if(trim($unit_description)){ ?>
						<div class="description"><span class="bid13-label"><?php _e("Unit Contents","bid13-auction-feed");?>:</span> <?php echo $unit_description; ?></div>							
					
					<?php } ?>
				<?php	} ?>
				<?php if(get_option('wpt_show_locations')){ ?>
					<div class="description"><span class="bid13-label"><?php _e("Pickup Location","bid13-auction-feed");?>:</span> <?php echo $auction->location; ?></div>
				<?php	} ?>	
	
				<?php if($auction->active && !$auction->cancelled) { ?>
					<div class="bid13-price"><span class="bid13-label"><?php _e("Current Bid","bid13-auction-feed");?>:</span> <?php echo $auction->price; ?> (<a href="<?php echo $auction->url; ?>" <?php echo $openLinksInNewTab ? 'target="_blank"':''; ?> ><?php _e("place bid","bid13-auction-feed");?></a>)</div>
					<div class="bid13-time"><span class="bid13-label"><?php _e("Time Left","bid13-auction-feed");?>:</span> <?php echo bid13_auction_feed_time_left($auction->expiry); ?></div>
				<?php } else if ($auction->cancelled){ ?>
					<div class="bid13-price"><span class="bid13-label"><?php _e("Auction Cancelled","bid13-auction-feed");?></span></div>
				<?php } else { ?>
					<div class="bid13-price"><span class="bid13-label"><?php _e("Auction Complete, Final Price","bid13-auction-feed");?>:</span> <?php echo $auction->price; ?></div>
				<?php } ?>
			</div>
		</div>
	<?php	}	
}

/**
 *  Register main auction feed shortcode, 
 */
add_shortcode( 'auction_feed', 'bid13_auction_feed_display' );
