<?php 

namespace LcFramework\Controllers;

class BsppLogger{
    
    private $logData = array();
    private $table = 'bspp_logs';
    
    /**
     * set log module name
     */
    public function setModuleName(String $module){
        $this->logData['module_name'] = $module;
    }
    
    /**
     * set log content
     */
    public function setLogContent(String $content){
        $this->logData['log_content'] = $content;
    }
    
    /**
     * creates data in from logs table
     */
    public function createLog(){
        global $wpdb;
        $table = $wpdb->prefix.$this->table;
        
        $this->logData['created'] = current_time('mysql');
        $wpdb->insert($table, $this->logData);
    }
    
    /**
     * returns data from logs table
     */
    public function logIndex(){
        global $wpdb;
        $table = $wpdb->prefix.$this->table;
        
        $query = "SELECT * FROM $table";
        return $wpdb->get_results($query);
    }
    
    /**
     * returns data from logs table
     */
    public function viewLog(){
        global $wpdb;
        $table = $wpdb->prefix.$this->table;
        
        $id = getArrayValue($_GET, 'log_id');
        $query = "SELECT * FROM $table WHERE id=$id";
        return $wpdb->get_row($query);
    }
}