<?php
/**
 * Child theme functions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: oceanwp
 * @link http://codex.wordpress.org/Plugin_API
 *
 */

/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */

defined( 'ABSPATH' ) || exit;

if(!session_id()) { session_start(); }

require_once get_stylesheet_directory().'/inc/init.php';

function oceanwp_child_enqueue_parent_style() {
	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
	$theme   = wp_get_theme( 'OceanWP' );
	$version = $theme->get( 'Version' );
	// Load the stylesheet
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css?'.time(), array( 'oceanwp-style' ), $version );
	wp_enqueue_script( 'main-js', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'));


	// Load signature.js on specific pages only
	if (is_page( 'dashboard' ) || is_checkout()) {
		wp_enqueue_script( 'signature-pad', get_stylesheet_directory_uri() . '/js/signature_pad.min.js', array('jquery'), NULL, true );
		wp_enqueue_script( 'signature', get_stylesheet_directory_uri() . '/js/signature.js', NULL, NULL, true);
	}

}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );


add_action('init', 'CalibSaveFormData');
function CalibSaveFormData(){
	if(isset($_POST['plantype']) && isset($_POST['zipcode'])){
		$_REQUEST['sex'] = 'Male';
		$_REQUEST['spousesex'] = 'Male';
		$_REQUEST['tobaccouser'] = 'yes';
		$_REQUEST['spousetobacco'] = 'yes';
		$_REQUEST['payment'] = 'Monthly';
		if($_REQUEST['sex'] == 'on'){
			$_REQUEST['sex'] = 'female';
		}
		if($_REQUEST['spousesex'] == 'on'){
			$_REQUEST['spousesex'] = 'female';
		}
		if($_REQUEST['tobaccouser'] == 'on'){
			$_REQUEST['tobaccouser'] = 'no';
		}
		if($_REQUEST['spousetobacco'] == 'on'){
			$_REQUEST['spousetobacco'] = 'no';
		}
		if($_REQUEST['payment'] == 'on'){
			$_REQUEST['payment'] = 'Up Front';
		}

		$_SESSION["planForm"] = $_REQUEST;

		$raq_content  = YITH_Request_Quote()->get_raq_return();
        if (!empty($raq_content)) {
           foreach ( $raq_content as $key => $raq ){
                //YITH_Request_Quote()->remove_item( sanitize_key( wp_unslash($key) ),$raq_content );
            }
        }
        do_action( 'woocommerce_set_cart_cookies', true );

		YITH_Request_Quote()->session_class = new YITH_YWRAQ_Session();
		YITH_Request_Quote()->set_session();
        //$defaultProducts = array(1015,895,873);
        $raq_data = array(
            'product_id'   => absint(1015),
            'quantity'     => 1,
        );
    	YITH_Request_Quote()->add_item( $raq_data );

    	$raq_data = array(
            'product_id'   => absint(895),
            'quantity'     => 1,
        );
    	YITH_Request_Quote()->add_item( $raq_data );

    	$raq_data = array(
            'product_id'   => absint(873),
            'quantity'     => 1,
        );
    	YITH_Request_Quote()->add_item( $raq_data );

		wp_redirect( site_url().'/request-quote' );
		exit;
	}
}

add_shortcode( 'planform', 'planform_handler' );
function planform_handler( $atts ) {

	$today = date('Y-m-d');
	$spousedate = date('Y-m-d', strtotime('-18 years'));
    return '<div class="plan_formbackground">
	<form method="post">
		<div class="mainArea">
			<div class="planpadding">
				<h1>Let&#39;s get started! </h1>
				<div style="float:right"><a id="showhidespouse">+ Spouse</a> <a id="showhidechild">+ Child</a></div>
				<div class="plantype">
					<label>Plan type</label>
					<select name="plantype">
						<option>All Plans</option>
						<option>Basic Plans</option>
						<option>Intermediate Plans</option>
						<option>Comprehensive Plans</option>
						<option>Catastrophic Plans</option>
						<option>Supplemental Plans</option>
					</select>
				</div>
				<div class="zipcode">
					<label>Zip Code</label>
					<input type="text" name="zipcode" class="zipcode" placeholder="Zip Code">
				</div>
				<div class="appendchildFields"></div>
				<div class="applicantArea">
					<h5>Applicant</h5>
					<div class="applicant">
						<div class="width180">
							<label>First Name</label>
							<input type="text" name="fname" id="datepicker-input" placeholder="First Name">
						</div>
						<div class="width180">
							<label>Last Name</label>
							<input type="text" name="lname" id="datepicker-input" placeholder="Last Name">
						</div>
						<div class="width200">
							<label>Email</label>
							<input type="text" name="email" id="datepicker-input" placeholder="Email">
						</div>
					</div>
					<div class="applicant" style="margin-top:10px;">
						<div class="width200" style="margin-right:15px;">
							<label>Date of birth</label>
							<input type="date" name="birth" id="datepicker-input" placeholder="Date of birth" max="'.$spousedate.'">
						</div>
						<div class="width180">
							<label>Sex</label>
							<div class="can-toggle demo-rebrand-1">
							  <input id="d" type="checkbox" name="sex">
							  <label for="d">
								<div class="can-toggle__switch" data-checked="F" data-unchecked="M"></div>
							  </label>
							</div>
						</div>
						<div class="width180">
							<label>Tobacco user</label>
							<div class="can-toggle demo-rebrand-1">
							  <input id="e" type="checkbox" checked name="tobaccouser">
							  <label for="e">
								<div class="can-toggle__switch can-toggle__switch2" data-checked="No" data-unchecked="Yes"></div>
							  </label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="planpadding2">
				<div class="planArea">
					<div class="applicant">
						<div class="width50per">
							<label>Payment</label>
							<div class="can-toggle demo-rebrand-4">
							  <input id="f" type="checkbox" name="payment">
							  <label for="f">
								<div class="can-toggle__switch can-toggle__switch3" data-checked="Up Front" data-unchecked="Monthly"></div>
							  </label>
							</div>
						</div>
						<div class="width50per">
							<label>Start date</label>
							<input type="date" name="startdate" id="dp1" placeholder="Start date" >
						</div>
					</div>
				</div>
				<div style="clear:both;">
					<a href="'.get_permalink( woocommerce_get_page_id( 'shop' ) ).'" class="compareplans"> View plan </a>
					<input type="submit" class="getquoto" value="Get a Quote">
				</div>
			</div>
		</div>

		<div class="childspouseArea">
			<div class="childspousediv">
				<div class="childArea">
					<h5>Add Child <span class="addchild">Add More</span></h5>
					<div class="dateofbirth">
						<label>Date of birth</label>
						<input type="date" class="childbirth" id="datepicker-input" placeholder="Date of birth" max="'.$today.'">
					</div>
					<div class="width118">
						<label>Sex</label>
						<div class="can-toggle demo-rebrand-1">
						  <input id="childd" type="checkbox" class="childsex">
						  <label for="childd">
							<div class="can-toggle__switch" data-checked="F" data-unchecked="M"></div>
						  </label>
						</div>
					</div>
					<div class="width118">
						<label>Tobacco user</label>
						<div class="can-toggle demo-rebrand-1">
						  <input id="childe" class="childtobacco" type="checkbox" checked>
						  <label for="childe">
							<div class="can-toggle__switch can-toggle__switch2" data-checked="No" data-unchecked="Yes"></div>
						  </label>
						</div>
					</div>
					<div class="childdata"></div>
				</div>
				<div class="spouseArea">
					<h5>Add Spouse <span class="addSpouse">Save</span></h5>
					<div class="dateofbirth">
						<label>Date of birth</label>
						<input type="date" name="spousebirth" class="spousebirth" placeholder="Date of birth" max="'.$spousedate.'">
					</div>
					<div class="width118">
						<label>Sex</label>
						<div class="can-toggle demo-rebrand-1">
						  <input id="spoused" type="checkbox" name="spousesex" class="spousesex">
						  <label for="spoused">
							<div class="can-toggle__switch" data-checked="F" data-unchecked="M"></div>
						  </label>
						</div>
					</div>
					<div class="width118">
						<label>Tobacco user</label>
						<div class="can-toggle demo-rebrand-1">
						  <input id="spousee" type="checkbox" checked name="spousetobacco" class="spousetobacco">
						  <label for="spousee">
							<div class="can-toggle__switch can-toggle__switch2" data-checked="No" data-unchecked="Yes"></div>
						  </label>
						</div>
					</div>
					<div class="spousedata"></div>
				</div>
			</div>
		</div>
	</form>
</div>';
}

