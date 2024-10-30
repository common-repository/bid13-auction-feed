<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Bid13_Auction_Feed_Settings {

	/**
	 * The single instance of Bid13_Auction_Feed_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpt_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( __( 'Plugin Settings', 'bid13-auction-feed' ) , __( 'Bid13 Settings', 'bid13-auction-feed' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'bid13-auction-feed' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {
		if(get_option('wpt_api_key')){
			if(is_admin()){
				$results = bid13_auction_feed_get_facilities();
				update_option("bid13_auctions_cached_facilities", $results);				
			} else {
				$results = get_option("bid13_auctions_cached_facilities");
			}
			$facility_checkboxes = array();
			$default_facility = array();
			foreach($results as $result){
				$facility_checkboxes[$result->nid] = $result->street;
				$default_facility[] = $result->nid;
			}
		}
		


		$settings['standard'] = array(
			'title'					=> __( 'API Settings', 'bid13-auction-feed' ),
			'description'			=> __( 'Please enter your API key here.  If you do not have an API key and wish to place a request for one, please email <a href="mailto:auctions@bid13.com">auctions@bid13.com</a>.', 'bid13-auction-feed' ),
			'fields'				=> array(
				array(
					'id' 			=> 'API_Key',
					'label'			=> __( 'API Key' , 'bid13-auction-feed' ),
					'description'	=> __( 'Please enter your api key.', 'bid13-auction-feed' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'API key', 'bid13-auction-feed' )
				)
			)
		);
		if(get_option('wpt_api_key')){
			$settings['standard']['fields'][] = array(
					'id' 			=> 'facilityNIDs',
					'label'			=> __( 'Facilities To Display', 'bid13-auction-feed' ),
					'description'	=> __( 'Please check all facilities you wish to display auctions for.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox_multi',
					'options'		=> $facility_checkboxes,
					'default'		=> $default_facility
				);
		}
			
		$settings['extra'] = array(
			'title'					=> __( 'Customization', 'bid13-auction-feed' ),
			'description'			=> __( 'Here you can modify the look and feel of how the auction feed itegrates into your website.  <br/>
			If you find that there is some settings missing here, or a feature you would like, please email  <a href="mailto:auctions@bid13.com">auctions@bid13.com</a>.', 'bid13-auction-feed' ),
			'fields'				=> array(
				array(
					'id' 			=> 'link_color',
					'label'			=> __( 'Link Color', 'bid13-auction-feed' ),
					'description'	=> __( 'If you would like the links to appear as a different color, select it here.', 'bid13-auction-feed' ),
					'type'			=> 'color',
					'default'		=> '#3bb44b'
				),
				array(
					'id' 			=> 'text_color',
					'label'			=> __( 'Text Color', 'bid13-auction-feed' ),
					'description'	=> __( 'If you would like the text to appear as a different color, select it here.' ),
					'type'			=> 'color',
					'default'		=> '#000000'
				),
				array(
					'id' 			=> 'headline_color',
					'label'			=> __( 'Headline Color', 'bid13-auction-feed' ),
					'description'	=> __( 'If you would like the headlines to appear as a different color, select it here.' ),
					'type'			=> 'color',
					'default'		=> '#000000'
				),
				array(
					'id' 			=> 'geo_sorted_view',
					'label'			=> __( 'Organize Output by Geolocation?', 'bid13-auction-feed' ),
					'description'	=> __( 'Check this box if you would like to print the auctions organized by State and City.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'hide_expired',
					'label'			=> __( 'Hide past auctions?', 'bid13-auction-feed' ),
					'description'	=> __( 'Check this if you would like to hide past auctions, so that they do not appear in the feed.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'open_links_in_new_tab',
					'label'			=> __( 'Open links in new tab?', 'bid13-auction-feed' ),
					'description'	=> __( 'When checked, all links will open in a new tab (target=_blank).', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'show_unit_descriptions',
					'label'			=> __( 'Display the unit description?', 'bid13-auction-feed' ),
					'description'	=> __( 'When checked, the unit contents description will next to each auction.  This description is controllable via your bid13 account.  In the future this will be replaced by tags.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'show_locations',
					'label'			=> __( 'Display the unit location?', 'bid13-auction-feed' ),
					'description'	=> __( 'When checked, the facility address is printed below each auction.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> true
				),
				array(
					'id' 			=> 'use_video',
					'label'			=> __( 'Display video instead of photos?', 'bid13-auction-feed' ),
					'description'	=> __( 'If checked the photo container will be replaced with the unit video.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
				),
				array(
					'id' 			=> 'sort_descending',
					'label'			=> __( 'Most Recent Auctions First?', 'bid13-auction-feed' ),
					'description'	=> __( 'If checked the most recent auctions will display at the top of the page, and the older auctions below.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> true
				),
				array(
					'id' 			=> 'video_max_width',
					'label'			=> __( 'Media Max Width' , 'bid13-auction-feed' ),
					'description'	=> __( 'The image/video container is responsive, but you may want to limit how big it gets. Numbers only please (pixels)', 'bid13-auction-feed' ),
					'type'			=> 'text',
					'default'		=> '853',
				),
				array(
					'id' 			=> 'newsletter_link',
					'label'			=> __( 'Hide newsletter link?', 'bid13-auction-feed' ),
					'description'	=> __( 'If checked the the plugin will not print a link to the bid13 newsletter when there are no auctions.', 'bid13-auction-feed' ),
					'type'			=> 'checkbox',
					'default'		=> true
				)			
			)
		);

		if(is_admin() && $_GET['page'] == 'bid13_auction_feed_settings'){
			$results = bid13_auction_feed_get_auctions();
			if($results && !(is_object($results) && property_exists($results, 'error')) && !(is_array($results) && array_key_exists('error', $results))){
				$links = array();
				foreach($results as $key => $result){
					$auction_settings_fields = array();
	
					$auction_settings_fields[] = array(
						'id' 			=> 'unit_description-'.$result->unit_id,
						'label'			=> __( 'Unit Description' , 'bid13-auction-feed' ),
						'description'	=> __( 'Please enter a description of the unit contents.', 'bid13-auction-feed' ),
						'type'			=> 'textarea',
						'default'		=> $result->description,
						'placeholder'	=> __( 'Please enter a description of the unit contents', 'bid13-auction-feed' )
					);
					array_walk($result->thumbnails, 'bid13_auction_feed_url_to_img');
					$auction_settings_images = array_merge($result->thumbnails, array('custom' => 'Upload a different image'));
					$auction_settings_fields[] = 	array(
						'id' 			=> 'preferred_image-'.$result->unit_id,
						'label'			=> __( 'Preferred Image', 'bid13-auction-feed' ),
						'description'	=> __( 'Please select which image you would like to appear in the feed.  NOTE: This has no impact if you have selected display videos instead of images in the general settings section.', 'bid13-auction-feed' ),
						'type'			=> 'radio',
						'options'		=> $auction_settings_images,
						'default'		=> 0
					);
					$auction_settings_fields[] = array(
						'id' 			=> 'alternate_image-'.$result->unit_id,
						'label'			=> __( ' Alternate Image' , 'bid13-auction-feed' ),
						'description'	=> __(  "If you selected 'Upload a different image' above please do so here.  We recomend discussing the use of this feature with your web development team, depending on how they implement the feed on your website, they might give you a minimum image size suggestion.", 'bid13-auction-feed' ),
						'type'			=> 'image',
						'default'		=> '',
						'placeholder'	=> ''
					);			
					$settings['auction_'.$result->unit_id] = array(
						'title'					=> __( $result->title.' Settings', 'bid13-auction-feed' ),
						'description'			=> __( 'You can use this screen to customize how '.$result->title.' will display on your website.', 'bid13-auction-feed' ),
						'fields'				=> $auction_settings_fields,
					);
	
					$links[] = '<a href="'.add_query_arg( array( 'tab' => 'auction_'.$result->unit_id )).'">'.$result->title.'</a>';
				}
			}			
		}


		$html = 'Below is a list of all your auctions, sorted by date posted, click on any of the links if you wish to customize the display settings for that particular auction.<br/>';
		if($links){
			foreach($links as $link){
				$html .= $link . '<br />';
			}			
		}
		
		$settings['auction_settings'] = array(
			'title'					=> __( 'Auction Settings', 'bid13-auction-feed' ),
			'description'		=> __( $html, 'bid13-auction-feed' ),
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && sanitize_key($_POST['tab']) ) {
				$current_section = sanitize_key($_POST['tab']);
			} else {
				if ( isset( $_GET['tab'] ) && sanitize_key($_GET['tab']) ) {
					$current_section = sanitize_key($_GET['tab']);
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->_token . '_settings' );

				if($data['fields'] && is_array($data['fields'])){
					foreach ( $data['fields'] as $field ) {
	
						// Validation callback for field
						$validation = '';
						if ( isset( $field['callback'] ) ) {
							$validation = $field['callback'];
						}
	
						// Register field
						$option_name = $this->base . $field['id'];
						register_setting( $this->parent->_token . '_settings', $option_name, $validation );
	
						// Add field to page
						add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
					}					
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Bid13 Settings' , 'bid13-auction-feed' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && sanitize_key($_GET['tab']) ) {
				$tab .= sanitize_key($_GET['tab']);
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == sanitize_key($_GET['tab']) ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab	
					if(!preg_match("(auction_[0-9]+)", $section)){
						$html .= '<a href="' . $tab_link . '" class="' . sanitize_text_field( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";
					}

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data" class="settings-tab-'.$tab.'">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . sanitize_text_field( $tab ) . '" />' . "\n";
					if($tab != 'auction_settings'){
						$html .= '<input name="Submit" type="submit" class="button-primary" value="' . sanitize_text_field( __( 'Save Settings' , 'bid13-auction-feed' ) ) . '" />' . "\n";						
					}
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Bid13_Auction_Feed_Settings Instance
	 *
	 * Ensures only one instance of Bid13_Auction_Feed_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Bid13_Auction_Feed()
	 * @return Main Bid13_Auction_Feed_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}