<?php
    /*
	 * This is the backend page for choosing the overlay
	 */
    require_once( $optin_content_pfad . 'include/optin_content_func_database.php' );

    function optin_overlay_backend_menu() {
        if(isset($_POST['overlay_save'])){
            optin_create_table_for_overlay();
            optin_update_table_for_overlay( $_POST );
        }

        global $wpdb;
        $get_contentbox_data = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content' );

        wp_enqueue_media();
    ?>
    <div class="backend_optin_iframe">
        <h1>Overlays</h1>
        <?php foreach ( $get_contentbox_data as $contentbox ) {
            $plus = ' active';
            $minus = '';
            $inner = '';
            if($_POST['contentbox_id'] == $contentbox->id){
                $minus = ' active';
                $inner = ' active';
                $plus = '';
            }
            $get_contentbox_overlay = $wpdb->get_results( 'select * from ' . $wpdb->prefix . 'optin_content_overlay where id = '.$contentbox->id );
            ?>
            <form method="post">
                <div class="backend_optin_iframe__container iframe<?php echo $contentbox->id;?>">
                    <input type="hidden" name="contentbox_id" value="<?php echo $contentbox->id; ?>"/>
                    <div class="header">
                        <h2>Overlay from "<?php echo $contentbox->contentbox_name;?>"</h2>
                        <div class="icon minus<?php echo $minus;?>">-</div>
                        <div class="icon plus<?php echo $plus;?>">+</div>
                    </div>
                    <div class="backend_optin_iframe__inner<?php echo $inner;?>">
                        <table>
                            <tr>
                                <th>
                                    <label>Button Text:</label>
                                </th>
                                <td>
                                    <input type="text" name="overlay_button_text" value="<?php echo $get_contentbox_overlay[0]->button_text;?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>Button GDPR:</label>
                                </th>
                                <td>
                                    <div class="label_buttons">
                                        <?php
                                        if($get_contentbox_overlay[0]->datenschutz_on_or_off == 1){
                                            $display_select_pages = ' active';
                                            $labels = '<label class="btn active">Yes</label><label class="btn wert0">No</label>';
                                        }
                                        else {
                                            $display_select_pages = '';
                                            $labels = '<label class="btn">Yes</label><label class="btn wert0 active">No</label>';
                                        }

                                        echo $labels;
                                        ?>
                                    </div>
                                    <input type="hidden" name="overlay_button_gdpr" value="<?php echo $get_contentbox_overlay[0]->datenschutz_on_or_off;?>"/>
                                    <div class="select_pages<?php echo $display_select_pages;?>">
                                        <?php echo optin_select_pages_for_data_protection($get_contentbox_data->id);?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>Background Color:</label>
                                </th>
                                <td>
                                    <input class="mm-color-picker" type="text" name="overlay_color" value="<?php echo $get_contentbox_overlay[0]->overlay_color;?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>Overlay Size (in px)</label>
                                </th>
                                <td>
                                    <input style="width:50px;"  name="width" type="text" value="<?php echo $get_contentbox_overlay[0]->width;?>"/> x <input style="width:50px;" name="height" type="text" value="<?php echo $get_contentbox_overlay[0]->height;?>"/>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>Image URL:</label>
                                </th>
                                <td>
                                    <div class="image_for_overlay">
                                        <?php
                                            $label_buttons = '';
                                            $display_none = '';
                                            if( $get_contentbox_overlay[0]->image_id == 0 | $get_contentbox_overlay[0]->image_id == '' ){
                                                $display_none = ' style="display: none;"';
                                            }
                                        ?>
                                        <input type="hidden" name="overlay_image" value="<?php echo $get_contentbox_overlay[0]->image_id;?>"/>
                                        <?php echo get_image_for_overlay( $get_contentbox_overlay[0]->image_id, 'thumbnail');?>
                                        <br />
                                        <button class="upload_image_button">Upload</button>
                                        <button class="remove_image_button"<?php echo $display_none;?>>Remove</button>
                                    </div>
                                    <div class="image_size" <?php echo $display_none;?>>
                                        <div class="label_buttons">
                                            <?php
                                            $sizes = array('thumbnail', 'medium', 'large', 'full');
                                            foreach ( $sizes as $size ) {
                                                if( $size == $get_contentbox_overlay[0]->image_size){
                                                    $label_buttons .= '<label class="btn active">'.$size.'</label>';
                                                }
                                                else {
                                                    $label_buttons .= '<label class="btn">'.$size.'</label>';
                                                }
                                            }

                                            echo $label_buttons;
                                            ?>
                                        </div>
                                        <input type="hidden" name="image_size" value="<?php echo $get_contentbox_overlay[0]->image_size;?>"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>Overlay Text:</label>
                                </th>
                                <td>
                                    <textarea name="overlay_text"><?php echo $get_contentbox_overlay[0]->overlay_text;?></textarea>
                                    <br />
                                    <br />
                                    <br />
                                    <div class="label_buttons">
                                        <?php
                                        if($get_contentbox_overlay[0]->display_text == 1){
                                            $labels = '<label class="btn active">Yes</label><label class="btn wert0">No</label>';
                                        }
                                        else {
                                            $labels = '<label class="btn">Yes</label><label class="btn wert0 active">No</label>';
                                        }

                                        echo $labels;
                                        ?>
                                    </div>
                                    <input type="hidden" name="display_text" value="<?php echo $get_contentbox_overlay[0]->display_text;?>" />
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="overlay_save" value="Save"/>
                    </div>
                </div>
            </form>
            <?php
            }
            ?>
        </div>
    <?php
    }

    /*
	 * This is the backend page for all iframes
	 */
    function optin_content_backend_menu() {
        global $wpdb;

        if ( $_POST['save'] ) {
            optin_create_table_for_content();
            option_update_table_for_content( $_POST );
        }

        if ( $_POST['delete'] ) {
            optin_delete_content_by_id(  $_POST['contentbox_id'] );
        }

        $i         = 1;
        $table_name  = $wpdb->prefix . 'optin_content';
        $get_contentboxes = $wpdb->get_results( 'select * from ' . $table_name );


        ?>
        <div class="backend_optin_iframe">
            <h1>Content Boxes</h1>

            <?php foreach ( $get_contentboxes as $contentbox ) {
                $shortcode = strtolower ( $contentbox->contentbox_name );
                $shortcode = preg_replace("/[^0-9a-zA-Z \-\_]/", "", $shortcode);
                $shortcode = str_replace( ' ', '-', $shortcode);
                $shortcode = str_replace( '---', '-', $shortcode);

                $plus = ' active';
                $minus = '';
                $inner = '';
                if($_POST['contentbox_id'] == $contentbox->id){
                    $minus = ' active';
                    $inner = ' active';
                    $plus = '';
                }
                ?>
                <div class="backend_optin_iframe__container">
                    <form method="post">
                        <div class="header">
                            <h2><?php echo $contentbox->contentbox_name; ?> </h2>
                            <div class="icon minus<?php echo $minus;?>">-</div>
                            <div class="icon plus<?php echo $plus;?>">+</div>
                        </div>
                        <div class="backend_optin_iframe__inner iframe<?php echo $contentbox->id.$inner; ?>">
                            <input type="hidden" name="contentbox_id" value="<?php echo $contentbox->id; ?>"/>
                            <table>
                                <tr>
                                    <th>
                                        <label>Shortcode:</label>
                                    </th>
                                    <td>
                                        <code class="shortcode">[optin_content name="<?php echo $shortcode; ?>" id="<?php echo $contentbox->id; ?>"]</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label>Name:</label>
                                    </th>
                                    <td>
                                        <input type="text" name="contentbox_name" value="<?php echo $contentbox->contentbox_name; ?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label>Content Code:</label>
                                    </th>
                                    <td>
                                        <textarea name="contentbox_code"><?php echo $contentbox->contentbox_code; ?></textarea>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" name="save" value="Save"/>
                            <input type="submit" name="delete" value="Delete"/>
                        </div>
                    </form>
                </div>

                <?php
                $i ++;
            }
            ?>
            <button class="new_row">New</button>
            <br/>
            <br/>
        </div>
        <?php
    }