//ADD CHATE MENU TO MAIN MENU
if ( ! function_exists( 'oceanwp_add_expert_chat_to_menu' ) ) {
	function oceanwp_add_expert_chat_to_menu( $items, $args ) {

		if ( $args->menu->slug === 'main' ) :
			// Add search item to menu
			$items .= '<li class="sharingexpert-li"><a href="#" class="menu-link sharingexpert"><span> &nbsp;Sharing Expert<br><span>1(800) 323-3384</span></span></a></li>';
			$items .= '<li class="chatnow-li"><a href="#" class="menu-link chatnow"><span>Chat now!</span></a></li>';
			// Return nav $items
		endif;
		return $items;
	}
	add_filter( 'wp_nav_menu_items', 'oceanwp_add_expert_chat_to_menu', 11, 3 );
}

//ADD SEARCH TO MAIN MENU
if ( ! function_exists( 'oceanwp_add_search_to_menu' ) ) {

	function oceanwp_add_search_to_menu( $items, $args ) {

		// Only used on main menu
		if ( 'main_menu' != $args->theme_location ) {
			return $items;
		}

		// Get search style
		$search_style = oceanwp_menu_search_style();
		$header_style = oceanwp_header_style();

		// Return if disabled
		if ( ! $search_style
			|| 'disabled' == $search_style
			|| 'top' == $header_style
			|| 'vertical' == $header_style ) {
			return $items;
		}

		// Get correct search icon class
		if ( 'drop_down' == $search_style ) {
			$class = ' search-dropdown-toggle';
		} elseif ( 'header_replace' == $search_style ) {
			$class = ' search-header-replace-toggle';
		} elseif ( 'overlay' == $search_style ) {
			$class = ' search-overlay-toggle';
		} else {
			$class = '';
		}

		// Add search item to menu
		$items .= '<li class="search-toggle-li">';
			if ( 'full_screen' == $header_style ) {
				$items .= '<form method="get" action="'. esc_url( home_url( '/' ) ) .'" class="header-searchform">';
					$items .= '<input type="search" name="s" value="" autocomplete="off" />';
					// If the headerSearchForm script is not disable
					if ( OCEAN_EXTRA_ACTIVE
						&& class_exists( 'Ocean_Extra_Scripts_Panel' )
						&& Ocean_Extra_Scripts_Panel::get_setting( 'oe_headerSearchForm_script' ) ) {
						$items .= '<label>'. esc_html__( 'Type your search', 'oceanwp' ) .'<span><i></i><i></i><i></i></span></label>';
					}
					if( !function_exists('is_plugin_active') ) {
						include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					}
					if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ){
						$my_current_lang = apply_filters( 'wpml_current_language', NULL );
						if( ! empty($my_current_lang) ){
							$items .= '<input type="hidden" name="lang" value="'. $my_current_lang .'"/>';
						}
					}
				$items .= '</form>';
			} else {
				$items .= '<a href="#" class="site-search-toggle'. $class .'" aria-label="'. esc_attr( 'Search website', 'oceanwp' ) .'">';
					$items .= '<span class="icon-magnifier" aria-hidden="true"></span>';
				$items .= '</a>';
			}
		$items .= '</li>';

		// Return nav $items
		return $items;

	}
	//add_filter( 'wp_nav_menu_items', 'oceanwp_add_search_to_menu', 11, 2 );
}


