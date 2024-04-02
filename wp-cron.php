<?php
/**
 * A pseudo-cron daemon for scheduling WordPress tasks.
 *
 * WP-Cron is triggered when the site receives a visit. In cases where a site may not receive
 * enough visits to execute scheduled tasks in a timely manner, this file can be called directly
 * or via a server cron daemon for X number of times.
 *
 * Defining DISABLE_WP_CRON as true and calling this file directly are mutually exclusive,
 * and the latter does not rely on the former to work.
 *
 * The HTTP request to this file will not slow down the visitor who happens to visit
 * when a scheduled cron event runs.
 *
 * @package WordPress
 */

// Ignore user aborts and allow the script to run indefinitely
ignore_user_abort(true);

// Prevent caching
if (!headers_sent()) {
    header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
}

// Attempt to raise the PHP memory limit for cron event processing
wp_raise_memory_limit('cron');

// Define DOING_CRON to indicate that cron is running
define('DOING_CRON', true);

// Load WordPress environment if not already loaded
if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/wp-load.php';
}

// Retrieve the cron lock
function _get_cron_lock() {
    global $wpdb;

    $value = 0;
    if (wp_using_ext_object_cache()) {
        $value = wp_cache_get('doing_cron', 'transient', true);
    } else {
        $row = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", '_transient_doing_cron'));
        if (is_object($row)) {
            $value = $row->option_value;
        }
    }

    return $value;
}

// Fetch all scheduled cron events
$crons = wp_get_ready_cron_jobs();
if (empty($crons)) {
    die();
}

// Get current GMT time
$gmt_time = microtime(true);

// Get the current cron lock
$doing_cron_transient = get_transient('doing_cron');

// Use the global $doing_wp_cron lock, or the GET lock if available
if (empty($doing_wp_cron)) {
    if (empty($_GET['doing_wp_cron'])) {
        if ($doing_cron_transient && ($doing_cron_transient + WP_CRON_LOCK_TIMEOUT > $gmt_time)) {
            return;
        }
        $doing_wp_cron = sprintf('%.22F', microtime(true));
        $doing_cron_transient = $doing_wp_cron;
        set_transient('doing_cron', $doing_wp_cron);
    } else {
        $doing_wp_cron = $_GET['doing_wp_cron'];
    }
}

// Check if the cron lock matches the current key
if ($doing_cron_transient !== $doing_wp_cron) {
    return;
}

// Iterate through each cron event and fire scheduled events
foreach ($crons as $timestamp => $cronhooks) {
    if ($timestamp > $gmt_time) {
        break;
    }

    foreach ($cronhooks as $hook => $keys) {
        foreach ($keys as $k => $v) {
            $schedule = $v['schedule'];

            // Reschedule event if required
            if ($schedule) {
                $result = wp_reschedule_event($timestamp, $schedule, $hook, $v['args'], true);

                if (is_wp_error($result)) {
                    error_log(sprintf(__('Cron reschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s'), $hook, $result->get_error_code(), $result->get_error_message(), wp_json_encode($v)));
                    do_action('cron_reschedule_event_error', $result, $hook, $v);
                }
            }

            // Unschedule event
            $result = wp_unschedule_event($timestamp, $hook, $v['args'], true);

            if (is_wp_error($result)) {
                error_log(sprintf(__('Cron unschedule event error for hook: %1$s, Error code: %2$s, Error message: %3$s, Data: %4$s'), $hook, $result->get_error_code(), $result->get_error_message(), wp_json_encode($v)));
                do_action('cron_unschedule_event_error', $result, $hook, $v);
            }

            // Fire the scheduled event
            do_action_ref_array($hook, $v['args']);

            // Check if another cron process stole the lock
            if (_get_cron_lock() !== $doing_wp_cron) {
                return;
            }
        }
    }
}

// If the current lock matches, delete the cron lock
if (_get_cron_lock() === $doing_wp_cron) {
    delete_transient('doing_cron');
}

// Terminate script execution
die();
