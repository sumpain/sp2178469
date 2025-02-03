<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );


function my_custom_sidebar() {
	register_sidebar(
		array (
			'name' => __( 'Custom Sidebar Area', 'hello-elementor-child' ),
			'id' => 'custom-side-bar',
			'description' => __( 'This is the custom sidebar that you registered using the code snippet. You can change this text by editing this section in the code.', 'your-theme-domain' ),
			'before_widget' => '<div class="widget-content">',
			'after_widget' => "</div>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'my_custom_sidebar' );


// Pagebuilder Locale
function sp_unload_textdomain_elementor() {
	if (is_admin()) {
		$user_locale = get_user_meta( get_current_user_id(), 'locale', true );
		if ( 'en_US' === $user_locale ) {
			unload_textdomain( 'elementor' );
			unload_textdomain( 'elementor-pro' );
		}
	}
}
add_action( 'init', 'sp_unload_textdomain_elementor', 100 );

/* Icon Widget Fix - Link now applies to the whole element (not only icon & title) */ 

function tdau_link_whole_icon_box ( $content, $widget ) {
	
    if ( 'icon-box' === $widget->get_name() ) {
        $settings = $widget->get_settings_for_display();

		$wrapper_tag = 'div';

		$has_icon = ! empty( $settings['icon'] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$wrapper_tag = 'a';
		}

		$icon_attributes = $widget->get_render_attribute_string( 'icon' );
		$link_attributes = $widget->get_render_attribute_string( 'link' );

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $is_new = ! isset( $settings['icon'] ) && Elementor\Icons_Manager::is_migration_allowed();
		
		ob_start();

		?>
		<<?php echo implode( ' ', [ $wrapper_tag, $link_attributes ] ); ?> class="elementor-icon-box-wrapper elementor-icon-box-wrapper-tdau elementor-animation-<?php echo $settings['hover_animation']; ?>">
			<?php if ( $has_icon ) : ?>
			<div class="elementor-icon-box-icon">
				<<?php echo implode( ' ', [ 'span', $icon_attributes ] ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['icon'] ) ) {
					?><i <?php echo $widget->get_render_attribute_string( 'i' ); ?>></i><?php
				}
				?>
				</span>
			</div>
			<?php endif; ?>
			<div class="elementor-icon-box-content">
				<<?php echo $settings['title_size']; ?> class="elementor-icon-box-title">
					<?php echo $settings['title_text']; ?>
				</<?php echo $settings['title_size']; ?>>
				<?php if ( ! Elementor\Utils::is_empty( $settings['description_text'] ) ) : ?>
				<p <?php echo $widget->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></p>
				<?php endif; ?>
			</div>
		</<?php echo $wrapper_tag; ?>>
		<?php

		$content = ob_get_clean();

    }

    return $content;
}
// Add a custom product note after add to cart button in single product pages
//add_action('woocommerce_after_add_to_cart_button', 'wpo_wcpdf_show_purchase_notes', 10 );

//add_action( 'wpo_wcpdf_after_item_meta', 'wpo_wcpdf_show_purchase_notes', 10, 3 );
function wpo_wcpdf_show_purchase_notes ( $template_type, $item, $order ) {
    if (!empty($item['product'])) {
        $purchase_note = $item['product']->get_purchase_note();
        if ( !empty( $purchase_note ) ) {
            echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) );
        }
    }
}

//add_action('woocommerce_product_meta_end','cmk_additional_button');
function cmk_additional_button() {
    $product = wc_get_product(get_the_ID());
    $note = $product->get_purchase_note();
	echo '<div class="inhalt">';
    echo '<p class="product-purchase-note">Kaufhinweis: '.$note.'</p>';    
	echo '</div>';
}

