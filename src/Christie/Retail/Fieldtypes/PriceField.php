<?php

namespace Christie\Retail\Fieldtypes;

class PriceField extends \Christie\Bones\Fieldtypes\BonesField implements \Christie\Bones\Interfaces\FieldtypeInterface {

    public static $title = 'Price';

    protected $error = false;

    public $data_json_fields = array(
        'text_data' => array('type', 'prices', 'available')
    );

    public $data_json_attributes = array(
        'type'       => 'text_data',
        'variations' => 'text_data',
        'available'  => 'text_data'
    );

    public $data_json_defaults = array(
        'type'       => 'single',
        'variations' => array(),
        'available'  => null
    );

    /*
     *  This is used to make showing the total easier
     *  The basket library will update this to the number of instances in the basket
     */
    public $quantity  = 1;
    public $variation = null;

    public function __get($field) {
        if (in_array($field, array('variation_title', 'variation_price')) && $variation = $this->getVariation()) {

            if ($field == 'variation_title') {
                return $variation->title;
            } else if ($field == 'variation_price') {
                return number_format($variation->price, 2);
            }

        }

        return parent::__get($field);
    }

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
     *  Return the current variation
     */
    public function getVariation() {
        if (!$this->variation) return null;

        foreach ($this->variations as $variation) {
            if ($variation->sku == $this->variation)
                return $variation;
        }

        return null;
    }

    /*
     *  Return true/false indicating if the product has the quantity available
     */
    public function hasAvailable($quantity, $variation_sku = null) {
        // Check the variation
        if ($variation_sku) {
            foreach ($this->variations as $variation) {
                // If the variation has an available value, compare it
                if ($variation->sku == $variation_sku && is_numeric($variation->available))
                    return ($variation->available >= $quantity);
            }
        }

        // If the product has an available value, compare it
        if (is_numeric($this->available))
            return $this->available >= $quantity;

        // If we've got this far, it's unlimited
        return true;
    }

    /*
     *  Reduce the availability of the product
     */
    public function commit() {
        // Check the variation
        if ($this->variation) {
            $variations = $this->variations;
            foreach ($variations as &$variation) {
                // If the variation has an available value, reduce it
                if ($variation->sku == $this->variation && is_numeric($variation->available)) {
                    $variation->available = (int)$variation->available - $this->quantity;

                    $this->variations = $variations;
                    $this->save();
                    return;
                }
            }
        }

        // If the product has an available value, reduce it
        if (is_numeric($this->available)) {
            $this->field_data->available = (int)$this->field_data->available - $this->quantity;
            $this->field_data->save();
            return;
        }
    }

    /*
     *  This is a price field, so return it as an int if needed, sadly there's no magic method for this
     */
    public function value() {
        // If this isn't a variation, just return the string field
        if (!$this->variation) {
            if ($this->field_data == null || $this->field_data->string_data == null) {
                return 0;
            } else {
                return (float)$this->field_data->string_data;
            }
        // If IS a variation, so find the right one
        } else {
            foreach ($this->variations as $variation) {
                // Match SKU
                if ($variation->sku == $this->variation) {
                    return (float)$variation->price;
                }
            }

            // We didn't find the right one, so return null
            return null;
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

        if (array_key_exists($this->name.'_available', $input))
            $this->field_data->available = $input[ $this->name.'_available' ];

        if (array_key_exists($this->name.'_variations', $input)) {
            // Build an array of actual values
            $data = array();
            foreach ($input[$this->name.'_variations'] as $variation) {
                if ($variation['sku'] && $variation['title'] && $variation['price']) {
                    $data[] = (object)$variation;
                    // Convert blank available to null
                    if ($data[count($data)-1]->available == '') $data[count($data)-1]->available = null;
                }
            }

            // Save them
            $this->field_data->variations = $data;
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
            foreach ($this->field_data->variations as $variation) {
                if (
                        !$variation->sku ||
                        !$variation->title ||
                        !is_numeric($variation->price) ||
                        !(is_numeric($variation->available) || is_null($variation->available))) {

                    $this->error = 'All variations must have an sku, title and numeric price.';
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