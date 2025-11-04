<?php

namespace Src\Admin;

class AdminDisplayDebug
{

    /**
     * creates the transaction history table sorted accordingly to the url parameters
     * @param string $column
     * @param boolean $desc
     * @return void
     */
    public static function render_page($column, $desc) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'donations_for_paynow_debug';
        
        $order = $desc ? 'DESC' : 'ASC';

        $allowed_columns = ['id','transaction_id', 'internal_ref', 'status', 'created_at'];
        if (!in_array($column, $allowed_columns)) $column = 'id';

        $limit = (!empty($_GET['filter_limit'])) ? intval($_GET['filter_limit']) : 25;

        $whereArray = self::build_where_clause();

        $whereClause = $whereArray['where'];
        $values = $whereArray['values'];

        $query = $wpdb->prepare("SELECT * FROM $table_name $whereClause ORDER BY $column $order LIMIT %d", ...array_merge($values, [$limit]));

        $results = $wpdb->get_results($query, ARRAY_A);

        echo '<table class="widefat donationsForPaynowTable" cellspacing="0">';
        echo '<thead><tr>
            <th>ID' . self::create_sort_button('id') . '</th>
            <th>Status' . self::create_sort_button('status') . '</th>
            <th>Transaction ID' . self::create_sort_button('transaction_id') . '</th>
            <th>Internal Ref' . self::create_sort_button('internal_ref') . '</th>
            <th>Creation Date' . self::create_sort_button('created_at') . '</th>
            </tr></thead>';

        if ( $results ) {
            
            echo '<tbody>';
            foreach ( $results as $row ) {
                echo '<tr>';
                echo '<td>' . esc_html( $row['id'] ) . '</td>';
                echo '<td width="12px"' . '> <span class="status ' . esc_attr( $row['status'] ) . '">' . esc_html( $row['status'] ) . '</span></td>';
                echo '<td>' . esc_html( $row['transaction_id'] ) . '</td>';
                echo '<td>' . esc_html( $row['internal_ref'] ) . '</td>';
                echo '<td>' . esc_html( $row['created_at'] ) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No data found in the table.</p>';
        }

        echo '</div>';
    }

    /**
     * builts the SQL where clause based on the URL filter_ params
     * @return array{values: array, where: string}
     */
    private static function build_where_clause(){
        global $wpdb;
        $filters = [];
        $values  = [];

        if( !empty($_GET['filter_transaction_id'])){
            $filters[] = 'transaction_id LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_transaction_id']) . '%';
        }

        if( !empty($_GET['filter_internal_ref'])){
            $filters[] = 'internal_ref LIKE %s';
            $values[] = '%' . $wpdb->esc_like($_GET['filter_internal_ref']) . '%';
        }

        $allowed_statuses = ['NEW','PENDING','CONFIRMED','REJECTED','ERROR','EXPIRED','ABANDONED'];

        $filter_status = $_GET['filter_status'] ?? '';
        if (!in_array($filter_status, $allowed_statuses)) {
             $filter_status = null; // ignore invalid value
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
        $current_sortby = $_GET['sortby'] ?? 'id';
        $current_desc = $_GET['desc'] ?? '1';

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