if ( ! function_exists( 'wc_print_details' )) {
	add_action( 'woocommerce_before_main_content', 'wc_print_details');
	function wc_print_details(){

		$details = $_SESSION["planForm"];
		//print_r($details);
		$date = date('Y-m-d');
		$d1 = new DateTime($date);
		$d2 = new DateTime($details['birth']);
		$diff = $d2->diff($d1);

		$submitchilddata = $details['submitchilddata'];
		$additionalData = '';
		$count = 0;

		if($details['spousebirth']){
			$count++;
		}

		if(isset($submitchilddata)){
			if(count($submitchilddata) > 0){
				$count += count($submitchilddata);
			}
		}

		$additionalData = $count.' dependents';
		$sessionValue = '';
		if($details['fname'] && $details['lname']){
			$sessionValue = $details['fname'].' '.$details['lname'].'  &nbsp;   &nbsp;   &nbsp; Age:'.$diff->y.'<br>Email: '.$details['email'].'<br>Additional Dependents: '.$additionalData;
		}

		?>
		<div id="content-wrap" class="container clr shoppagemenu">
			<div class="menu-left-area">
				<?php echo $sessionValue ?>
			</div>
			<div class="menu-right-area">
				<?php wp_nav_menu(array('theme_location' => 'category_menu','container_class' => 'category-menu')); ?>
			</div>
		</div>
		<?php
	}
}

//add_action( 'woocommerce_before_shop_loop', 'wc_print_featured_products');
function wc_print_featured_products(){
	echo '<div class="featured_products_area">'.do_shortcode('[featured_products per_page="3" orderby="rand" order="rand" columns="3"]').'</div>';
	//echo 'test';
}

/**
 * Override theme default specification for product # per row
 */
function loop_columns() {
return 4; // 5 products per row
}
add_filter('loop_shop_columns', 'loop_columns', 999);


function getCategories(){
	$args = array(
	  'taxonomy' => 'product_cat',
	  'hide_empty' => false,
	  'parent'   => 0
	);
	$product_cat = get_terms( $args );
	$cat = '<ul>';
	foreach ($product_cat as $parent_product_cat){
		$cat .= '<li><a class="dropbtn" href="'.get_term_link($parent_product_cat->term_id).'">'.$parent_product_cat->name.'</a>';
		$child_args = array(
		  'taxonomy' => 'product_cat',
		  'hide_empty' => false,
		  'parent'   => $parent_product_cat->term_id
		);
		$child_product_cats = get_terms( $child_args );
		$count = 0;
		$cat .= '<ul class="dropdown-content">';
		foreach ($child_product_cats as $child_product_cat)
		{
			$count++;

			$cat .= '<li><a href="'.get_term_link($child_product_cat->term_id).'">'.$child_product_cat->name.'</a></li>';

		}
		$cat .= '</ul>';
		$cat .= '</li>';
	}
	$cat .= '</ul>';
	return $cat;
	/*$orderby = 'name';
	$order = 'asc';
	$hide_empty = false ;
	$cat_args = array(
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => $hide_empty,
	);

	$product_categories = get_terms( 'product_cat', $cat_args );
	if( !empty($product_categories) ){
		$cat = '<ul class="shop_categories">';
		foreach ($product_categories as $key => $category) {
			$cat .= '<li style="float:left;">';
			$cat .= '<a href="'.get_term_link($category).'" >';
			$cat .= $category->name;
			$cat .= '</a>';
			$cat .= '</li>';
		}
		$cat .= '</ul>';
		return $cat;
	}*/
}


function child_theme_register_nav_menu(){
	register_nav_menus( array(
		'category_menu' => __( 'Category Menu' ),
	));
}
add_action( 'after_setup_theme', 'child_theme_register_nav_menu', 0 );

/**
 * Remove Suplemental products from main shop query instance.
 *
 * @param   obj|WP_Query $q Query instance.
 *
 * @return  obj|WP_Query 	Returns modified taxonomy query for query instance.
 */
function remove_suplemental_products_from_main_query( $q ) {
	if ( is_shop() ) :
		$q->set( 'tax_query', array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'supplemental-plans' ),
				'operator' => 'NOT IN'
			)
		) );
	endif;
}
add_action( 'woocommerce_product_query', 'remove_suplemental_products_from_main_query' );

//ADD FEATURED PRODUCTS ON SHOP PAGE
function add_opening_divs_before_shop_loop() { ?>
	<div class="main-shop-products">
		<div class="insert-product">
			<?php
}
add_action( 'woocommerce_before_shop_loop', 'add_opening_divs_before_shop_loop', 50 );

// add_action('woocommerce_before_shop_loop', 'add_featured_products_before_shop_loop', 50);
function add_featured_products_before_shop_loop(){
	$args = array('post_type' => 'product', 'post_status' => 'publish','posts_per_page' => -1,'tax_query' => array(array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array('featured'), 'operator' => 'IN' )));
	$pro_query = new WP_Query( $args );
	// The Loop
	?>
	<div class="shop-featured-products">
		<h2><?php _e('Featured Products'); ?></h2>
		<?php
		woocommerce_product_loop_start();
		if ( $pro_query->have_posts() ) :
			while ( $pro_query->have_posts() ) : $pro_query->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		endif;
		woocommerce_product_loop_end();
		// Reset Post Data
		wp_reset_postdata();
		?>
		<div class="vce vce-separator-container vce-separator--align-center vce-separator--style-shadow" ><div class="vce-separator vce-separator--color-bfc0c1 vce-separator--width-88 vce-separator--thickness-2" ><div class="vce-separator-shadow vce-separator-shadow-left"></div><div class="vce-separator-shadow vce-separator-shadow-right"></div></div></div>
	</div>
	<div class="main-shop-products">
		<div class="insert-product">
	<?php
}

