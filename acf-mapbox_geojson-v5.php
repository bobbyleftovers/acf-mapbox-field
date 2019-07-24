<?php

class acf_field_mapbox_geojson extends acf_field {

    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type    function
    *  @date    5/03/2014
    *  @since   5.0.0
    *
    *  @param   n/a
    *  @return  n/a
    */
    function __construct() {
        // name (string) Single word, no spaces. Underscores allowed
        $this->name = 'mapbox_geojson';

        // label (string) Multiple words, can include spaces, visible when selecting a field type
        $this->label = __('Mapbox geoJSON', 'acf-mapbox_geojson');

        // category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
        $this->category = 'basic';

        // defaults (array) Array of default settings which are merged into the field object. These are used later in settings
        $this->defaults = array(
            'height'    => 400,
        );

        // l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
        // var message = acf._e('mapbox_geojson', 'error');
        $this->l10n = array(
            'error' => __('Error! Please enter a higher value', 'acf-mapbox_geojson'),
        );

        // do not delete!
        parent::__construct();
    }

    /*
    *  render_field_settings()
    *
    *  Create extra settings for your field. These are visible when editing a field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field (array) the $field being edited
    *  @return  n/a
    */
    function render_field_settings( $field ) {
        /*
        *  acf_render_field_setting
        *
        *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
        *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
        *
        *  More than one setting can be added by copy/paste the above code.
        *  Please note that you must also have a matching $defaults value for the field name (font_size)
        */
        acf_render_field_setting( $field, array(
            'label'         => __('API access token','acf-mapbox_geojson'),
            'instructions'  => __('You can find it in your account editor.','acf-mapbox_geojson'),
            'type'          => 'text',
            'name'          => 'mapbox_access_token',
        ));
        acf_render_field_setting( $field, array(
            'label'         => __('Map Style','acf-mapbox_geojson'),
            'instructions'  => __('Styles this map sould use. Set up one in the mapbox style editor or use one of their defaults','acf-mapbox_geojson'),
            'type'          => 'text',
            'name'          => 'mapbox_map_style',
        ));
        acf_render_field_setting( $field, array(
            'label'         => __('Height','acf-mapbox_geojson'),
            'instructions'  => __('Height of the map','acf-mapbox_geojson'),
            'type'          => 'number',
            'name'          => 'height',
            'prepend'       => 'px',
        ));
    }

    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param   $field (array) the $field being rendered
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field (array) the $field being edited
    *  @return  n/a
    */

