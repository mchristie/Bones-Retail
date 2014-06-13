<?php

namespace Christie\Retail\Fieldtypes;

class OrderDetailsField extends \Christie\Bones\Fieldtypes\BonesField implements \Christie\Bones\Interfaces\FieldtypeInterface {

    public static $title = 'Order Details';

    protected $error = false;

    public $data_json_fields = array(
        'text_data' => array('items', 'payment_ref', 'total', 'discount')
    );

    public $data_json_attributes = array(
        'items'       => 'text_data',
        'payment_ref' => 'text_data',
        'total'       => 'text_data',
        'discount'    => 'text_data'
    );

    public $data_json_defaults = array(
        'items'       => array(),
        'payment_ref' => '',
        'total'       => 0,
        'discount'    => 0
    );

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
     *  This field isn't for editing
     */
    public function displaysEditForm() {
        return false;
    }

    public function editForm() {
        return '';
    }

    /*
     *  Fill the field from the input array
     *  Store the memory to re-populate the form, but DON'T save it
     */
    public function populate( Array $input ) {
        if (array_key_exists('items', $input))
            $this->field_data->items = $input[ 'items' ];

        if (array_key_exists('payment_ref', $input))
            $this->field_data->payment_ref = $input[ 'payment_ref' ];

        if (array_key_exists('total', $input))
            $this->field_data->total = $input[ 'total' ];

        if (array_key_exists('discount', $input))
            $this->field_data->discount = $input[ 'discount' ];
    }

    /*
     *  Perform validation on the input array, and return true/false for valid/not-valid
     */
    public function validates() {
        return true;
    }

}