//ADD SUPPLIMENTS PRODUCTS ON SHOP PAGE
add_action( 'woocommerce_after_shop_loop', 'add_suplemental_products_after_shop_loop', 20 );
function add_suplemental_products_after_shop_loop(){
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'supplemental-plans' ),
				'include_children' => true,
				'operator' => 'IN'
			)
		)
	);

	$pro_query = new WP_Query( $args );
	// The Loop
	?>
	</div></div>
	<div class="shop-supplemental-plans">
	<!-- <div class="vce vce-separator-container vce-separator--align-center vce-separator--style-shadow" ><div class="vce-separator vce-separator--color-bfc0c1 vce-separator--width-88 vce-separator--thickness-2" ><div class="vce-separator-shadow vce-separator-shadow-left"></div><div class="vce-separator-shadow vce-separator-shadow-right"></div></div></div> -->
		<h2><?php esc_html_e('Supplemental Plans'); ?></h2>
		<?php
		woocommerce_product_loop_start();
		if ( $pro_query->have_posts() ) :
			while ( $pro_query->have_posts() ) : $pro_query->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile;
		endif;
		woocommerce_product_loop_end();
		// Reset Post Data
		wp_reset_postdata();
		?>

	</div>
	<?php
}

//CALL PRODUCT/CHECKOUT PAGE SIDEBAR
add_filter('ocean_get_sidebar', 'call_single_product_sidebar',20);
function call_single_product_sidebar($sidebar){
	if (is_singular('product')) {
		$sidebar = 'ocs-product-page-sidebar';
	}
	if (is_page('checkout')) {
		$sidebar = 'ocs-checkout-page-sidebar';
	}
	return $sidebar;
}

/*add_action('woocommerce_after_add_to_cart_button', 'wc_after_add_to_cart_button');
function wc_after_add_to_cart_button(){
	?>
	<a href="#omw-2146" class="button omw-open-modal"><?php _e('Get A Quote'); ?></a>
	<?php
}*/

//REMOVE ZOOM AND HOVER FROM IMAGE ON PRODUCT PAGE
add_action( 'after_setup_theme', 'remove_wc_gallery_lightbox', 100 );
function remove_wc_gallery_lightbox() {
	remove_theme_support( 'wc-product-gallery-lightbox' );
}
function remove_image_zoom_support() {
    remove_theme_support( 'wc-product-gallery-zoom' );
}
add_action( 'wp', 'remove_image_zoom_support', 100 );

//ADD NEW BUTTON ON PRODUCT IMAGE
add_action( 'woocommerce_product_thumbnails', 'wc_add_view_pdf_product_image', 100 );
add_action( 'calib_widget_product_thumbnail', 'wc_add_view_pdf_product_image', 100 );
function wc_add_view_pdf_product_image(){
	global $post;
	$pdf_url = get_post_meta( $post->ID, '_pdf_url', true );
	if (!empty($pdf_url)) {
	?>
		<a href="<?php echo $pdf_url; ?>" target="_blank" class="view-pdf"><?php _e('VIEW PDF'); ?></a>
	<?php
	}
}


/**
 * The Class.
 */
class AddPDFButtonToProduct {

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
        add_action('admin_print_scripts', array( $this, 'media_admin_scripts'));
    }

	function media_admin_scripts() {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
	}

    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array('product');

        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'product-pdf',
                __( 'Add PDF', 'oceanwp'),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'side',
                'low'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['add_pdf_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['add_pdf_nonce'];
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'add_pdf_box' ) ) {
            return $post_id;
        }

        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'product' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        /* OK, it's safe for us to save the data now. */
        if (isset($_POST['pdf_url'])) {
        	// Sanitize the user input.
	        $pdf_url = sanitize_text_field( $_POST['pdf_url'] );
	        // Update the meta field.
	        update_post_meta( $post_id, '_pdf_url', $pdf_url );
	        ///die;
        }

    }


    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'add_pdf_box', 'add_pdf_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $pdf_url = get_post_meta( $post->ID, '_pdf_url', true );

        // Display the form, using the current value.
        ?>
		<p>
            <?php if(!empty($pdf_url)): ?>
                <iframe style="max-width:254px;height:auto;<?php echo empty($pdf_url)?$pdf_url:''; ?>" id="meta-image-preview" src="<?php if ( isset ( $pdf_url ) ){ echo $pdf_url; } ?>"></iframe>
            <?php else: ?>
                <iframe style="max-width:254px;height:auto;<?php echo empty($pdf_url)?$pdf_url:''; ?>display: none;" id="meta-image-preview" src="<?php if ( isset ( $pdf_url ) ){ echo $pdf_url; } ?>"></iframe>
            <?php endif; ?>
            <input type="hidden" name="pdf_url" id="meta-image" class="meta_image" value="<?php if ( isset ( $pdf_url ) ){ echo $pdf_url; } ?>" />
			<input type="button" id="meta-image-button" class="button" value="Choose an PDF" />
		</p>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
			    jQuery('#meta-image-button').click(function() {
				    var send_attachment_bkp = wp.media.editor.send.attachment;
				    wp.media.editor.send.attachment = function(props, attachment) {
				        jQuery('#meta-image').val(attachment.url);
						jQuery('#meta-image-preview').attr('src',attachment.url);
				        jQuery('#meta-image-preview').show();
				        wp.media.editor.send.attachment = send_attachment_bkp;
				    }
				    wp.media.editor.open();
				    return false;
				});
			  });
		</script>
        <?php
    }
}
new AddPDFButtonToProduct();



/**
* Adds a submenu page under a Theme option.
*/
add_action('admin_menu', 'application_register_ref_page');
function application_register_ref_page() {
    add_submenu_page(
        'oceanwp-panel',
        __( 'Manage Application Step','oceanwp'),
        __( 'Manage Application Step','oceanwp'),
        'manage_options',
        'application-step',
        'application_step_page_callback'
    );
}

