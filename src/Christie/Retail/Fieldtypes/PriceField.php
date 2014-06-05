<?php

namespace Christie\Retail\Fieldtypes;

class PriceField extends \Christie\Bones\Fieldtypes\BonesField implements \Christie\Bones\Interfaces\FieldtypeInterface {

    public static $title = 'Price';

    protected $error = false;

    public $json_fields = array(
        'text_data' => array('type', 'prices')
    );

    public $json_attributes = array(
        'type'   => 'text_data',
        'prices' => 'text_data'
    );

    public $json_defaults = array(
        'type'   => 'single',
        'prices' => array()
    );

    /*
     *  This is used to make showing the total easier
     *  The basket library will update this to the number of instances in the basket
     */
    public $quantity  = 1;
    public $variation = null;

    /*
     *  Show the field data, as we would on the front-end
     */
    public function render() {
        if ($this->field_data == null || $this->field_data->string_data == null) {
            return '0.00';
        } else {
            return number_format($this->field_data->string_data, 2);
        }
    }

    /*
     *  This is a price field, so return it as an int if needed, sadly there's no magic method for this
     */
    public function value() {
        if ($this->field_data == null || $this->field_data->string_data == null) {
            return 0;
        } else {
            return (float)$this->field_data->string_data;
        }
    }

    /*
     *  Multiple the value by the quantity
     */
    public function total() {
        return number_format( $this->value() * $this->quantity, 2 );
    }

    /*
     *  Get or set the quantity value
     */
    public function quantity($set = null) {
        if ($set)
            $this->quantity = $set;

        return $this->quantity;
    }

    /*
     *  Get or set the variation
     */
    public function variation($set = null) {
        if ($set)
            $this->variation = $set;

        return $this->variation;
    }

    /*
     *  Return a field, or anything else we need, for the entry form
     *  TODO: This should probably use views and show it's own label etc
     */
    public function editForm() {
        $bones = \App::make('bones');
        return $bones->view('retail::price_field_form', array(
            'field'      => $this,
            'field_data' => $this->field_data->string_data
        ));
    }

    /*
     *  Fill the field from the input array
     *  Store the memory to re-populate the form, but DON'T save it
     */
    public function populate( Array $input ) {
        if (array_key_exists($this->name.'_type', $input))
            $this->field_data->type = $input[ $this->name.'_type' ];

        if (array_key_exists($this->name.'_single', $input))
            $this->field_data->string_data = $input[ $this->name.'_single' ];

        if (array_key_exists($this->name.'_prices', $input)) {
            // Build an array of actual values
            $data = array();
            foreach ($input[$this->name.'_prices'] as $price) {
                if ($price['title'] && $price['price'])
                    $data[] = (object)$price;
            }

            // Save them
            $this->field_data->prices = $data;
        }
    }

    /*
     *  Perform validation on the input array, and return true/false for valid/not-valid
     */
    public function validates() {
        if ($this->type == 'single') {
            if (!is_numeric($this->field_data->string_data)) {
                $this->error = 'The price field must be numeric.';
                return false;
            }

        } else if ($this->type == 'variable') {
            foreach ($this->field_data->prices as $price) {
                if (!$price->title || !is_numeric($price->price)) {
                    $this->error = 'All variations must have a title and numeric price.';
                    return false;
                }
            }

        } else {
            $this->error = 'The type specified was not valid';
            return false;
        }

        return true;
    }

}