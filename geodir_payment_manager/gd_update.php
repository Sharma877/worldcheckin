<?php
/**
 * Contains functions for updating the pluing from the GeoDirectory server.
 *
 * @since 1.0.0
 */

// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
//set_site_transient('update_plugins', null);

$gd_api_url = 'http://wpgeodirectory.com/updates/';
$plugin_slug = basename(dirname(__FILE__));


if (!function_exists('gd_check_for_plugin_update')) {
    /**
     * Check for plugin update.
     *
     * @since 1.0.0
     *
     * @global string $gd_api_url The API url where the plugin can check for update.
     * @global string $plugin_slug The plugin slug to check for update.
     *
     * @param object $checked_data Checked plugin data.
     * @return object
     */
    function gd_check_for_plugin_update($checked_data)
    {
        global $gd_api_url;

        if ($ssl = wp_http_supports(array('ssl')))
            $gd_api_url = set_url_scheme($gd_api_url, 'https');

        $empty_checked = false;

        if (empty($checked_data->checked) && isset($_POST['action']) && isset($_POST['action']) == 'update-plugin' && isset($_POST['plugin']) && $_POST['plugin']) {
            $plugins = get_plugins();
            $p_arr = array();
            foreach ($plugins as $p_name => $p_val) {
                if ($p_name == $_POST['plugin'] && isset($p_val['Version'])) {
                    $p_arr[$p_name] = $p_val['Version'];
                }
            }
            if (!empty($p_arr)) {
                $checked_data->checked = $p_arr;
            }
            $empty_checked = true;
        }


        if (empty($checked_data->checked)) {
            return $checked_data;
        } else {


            $uname = get_option('gd_update_uname');
            $request_args = array(
                'slug' => $checked_data->checked,
                'version' => GEODIRECTORY_VERSION,
                'site' => home_url(),
                'user' => $uname,
            );

            $request_string = gd_prepare_request('basic_check', $request_args);
            // Start checking for an update
            $raw_response = wp_remote_post($gd_api_url, $request_string);

            if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
                $response = unserialize($raw_response['body']);


            if (is_object($response) && !empty($response)) {// Feed the update data into WP updater

                foreach ($response as $key => $pinfo) {
                    $pieces = explode("/", $key);
                    $slug = $pieces[0];
                    $pinfo->slug = $slug;
                    $pinfo->plugin = $key;
                    $pinfo->url = 'https://wpgeodirectory.com/';
                    $checked_data->response[$key] = $pinfo;
                }

            }

        }

        if ($empty_checked) {
            $checked_data->checked = array();
        }
        return $checked_data;
    }
}


if (!function_exists('gd_api_info_call')) {
    /**
     * Plugin update check api request.
     *
     * @since 1.0.0
     *
     * @global string $gd_api_url The API url where the plugin can check for update.
     * @global string $plugin_slug The plugin slug to check for update.
     *
     * @param object|bool $def The result object. Default false.
     * @param string $action The type of information being requested from the Plugin Install API.
     * @param object $args Plugin API arguments.
     * @return bool|mixed|WP_Error
     */
    function gd_api_info_call($def, $action, $args)
    {
        global $plugin_slug, $gd_api_url;

        if (isset($args->slug) && strpos($args->slug, 'geodir_') !== false) {
        } else {
            return false;
        }// if not a geodir plugin bail


        // Get the current version
        $plugin_info = get_site_transient('update_plugins');
        $current_version = 1;
        $args->version = $current_version;

        $request_string = gd_prepare_request($action, $args);

        $request = wp_remote_post($gd_api_url, $request_string);
//print_r($request);
        if (is_wp_error($request)) {
            $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
        } else {
            $res = unserialize($request['body']);
            if ($res === false)
                $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
        }

        return $res;
    }
}


