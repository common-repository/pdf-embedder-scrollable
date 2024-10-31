<?php
/**
 * Plugin Name: PDF Embedder - Scrollable
 * Description: A WordPress plugin to embed scrollable PDFs with customizable scales.
 * Version: 1.1.0
 * Author: Aurorbyte
 * Author URI: https://aurorbyte.com/
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: pdf-embedder-scrollable
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function pes_pdf_scrollable_enqueue_scripts() {
    // Enqueue custom style
    wp_enqueue_style(
        'pdf-embedder-scrollable-style', 
        plugin_dir_url(__FILE__) . 'css/pdf-embedder-scrollable.css', 
        array(), 
        filemtime(plugin_dir_path(__FILE__) . 'css/pdf-embedder-scrollable.css'), // Version based on file modification time
        'all'
    );

    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue pdf.js library
    wp_enqueue_script(
        'pdfjs',
        plugin_dir_url(__FILE__) . 'js/pdf.min.js',
        array(),
        '2.10.377',
        true
    );

    // Enqueue custom script
    wp_enqueue_script(
        'pdf-embedder-scrollable-script', 
        plugin_dir_url(__FILE__) . 'js/pdf-embedder-scrollable.js', 
        array('jquery', 'pdfjs'), 
        filemtime(plugin_dir_path(__FILE__) . 'js/pdf-embedder-scrollable.js'), // Version based on file modification time
        true
    );
	wp_localize_script(
		'pdf-embedder-scrollable-script',
		'pdfWorkerUrl', // Object name available in JS
		plugin_dir_url( __FILE__ ) . 'js/pdf.worker.min.js'
	);
}
add_action('wp_enqueue_scripts', 'pes_pdf_scrollable_enqueue_scripts');

function pes_pdf_scrollable_embedder_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'url' => '',
            'scale' => '1.5',
            'width' => '100%',
            'height' => '100vh',
            'max-width' => '100%',
            'max-height' => '100vh',
        ),
        $atts,
        'pes_pdf_scrollable_embed'
    );

    if (empty($atts['url'])) {
        return '<p>Please provide a PDF URL.</p>';
    }
	
	$url = $atts['url'];

    // Check if the URL is relative
    if (strpos($url, 'http') !== 0) {
        $url = site_url($url); // Convert relative URL to absolute by appending site URL
    }

    $scale = $atts['scale'];
    $width = $atts['width'];
    $height = $atts['height'];
    $max_width = $atts['max-width'];
    $max_height = $atts['max-height'];

    ob_start(); ?>
    <div class="pes-pdf-scrollable--viewer" data-url="<?php echo esc_url( $url ); ?>" data-scale="<?php echo esc_attr( $scale ); ?>" style="width: <?php echo esc_attr( $width ); ?>; height: <?php echo esc_attr( $height ); ?>; max-width: <?php echo esc_attr( $max_width ); ?>; max-height: <?php echo esc_attr( $max_height ); ?>;"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('pes_pdf_scrollable_embed', 'pes_pdf_scrollable_embedder_shortcode');
