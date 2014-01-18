<?php
/*
The main plugin file defines a singleton class to handle all functionality of the plugin
*/

class WP_Plugin_Framework
{
    //Version number used to track updates, change when you release a new version
    const VERSION = '1.0';

    //The singleton instance
    public static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new WP_Plugin_Framework();
        return self::$instance;
    }

    //Constructor called first time this class is accessed (during plugin activation)
    //and is responsible for creating hooks and setting defaults
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
        add_action('wp_ajax_pf_update_settings', array($this, 'update_settings'));
        //add a shortcode [pf_form] to be handled by the pf_form() function in this class
        add_shortcode('pf_form', array($this, 'pf_form'));
        //ajax actions for admin and non-admin ajax posts
        add_action('wp_ajax_nopriv_pf_form_post', array($this, 'pf_form_post'));
        add_action('wp_ajax_pf_form_post', array($this, 'pf_form_post'));

        //set option defaults if we have no options or a different version of the plugin
        $options = get_option('WP_PF_Options');
        if ($options == '' || $options['pluginVersion'] != self::VERSION)
        {
            $this->set_option_defaults($options);
        }

        //OPTIONAL: Any other initialization you need to do goes here
    }

    function set_option_defaults($options)
    {
        if ($options == '')
        {
            $options = array(
                'pluginVersion' => self::VERSION,
                'textOption' => 'Default Text',
                'toggleOption' => '0',
                'numOption' => '999',
                'urlOption' => 'http://mammothology.com'
            );
        }
        else
        {
            $options['pluginVersion'] = self::VERSION;
            if (!array_key_exists('textOption', $options)) $options['textOption'] = 'Default Text';
            if (!array_key_exists('toggleOption', $options)) $options['toggleOption'] = '0';
            if (!array_key_exists('numOption', $options)) $options['numOption'] = '999';
            if (!array_key_exists('urlOption', $options)) $options['urlOption'] = 'http://mammothology.com';
        }

        update_option('WP_PF_Options', $options);
    }

    //Hooked to the admin_menu action in the constructor, this will add a plugin options page under the Settings menu in the WordPress dashboard
    public function admin_menu()
    {
        $menu_hook = add_options_page('WP Plugin Framework', 'Plugin Framework', 'manage_options', 'wp-plugin-framework-settings', array($this, 'admin_settings_page'));
        //using the menu hook we can ensure our scripts are loaded only on the admin settings page
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_menu_scripts'));
    }

    public function admin_settings_page()
    {
        //check if the user attempting to access the settings page is allowed
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'wp-plugin-framework-settings.php';
    }

    public function admin_menu_scripts()
    {
        //Include our admin pages javascript, which handles form submissions from the settings page via ajax
        wp_enqueue_script('pf-admin-js', plugin_dir_url(__FILE__) . 'js/pf-admin.js', array('jquery'));
        //pass a parameter to the javascript, in this case the WordPress ajax url for submitting the form to
        wp_localize_script('pf-admin-js', 'admin_ajaxurl', admin_url('admin-ajax.php'));
    }

    //Hooked to the plugin_action_links filter, this appends a 'Settings' link to the links below the plugin name on the Plugins page in the WordPress dashboard
    public function plugin_action_links($links, $file)
    {
        //check if we're looking at this particular plugin
        if ($file == 'wp-plugin-framework/wp-plugin-framework.php')
        {
            //The parameter to menu_page_url is the same as the name defined for the add_options_page in the admin_menu() function above
            $settings_link = '<a href="' . menu_page_url('wp-plugin-framework-settings', false) . '">' . esc_html(__('Settings', 'wp-plugin-framework')) . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    function setup_db()
    {
        include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'wp-plugin-framework-database.php';
        WP_Plugin_Framework_DB::getInstance()->setup_db();
    }

    //Settings changes (set by user in wp-plugin-framework-settings.php) are saved here.
    /*
     * How this all links together for Ajax:
     *      1) The <form> must include a hidden field named action:
     *          <input type="hidden" name="action" value="pf_update_settings"/>
     *      2) In the constructor for this plugin, we assign the WordPress ajax action to this function:
     *          add_action('wp_ajax_pf_update_settings', array($this, 'update_settings'));
     *      3) In the javascript we included with the settings page (see admin_menu_scripts() above) we post the form via ajax:
     *          data: $form.serialize()   //see pf-admin.js for full ajax call
     *
     */
    public function update_settings()
    {
        //$_POST contains our form values as usual
        $options = get_option('WP_PF_Options');
        $options['textOption'] = $_POST['textOption'];
        $options['toggleOption'] = $_POST['toggleOption'];
        $options['numOption'] = $_POST['numOption'];
        $options['urlOption'] = $_POST['urlOption'];
        update_option('WP_PF_Options', $options);

        //The correct way to return JSON from WordPress
        header("Content-Type: application/json");
        echo json_encode(array('success' => true));
        exit;
    }

    //Example of a shortcode handler which puts a form on any page or post containing the shortcode
    public function pf_form($atts)
    {
        //This extracts any attributes provided into variables and provides defaults if no attributes exists
        //Example: [pf_form form="test" size="large"] would give us $form and $size here with values "test" and "large"
        extract(shortcode_atts(array(
            'form' => 'default',
        ), $atts));

        $options = get_option('WP_PF_Options');

        //Add our javascript for the front-end only when this shortcode is active
        wp_enqueue_script('pf-js', plugin_dir_url(__FILE__) . 'js/pf.js', array('jquery'));
        wp_localize_script('pf-js', 'ajaxurl', admin_url('admin-ajax.php', 'relative'));
        wp_localize_script('pf-js', 'pf_options',
            array('textOption' => $options['textOption'], 'toggleOption' => $options['toggleOption'],
                'numOption' => $options['numOption'], 'urlOption' => $options['urlOption']));

        //using the Output Buffer functions here ensures the shortcode html is inserted into the page/post correctly
        ob_start();
        include 'views/wp-plugin-framework-form.php';
        $content = ob_get_clean();
        //using apply_filters allows other developers to customize the form using the pf_form_html filter
        return apply_filters('pf_form_html', $content);
    }

    //Handles submission of our shortcode form
    public function pf_form_post()
    {
        $textVal = sanitize_text_field($_POST['textVal']);

        //The correct way to return JSON from WordPress
        header("Content-Type: application/json");
        echo json_encode(array('msg' => "Received text: $textVal"));
        exit;
    }

}

WP_Plugin_Framework::getInstance();