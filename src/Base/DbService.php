<?php

namespace Kszkl\Donations\Base;

class DbService
{
    private $db;
    private $table;
    private $debug_table;
    private $table_suffix = "donations_for_paynow";
    private $debug_table_suffix = "donations_for_paynow_debug";
    
    public function __construct(){
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->db->prefix . $this->table_suffix;
        $this->debug_table = $this->db->prefix . $this->debug_table_suffix;
    }

    /**
     * creates required wp_donations_for_paynow table and wp_donations_for_paynow_debug table
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
        t_id_closed BOOLEAN DEFAULT 0,
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
     * @return bool
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
     *   @return bool
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
     * @return bool
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
     * @return bool
    */
    public function updatePayment(array $data){

        // check whether new status is an allowed status
        if( !$this->verifyStatus( $data['new_status'])){
            return false;
        }

        //get current transaction_id and t_id_closedd
        $transaction_info = $this->getTransactionInfo($data['internal_ref']);

        //if the payment is already closed and the transaction_id hasn't changed, ignore the notification
        if( $transaction_info->transaction_id === $data['transaction_id'] && $transaction_info->t_id_closed ){
            return false;
        }
        
        //if transaction id has changed, update the transaction_id and set t_id_closed to 0
        if( $transaction_info->transaction_id !== $data['transaction_id']){
            $this->open_transaction_id( $data['internal_ref'], $data['transaction_id']);
        }

        //if new status is a ending status set the t_id_closed to 1
        if($this->isEndingStatus($data['new_status'])){
            $this->close_transaction_id($data['internal_ref']);
        }

        $result = $this->db->update(
            $this->table,
            [
                'status' => $data['new_status'],
            ],
            [
                'internal_ref' => $data['internal_ref']
            ],
            [
                '%s',
            ],
            [
                '%s'
            ]
            );

        return $result !== false;
    }

    /**
     * return the transaction_id and t_id_closed of the transaction
     * @param string $internal_ref
     * 
     */
    public function getTransactionInfo(string $internal_ref){
        $query = $this->db->prepare("SELECT transaction_id, t_id_closed from {$this->table} WHERE internal_ref = %s LIMIT 1", $internal_ref);
        $transaction_info = $this->db->get_row($query);
        return $transaction_info;
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
     * Checks whether the status is one of the transaction ending statuses
     * @param string $status
     * @return bool
     */
    private function isEndingStatus(string $status){
        $ending_status_array = [
            'CONFIRMED',
            'REJECTED',
            'ERROR',
            'EXPIRED',
            'ABANDONED'
        ];

        return in_array($status, $ending_status_array);
    }

    /**
     * sets t_id_closed to 0 for the provided internal_ref, returns true/false on success/failure
     * @param string $internal_ref
     * @return bool
     */
    private function close_transaction_id(string $internal_ref){
        $result = $this->db->update(
            $this->table,
            [
                't_id_closed' => 1
            ],
            [
                'internal_ref' => $internal_ref
            ],
            [
                '%d'
            ],
            [
                '%s'
            ]
        );

        return $result !== false;
    }

    /**
     * sets t_id_closed to 0 and updates the transaction_id for provided internal_ref, returns true/false on success/failure
     * @param string $internal_ref
     * @param string $new_transaction_id
     * @return bool
     */
    private function open_transaction_id(string $internal_ref, string $new_transaction_id){
        $result = $this->db->update(
            $this->table,
            [
                't_id_closed' => 0,
                'transaction_id' => $new_transaction_id,
            ],
            [
                'internal_ref' => $internal_ref
            ],
            [
                '%d',
                '%s'
            ],
            [
                '%s'
            ]
        );

        return $result !== false;
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