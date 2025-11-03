<?php

namespace Src\Admin;

class AdminDisplayHistory
{

    /**
     * creates the transaction history table sorted accordingly to the url parameters
     * @param string $column
     * @param boolean $desc
     * @return void
     */
    public static function render_page($column, $desc) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'paynow_donations_transactions';
        
        $order = $desc ? 'DESC' : 'ASC';

        $allowed_columns = ['id','status','amount','description','user_email','user_name','user_surname','transaction_id','created_at','updated_at'];
        if (!in_array($column, $allowed_columns)) $column = 'id';

        $limit = (!empty($_GET['filter_limit'])) ? intval($_GET['filter_limit']) : 25;

        $whereArray = self::build_where_clause();

        $whereClause = $whereArray['where'];
        $values = $whereArray['values'];

        $query = $wpdb->prepare("SELECT * FROM $table_name $whereClause ORDER BY $column $order LIMIT %d", ...array_merge($values, [$limit]));

        $results = $wpdb->get_results($query, ARRAY_A);

        echo '<table class="widefat" cellspacing="0">';
        echo '<thead><tr>
            <th>ID' . self::create_sort_button('id') . '</th>
            <th>Status' . self::create_sort_button('status') . '</th>
            <th>Amount' . self::create_sort_button('amount') . '</th>
            <th>Description' . self::create_sort_button('description') . '</th>
            <th>Email' . self::create_sort_button('user_email') . '</th>
            <th>Name' . self::create_sort_button('user_name') . '</th>
            <th>Surname' . self::create_sort_button('user_surname') . '</th>
            <th>Transaction ID' . self::create_sort_button('transaction_id') . '</th>
            <th>Creation Date' . self::create_sort_button('created_at') . '</th>
            <th>Update Date' . self::create_sort_button('updated_at') . '</th>
            </tr></thead>';

        if ( $results ) {
            
            echo '<tbody>';
            foreach ( $results as $row ) {
                echo '<tr>';
                echo '<td>' . esc_html( $row['id'] ) . '</td>';
                echo '<td width="12px"' . '> <span class="status ' . esc_attr( $row['status'] ) . '">' . esc_html( $row['status'] ) . '</span></td>';
                echo '<td>' . esc_html( $row['amount'] ) . '</td>';
                echo '<td>' . esc_html( $row['description'] ) . '</td>';
                echo '<td>' . esc_html( $row['user_email'] ) . '</td>';
                echo '<td>' . esc_html( $row['user_name'] ) . '</td>';
                echo '<td>' . esc_html( $row['user_surname'] ) . '</td>';
                echo '<td>' . esc_html( $row['transaction_id'] ) . '</td>';
                echo '<td>' . esc_html( $row['created_at'] ) . '</td>';
                echo '<td>' . esc_html( $row['updated_at'] ) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No data found in the table.</p>';
        }

        echo '</div>';
    }

    /**
     * @return array{values: array, where: string}
     */
    private static function build_where_clause(){
        global $wpdb;
        $filters = [];
        $values  = [];

        if( !empty($_GET['filter_name'])){
            $filters[] = 'user_name LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_name']) . '%';
        }

        if( !empty($_GET['filter_surname'])){
            $filters[] = 'user_surname LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_surname']) . '%';
        }

        if( !empty($_GET['filter_email'])){
            $filters[] = 'user_email LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_email']) . '%';
        }

        if( !empty($_GET['filter_transaction_id'])){
            $filters[] = 'transaction_id LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_transaction_id']) . '%';
        }

        if( !empty($_GET['filter_description'])){
            $filters[] = 'description LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_description']) . '%';
        }

        if( !empty($_GET['filter_amount_min'])){
            $filters[] = 'amount >= %f';
            $values[] = floatval($_GET['filter_amount_min']);
        };
        
        if( !empty($_GET['filter_amount_max'])){
            $filters[] = 'amount <= %f';
            $values[] = floatval($_GET['filter_amount_max']);
        };

        $allowed_statuses = ['NEW','PENDING','CONFIRMED','REJECTED','ERROR','EXPIRED','ABANDONED'];

        $filter_status = $_GET['filter_status'] ?? '';
        if (!in_array($filter_status, $allowed_statuses)) {
             $filter_status = ''; // ignore invalid value
        }

        if (!empty($filter_status)) {
            $filters[] = 'status = %s';
            $values[] = $filter_status;
        }

        $where = '';

        if (!empty($filters)) {
            $where = 'WHERE ' . implode(' AND ', $filters);
        }

        return [
            'where' => $where,
            'values' => $values
        ];
    }

    private static function create_sort_button($column_id){
        $current_sortby = isset($_GET['sortby']) ? $_GET['sortby'] : 'id';
        $current_desc = isset( $_GET['desc']) ? $_GET['desc'] : '1';

        $desc = ($current_sortby === $column_id && $current_desc === '0') ? 1 : 0;

        $chosen_class = ($current_sortby === $column_id) ? ' currentSortButton' : '';

        $url = add_query_arg([
            'sortby' => $column_id,
            'desc' => $desc,
            ]
        );

        return '<a class="sortButton' . esc_attr($chosen_class) . '" href="' . esc_url($url) . '">' . ($desc ? '▲':'▼') . '</a>';
    }
}