if (!function_exists('gd_prepare_request')) {
    /**
     * Prepare api request.
     *
     * @since 1.0.0
     *
     * @global string $wp_version WordPress version.
     *
     * @param string $action The type of information being requested from the Plugin Install API.
     * @param object $args Plugin API arguments.
     * @return array
     */
    function gd_prepare_request($action, $args)
    {
        global $wp_version;

        return array(
            'body' => array(
                'action' => $action,
                'request' => serialize($args),
                'api-key' => md5(get_bloginfo('url'))
            ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
            'sslverify' => false // this is needed for some old old servers
        );
    }
}


if (!function_exists('gd_plugin_upgrade_errors')) {
    /**
     * Set plugin upgrade errors.
     *
     * @since 1.0.0
     *
     * @global object $wpdb WordPress Database object.
     *
     * @param bool $false Whether to bail without returning the package. Default false.
     * @param string $src The package file url.
     * @param object $Uthis The WP_Upgrader instance.
     * @return mixed
     */
    function gd_plugin_upgrade_errors($false, $src, $Uthis)
    {
        global $wpdb;
        if (strstr($src, 'https://wpgeodirectory.com/download/') || strstr($src, 'http://wpgeodirectory.com/download/')) {// if downloading e then verify login details
            $Uthis->strings['incompatible_archive'] = __('Login details for GeoDirectory failed! Please check GeoDirectory > Auto Updates and that your membership is active.', 'geotheme');
            $Uthis->strings['download_failed'] = __('Login details for GeoDirectory failed! Please check GeoDirectory>Auto Updates and that your membership is active.', 'geotheme');
        }

        return $false;
    }
}


if (!function_exists('gd_plugin_upgrade_login')) {
    /**
     * Login for plugin upgrade.
     *
     * @since 1.0.0
     *
     * @global object $wpdb WordPress Database object.
     *
     * @param array $args An array of HTTP request arguments.
     * @param string $src The request URL.
     * @return mixed
     */
    function gd_plugin_upgrade_login($args, $src)
    {

        global $wpdb;
        if (strstr($src, 'https://wpgeodirectory.com/download/') || strstr($src, 'http://wpgeodirectory.com/download/')) {// if downloading then verify login details
            $uname = get_option('gd_update_uname');
            $upass = get_option('gd_update_upass');
            if ($uname) {
                $args['method'] = 'POST';
                $args['body'] = 'gd_auth_update=1&uname=' . base64_encode($uname) . '&upass=' . $upass;
                $args['sslverify'] = '0'; // needed for older servers
            }
        }

        return $args;
    }
}


if (is_admin()) {

    // Take over the update check
    add_filter('pre_set_site_transient_update_plugins', 'gd_check_for_plugin_update');
    add_filter('pre_set_site_transient_update_plugins', 'gd_check_for_messages');

    // Take over the Plugin info screen
    add_filter('plugins_api', 'gd_api_info_call', 10, 3);

    add_filter('upgrader_pre_download', 'gd_plugin_upgrade_errors', 10, 3);

    add_filter('http_request_args', 'gd_plugin_upgrade_login', 10, 2);

    add_filter('geodir_settings_tabs_array', 'geodir_adminpage_auto_update', 5);

    add_action('geodir_admin_option_form', 'geodir_auto_update_tab_content', 5);

    add_action('admin_init', 'geodir_auto_update_from_submit_handler');

}


if (!function_exists('geodir_adminpage_auto_update')) {
    /**
     * Adds auto updates tab to geodirectory settings.
     *
     * @since 1.0.0
     *
     * @param array $tabs Geodirectory settings page tab list.
     * @return array Modified Tabs list
     */
    function geodir_adminpage_auto_update($tabs)
    {

        $tabs['auto_update_fields'] = array(
            'label' => __('Auto Updates', 'geodirectory')
        );

        return $tabs;
    }
}

if (!function_exists('geodir_auto_update_tab_content')) {
    /**
     * Adds content to auto updates tab.
     *
     * @since 1.0.0
     *
     * @param string $tab Geodirectory settings page tab name.
     */
    function geodir_auto_update_tab_content($tab)
    {

        switch ($tab) {

            case 'auto_update_fields':

                geodir_auto_update_setting_fields();

                break;

        }

    }
}

if (!function_exists('geodir_auto_update_setting_fields')) {
    /**
     * Adds setting fields to auto updates tab.
     *
     * @since 1.0.0
     *
     * @global object $wpdb WordPress Database object.
     */
    function geodir_auto_update_setting_fields()
    {
        global $wpdb;
        ?>

        <div class="inner_content_tab_main">
            <div class="gd-content-heading active">
                <h3><?php _e('Enter your GeoDirectory membership details to allow you to update plugins from dashboard', 'geodirectory'); ?></h3>

                <table class="form-table">
                    <?php
                    $uname = get_option('gd_update_uname');
                    $upass = get_option('gd_update_upass');
                    if ($upass) {
                        $upass = 'fakefakefakefakefakefakefakefakefakefake';
                    }
                    ?>
                    <tbody>
                    <tr valign="top">
                        <th scope="row"
                            class="titledesc"><?php _e('Geodirectory username/email', 'geodirectory'); ?></th>
                        <td class="forminp">
                            <input name="gd_update_uname" id="gd_update_uname" type="text" style=" min-width:300px;"
                                   value="<?php echo $uname;?>">
                            <span
                                class="description"><?php _e('Enter your GeoDirectory username or email', 'geodirectory'); ?></span>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"
                            class="titledesc"><?php _e('Licence Key', 'geodirectory'); ?></th>
                        <td class="forminp">
                            <input name="gd_update_upass" id="gd_update_upass" type="password" style=" min-width:300px;"
                                   value="<?php echo $upass;?>">
                            <span
                                class="description"><?php _e('Enter your GeoDirectory Licence Key, this can be found on your profile page on ', 'geodirectory'); ?>
                                <a href="https://wpgeodirectory.com/"
                                   target="_blank">https://wpgeodirectory.com/</a></span>
                        </td>
                    </tr>


                    </tbody>
                </table>


                <p class="submit" style="margin-top:10px;">
                    <input name="geodir_auto_update_general_options_save" class="button-primary" type="submit"
                           value="<?php _e('Save changes', 'geodirectory'); ?>"/>
                    <input type="hidden" name="subtab" id="last_tab"/>
                </p>

            </div>
        </div>

    <?php

    }
}

if (!function_exists('geodir_auto_update_from_submit_handler')) {
    /**
     * Handles submitted form data of auto updates tab.
     *
     * @since 1.0.0
     */
    function geodir_auto_update_from_submit_handler()
    {

        if (isset($_REQUEST['geodir_auto_update_general_options_save'])) {

            //echo "<pre>"; print_r($_REQUEST);


            if ($_REQUEST['gd_update_uname']) {
                update_option('gd_update_uname', $_REQUEST['gd_update_uname']);
            }
            if ($_REQUEST['gd_update_upass'] && $_REQUEST['gd_update_upass'] != 'fakefakefakefakefakefakefakefakefakefake') {
                update_option('gd_update_upass', base64_encode($_REQUEST['gd_update_upass']));
            }


            $msg = __('Your settings have been saved.', 'geodirectory');

            $msg = urlencode($msg);

            $location = admin_url() . "admin.php?page=geodirectory&tab=auto_update_fields&adl_success=" . $msg;
            wp_redirect($location);
            exit;

        }

    }
}

#################################################
########## CHECK FOR GD MESSAGES ################
#################################################

if (!function_exists('gd_check_for_messages')) {
    /**
     * Check plugin upgrade messages and update into the db.
     *
     * @since 1.0.0
     *
     * @global string $gd_api_url The API url where the plugin can check for update.
     * @global string $plugin_slug The plugin slug to check for update.
     * @global object $wpdb WordPress Database object.
     *
     * @param object $checked_data Checked plugin data.
     * @return object
     */
    function gd_check_for_messages($checked_data)
    {
        global $gd_api_url, $plugin_slug, $wpdb;
        $gd_arr = array();
        if (empty($checked_data->checked)) {
            return $checked_data;
        } else {
            foreach ($checked_data->checked as $key => $value) {// build an array of installed GD plugins and versions
                if (strpos($key, 'geodir_') !== false) {
                    $pieces = explode("/", $key);
                    $gd_arr[$pieces[0]] = array("ver" => $value, "last" => get_option($pieces[0] . "_last"));
                }
            }

            $gd_arr['geodirectory'] = array("ver" => GEODIRECTORY_VERSION, "last" => get_option("geodirectory_last"));// add core
            $gd_arr['geodirectory_general'] = array("ver" => '', "last" => get_option("geodirectory_general_last"));// add general messages


            $uname = get_option('gd_update_uname');
            $request_args = array(
                'plugins' => $gd_arr,
                'version' => GEODIRECTORY_VERSION,
                'site' => home_url(),
                'user' => $uname,
            );


            $request_string = gd_prepare_request('message_check', $request_args);
            // Start checking for an update
            $raw_response = wp_remote_post($gd_api_url, $request_string);

            if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
                $response = unserialize($raw_response['body']);

            if (!empty($response)) {// Feed the message into a wp_option

                $gd_msg = get_option('geodir_messages');

                if (is_array($gd_msg)) {
                    //$result = array_merge($gd_msg,$response);
                    $result = $response + $gd_msg;
                } else {
                    $result = $response;
                }

                foreach ($result as $key => $res) {// check the notification is for the correct version if not remove it
                    if (empty($res['ver'])) $res['ver'] = 0;
                    if ($res['ver'] <= $gd_arr[$res['plugin']]['ver']) {

                    } else {
                        unset($result[$key]);
                    }
                }


                $result = array_unique($result);
                update_option('geodir_messages', $result);

            }

        }
        return $checked_data;
    }
}

if (!function_exists('geodir_show_message')) {
    /**
     * Adds messaged to the admin screen from the GeoDirectory server.
     *
     * @since 1.0.0
     *
     * @param string $message Message string.
     * @param string $msg_type Message type.
     * @param string $plugin Plugin name.
     * @param string $timestamp Timestamp.
     * @param string $js Extra js.
     * @param string $css Extra css.
     */
    function geodir_show_message($message, $msg_type = 'update-nag', $plugin, $timestamp, $js = '', $css = '')
    {
        /*
        $msg_type = error
        $msg_type = updated fade
        $msg_type = update-nag
        */


        echo '<div id="' . $timestamp . '" class="' . $msg_type . '">';
        echo '<span class="gd-remove-noti" onclick="gdRemoveNotification(\'' . $plugin . '\',\'' . $timestamp . '\');" ><i class="fa fa-times"></i></span>';
        echo "<img class='gd-icon-noti' src='" . plugin_dir_url('') . "geodirectory/geodirectory-assets/images/favicon.ico' > ";
        echo "$message";
        echo "</div>";

        ?>
        <script>
            function gdRemoveNotification($plugin, $timestamp) {

                jQuery('#' + $timestamp).css("background-color", "red");
                jQuery('#' + $timestamp).fadeOut("slow");
                // This does the ajax request
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'geodir_remove_notification',
                        'plugin': $plugin,
                        'timestamp': $timestamp
                    },
                    success: function (data) {
                        // This outputs the result of the ajax request
                        //alert(data);
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });

            }
            <?php echo $js;// extra js if needed?>
        </script>
        <style>
            .gd-icon-noti {
                float: left;
                margin-top: 10px;
                margin-right: 5px;
            }

            .update-nag .gd-icon-noti {
                margin-top: 2px;
            }

            .gd-remove-noti {
                float: right;
                margin-top: -20px;
                margin-right: -20px;
                color: #FF0000;
                cursor: pointer;
            }

            .updated .gd-remove-noti, .error .gd-remove-noti {
                float: right;
                margin-top: -10px;
                margin-right: -17px;
                color: #FF0000;
                cursor: pointer;
            }

            <?php echo $css;// extra styles if needed?>
        </style>
    <?php

    }
}