function woocommerce_custom_fields_display()
{
  global $post;
  $product = wc_get_product($post->ID);
  $custom_product_deposit = $product->get_meta('_custom_product_deposit');
  $custom_product_per_box_unit_count = $product->get_meta('_custom_product_per_box_unit_count');
  $custom_product_per_liter_preis = $product->get_meta('_custom_product_per_liter_preis');
  $custom_product_per_bottole_amount = $product->get_meta('_custom_product_per_bottole_amount');

  		if ($custom_product_per_box_unit_count) {
			echo '<div class="inhalt">';
			echo '<p class="product-purchase-inhalt">Preis/l: ('.$custom_product_per_liter_preis.' € * / 1 Liter)</p>';    
			echo '<p class="product-purchase-zzgl">zzgl. Pfand: '.$custom_product_deposit.'</p>';
			echo '</div>';
  		}
}
 
add_action('woocommerce_product_meta_start', 'woocommerce_custom_fields_display',39);

function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_deposit',
            'placeholder' => 'Pfand',
            'label' => __('Pfand', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
	woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_per_box_unit_count',
            'placeholder' => 'Inhalt',
            'label' => __('Inhalt', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
	woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_per_bottole_amount',
            'placeholder' => 'Inhalt pro Flasche',
            'label' => __('Inhalt / Flasche', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
	woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_per_liter_preis',
            'placeholder' => 'Preis pro l',
            'label' => __('Preis/l', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );	
    echo '</div>';
}

function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_custom_product_deposit = $_POST['_custom_product_deposit'];
	$woocommerce_custom_product_per_box_unit_count = $_POST['_custom_product_per_box_unit_count'];
	$woocommerce_custom_product_per_bottole_amount = $_POST['_custom_product_per_bottole_amount'];
	$woocommerce_custom_product_per_liter_preis = $_POST['_custom_product_per_liter_preis'];
    if (!empty($woocommerce_custom_product_deposit))
        update_post_meta($post_id, '_custom_product_deposit', esc_attr($woocommerce_custom_product_deposit));

    if (!empty($woocommerce_custom_product_per_box_unit_count))
        update_post_meta($post_id, '_custom_product_per_box_unit_count', esc_attr($woocommerce_custom_product_per_box_unit_count));

    if (!empty($woocommerce_custom_product_per_bottole_amount))
        update_post_meta($post_id, '_custom_product_per_bottole_amount', esc_attr($woocommerce_custom_product_per_bottole_amount));

    if (!empty($woocommerce_custom_product_per_liter_preis))
        update_post_meta($post_id, '_custom_product_per_liter_preis', esc_attr($woocommerce_custom_product_per_liter_preis));
}

add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

function wpb_hook_javascript() {
    ?>
        <script type="text/javascript">
			(function ($) {

				$(document).on('click', '.single_add_to_cart_button', function (e) {
					e.preventDefault();

					var $thisbutton = $(this),
							$form = $thisbutton.closest('form.cart'),
							id = $thisbutton.val(),
							product_qty = $form.find('input[name=quantity]').val() || 1,
							product_id = $form.find('input[name=product_id]').val() || id,
							variation_id = $form.find('input[name=variation_id]').val() || 0;

					var data = {
						action: 'woocommerce_ajax_add_to_cart',
						product_id: product_id,
						product_sku: '',
						quantity: product_qty,
						variation_id: variation_id,
					};

					$(document.body).trigger('adding_to_cart', [$thisbutton, data]);

					$.ajax({
						type: 'post',
						url: wc_add_to_cart_params.ajax_url,
						data: data,
						beforeSend: function (response) {
							$thisbutton.removeClass('added').addClass('loading');
						},
						complete: function (response) {
							$thisbutton.addClass('added').removeClass('loading');
						},
						success: function (response) {

							if (response.error && response.product_url) {
								window.location = response.product_url;
								return;
							} else {
								$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
							}
						},
					});

					return false;
				});
			})(jQuery);
        </script>
    <?php
}
add_action('wp_head', 'wpb_hook_javascript');
//
function my_text_strings( $translated_text, $text, $domain ) {
	switch ( $translated_text ) {
		case 'Product contains' :
			$translated_text = __( 'Produkt enthält', 'woocommerce-germanized' );
			break;
		case 'plus ':
			$translated_text = __( 'zzgl ', 'woocommerce-germanized' );
			break;
		case 'Related Products':
			$translated_text = __( 'Verwandte Produkte', 'woocommerce-germanized' );
			break;
		case 'Proceed to Checkout':
			$translated_text = __( 'Zur Kasse', '' );
			break;
		case 'Update Cart':
			$translated_text = __( 'Warenkorb aktualisieren', 'woocommerce' );
			break;
		case 'Apply coupon':
			$translated_text = __( 'Gutschein anwenden', 'woocommerce' );
			break;
		case 'Coupon code':
			$translated_text = __( 'Gutscheincode', 'woocommerce-germanized' );
			break;
		case 'Description':
			$translated_text = __( 'Beschreibung', 'woocommerce' );
			break;
		case 'If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.':
			$translated_text = __( 'Wenn Sie schon einmal bei uns eingekauft haben, geben Sie bitte unten Ihre Daten ein. Wenn Sie ein neuer Kunde sind, gehen Sie bitte zum Abschnitt Rechnungsstellung.', 'woocommerce' );
			break;
			
		case 'Read More':
			$translated_text = __( 'Weiterlesen', 'hello-elementor' );
			break;
		case 'Search...':
			$translated_text = __( 'Suchen...', 'hello-elementor' );
			break;
			
		case 'Search results for':
			$translated_text = __( 'Suchergebnisse für', 'hello-elementor' );
			break;			
	}
	return $translated_text;
}
add_filter( 'gettext', 'my_text_strings', 20, 3 );

// Change WooCommerce "Related products" text

add_filter('gettext', 'change_rp_text', 10, 3);
add_filter('ngettext', 'change_rp_text', 10, 3);

function change_rp_text($translated, $text, $domain)
{
     if ($text === 'Related products' && $domain === 'woocommerce') {
         $translated = esc_html__('Verwandte Produkte', $domain);
     }
     return $translated;
}

// To change add to cart text on single product page
add_filter( 'woocommerce_order_button_text', 'my_custom_checkout_button_text' ); 
function my_custom_checkout_button_text() {
    return __( 'Zur Kasse', 'woocommerce' ); 
}

// To change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
    return __( 'In den Warenkorb legen', 'woocommerce' ); 
}

