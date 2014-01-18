<?php
/*
    Database interaction class
*/

class WP_Plugin_Framework_DB
{
    const tableName = 'plugin_framework_table';

    //The singleton instance
    public static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new WP_Plugin_Framework_DB();
        return self::$instance;
    }

    public function setup_db()
    {
        //This is required for the dbDelta function
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        //Get the global handle to the WordPress database functionality
        global $wpdb;
        //Make sure to use $wpdb->prefix as user can change the prefix
        $table = $wpdb->prefix . self::tableName;

        $sql = "CREATE TABLE " . $table . " (
        tableID INT NOT NULL AUTO_INCREMENT,
        textField VARCHAR(500) NOT NULL,
        numField INT NOT NULL,
        created DATETIME NOT NULL,
        UNIQUE KEY tableID (tableID)
        );";

        //database write/update
        dbDelta($sql);
    }

    public function insert($data)
    {
        /*$data will be an array in the format:
            array('textField' => 'text field value', 'numField' => 123, 'created' => date('Y-m-d H:i:s'));
        */

        global $wpdb;
        $wpdb->insert($wpdb->prefix . self::tableName, $data);
        return $wpdb->insert_id; //useful if AUTO_INCREMENT ID is used on table
    }

    public function update($id, $data)
    {
        global $wpdb;
        $wpdb->update($wpdb->prefix . self::tableName, $data, array('tableID' => $id));
    }

    public function delete($id)
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . self::tableName, array('tableID' => $id));
    }

    public function get($id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . self::tableName . " WHERE tableID=%d", $id));
    }

    public function get_all()
    {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . self::tableName);
    }

    //Run a raw query on the DB.  Be careful to use safe SQL here
    public function query($sql)
    {
        //NOTE: query() returns integer value of rows affected.
        global $wpdb;
        return $wpdb->query($sql);
    }
}