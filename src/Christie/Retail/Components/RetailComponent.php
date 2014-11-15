<?php

namespace Christie\Retail\Components;

use Christie\Bones\Models\Field as Field;
use Christie\Bones\Models\FieldData as FieldData;
use Christie\Bones\Models\Entry as Entry;

class RetailComponent extends \Christie\Bones\Components\BonesComponent {

    public static $title = 'Retail';

    public $name = 'retail';

    public function configure() {
        // Nothing to see here
    }

    public static function hasSettings() {
        return true;
    }

    public function findOrdersForProduct($product_id) {
        // Order detail fields
        $order_detail_fields = Field::where('field_type', 'order_details')->get();
        $order_detail_field_ids = array();
        foreach ($order_detail_fields as $f)
            $order_detail_field_ids[] = $f->id;

        $feild_datas = FieldData::whereIn('field_id', $order_detail_field_ids)->get();

        $entry_ids = array();
        foreach ($feild_datas as $fd)
            $entry_ids[] = $fd->entry_id;


        $orders = Entry::whereIn('id', $entry_ids)->get();

        $return_orders = array();
        foreach ($orders as $order) {
            foreach ($order->order_details->items as $item) {
                if ($item->entry_id == $product_id) {
                    $return_orders[] = $order;
                }
            }
        }

        return $return_orders;
    }

}