    function render_field( $field ) {
        /*
        *  Review the data of $field.
        *  This will show what data is available
        */
        $states = [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District Of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];?>
        <div id="mapbox-geojson">
            <div class="mapbox-geojson__search-wrap">
                <div class="mapbox-geojson__input-wrap">
                    <div class="acf-label mapbox-geojson__label">Street</div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input class="mapbox-geojson__street mapbox-geojson__input" type="text" name="mapbox-geojson__street-<?= esc_attr($field['name']) ?>" value="" placeholder="ex: 80 South St"/>
                        </div>
                    </div>
                </div>
                <div class="mapbox-geojson__input-wrap">
                    <div class="acf-label mapbox-geojson__label">City</div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input class="mapbox-geojson__city mapbox-geojson__input" type="text" name="mapbox-geojson__city-<?= esc_attr($field['name']) ?>" value="" placeholder="ex: Arlington"/>
                        </div>
                    </div>
                </div>
                <div class="mapbox-geojson__input-wrap">
                    <div class="acf-label mapbox-geojson__label">State</div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <select class="mapbox-geojson__state mapbox-geojson__input" type="text" name="mapbox-geojson__state-<?= esc_attr($field['name']) ?>" value="">
                                <option value="" disabled selected>Choose your state</option><?php
                                foreach($states as $key => $value){?>
                                    <option value="<?= $key ?>"><?= $value ?></option><?php
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mapbox-geojson__input-wrap">
                    <div class="acf-label mapbox-geojson__label">Postal Code</div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input class="mapbox-geojson__zip mapbox-geojson__input" type="text" name="mapbox-geojson__zip-<?= esc_attr($field['name']) ?>" value="" placeholder="ex: 02554"/>
                        </div>
                    </div>
                </div>
                <div class="mapbox-geojson__input-wrap">
                    <div class="acf-label">Click to center map</div>
                    <button id="mapbox-geojson__find" class="mapbox-geojson__button button--find" data-access-token="<?= esc_attr($field['mapbox_access_token']) ?>">Search</button>
                    <button id="mapbox-geojson__clear" class="mapbox-geojson__button button--clear" data-access-token="<?= esc_attr($field['mapbox_access_token']) ?>">Clear</button>
                </div>
            </div>
            <input id="mapbox-geojson-value__<?= esc_attr($field['key']) ?>" class="mapbox-geojson-value" type="hidden" name="<?= esc_attr($field['name']) ?>" value='<?= $field['value'] ?>' />
            <div class="mapbox-geojson-map-container">
                <div id="mapbox-geojson-map" class="mapbox-geojson-map" data-access-token="<?= esc_attr($field['mapbox_access_token']) ?>" data-map-style="<?= esc_attr($field['mapbox_map_style']) ?>" style="height:<?= $field['height'] ?>px;"></div>
            </div>
        </div>
        <?php
    }

    /*
    *  input_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add CSS + JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_enqueue_scripts)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    function input_admin_enqueue_scripts() {

        $dir = plugin_dir_url( __FILE__ );
        // register & include JS
        wp_enqueue_script( 'acf-input-mapbox_geojson_axios', 'https://unpkg.com/axios/dist/axios.min.js', array(), null );
        wp_enqueue_script( 'acf-input-mapbox_geojson_mapbox_js', 'https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/0.53.1/mapbox-gl.js' );
        wp_enqueue_script( 'acf-input-mapbox_geojson', "{$dir}js/input.js", array('acf-input-mapbox_geojson_mapbox_js', 'acf-input-mapbox_geojson_axios'), '0.0.3', true );

        // register & include CSS
        wp_enqueue_style( 'acf-input-mapbox_geojson_mapbox_css', 'https://api.tiles.mapbox.com/mapbox-gl-js/v1.0.0/mapbox-gl.css');
        wp_enqueue_style( 'acf-input-mapbox_geojson', "{$dir}css/input.css", array(), '0.0.3' );
    }


    /*
    *  input_admin_head()
    *
    *  This action is called in the admin_head action on the edit screen where your field is created.
    *  Use this action to add CSS and JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_head)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    /*function input_admin_head() {}*/


    /*
    *  input_form_data()
    *
    *  This function is called once on the 'input' page between the head and footer
    *  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
    *  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
    *  seen on comments / user edit forms on the front end. This function will always be called, and includes
    *  $args that related to the current screen such as $args['post_id']
    *
    *  @type    function
    *  @date    6/03/2014
    *  @since   5.0.0
    *
    *  @param   $args (array)
    *  @return  n/a
    */
    /*function input_form_data( $args ) {}*/


    /*
    *  input_admin_footer()
    *
    *  This action is called in the admin_footer action on the edit screen where your field is created.
    *  Use this action to add CSS and JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_footer)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    /*function input_admin_footer() {}*/


    /*
    *  field_group_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
    *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
    *
    *  @type    action (admin_enqueue_scripts)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    /*function field_group_admin_enqueue_scripts() {}*/

    /*
    *  field_group_admin_head()
    *
    *  This action is called in the admin_head action on the edit screen where your field is edited.
    *  Use this action to add CSS and JavaScript to assist your render_field_options() action.
    *
    *  @type    action (admin_head)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */
    /*function field_group_admin_head() {}*/

    /*
    *  load_value()
    *
    *  This filter is applied to the $value after it is loaded from the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */
    function load_value( $value, $post_id, $field ) {
        return $value;
    }

    /*
    *  update_value()
    *
    *  This filter is applied to the $value before it is saved in the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */
    /*function update_value( $value, $post_id, $field ) {
        return $value;
    }*/


    /*
    *  format_value()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value which was loaded from the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *
    *  @return  $value (mixed) the modified value
    */
    function format_value( $value, $post_id, $field ) {

        // bail early if no value
        if( empty($value) ) {
            return $value;
        }

        // apply setting
        if( $field['font_size'] > 12 ) {

            // format the value
            // $value = 'something';

        }

        // return
        return $value;
    }

    /*
    *  validate_value()
    *
    *  This filter is used to perform validation on the value prior to saving.
    *  All values are validated regardless of the field's required setting. This allows you to validate and return
    *  messages to the user if the value is not correct
    *
    *  @type    filter
    *  @date    11/02/2014
    *  @since   5.0.0
    *
    *  @param   $valid (boolean) validation status based on the value and the field's required setting
    *  @param   $value (mixed) the $_POST value
    *  @param   $field (array) the field array holding all the field options
    *  @param   $input (string) the corresponding input name for $_POST value
    *  @return  $valid
    */
    /*function validate_value( $valid, $value, $field, $input ){
        // Basic usage
        if( $value < $field['custom_minimum_setting'] )
        {
            $valid = false;
        }

        // Advanced usage
        if( $value < $field['custom_minimum_setting'] )
        {
            $valid = __('The value is too little!','acf-mapbox_geojson'),
        }

        // return
        return $valid;
    }*/


    /*
    *  delete_value()
    *
    *  This action is fired after a value has been deleted from the db.
    *  Please note that saving a blank value is treated as an update, not a delete
    *
    *  @type    action
    *  @date    6/03/2014
    *  @since   5.0.0
    *
    *  @param   $post_id (mixed) the $post_id from which the value was deleted
    *  @param   $key (string) the $meta_key which the value was deleted
    *  @return  n/a
    */
    /*function delete_value( $post_id, $key ) {}*/


    /*
    *  load_field()
    *
    *  This filter is applied to the $field after it is loaded from the database
    *
    *  @type    filter
    *  @date    23/01/2013
    *  @since   3.6.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  $field
    */

    function load_field( $field ) {
        return $field;
    }


    /*
    *  update_field()
    *
    *  This filter is applied to the $field before it is saved to the database
    *
    *  @type    filter
    *  @date    23/01/2013
    *  @since   3.6.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  $field
    */
    /*function update_field( $field ) {
        return $field;
    }*/


    /*
    *  delete_field()
    *
    *  This action is fired after a field is deleted from the database
    *
    *  @type    action
    *  @date    11/02/2014
    *  @since   5.0.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  n/a
    */
    /*function delete_field( $field ) {}*/


}

// create field
new acf_field_mapbox_geojson();?>