if (!function_exists('geodir_admin_messages')) {
    /**
     * Get the admin messaged from the options and calls the function to disaplay them.
     *
     * @since 1.0.0
     *
     * @global object $wpdb WordPress Database object.
     */
    function geodir_admin_messages()
    {
        global $wpdb;
        $gd_msg = get_option('geodir_messages');
        if (empty($gd_msg)) {
            return;
        }
        foreach ($gd_msg as $msg) {
            geodir_show_message($msg['msg'], $msg['type'], $msg['plugin'], $msg['timestamp'], $msg['js'], $msg['css']);
        }

    }
}
add_action('admin_notices', 'geodir_admin_messages');


if (!function_exists('geodir_remove_notification')) {
    /**
     * Remove GeoDirectory admin messages messages.
     *
     * @since 1.0.0
     *
     * @global object $wpdb WordPress Database object.
     */
    function geodir_remove_notification()
    {
        global $wpdb;
        // The $_REQUEST contains all the data sent via ajax
        if (isset($_POST)) {


            $gd_msg = get_option('geodir_messages');
            foreach ($gd_msg as $key => $msg) {
                if ($msg['plugin'] == $_POST['plugin'] && $msg['timestamp'] == $_POST['timestamp']) {
                    update_option($msg['plugin'] . '_last', current_time('timestamp', 1));
                    unset($gd_msg[$key]);
                }
            }
            update_option('geodir_messages', $gd_msg);

        }

        // Always die in functions echoing ajax content
        die();
    }
}

add_action('wp_ajax_geodir_remove_notification', 'geodir_remove_notification');



