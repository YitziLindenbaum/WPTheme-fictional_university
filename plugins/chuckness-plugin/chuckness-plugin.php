<?php

/*
  Plugin Name: Chuckness Plugin
  Description: A plugin that adds the necessary amount of Chuckness to your website.
  Version: 1.0
  Author: Chuck
  Author URI: https://www.udemy.com/user/chuck
  Text Domain: chuckplugindomain
  Domain Path: /languages
*/

class WordCountAndTimePlugin
{
  function __construct()
  {
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifWrap'));
    add_action('init', array($this, 'languages'));
  }

  function languages() {
    load_plugin_textdomain('chuckplugindomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
  }

  function ifWrap($content)
  {
    if (
      is_main_query() and is_single() and
      (
        get_option('wcp_wordcount', '1') or
        get_option('wcp_charcount', '1') or
        get_option('wcp_readtime', '1')
      )
    ) {
      return $this->createHTML($content);
    }
    return $content;
  }

  function createHTML($content)
  {
    $html = '<h3>' . esc_html(get_option('wpc_headline', 'Post Statistics')) . '</h3><p>';

    if (get_option('wcp_wordcount', '1') or get_option('wcp_readtime', '1')) {
      $wordCount = str_word_count(strip_tags($content));
    }

    if (get_option('wcp_wordcount', '1')) {
      $html .= esc_html__('This post has', 'chuckplugindomain') . ' ' .$wordCount . ' ' . esc_html__('words.', 'chuckplugindomain') . '<br>';
    }

    if (get_option('wcp_charcount', '1')) {
      $html .= 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
    }

    if (get_option('wcp_readtime', '1')) {
      $numMinutes = round($wordCount / 220);
      $html .= 'This post will take about ' . $numMinutes . ' minute' . ($numMinutes == 1 ? null : 's') . ' to read.<br>';
    }
    
    $html .= '</p>';

    if (get_option('wcp_loc', '0') == '0') {
      return $html . $content;
    }
    return $content . $html;
  }

  function settings()
  {
    add_settings_section('wcp_section1', null, null, 'word-count-settings-page');

    add_settings_field('wcp_loc', 'Display Location', array($this, 'locHTML'), 'word-count-settings-page', 'wcp_section1');
    register_setting('wordcountplugin', 'wcp_loc', array(
      'sanitize_callback' => array($this, 'sanitizeLocation'),
      'default' => '0'
    ));

    add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_section1');
    register_setting('wordcountplugin', 'wcp_headline', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => 'Post Statistics'
    ));

    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_section1', array('theName' => 'wcp_wordcount'));
    register_setting('wordcountplugin', 'wcp_wordcount', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => '1'
    ));

    add_settings_field('wcp_charcount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_section1', array('theName' => 'wcp_charcount'));
    register_setting('wordcountplugin', 'wcp_charcount', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => '1'
    ));

    add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_section1', array('theName' => 'wcp_readtime'));
    register_setting('wordcountplugin', 'wcp_readtime', array(
      'sanitize_callback' => 'sanitize_text_field',
      'default' => '1'
    ));
  }

  function sanitizeLocation($input)
  {
    if ($input != '0' and $input != '1') {
      add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end');
      return get_option('wcp_location');
    }
    return $input;
  }

  function checkboxHTML($args)
  { ?>
    <input type="checkbox" name="<?php echo $args['theName']; ?>" value="1" <?php checked(get_option($args['theName']), '1'); ?>>
  <?php }

  function headlineHTML()
  { ?>
    <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')); ?>">
  <?php }

  function locHTML()
  { ?>
    <select name="wcp_loc">
      <option value="0" <?php selected(get_option('wcp_loc'), '0'); ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_loc'), '1'); ?>>End of post</option>
    </select>
  <?php }

  function adminPage()
  {
    add_options_page('Word Count Settings', esc_html__('Word Count', 'chuckplugindomain'), 'manage_options', 'word-count-settings-page', array($this, 'ourHTML'));
  }

  function ourHTML()
  { ?>
    <div class="wrap">
      <h1>Word Count Settings</h1>
      <form action="options.php" method="POST">
        <?php
        settings_fields('wordcountplugin');
        do_settings_sections('word-count-settings-page');
        submit_button();
        ?>
      </form>
    </div>
<?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();



?>