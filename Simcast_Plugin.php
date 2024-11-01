<?php

include_once('Simcast_LifeCycle.php');

class Simcast_Plugin extends Simcast_LifeCycle {

  /**
   * See: http://plugin.michael-simpson.com/?page_id=31
   * @return array of option meta data.
   */
  public function getOptionMetaData() {
    //  http://plugin.michael-simpson.com/?page_id=31
    return array(
        //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
        'SimpleCastAPI' => array(__('SimpleCast API Key', 'simcast-plugin')),
        'PodcastID' => array(__('Your Podcast ID', 'simcast-plugin')),
        // 'UseV2' => array(__('Use SimpleCast Version 2 API?', 'simcast-plugin'), 'true', 'false'),
        'ShowEmbeds' => array(__('Show embedded player with each episode?', 'simcast-plugin'), 'false', 'true'),
        'UseStyling' => array(__('Use styling?', 'simcast-plugin'), 'true', 'false'),
        'CacheLength' => array(__('How long should the episode list be cached?', 'simcast-plugin'),
                                    'One Week', 'One Day', 'One Month')
    );
  }

  protected function initOptions() {
      $options = $this->getOptionMetaData();
      if (!empty($options)) {
          foreach ($options as $key => $arr) {
              if (is_array($arr) && (count($arr) > 1)) {
                  $this->addOption($key, $arr[1]);
              }
          }
      }
  }

  public function getPluginDisplayName() {
      return 'Simcast';
  }

  protected function getMainPluginFileName() {
      return 'simcast.php';
  }

  /**
   * See: http://plugin.michael-simpson.com/?page_id=101
   * Called by install() to create any database tables if needed.
   * Best Practice:
   * (1) Prefix all table names with $wpdb->prefix
   * (2) make table names lower case only
   * @return void
   */
  protected function installDatabaseTables() {
      //        global $wpdb;
      //        $tableName = $this->prefixTableName('mytable');
      //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
      //            `id` INTEGER NOT NULL");
  }

  /**
   * See: http://plugin.michael-simpson.com/?page_id=101
   * Drop plugin-created tables on uninstall.
   * @return void
   */
  protected function unInstallDatabaseTables() {
      //        global $wpdb;
      //        $tableName = $this->prefixTableName('mytable');
      //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
  }


  /**
   * Perform actions when upgrading from version X to version Y
   * See: http://plugin.michael-simpson.com/?page_id=35
   * @return void
   */
  public function upgrade() {
  }

  public function addActionsAndFilters() {

    // Add options administration page
    // http://plugin.michael-simpson.com/?page_id=47
    add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

    // Example adding a script & style just for the options administration page
    // http://plugin.michael-simpson.com/?page_id=47
    //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
    //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
    //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
    //        }


    // Add Actions & Filters
    // http://plugin.michael-simpson.com/?page_id=37


    // Adding scripts & styles to all pages
    // Examples:
    //        wp_enqueue_script('jquery');
    //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
    //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));


    // Register short codes
    // http://plugin.michael-simpson.com/?page_id=39
    add_shortcode('simcast', array($this, 'doSimcastShortcode'));

    // Register AJAX hooks
    // http://plugin.michael-simpson.com/?page_id=41
    add_action('wp_ajax_ClearTransients', array(&$this, 'ajaxClearTransients'));

  }
  
  public function ajaxClearTransients() {

    // Delete the transient
    delete_transient( 'simplecastdata' );

    header("Content-type: application/json");
 
    echo 'Success!';

    die();
  
  }

