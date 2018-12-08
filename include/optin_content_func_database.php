<?php

    /*
	 * This function loop at all pages. User can choose data protection
	 */
    function optin_select_pages_for_data_protection($content_id) {
        global $wpdb;

        $table_name  = $wpdb->prefix . 'posts';
        $getData = $wpdb->get_results( 'select * from ' . $table_name.' where post_type like "page" and post_status like "publish"');
        $getID = $wpdb->get_results( 'select * from '.$wpdb->prefix . 'optin_content_overlay where id = '.$content_id);

        $select = '<select name="page" size="5">';
        foreach ($getData as $data) {
            if($getID[0]->datenschutz == $data->ID){
                $select .= '<option value="'.$data->ID.'" selected>'.$data->post_title.'</option>';
            }
            else {
                $select .= '<option value="'.$data->ID.'">'.$data->post_title.'</option>';
            }
        }
        $select .= '</select>';

        return $select;
    }

    /*
     * This function set the table for iframes
     */
    function optin_create_table_for_content() {
        global $wpdb;

        //TODO: set newDatabase
        $table_name  = $wpdb->prefix . 'optin_content';
        $exist_table = $wpdb->get_results( "SHOW TABLES LIKE '$table_name'" );

        if ( ! $exist_table ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql             = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    contentbox_name varchar(255) NOT NULL,
                    contentbox_shortcode varchar(255) NOT NULL,
                    contentbox_code text NOT NULL,
                      UNIQUE KEY id (id)
                    ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }

    /*
     * This function set the table for the iframe overlay
     */
    function optin_create_table_for_overlay() {
        global $wpdb;

        //TODO: set newDatabase
        $table_name  = $wpdb->prefix . 'optin_content_overlay';
        $exist_table = $wpdb->get_results( "SHOW TABLES LIKE '$table_name'" );

        $fields = array(
            'id',
            'overlay_color',
            'button_text',
            'image_id',
            'image_size',
            'overlay_text',
            'display_text',
            'datenschutz_on_or_off',
            'datenschutz',
            'height',
            'width'
        );
        $dataType = array(
            'mediumint(9) NOT NULL',
            'varchar(255) NOT NULL',
            'varchar(255) NOT NULL',
            'int(11) NOT NULL',
            'varchar(255) NOT NULL',
            'text NOT NULL',
            'int(11) NOT NULL',
            'int(11) NOT NULL',
            'varchar(255) NOT NULL',
            'varchar(255) NOT NULL',
            'varchar(255) NOT NULL'
        );

        if ( ! $exist_table ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (";
            for ( $i = 0; $i < count($fields); $i++) {
                $sql .= $fields[$i] . " " . $dataType[$i] . ", ";
            }
            $sql .= "UNIQUE KEY id (id)
                      ) ". $charset_collate ." ;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
        else {
            $table_cols = $wpdb->get_col( "show columns from ". $table_name);

            for ( $i = 0; $i < count($fields); $i++) {
                if( $table_cols[$i] != $fields[$i]) {
                    $wpdb->query( "ALTER TABLE ".$table_name." ADD ".$fields[$i]." ".$dataType[$i]." ;");
                }
            }
        }
    }

    /*
     * This function save one iframe into the database
     */
    function option_update_table_for_content( $value ) {
        global $wpdb;
        $table_name  = $wpdb->prefix . 'optin_content';
        $wpdb->get_results( 'select * from ' . $table_name.' where id = ' . $value['contentbox_id'] );
        $count = $wpdb->num_rows;

        if ( $count != 0 ) {
            $wpdb->update( $table_name, array( 'contentbox_code' => stripslashes( $value['contentbox_code'] ), 'contentbox_name' => stripslashes( $value['contentbox_name'] ) ), array( 'id' => $value['contentbox_id'] ) );

        } else {
            $wpdb->insert( $table_name, array( 'contentbox_code' => stripslashes( $value['contentbox_code'] ), 'contentbox_name' => stripslashes( $value['contentbox_name'] ) ) );
        }
    }

    /*
     * This function save the iframe overlay into the database
     */
    function optin_update_table_for_overlay( $value ) {
        global $wpdb;
        $table_name  = $wpdb->prefix . 'optin_content_overlay';
        $wpdb->get_results( 'select * from ' . $table_name . ' where id = '. $value['contentbox_id'] );
        $count = $wpdb->num_rows;

        // TODO: größe wird nicht gespeichert
        if ( $count != 0 ) {
            $wpdb->update( $table_name, array( 'overlay_color' => stripslashes( $value['overlay_color'] ),
                'button_text' => stripslashes( $value['overlay_button_text'] ),
                'overlay_text' => stripslashes( $value['overlay_text'] ),
                'image_id' => stripslashes( $value['overlay_image'] ),
                'datenschutz' => stripslashes( $value['page'] ),
                'datenschutz_on_or_off' => stripslashes( $value['overlay_button_gdpr'] ),
                'image_size' => stripslashes( $value['image_size'] ),
                'height' => stripslashes( $value['height'] ),
                'width' => stripslashes( $value['width'] ),
                'display_text' => stripslashes( $value['display_text'] )), array( 'id' => $value['contentbox_id'] ));
        } else {
            $wpdb->insert( $table_name, array( 'overlay_color' => stripslashes( $value['overlay_color'] ),
                'id' => $value['contentbox_id'],
                'button_text' => stripslashes( $value['overlay_button_text'] ),
                'overlay_text' => stripslashes( $value['overlay_text'] ),
                'image_id' => stripslashes( $value['overlay_image'] ),
                'datenschutz' => stripslashes( $value['page'] ),
                'datenschutz_on_or_off' => stripslashes( $value['overlay_button_gdpr'] ),
                'image_size' => stripslashes( $value['image_size'] ),
                'height' => stripslashes( $value['height'] ),
                'width' => stripslashes( $value['width'] ),
                'display_text' => stripslashes( $value['display_text'] )));
        }
    }

    /*
    * This function delete one iframe from the database
    */
    function optin_delete_content_by_id( $id ){
        global $wpdb;
        $table_name  = $wpdb->prefix . 'optin_content';
        $wpdb->query( 'delete from ' . $table_name . ' where id = '.$id );
        $wpdb->query( 'delete from ' . $table_name . '_overlay where id = '.$id );
    }