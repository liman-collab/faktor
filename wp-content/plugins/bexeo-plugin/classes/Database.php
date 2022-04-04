<?php
/**
 * Custom DataBase class (Bexeo GmbH)
 */
global $wpdb;

class Database {
    private static $instance;
    private static $prefix;
    private static $wpdb;

    static function getInstance(){
        global $wpdb;
        self::$wpdb = $wpdb;
        self::$prefix = $wpdb->prefix;

        if(!self::$instance){
            self::$instance = new Database;
        }
        return self::$instance;
    }

    function lastId(){
        return self::$wpdb->insert_id;
    }

    public function save(string $table, array $data)
    {
        return self::$wpdb->insert(self::$prefix . $table, $data);
    }

    public function update($table, $data, $condition)
    {
        return self::$wpdb->update(self::$prefix . $table, $data, $condition);
    }

    public function delete(string $table, array $condition, $redir = true, $redirPage = false)
    {
        return self::$wpdb->delete(self::$prefix . $table, $condition);
    }

    public function getById(string $table, int $id)
    {
        return self::$wpdb->get_row("select * from " . self::$prefix . $table . " where id = " . $id);
    }

    public function get(string $table, string $condition = null)
    {
        $sql = "select * from " . self::$prefix . $table;
        if ($condition) {
            $sql .= " where " . $condition;
        }
        return self::$wpdb->get_results($sql);
    }
}