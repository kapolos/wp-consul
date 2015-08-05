<?php

/*
Plugin Name: Wp Consul
Description: Storing data in Consul's Key/Value store via WordPress
Version: 1.0
Author: John Kapolos
Author URI: http://kapolos.com
License: MIT
*/

defined('ABSPATH') or die('No direct access');

register_activation_hook(__FILE__, 'wpconsul_activation');
add_action('wpconsul_action', 'wpconsul_updateStore');
function wpconsul_activation()
{
    if (!wp_next_scheduled('wpconsul_updateStore')) {
        wp_schedule_event(time(), 'hourly', 'wpconsul_action');
    }
}

register_deactivation_hook(__FILE__, 'wpconsul_deactivation');
function wpconsul_deactivation()
{
    wp_clear_scheduled_hook('wpconsul_updateStore');
}

function wpconsul_updateStore()
{
    /**
     * Modify this closure for your use case
     *
     * @type callable $payload
     * @return string
     */
    $payload = function () {
        return 'Change Me';
    };

    $opts = get_option('wpconsul');
    if (!empty($opts["ip"]) && !empty($opts["port"]) && !empty($opts["key"])) {
        $url = 'http://' . $opts["ip"] . ':' . $opts["port"] . '/v1/kv/' . $opts["key"] . '?token=' . $opts["token"];

        $response = wp_remote_request($url, [
            'method' => 'PUT',
            'body'   => $payload()
        ]);
    }
}

function wpconsul_init()
{
    register_setting('wpconsul', 'wpconsul');
}

add_action('admin_menu', 'wpconsul_menu');
function wpconsul_menu()
{
    add_menu_page('WP Consul', 'WP Consul', 'manage_options', 'wpconsul', 'wpconsul_options');
    add_action('admin_init', 'wpconsul_init');
}

function wpconsul_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>

    <style>
        #wpconsul_form input[type=text] {
            width: 400px;
        }
    </style>
    <div class="wrap">
        <h2>WP Consul Settings</h2>

        <form id="wpconsul_form" name="wpconsul_form" method="post" action="options.php" class="form-table">
            <?php
            settings_fields('wpconsul');
            do_settings_sections('wpconsul');
            $wpconsul = array_merge(
                ['ip' => '', 'port' => 8500, 'key' => '', 'token' => ''],
                (array)get_option('wpconsul'));
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Consul Agent IP Address</th>
                    <td><input type="text" name="wpconsul[ip]"
                               value="<?php echo esc_attr($wpconsul["ip"]); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Consul Agent Port</th>
                    <td><input type="text" name="wpconsul[port]"
                               value="<?php echo esc_attr($wpconsul['port']); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Key</th>
                    <td><input type="text" name="wpconsul[key]" value="<?php echo esc_attr($wpconsul['key']); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">ACL Token</th>
                    <td><input type="text" name="wpconsul[token]" value="<?php echo esc_attr($wpconsul['token']); ?>"/>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <?php
}