/**
* Display callback for the submenu page.
*/
function application_step_page_callback() {
	$success = '';
	if (isset($_POST['manage_application_content']) &&  isset( $_POST['appli_nonce_field'] )
    && wp_verify_nonce( $_POST['appli_nonce_field'], 'appli_action' )) {
		update_option('manage_application_content', $_POST['manage_application_content']);
		$success = 'Data saved successfully.';
	}
    ?>
    <div class="wrap">
        <h1><?php _e( 'Manage Application Step'); ?></h1>
        <form action="" method="post">
        	<p style="color: green;"><?php echo $success; ?></p>
        	<p>Add Application step content</p>
	        <?php
			$content   = get_option('manage_application_content');;
			$editor_id = 'manage_application_content';
			$settings  = array( 'media_buttons' => false );
			wp_editor( $content, $editor_id, $settings );
	        ?>
	        <?php wp_nonce_field( 'appli_action', 'appli_nonce_field' ); ?>
	        <p><button type="submit" class="btn button">Save</button></p>
        </form>
    </div>
    <?php
}

if ( ! function_exists( 'wmsc_delete_checkout_step' ) ) {
	function wmsc_delete_checkout_step( $steps ) {
		//print_r($steps);
	    unset( $steps['billing'] );
		unset( $steps['shipping'] );
		unset( $steps['payment'] );
		//print_r($steps);
	    return $steps;
	}
}
add_filter( 'wpmc_modify_steps', 'wmsc_delete_checkout_step' );

/**
 * Add the Delivery Time step
 */
if ( ! function_exists( 'wpmc_add_application_step' ) ) {
	function wpmc_add_application_step( $steps ) {
	    $steps['application'] = array(
		'title'     => __( 'Application','oceanwp' ),
		'position'  => 1,
		'class'     => 'wpmc-step-application',
		'sections'  => array( 'application' ),
	    );
		$steps['eligibilty'] = array(
		'title'     => __( 'Eligibilty','oceanwp' ),
		'position'  => 10,
		'class'     => 'wpmc-step-eligibilty',
		'sections'  => array( 'eligibilty' ),
		);
		$steps['eligibility_acknowledgement'] = array(
		'title'     => __( 'Eligibilty Acknowledgement','oceanwp' ),
		'position'  => 15,
		'class'     => 'wpmc-step-eligibilty-acknowledgement',
		'sections'  => array( 'eligibility_acknowledgement' ),
		'hide_tab'	=> 1,
		);
	    $steps['payment2'] = array(
		'title'     => __( 'Payment','oceanwp' ),
		'position'  => 20,
		'class'     => 'wpmc-step-payment2',
		'sections'  => array( 'payment2' ),
		);
	    return $steps;
	}
}
add_filter( 'wpmc_modify_steps', 'wpmc_add_application_step' );

/**
 * Add content to the Application step
 */
if ( ! function_exists( 'wmsc_step_content_application' ) ) {
    function wmsc_step_content_application() {

    	$checkout = WC()->checkout();

		$fname = $lname = $email = $sex = $tobaccouser = $birth = $startdate = $payment = $submitchilddata = $spousebirth = $spousesex = $spousetobacco = '';

    	if (isset($_SESSION["planForm"]) && !empty($_SESSION["planForm"])) {
    		$planForm = $_SESSION["planForm"];
    		$fname = $planForm['fname'];
    		$lname = $planForm['lname'];
    		$email = $planForm['email'];
    		$sex = $planForm['sex'];
    		$tobaccouser = $planForm['tobaccouser'];
    		$birth = $planForm['birth'];
    		$startdate = $planForm['startdate'];
    		$payment = $planForm['payment'];
    		$submitchilddata = $planForm['submitchilddata'];
    		$spousebirth = $planForm['spousebirth'];
    		$spousesex = $planForm['spousesex'];
    		$spousetobacco = $planForm['spousetobacco'];
    	}

        ?>
        <div class="statement">
        	<?php
				$content = get_option('manage_application_content');;
				echo wpautop($content);
        	?>
        	<p class="statement-read">
        		<a data-val="yes">YES</a>
        		<a data-val="no">NO</a>
        		<input type="text" style="display: none;" required="required" name="statement_read" value="">
        	</p>
        </div>
        <div class="application-form">
        	<h2>Demographics & Dependents</h2>
        	<hr>
        	<div class="application-step-form">
    			<?php do_action('application_step_content'); ?>
        	</div>
        </div>
        <?php
    }
}
//add_action( 'wmsc_step_content_application', 'wmsc_step_content_application' );

/**
 * Add content to the ELIGIBILTY step
 */
if ( ! function_exists( 'wmsc_step_content_eligibilty' ) ) {
    function wmsc_step_content_eligibilty() {

    	$checkout = WC()->checkout();

		$fname = $lname = $email = $sex = $tobaccouser = $birth = $startdate = $payment = $submitchilddata = $spousebirth = $spousesex = $spousetobacco = '';

    	if (isset($_SESSION["planForm"]) && !empty($_SESSION["planForm"])) {
    		$planForm = $_SESSION["planForm"];
    		$fname = $planForm['fname'];
    		$lname = $planForm['lname'];
    		$email = $planForm['email'];
    		$sex = $planForm['sex'];
    		$tobaccouser = $planForm['tobaccouser'];
    		$birth = $planForm['birth'];
    		$startdate = $planForm['startdate'];
    		$payment = $planForm['payment'];
    		$submitchilddata = $planForm['submitchilddata'];
    		$spousebirth = $planForm['spousebirth'];
    		$spousesex = $planForm['spousesex'];
    		$spousetobacco = $planForm['spousetobacco'];
    	}
        ?>
		<div class="eligibilty-form">
        	<div class="eligibilty-step-form">
    			<?php do_action('eligibility_step_content'); ?>
        	</div>
        </div>
        <?php

        // Add the date field called "Choose Date"

    }
}
add_action( 'wmsc_step_content_eligibilty', 'wmsc_step_content_eligibilty' );




/**
 * Add content to the ELIGIBILTY ACKNOWLEDGEMENT step
 */
if ( ! function_exists( 'wmsc_step_content_eligibility_acknowledgement' ) ) {
    function wmsc_step_content_eligibility_acknowledgement() {
        ?>
		<div class="eligibilty-acknowledgement-form">
        	<div class="eligibilty-acknowledgement-step-form">
    			<?php do_action('eligibility_acknowledgement_step_content'); ?>
        	</div>
        </div>
        <?php

        // Add the date field called "Choose Date"

    }
}
add_action( 'wmsc_step_content_eligibility_acknowledgement', 'wmsc_step_content_eligibility_acknowledgement' );


