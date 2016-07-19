<?php

class Equipment_Management_Item {
    
    /**
     * @var array Attributes of this item
     */
    public $attrs;
    
    public function __construct( $attrs ) {
        
        $this->set( $attrs );
    }
    
    public function set( $attrs ) {
        
        $this->attrs = array(
            'id'             => $attrs['id'],
            'post_id'        => $attrs['post_id'],
            'date_added'     => $attrs['date_added'],
            'name'           => $attrs['name'],
            'category'       => $attrs['category'],
            'category_tags'  => $attrs['category_tags'],
            'specification'  => $attrs['specification'],
            'application'    => $attrs['application'],
            'notes'          => $attrs['notes'],
            'price'          => $attrs['price'],
            'date_bought'    => $attrs['date_bought'],
            'bought_note'    => $attrs['bought_note'],
            'vendor'         => $attrs['vendor'],
            'vendor_item_id' => $attrs['vendor_item_id'],
            'bundle'         => $attrs['bundle'],
            'amount'         => $attrs['amount'],
            'use'            => $attrs['use'],
        );
    }
    
    public function sync() {
        
        global $equipment_management;
        global $wpdb;
        
        $table_name = $equipment_management->database->table_names['main_table'];
        $id = $this->attrs['equip_id'];
        if( null == $wpdb->get_row("SELECT * FROM $table_name WHERE 'id=$id'") ) { // New entry
            $wpdb->insert( $table_name,
                array(
                    'id'             => $this->attrs['id'],
                    'post_id'        => $this->attrs['post_id'],
                    'date_added'     => $this->attrs['date_added'],
                    'name'           => $this->attrs['name'],
                    'category'       => $this->attrs['category'],
                    'category_tags'  => $this->attrs['category_tags'],
                    'specification'  => $this->attrs['specification'],
                    'application'    => $this->attrs['application'],
                    'notes'          => $this->attrs['notes'],
                    'price'          => $this->attrs['price'],
                    'date_bought'    => $this->attrs['date_bought'],
                    'bought_note'    => $this->attrs['bought_note'],
                    'vendor'         => $this->attrs['vendor'],
                    'vendor_item_id' => $this->attrs['vendor_item_id'],
                    'bundle'         => $this->attrs['bundle'],
                    'amount'         => $this->attrs['amount'],
                ));
        } else {
            $wpdb->update( $table_name,
                array(
                    'post_id'        => $this->attrs['post_id'],
                    'date_added'     => $this->attrs['date_added'],
                    'name'           => $this->attrs['name'],
                    'category'       => $this->attrs['category'],
                    'category_tags'  => $this->attrs['category_tags'],
                    'specification'  => $this->attrs['specification'],
                    'application'    => $this->attrs['application'],
                    'notes'          => $this->attrs['notes'],
                    'price'          => $this->attrs['price'],
                    'date_bought'    => $this->attrs['date_bought'],
                    'bought_note'    => $this->attrs['bought_note'],
                    'vendor'         => $this->attrs['vendor'],
                    'vendor_item_id' => $this->attrs['vendor_item_id'],
                    'bundle'         => $this->attrs['bundle'],
                    'amount'         => $this->attrs['amount'],
                ), array(
                    'equip_id' => $this->attrs['equip_id'],
                ));
        }
        
        $this->attrs['use']->sync();
    }
    
    public static function create_item( $id ) {
        global $equipment_management;
        global $wpdb;
        
        $main_table_name = $equipment_management->database->table_names['main_table'];
        
        $row = $wpdb->get_row("SELECT * FROM $main_table_name WHERE id=$id", ARRAY_A);
        if($row == null) {
            return false;
        }
        
        $use_table_name = $equipment_management->database->table_names['use_table'];
        
        $use_rows = array();
        
        for( $i = 0; ; $i++ ) {
            $curr_row = $wpdb->get_row("SELECT * FROM $use_table_name WHERE equip_id=$id", ARRAY_A, $i);
            if( $curr_row == null ) {
                break;
            }
            array_push($use_rows, $curr_row);
        }
        
        $row['use'] = new Equipment_Management_Item_History($use_rows, $row['id']);
        return new Equipment_Management_Item($row);
    }
}