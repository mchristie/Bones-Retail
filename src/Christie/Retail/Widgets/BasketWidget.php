<?php

namespace Christie\Retail\Widgets;

class BasketWidget extends \Christie\Bones\Widgets\BonesWidget implements \Christie\Bones\Interfaces\WidgetInterface {

    public $title = 'Basket menu';

    public $fields = array();


    // Display the value of the widget, called by __toString
    public function render() {
        $html = '<table>';

        if ($items = \Basket::items()) {
            foreach ($items as $item)
                $html .= '<tr><th>'.$item->title.'</th><td>&pound;'.$item->price->total().' ('.$item->price->quantity.')</td></tr>';
        }

        $html .= '<tr><th>Total:</th><td>&pound;'.\Basket::total().'</td></tr></table>';

        return $html;
    }

    public function hasField($field) {
        return in_array($field, array('title')) ? true : false;
    }

    // BOOL Indicating if this field show in the admin area
    public function displaysSettingsForm() {
        return true;
    }

    // The field where admins can modify content
    public function settingsForm() {
        return \BonesForms::fields(array(
            /*array(
                'title'     => 'Channel',
                'name'      => 'channel_id',
                'type'      => 'select',
                'options'   => 'channels',
                'value'     => $this->channel_id
            )*/
        ));
    }

    /*
     *  Select the appropriate data from the POST input but DON'T save it
     *  The data should be stored in memory for displaying the form again if necessary
     */
    public function populate( Array $input ) {
        $this->_settings = array_merge($this->_settings, $input);
    }

    // Return BOOL indicating if the field data from populate is valid
    public function validates() {
        return true;
    }

    // Return BOOL indication if the field has errors to show from validation
    public function hasErrors() {
        return false;
    }

    // Return errors for the field
    public function showErrors() {
        return 'No errors';
    }

}