/**
 * Add content to the PAYMENT step
 */
if ( ! function_exists( 'wmsc_step_content_payment2' ) ) {
    function wmsc_step_content_payment2() {
    	$checkout = WC()->checkout();
        ?>
		<div id="payment" class="woocommerce-checkout-payment">
			<?php if ( WC()->cart->needs_payment() ) : ?>
				<ul class="wc_payment_methods payment_methods methods">
					<?php
					if ( ! empty( $available_gateways ) ) {
						foreach ( $available_gateways as $gateway ) {
							wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
						}
					} else {
						echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
					}
					?>
				</ul>
			<?php endif; ?>
			<div class="form-row place-order">
				<noscript>
					<?php
					/* translators: $1 and $2 opening and closing emphasis tags respectively */
					printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
					?>
					<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
				</noscript>

				<?php wc_get_template( 'checkout/terms.php' ); ?>

				<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

				<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

				<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

				<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
			</div>
		</div>
		<div class="billing-information">
        	<h3><?php _e('Billing information', 'woocommerce'); ?></h3>
        	<?php do_action('payment_step_content'); ?>
        </div>
		<?php
		if ( ! is_ajax() ) {
			do_action( 'woocommerce_review_order_after_payment' );
		}
    }
}
add_action( 'wmsc_step_content_payment2', 'wmsc_step_content_payment2' );


if ( ! function_exists( 'wmsc_shipping_step_last' ) ) {
	function wmsc_shipping_step_last( $steps ) {
		//print_r($steps);
	    $steps['shipping']['title'] = 'Eligibilty';
	    $steps['review']['title'] = 'Summary';
	    return $steps;
	}
}
//add_filter( 'wpmc_modify_steps', 'wmsc_shipping_step_last' );

//ADD NEW POSITION IN THE CHECKOUT FORM
add_filter('thwcfe_custom_section_positions', 'thwcfe_custom_section_positions_handle', 80);
function  thwcfe_custom_section_positions_handle($custom_positions){
	$custom_positions = array('application' => 'Application Step', 'eligibility' => 'Eligibility Step', 'eligibility_acknowledgement' => 'Eligibility Acknowledgement Step' );
	return $custom_positions;
}

add_action('payment_step_content', 'payment_step_content_handle');
function payment_step_content_handle(){
	$cls = new THWCFE_Public_Checkout('woocommerce-checkout-field-editor-pro', THWCFE_VERSION);
	$section = THWCFE_Utils::get_checkout_section('billing');
	//print_r($section);
	$cls->output_custom_section(array('billing'));
}

add_action('application_step_content', 'application_step_content_handle');
function application_step_content_handle(){
	$cls = new THWCFE_Public_Checkout('woocommerce-checkout-field-editor-pro', THWCFE_VERSION);
	$section = $cls->get_custom_sections_by_hook('application');
	//print_r($section);
	$fname = $lname = $email = $sex = $tobaccouser = $birth = $startdate = $payment = $submitchilddata = $spousebirth = $spousesex = $spousetobacco = '';

	if (isset($_SESSION["planForm"]) && !empty($_SESSION["planForm"])) {
		$planForm = $_SESSION["planForm"];
		$fname = $planForm['fname'];
		$lname = $planForm['lname'];
		$email = $planForm['email'];
		$sex = $planForm['sex'];
		$tobaccouser = $planForm['tobaccouser'];
		$birth = $planForm['birth'];
		$startdate = $planForm['startdate'];
		$payment = $planForm['payment'];
		$submitchilddata = $planForm['submitchilddata'];
		$spousebirth = $planForm['spousebirth'];
		$spousesex = $planForm['spousesex'];
		$spousetobacco = $planForm['spousetobacco'];
	}
	$cls->output_custom_section($section, $fname, true);
}

add_action('eligibility_step_content', 'eligibility_step_content_handle');
function eligibility_step_content_handle(){
	$cls = new THWCFE_Public_Checkout('woocommerce-checkout-field-editor-pro', THWCFE_VERSION);
	$section = $cls->get_custom_sections_by_hook('eligibility');
	$cls->output_custom_section($section);
}


add_action('eligibility_acknowledgement_step_content', 'eligibility_acknowledgement_step_content_handle');
function eligibility_acknowledgement_step_content_handle(){
	$cls = new THWCFE_Public_Checkout('woocommerce-checkout-field-editor-pro', THWCFE_VERSION);
	$section = $cls->get_custom_sections_by_hook('eligibility_acknowledgement');
	$cls->output_custom_section($section);
}

/***
 * Compare Button in single page
 */
function tt_init(){
    if(is_plugin_active('yith-woocommerce-compare-premium/init.php')) {
        global $yith_woocompare;
        remove_action('woocommerce_single_product_summary', array($yith_woocompare->obj, 'add_compare_link'), 35);
        //add_action('woocommerce_after_single_variation', array($yith_woocompare->obj, 'add_compare_link'), 20);
        add_action('woocommerce_before_add_to_cart_button', array($yith_woocompare->obj, 'add_compare_link'), 20);

        /**
         * ON loop
         */
        add_action('ocean_before_archive_product_add_to_cart_inner', array($yith_woocompare->obj, 'add_compare_link'), 20);
        //add_action('do_compare_button_link', array($yith_woocompare->obj, 'add_compare_link'), 20);
    }



}
add_action('init','tt_init');


function archive_request_quote_button(){
    global $product;
    if(function_exists('yith_ywraq_render_button')){
        yith_ywraq_render_button($product->ID);

    }
}
add_action('ocean_before_archive_product_add_to_cart_inner','archive_request_quote_button',10);


/***
 * REMOVE ADD TO CART MESSAGE
 */
add_filter( 'wc_add_to_cart_message_html', '__return_null' );

    /**
     * Redirect users after add to cart.
     */
