<?php
use \Elementor\Controls_Manager;

/**
 * Author Name: Widgetmax
 * Author URL: https://widgetmax.com
 * Date: 3/6/2021
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

class Widgetmax_Custom_CSS
{

	/*
	 * Instance of this class
	 */
	private static $instance = null;

	public function __construct()
	{

		// Add new controls to advanced tab globally
		add_action("elementor/element/after_section_end", array($this, 'widgetmax_add_section_custom_css_controls'), 25, 3);

		// Render the custom CSS
		if (!defined('ELEMENTOR_PRO_VERSION')) {
			add_action('elementor/element/parse_css', array($this, 'widgetmax_add_post_css'), 10, 2);
		}
	}



	public function widgetmax_add_section_custom_css_controls($widget, $section_id, $args)
	{

		if ('section_custom_css_pro' !== $section_id ) {
			return;
		}
		if (!defined('ELEMENTOR_PRO_VERSION')) {

			$widget->start_controls_section(
				'widgetmax_custom_css_section',
				array(
					'label'     => __('Custom CSS by Widgetmax', 'widgetmax'),
					'tab'       => Controls_Manager::TAB_ADVANCED
				)
			);

			$widget->add_control(
				'widgetmax_custom_css',
				array(
					'type'        => Controls_Manager::CODE,
					'label'       => __('Custom CSS', 'widgetmax'),
					'label_block' => true,
					'language'    => 'css'
				)
			);
			ob_start(); ?>
			<pre>
Examples:
// To target main element
selector { color: red; }
// For child element
selector .child-element{ margin: 10px; }
</pre><?php
			$output = ob_get_clean();

			$widget->add_control(
				'widgetmax_custom_css_description',
				array(
					'raw'             => __('Use "selector" keyword to target wrapper element.', 'widgetmax') . $output,
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-descriptor',
					'separator'       => 'none'
				)
			);

			$widget->end_controls_section();
		}
	}



	public function widgetmax_add_post_css($post_css, $element)
	{
		$element_settings = $element->get_settings();

		if (empty($element_settings['widgetmax_custom_css'])) {
			return;
		}

		$css = trim($element_settings['widgetmax_custom_css']);

		if (empty($css)) {
			return;
		}
		$css = str_replace('selector', $post_css->get_element_unique_selector($element), $css);

		// Add a css comment
		$css = sprintf('/* Start custom CSS for %s, class: %s */', $element->get_name(), $element->get_unique_selector()) . $css . '/* End custom CSS */';

		$post_css->get_stylesheet()->add_raw_css($css);
	}



	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Widgetmax_Custom_CSS::get_instance();

