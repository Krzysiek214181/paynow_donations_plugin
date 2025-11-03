<?php

namespace Src\Base;

class DbService
{
    private $db;
    private $table;
    private $debug_table;
    private $table_suffix = "paynow_donations_transactions";
    private $debug_table_suffix = "paynow_donations_debug";
    

    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . $this->table_suffix;
        $this->debug_table = $this->db->prefix . $this->debug_table_suffix;
    }

    /**
     * creates required wp_paynow_donations_transactions table and wp_paynow_donations_debug table
     * @return void
     */
    public function register(){

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
    
        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE {$this->table} (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        internal_ref varchar(50) NOT NULL UNIQUE,
        transaction_id varchar(16) UNIQUE,
        amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(10) NOT NULL DEFAULT 'NEW',
        description VARCHAR(255) NOT NULL,
        user_email VARCHAR(255) NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        user_surname VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
        ) $charset_collate;"; 

        $debugSql = "CREATE TABLE {$this->debug_table} (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        transaction_id varchar(16),
        internal_ref varchar(50),
        status varchar(10),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)) $charset_collate;";

        if( !$this->table_exists($this->table)){
            dbDelta( $sql );
        }
        
        if( !$this->table_exists($this->debug_table)){
            dbDelta( $debugSql );
        }

        return;
    }

    /**
     * inserts the notification information into the debug table
     * @param array {
     *      internal_ref: string,
     *      transaction_id: string,
     *      status: string
     * }$data
     * @return boolean
     */
    public function debugNewNotification(array $data){
        $result = $this->db->insert(
            $this->debug_table,
            $data,
            [
                '%s',
                '%s',
                '%s'
            ]
        );
        return $result !== false;
    }

    /**
     * inserts the new payment into the wpdb, return false if failed
     *  @param array{
     *       internal_ref: string,
     *       amount: float,
     *       description: string,
     *       user_email: string,
     *       user_name: string,
     *       user_surname: string
     *   } $data
     *
     *   @return boolean
    */
    public function newPayment(array $data){

        $result = $this->db->insert(
            $this->table,
            [
                "internal_ref" => $data["internal_ref"],
                "amount" => $data["amount"],
                "description" => $data["description"],
                "user_email" => $data["user_email"],
                "user_name" => $data["user_name"],
                "user_surname" => $data["user_surname"],
            ],
            [
                '%s', // internal_ref
                '%f', // amount
                '%s', // description
                '%s', // user_email
                '%s', // user_name
                '%s', // user_surname
            ]
        );

        return $result !== false;
    }

    /**
     * adds the missing transaction_id to the transaction record
     * @param array{
     *      transaction_id: string,
     *      internal_ref: string
     * } $data
     * @return boolean
     */
    public function addPaymentTransactionId(array $data){
        $result = $this->db->update(
            $this->table,
            [
                'transaction_id' => $data['transaction_id']
            ],
            [
                'internal_ref' => $data['internal_ref']
            ],
            [
                '%s'
            ],
            [
                '%s'
            ]
            );

        return $result !== false;
    }

    /** 
     * updates payment status if the status is an allowed predefined status, also updates the paymentID, doesn't change the status if it's already CONFIRMED
     * @param array{
     *      transaction_id: string,
     *      internal_ref: string,
     *      new_status: string
     *   } $data
     * 
     * @return boolean
    */
    public function updatePayment(array $data){
        if( ! $this->verifyStatus( $data['new_status'])){
            return false;
        }

        //don't change the status if it's already confirmed ( fix for phantom notifications send by paynow after the payment is finished )
        if( $this->getCurrentStatus($data['internal_ref']) === 'CONFIRMED'){
            return false;
        }

        $result = $this->db->update(
            $this->table,
            [
                'status' => $data['new_status'],
                'transaction_id' => $data['transaction_id']
            ],
            [
                'internal_ref' => $data['internal_ref']
            ],
            [
                '%s',
                '%s'
            ],
            [
                '%s'
            ]
            );

        return $result !== false;
    }

     /**
     * Returns the status of the payment by internal_ref
     * @param string $arg
     * @return string
     */
    public function getCurrentStatus(string $internal_ref){
        $query = $this->db->prepare("SELECT status from {$this->table} WHERE interlan_ref = %s LIMIT 1". $internal_ref);
        $status = $this->db->get_var($query);
        return $status;
    }

    /**
     * Check the given status agaainst the list of predefined statuses
     * @param string $status
     * @return bool
     */
    private function verifyStatus(string $status){
        $allowed_status_array = [
            'NEW',
            'PENDING',
            'CONFIRMED',
            'REJECTED',
            'ERROR',
            'EXPIRED',
            'ABANDONED'
        ];

        return in_array( $status, $allowed_status_array);
    }
   
    /**
     * Check if the table exists
     * @param string $table_name
     * @return bool
     */
    private function table_exists(string $table_name) {
        $sql = $this->db->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        );
        return $this->db->get_var( $sql ) === $table_name;
    }
}