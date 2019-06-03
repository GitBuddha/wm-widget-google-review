<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WM_Google_Helper {

	/**
	 * The Google Maps API key holder
	 * @var string
	 */
	private $mapApiKey;

	/**
	 * @return string
	 */
	public function get_map_api_key() {
		return $this->mapApiKey;
	}

	/**
	 * @param string $mapApiKey
	 */
	public function set_map_api_key( $mapApiKey ) {
		$this->mapApiKey = $mapApiKey;
	}

	/**
	 * The single instance of the class.
	 *
	 * @var WM_Google_Helper
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Instance.
	 * @return WM_Google_Helper
	 * @throws Exception
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WM_Google_Helper ) ) {
			self::$instance = new WM_Google_Helper;
		}
		return self::$instance;
	}

	public function __construct() {
	}

	/**
	 * Get place ID
	 * @param $location
	 *
	 * @return string
	 */
	function get_place_id( $location ) {
		$locationclean = str_replace (" ", "+", $location);
		$details_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . $locationclean . "&key=" . $this->mapApiKey . '&language=en';
		$du = file_get_contents( $details_url );
		$get_details = json_decode( utf8_encode( $du ), true );
		return ( $get_details['status'] === 'OK' ) ? $get_details['results'][0]['place_id'] : __('Place ID not found', 'wm');
	}

	/**
	 * Get list of reviews
	 * @param $location
	 *
	 * @return array|mixed|object
	 */
	public function get_list_of_reviews( $location ) {
		$details_url = 'https://maps.googleapis.com/maps/api/place/details/json?reference='.$this->get_place_id( $location ).'&key='.$this->mapApiKey . '&language=en';
		$du = file_get_contents( $details_url );
		return $get_details = json_decode( utf8_encode( $du ), true );
	}

	public function wm_google_place( $rating, $name, $place_img, $url_cid, $reviews, $photo_reference ) {
	    $business_image = $this->wm_get_business_photo( $photo_reference ) ? $this->wm_get_business_photo( $photo_reference ) : $place_img;
		?>
		<div class="wp-google-left">
			<img src="<?php echo $business_image; ?>" alt="<?php echo $name; ?>">
		</div>
		<div class="wp-google-right">
			<div class="wp-google-name">
				<?php $place_name_content = '<span>' . $name . '</span>';
				echo $this->wm_google_anchor( $url_cid, '', $place_name_content, true, true); ?>
			</div>
			<div>
				<span class="wp-google-rating"><?php echo $rating; ?></span>
				<span class="wp-google-stars"><?php $this->wm_google_stars($rating); ?></span>
			</div>
            <div class="wp-google-powered">
                <img src="<?php echo WM_ASSETS; ?>/img/powered_by_google.png" alt="powered by Google">
            </div>
		</div>
		<?php
	}

	public function wm_google_anchor( $url, $class, $text, $open_link, $nofollow_link ) {
		?><a href="<?php echo $url; ?>" class="<?php echo $class; ?>" <?php if ($open_link) { ?>target="_blank"<?php } ?> <?php if ($nofollow_link) { ?>rel="nofollow"<?php } ?>><?php echo $text; ?></a><?php
	}

	public function wm_google_stars( $rating ) {
		?><span class="wp-stars"><?php
		foreach (array(1,2,3,4,5) as $val) {
			$score = $rating - $val;
			if ($score >= 0) {
				?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#e7711b"></path></svg></span><?php
			} else if ($score > -1 && $score < 0) {
				?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="#e7711b"></path></svg></span><?php
			} else {
				?><span class="wp-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="17" height="17" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#ccc"></path></svg></span><?php
			}
		}
		?></span><?php
	}

	public function wm_google_place_reviews( $reviews, $place_id, $text_size, $pagination, $reduce_avatars_size, $open_link, $nofollow_link, $lazy_load_img ) {
		?>
        <div class="wp-google-reviews">
			<?php
			$hr = false;
			if ( count( $reviews ) > 0 ) {
				$i = 0;
				foreach ($reviews as $review) {
					if ( $pagination > 0 && $pagination <= $i++ ) {
						$hr = true;
					}
					?>
                    <div class="wp-google-review<?php if ( $hr ) { ?> wp-google-hide<?php } ?>">
                        <div class="wp-google-left">
							<?php
							if ( strlen( $review['profile_photo_url'] ) > 0) {
								$profile_photo_url = $review['profile_photo_url'];
							} else {
								$profile_photo_url = WM_GOOGLE_AVATAR;
							}
							if ( $reduce_avatars_size ) {
								$profile_photo_url = str_replace('s128', 's50', $profile_photo_url );
							}
							$this->wm_google_image( $profile_photo_url, $review['author_name'], false, WM_GOOGLE_AVATAR );
							?>
                        </div>
                        <div class="wp-google-right">
							<?php
							if ( strlen( $review['author_url'] ) > 0 ) {
								$this->wm_google_anchor( $review['author_url'], 'wp-google-name', $review['author_name'], $open_link, $nofollow_link );
							} else {
								if ( strlen( $review['author_name'] ) > 0 ) {
									$author_name = $review['author_name'];
								} else {
									$author_name = $this->wm_google_i('Google User');
								}
								?><div class="wp-google-name"><?php echo $author_name; ?></div><?php
							}
							?>
                            <div class="wp-google-time" data-time="<?php echo $review['time']; ?>"><?php echo gmdate('H:i d M y', $review['time']); ?></div>
                            <div class="wp-google-feedback">
                                <span class="wp-google-stars"><?php echo $this->wm_google_stars( $review['rating'] ); ?></span>
                                <span class="wp-google-text"><?php echo $this->wm_google_trim_text( $review['text'], $text_size ); ?></span>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			?>
        </div>
		<?php if ( $pagination > 0 && $hr ) { ?>
            <a class="wp-google-url" href="#" onclick="return rplg_next_reviews.call( this, 'google', <?php echo $pagination; ?> );">
				<?php echo $this->wm_google_i('Next Reviews'); ?>
            </a>
		<?php } else {
			$this->wm_google_anchor('https://search.google.com/local/reviews?placeid=' . $place_id, 'wp-google-url', $this->wm_google_i('See All Reviews'), true, true);
		}
	}

	public function wm_google_trim_text( $text, $size ) {
		if ( $size > 0 && strlen($text) > $size ) {
			$visible_text = $text;
			$invisible_text = '';
			$idx = $this->wm_rstrpos( $text, ' ', $size );
			if ( $idx < 1 ) {
				$idx = $size;
			}
			if ( $idx > 0 ) {
				$visible_text = substr( $text, 0, $idx );
				$invisible_text = substr( $text, $idx, strlen( $text ) );
			}
			echo $visible_text;
			if ( strlen( $invisible_text ) > 0 ) {
				?><span class="wp-more"><?php echo $invisible_text; ?></span><span class="wp-more-toggle" onclick="this.previousSibling.className='';this.textContent='';"><?php echo $this->wm_google_i('read more'); ?></span><?php
			}
		} else {
			echo $text;
		}
	}

	public function wm_google_image( $src, $alt, $lazy, $def_ava = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', $atts = '' ) {
		?><img <?php if ($lazy) { ?>src="<?php echo $def_ava; ?>" data-<?php } ?>src="<?php echo $src; ?>" class="rplg-review-avatar<?php if ( $lazy ) { ?> rplg-blazy<?php } ?>" alt="<?php echo $alt; ?>" onerror="if(this.src!='<?php echo $def_ava; ?>')this.src='<?php echo $def_ava; ?>';" <?php echo $atts; ?>><?php
	}

	public function wm_google_i( $text, $params = null ) {
		if ( ! is_array( $params ) ) {
			$params = func_get_args();
			$params = array_slice( $params, 1 );
		}
		return vsprintf( __( $text, 'wm' ), $params );
	}

	public function wm_rstrpos( $haystack, $needle, $offset ) {
		$size = strlen( $haystack );
		$pos = strpos( strrev( $haystack ), $needle, $size - $offset );

		if ( $pos === false )
			return false;

		return $size - $pos;
	}

	public function wm_get_business_photo( $photo_reference ) {
	    return 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=300&photoreference='.$photo_reference.'&key=' . $this->get_map_api_key();
    }

} //end class