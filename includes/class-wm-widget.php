<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WM Widget
 *
 * @class       WM_Widget
 * @version     1.0.0
 * @package     WM/Classes
 * @category    Class
 */
class WM_Widget extends WP_Widget {

	/**
	 * The single instance of the class.
	 *
	 * @var WM_Widget
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Instance.
	 * @return WM_Widget
	 * @throws Exception
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WM_Widget ) ) {
			self::$instance = new WM_Widget;
		}
		return self::$instance;
	}

	/**
	 * WM_Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'wm_widget_google_post_review',
			'WM Google Post Review',
			array( 'description' => __( 'Outputs list of google reviews', 'wm' ) )
		);

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'widget_scripts' ) );
			add_action( 'wp_head', array( $this, 'widget_style' ) );
		}
	}

	/**
	 * Get Name of Class
	 * @return string
	 */
	public function get_name_of_class() {
		return static::class;
	}

	public function widget_scripts() {}

	public function widget_style() {}

	/**
     * Widget outputs
	 * @param array $args
	 * @param array $instance
	 *
	 * @throws Exception
	 */
	function widget( $args, $instance ) {
	    global $post;

		WM()->google()->set_map_api_key( get_option('wm_google_api') );
		$nvr_custom = nvr_get_customdata($post->ID);
		$nvr_cf_sliderType = (isset($nvr_custom["slider_type"][0]))? $nvr_custom["slider_type"][0] : "";
		$posts = get_field('locations');
		$nvr_markerquery = nvr_property_mapquery( 'specific', maybe_unserialize( $nvr_custom['locations'][0] ) );
		$location = nvr_property_latlng( $nvr_markerquery )[0]['address'] . ' ' . nvr_property_latlng( $nvr_markerquery )[0]['title'];
        $reviews_list = WM()->google()->get_list_of_reviews( $location );

        $place_img = $reviews_list['result']['icon'];
        $name = $reviews_list['result']['name'];
        $url_cid = $reviews_list['result']['url'];
        $reviews = $reviews_list['result']['reviews'];
        $rating = $reviews_list['result']['rating'];
        $place_id = $reviews_list['result']['place_id'];
		$photo_reference = $reviews_list['result']['photos'][0]['photo_reference'];

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		ob_start(); ?>
        <?php if ( ! empty( $reviews ) ) : ?>
            <div class="wp-gr wpac">
                <div class="wp-google-list">
                    <div class="wp-google-place">
                        <?php echo WM()->google()->wm_google_place( $rating, $name, $place_img, $url_cid, $reviews, $photo_reference ); ?>
                    </div>
                    <div class="wp-google-content-inner">
                        <?php WM()->google()->wm_google_place_reviews( $reviews, $place_id, '120', '5', '1', '1', '1', '1'); ?>
                    </div>
                </div>
            </div>
            <p><?php
            ?></p>
        <?php else: ?>
        <p><?php _e('No reviews found', 'wm') ?></p>
        <?php endif; ?>
        <?php $html = ob_get_clean();
		echo $html;

		echo $args['after_widget'];
	}

	/**
	 * Widget update
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array|void
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['google_api'] = ( ! empty( $new_instance['google_api'] ) ) ? strip_tags( $new_instance['google_api'] ) : '';
        update_option( 'wm_google_api', $instance['google_api'] );

		return $instance;
	}

	/**
	 * Widget Settings
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$title = $instance['title'] ?? __( 'Reviews', 'wm' );
		$google_api = $instance['google_api'] ?? __( 'Google API', 'wm' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'google_api' ); ?>"><?php _e( 'Google API:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'google_api' ); ?>" name="<?php echo $this->get_field_name( 'google_api' ); ?>" type="text" value="<?php echo esc_attr( $google_api ); ?>">
        </p>
		<?php
	}

}