  public function doSimcastShortcode($atts) {
      
    extract(shortcode_atts(array(
        'limit'         => '',
        'hide_player'   => '',
        'link_text'     => '',
        'order'         => '', // @TODO: make this work
        'hide_image'    => 'false' // @TODO: make this work
    ), $atts));

    // $simcast_v2         = get_option('Simcast_Plugin_UseV2');
		$simcast_api_key    = get_option('Simcast_Plugin_SimpleCastAPI');
		$simcast_show_id    = get_option('Simcast_Plugin_PodcastID');
    $show_embeds	    = get_option('Simcast_Plugin_ShowEmbeds');
    $use_styling        = get_option('Simcast_Plugin_UseStyling');
    $cache_length       = get_option('Simcast_Plugin_CacheLength');
    switch ($cache_length) {
        case "One Week":
            $simcast_cache = WEEK_IN_SECONDS;
            break;
        case "One Day":
            $simcast_cache = DAY_IN_SECONDS;
            break;
        case "One Month":
            $simcast_cache = MONTH_IN_SECONDS;
            break;
    }
		
		if ($simcast_api_key && $simcast_show_id) {
		
			// If the transient is already saved, let's use that data
			if (get_transient( 'simplecastdata' ) ){
				
				
				$json = get_transient( 'simplecastdata' );
				
			
			// If not, then let's get the fresh data and save it to transients
			} else {
				
        // The SimpleCast V2.0 API URL
        //$url = 'https://api.simplecast.com/podcasts/'.$simcast_show_id.'/episodes?limit=30&status=published&fields=episode_url,title,description,published_at,id,status';
        
        $url = 'https://api.simplecast.com/podcasts/'.$simcast_show_id.'/episodes?limit=30&status=published&fields=episode_url,title,description,published_at,images,id';
    
        // Increase the amount of shows:
        // $url = 'https://api.simplecast.com/podcasts/'.$simcast_show_id.'/episodes?limit=20&offset=20';
 
        // Use the API key to authorize
        $context = stream_context_create(array(
            'http' => array(
                'header'  => "authorization: Bearer ".$simcast_api_key.""
            )
        ));
				
				// Get the feed
				$data = file_get_contents($url, false, $context);
				
				// JSON decode the feed
				$json_data = json_decode($data);
				
// 				echo '<pre>' . json_encode($json_data) . '</pre>';
				
        // Build a custom feed, because we need to follow the "href" to get further details about each show.
        $data_array = [];
        
        foreach ($json_data->collection as $episode){
          
          //= json_encode(array('item' => $post_data));
          
          $object = new stdClass();
          
          // Date formatting
          $date = new DateTime($episode->published_at);
          $new_date_format = $date->format('M d, Y');

          // Get the contents from another Simplecast URl because we need it to get the episode_url
          $episode_url        = $episode->href;
          $episode_data       = file_get_contents($episode_url);
          $episode_json_data  = json_decode($episode_data);

          
          // Add the items to the object
          $object->id = $episode->id;
          $object->episode_url = $episode->episode_url;
          $object->date_published = $new_date_format;
          $object->raw_date = $episode->published_at;
          
          if($episode_json_data->image_url){
            $object->image_url = $episode_json_data->image_url;
          } else {
            $object->image_url = '';
          }
          
          $object->title = $episode->title;
          $object->description = $episode->description;
          
          // Push object to the array
          $data_array[] = $object;
          
        }
        
        // JSON decode the feed
				$json = json_encode($data_array);
        
				// Set the transient with that data
				set_transient('simplecastdata', $json, 1 * $simcast_cache); // @TODO FIXED THIS
				
			}
      
     //echo '<pre>' . $json . '</pre>';
      
      
      $x = 0;
      $feed_data = '';
    
      // Let's loop over that data to produce the list of episodes
      foreach (json_decode($json) as $episode){
        
        $x++;
 
				//echo '<pre>' . json_encode($episode) . '</pre>';
        
        if($use_styling == 'true'){
            $styles = 'margin-bottom: 24px; padding: 15px;';
        } else {
              $styles = '';
        }

        $feed_data .= '<div class="simcast_episode" style="'.$styles.'">';
        
        $feed_data .= '<p class="sm_date">'.$episode->date_published.'</p>';
        
        $feed_data .= '<div class="title-header">';
          
            if($episode->image_url && $hide_image == 'false'){
              $feed_data .= '<img class="alignleft" style="max-width: 200px;" src="'.$episode->image_url.'">';
            }
            
            $feed_data .= '<div>';
            
            	$feed_data .= '<h2>'.$episode->title.'</h2>';
                
            $feed_data .= '</div>';
        
        $feed_data .= '</div>';
        
        $feed_data .= '<p class="sm_desc">'.$episode->description.'</p>';
            

        $feed_data .= '<p class="sm_cta"><a href="'.$episode->episode_url.'" target="_blank">';
            
          if($link_text) {
              $feed_data .= $link_text;
          } else {
              $feed_data .= 'Read Full Show Notes &rarr;';
          }

        $feed_data .= '</a></p>';


        if($show_embeds == 'true' && $hide_player !== 'true'){
          
          $feed_data .= '<iframe height="200px" width="100%" frameborder="no" scrolling="no" seamless src="https://player.simplecast.com/'.$episode->id.'?dark=false"></iframe>';
                  
        }

              

        $feed_data .= '</div>';
              
        if ($x == $limit) {
            break;
        }
  
      } // END foreach
                          
			return $feed_data;
		
		} else {
			
			return 'Your API key and show ID must be saved in order to display your podcast feed.';
			
		}

  }


}