function my_custom_add_to_cart_redirect( $url ) {

    $url = wc_get_checkout_url();
    // $url = wc_get_checkout_url(); // since WC 2.5.0

    return $url;

}
add_filter( 'woocommerce_add_to_cart_redirect', 'my_custom_add_to_cart_redirect' );

    /* Clear cart data before adding new */
// before addto cart, only allow 1 item in a cart
add_filter( 'woocommerce_add_to_cart_validation', 'woo_custom_add_to_cart_before',10,3 );

function woo_custom_add_to_cart_before( $true, $product_id, $quantity ) {
    global $woocommerce;

    if(!has_term('supplemental-plans','product_cat',$product_id)){ // if product doesn't  have supplimental cat empty cart
        $woocommerce->cart->empty_cart();
    }
    // Do nothing with the data and return
    return $true;
}

/***
 * New Code
 */
require_once get_stylesheet_directory().'/functions/init.php';

/**
 * Remove subscription info on archive pages.
 *
 * @param   array  $include  Array of price parts to be included.
 *
 * @return  array	         Returns filtered array.
 */
function owp_child_subscriptions_product_price_string_inclusions( $include ) {
	global $woocommerce_loop;

	if ( ( oceanwp_is_woo_shop() || oceanwp_is_woo_tax() ) ||
		is_product() && $woocommerce_loop['name'] == 'related' ||
		is_page_template( 'dashboard/dashboard.php' ) ) :
		$include['subscription_price'] = true;
		$include['subscription_period'] = true;
		$include['subscription_length'] = false;
		$include['sign_up_fee'] = false;
		$include['trial_length'] = false;
	endif;

    return $include;
}
add_filter( 'woocommerce_subscriptions_product_price_string_inclusions', 'owp_child_subscriptions_product_price_string_inclusions');
/**
 * Change the "Select option" label on grid view.
 *
 * NOTE: The condition is taken from
 * wp-content/themes/oceanwp/woocommerce/loop/loop-start.php
 * but here doesn't seem to work.
 *
 * @param   string  $label  Label for the button.
 *
 * @return  string          Returns filtered string.
 */
function owp_child_ywraq_product_add_to_quote( $label ) {
	global $product;

	if ( ( oceanwp_is_woo_shop() || oceanwp_is_woo_tax() ) &&
		get_theme_mod( 'ocean_woo_grid_list', true ) &&
		'list' === get_theme_mod( 'ocean_woo_catalog_view', 'grid' ) ) :

		$label = $product->is_purchasable() ? __( 'Select options', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
	elseif ( ( oceanwp_is_woo_shop() || oceanwp_is_woo_tax() ) &&
		get_theme_mod( 'ocean_woo_grid_list', true ) &&
		'grid' === get_theme_mod( 'ocean_woo_catalog_view', 'grid' ) ) :
		$label = $product->is_purchasable() ? __( 'Enroll', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
	endif;

	return $label;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'owp_child_ywraq_product_add_to_quote', 999 );

// display the extra data in the order admin panel
function calibrium_remove_shipping_address_order_data_in_admin( $order ) {
	echo "<div class=\"shipping_address_remove\"></div>";
	echo "<script> jQuery(document).ready( function($) { $('.shipping_address_remove').closest('.order_data_column').remove(); });</script>";
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'calibrium_remove_shipping_address_order_data_in_admin' );

function calibrium_order_data_in_admin( $order ) {
	echo "<style>.wcb2b-card .wcb2b-card-title {background:none !important; padding: 20px; }</style>";
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'calibrium_order_data_in_admin' );

/**
 * Get cheapest plan in category
 *
 * @param   string  $category  Product category slug.
 *
 * @return  int                Returns product ID.
 */
function owp_child_get_cheapest_plan_in_category( $category = 'basic-plans' ) {
	$args = array(
		'post_type'      => 'product',
		'product_cat'    => $category,
		'posts_per_page' => -1,
		'fields'         => 'ids'
	);

	$products = get_posts( $args );

	$prices = array();
	foreach ( $products as $id ) :
		$prices[$id] = get_post_meta( $id, '_min_variation_price', true );
	endforeach;
	$index = array_keys( $prices, min( $prices ) );
	return $index[0];
}
/**
 * Get cheapest plan in Everyday plans category
 *
 * @return  int  Returns product ID.
 */
function owp_child_get_cheapest_plan_in_everyday() {
	return owp_child_get_cheapest_plan_in_category( 'everyday-plans' );
}
/**
 * Get cheapest plan in Comprehensive plans category
 *
 * @return  int  Returns product ID.
 */
function owp_child_get_cheapest_plan_in_comprehensive() {
	return owp_child_get_cheapest_plan_in_category( 'comprehensive-plans' );
}
/**
 * Get cheapest plans from Basic, Everyday and Comprehensive product categories.
 *
 * @see YITH_Request_Quote()->get_raq_return()
 *
 * @return  array  Returns array matchin the one from YITH plugin.
 */
function owp_child_get_cheapest_plans() {
	$basic         = owp_child_get_cheapest_plan_in_category();
	$everyday      = owp_child_get_cheapest_plan_in_everyday();
	$comprehensive = owp_child_get_cheapest_plan_in_comprehensive();

	$all = array( $basic, $everyday, $comprehensive );
	$output = array();
	foreach ( $all as $plan ) :
		$output[md5('plan-' . $plan)] = array(
			'product_id' => $plan,
			'quantity'   => 1
		);
	endforeach;

	return $output;

}

/**
 * Get product visible attributes
 *
 * @return  string  Returns list of product attributes names and values.
 */
function owp_child_get_product_visible_attributes() {
	global $product;
	$output = '';

	if ( is_object( $product ) ) {
		$product_attributes = [];
		$attributes         = $product->get_attributes();
		foreach ( $attributes as $attribute ) {
			$values = [];

			if ( ! $attribute['visible'] ) {
				continue;
			}

			// if the current attribute is taxonomy
			if ( $attribute->is_taxonomy() ) {
				$attribute_values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'all' ] );

				foreach ( $attribute_values as $attribute_value ) {
					$values[] = esc_html( $attribute_value->name );
				}
			} else {
				$values = $attribute->get_options();
			}

			$product_attributes[] = [
				'name'  => wc_attribute_label( $attribute->get_name() ),
				'value' => implode( ',', $values ),
			];
		}

		if ( is_array( $product_attributes ) && ! empty( $product_attributes ) ) {
			$output .= '<ul class="product-visible-attributes">';

			foreach ( $product_attributes as $item ) {
				$output .= '<li>';
				$output .= '<strong>' . $item['name'] . ':</strong> ';
				$output .= '<span>' . $item['value'] . '</span>';
				$output .= '</li>';
			}

			$output .= '</ul>';
		}
	}

	return $output;
}