// To change add to cart text on product archives(Collection) page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );  
function woocommerce_custom_product_add_to_cart_text() {
    return __( 'In den Warenkorb legen', 'woocommerce' );
}
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text', 20);
function wcc_change_breadcrumb_home_text( $defaults ) {
    // Change the breadcrumb home text from 'Home' to 'Apartment'
	$defaults['home'] = 'Heim';
	return $defaults;
}

add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
        
function woocommerce_ajax_add_to_cart() {

	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
	$quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
	$variation_id = absint($_POST['variation_id']);
	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
	$product_status = get_post_status($product_id);

	if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

		do_action('woocommerce_ajax_added_to_cart', $product_id);

		if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
			wc_add_to_cart_message(array($product_id => $quantity), true);
		}

		WC_AJAX :: get_refreshed_fragments();
	} else {

		$data = array(
			'error' => true,
			'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

		echo wp_send_json($data);
	}

	wp_die();
}

// Adding a custom fee to cart based on a product custom field value calculation
add_action('woocommerce_cart_calculate_fees', 'add_custom_fees');
function add_custom_fees( WC_Cart $cart ){
    $fees = 0;
    foreach( $cart->get_cart() as $item ){
		$product = $item['data'];
		$deposit = str_replace("€","",get_post_meta( $product->get_id(), '_custom_product_deposit', true ));
		if(is_numeric($deposit)){
			$depo = $deposit;
		}else{
			$depo = 0;
		}
       $fees += $item[ 'quantity' ] * $depo ; 
    }
    if( $fees != 0 ){
        $cart->add_fee( 'Pfand', $fees);
    }
}