/**
 * Get product variations guideline
 *
 * @return  string  Returns ACF value for variations guideline.
 */
function owp_child_get_single_product_variations_guideline() {
	global $product;
	return '<div class="product-variations-guideline">' . wp_kses_post( get_field( 'single_product_variations_guideline', $product->id ) ) . '</div>';
}
/**
 * Product short description filter
 *
 * @param   string  $excerpt  Product short description; post excerpt.
 *
 * @return  string            Returns filtered product excerpt.
 */
function owp_child_woocommerce_short_description( $excerpt ) {
	$before = owp_child_get_product_visible_attributes();
	$after  = owp_child_get_single_product_variations_guideline();

	if ( is_singular( 'product' ) ) :
		return  $excerpt . $before . $after;
	endif;
}
add_filter( 'woocommerce_short_description', 'owp_child_woocommerce_short_description' );


/**
 * Add a dashboard sidebar.
 */
add_action( 'widgets_init', 'calib_register_sidebar' );

function calib_register_sidebar() {
  $sidebars = [
          [
          'name'=> __( 'Dashboard Enrollment Select Product', 'calibrium' ),
            'id'=> 'dash-select-sidebar',
          ],
          [
          'name'=> __( 'Dashboard Enrollment Select Product', 'calibrium' ),
            'id'=> 'dash-select-sidebar',
          ],
          [
          'name'=> __( 'Dashboard Enrollment Select Product', 'calibrium' ),
            'id'=> 'dash-select-sidebar',
          ],
    ];

$defaults = [
            'name'=> 'Dashboard Sidebar',
            'id'=> 'dash-sidebar',
            'description'=> '',
            'class'=> '',
            'before_widget'=> '<li id="%1$s" class="widget %2$s">','after_widget'=> '</li>',
            'before_title'=> '<h4 class="widget-title">',
            'after_title'=> '</h4>',
        ];

  foreach( $sidebars as $sidebar ) {
    $args = wp_parse_args( $sidebar, $defaults );
    register_sidebar( $args );
  }
}

// add user role for broker/agent
add_action( 'init', 'calib_add_user_role' );

function calib_add_user_role() {

    // todo: remove following code once wrong typo rule removed from the db
    if ( get_option( 'calib_user_role', 0 ) == 1 ) {
        remove_role( 'borker_agent' );
        delete_option('calib_user_role');
    }

    // check if already role not created in the db
    if ( get_option( 'calib_add_user_role', 0 ) < 1 ) {
       add_role('broker_agent', __( 'Broker/Agent'  ),
        [
            'read'  => false,
            'delete_posts'  => false,
            'delete_published_posts' => false,
            'edit_posts'   => false,
            'publish_posts' => true,
            'upload_files'  => true,
            'edit_pages'  => false,
            'edit_published_pages'  =>  false,
            'publish_pages'  => false,
            'delete_published_pages' => false, // This user will NOT be able to  delete published pages.
        ]
    );
        update_option( 'calib_user_role', 1 );
    }
}

// get user role
function calib_user_role( $user_id = false ) {

	$user_id = ( ! $user_id && is_user_logged_in() ) ? get_current_user_id() : $user_id;

	if ( $user_id ) {
	     if( user_can( $user_id, 'manage_options' ) ){

            return 'admin';
        }

		$user_meta = get_userdata( $user_id );

		if ( $user_meta ) {
			$user_roles = $user_meta->roles;

			if ( in_array( 'broker_agent', $user_roles ) ) {

				return 'broker';
			} else {

				return 'other';
			}
		}
	}

	return 'guest';
}

/**
 * Sort an array by a specific key. Maintains index association.
 *
 * @param   array               $array  Array to sort.
 * @param   string              $on     Key for sorting by.
 * @param   SORT_ASC|SORT_DESC  $order  DIrection of sorting
 *
 * @return  array   Returns sorted array.
 */
function array_sort( $array, $on, $order=SORT_ASC ) {
    $new_array      = array();
    $sortable_array = array();

    if ( count( $array ) > 0 ) :
        foreach ( $array as $key => $value ) :
            if ( is_array( $value ) ) :
                foreach ( $value as $key2 => $value2 ) :
                    if ( $key2 == $on ) :
                        $sortable_array[$key] = $value2;
					endif;
                endforeach;
            else :
                $sortable_array[$key] = $value;
			endif;
        endforeach;

        switch ( $order ) {
            case SORT_ASC:
                asort( $sortable_array );
            break;
            case SORT_DESC:
                arsort( $sortable_array );
            break;
        }

        foreach ( $sortable_array as $key => $value ) :
            $new_array[$key] = $array[$key];
		endforeach;
    endif;

    return $new_array;
}

/**
 * Reorder products by price
 *
 * @param   array  $items  Array to sort.
 *
 * @return  array          Sorted array.
 */
function owp_child_reorder_products_by_price( $items ) {
	// Add price to items array
	foreach ( $items as $key => $value ) :
		$product = wc_get_product( $value['product_id'] );
		if ( is_object( $product ) ) :
			$price = $product->get_price();
			$items[$key]['price'] = $price;
		endif;
	endforeach;
	// sort items by price ascending
	$items = array_sort( $items, 'price' );
	return $items;
}
