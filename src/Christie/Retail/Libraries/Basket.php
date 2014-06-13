<?php

namespace Christie\Retail\Libraries;

use \App;
use \File;
use \Mail;
use \mPDF;
use \Session;

class Basket {

    // Track which entries are in the basket
    protected $basket = array();

    // Keep a cache of the basket entries
    protected $entries = null;

    /*
     *  Basket structure is as follows
    $basket = array(
        array(
            :entry_id   => x,
            :quantity   => y,
            :variation  => z (optional)
        )
    );
     */

    public function __construct() {

        // Pull the basket out of the session, if there is one
        if (Session::has('basket'))
            $this->basket = Session::get('basket');
    }

    /*
     *  Remove all items from the basket
     */
    public function clear() {
        $this->basket = array();
        $this->entries = null;
        $this->saveBasketToSession();
    }

    /*
     *  Return the total of the basket, but don't format it
     */
    public function total() {
        $total = 0;

        // No entries means no total...
        if (!$entries = $this->items()) return $total;

        foreach ($entries as $i)
            // Add the entry price multiplied by the quantity
            $total += $i->price->total();

        return number_format($total, 2);
    }

    /*
     *  Return all the entries in the basket
     */
    public function items() {
        // Have we a cache of the entries?
        if ($this->entries) return $this->entries;

        // No entries means no total...
        if (count($this->basket) == 0) return null;

        // Fetch them, and keep a cache
        $this->entries = array();
        $entries = \Entry::whereIn( 'id', $this->basketEntryIds() )->get();

        // Loop through the basket items
        foreach ($this->basket as $item) {
            // And match it to the entry
            foreach ($entries as $entry) {
                if ($entry->id == $item['entry_id']) {
                    // Update the field with the item details
                    $entry->price->quantity( $item['quantity'] );
                    $entry->price->variation( $item['variation'] );

                    $this->entries[] = $entry;
                }
            }
        }

        return $this->entries;
    }

    /*
     *  Return the total number of items
     */
    public function totalItems() {
        $total = 0;
        foreach ($this->basket as $basket) {
            $total += $basket['quantity'];
        }

        return $total;
    }

    /*
     *  Add an entry to the basket
     */
    public function add($entry, $quantity = 1, $variation = null) {

        // Check that the entry has enough available
        if (!$entry->price->hasAvailable($quantity, $variation))
            return false;

        // Wipe the cache of entires, in case there is one
        $this->entries = null;

        // Update an existing basket entry quantity
        $found = false;
        foreach ($this->basket as $i => $item) {
            // We need an exact entry_id and variation match
            if ($item['entry_id'] == $entry->id && $item['variation'] == $variation) {
                // Update the quantity
                $this->basket[$i]['quantity'] += $quantity;
                $found = true;
            }
        }

        // If we didn't find one, add one
        if (!$found) {
            $this->basket[] = array(
                'entry_id'  => $entry->id,
                'quantity'  => $quantity,
                'variation' => $variation
            );
        }

        // Add save the basket to the session
        $this->saveBasketToSession();

        return true;
    }

    /*
     *  Remove an single instance of an entry for the basket
     *  Or if quantity === true, remove them all
     */
    public function remove($entry, $quantity = 1) {
        // Wipe the cache of entires, in case there is one
        $this->entries = null;

        foreach ($this->basket as $i => &$item) {
            // We need an exact entry_id and variation match
            if ($item['entry_id'] == $entry->id && $item['variation'] == $variation) {
                $item['quantity'] -= $quantity;

                // We we've none left in the basket, remove the item
                if ($item['quantity'] <= 0)
                    unset($this->basket[$i]);
            }
        }

        // Add save the changes to the session
        $this->saveBasketToSession();
    }

    /*
     *  Save the basket array into the session
     */
    public function saveBasketToSession() {
        Session::put('basket', $this->basket);
    }

    /*
     *  Return a flat array of unique entry ids
     */
    public function basketEntryIds() {
        $ids = array();

        foreach ($this->basket as $entry) {
            if (!in_array($entry['entry_id'], $ids))
                $ids[] = $entry['entry_id'];
        }

        return $ids;
    }

    /*
     *  Save the basket as a complete order
     */
    public function commit($order_entry, $payment_ref) {
        // Reduct the availability of all items
        foreach($this->items() as $item)
            $item->price->commit();

        // Update the order record
        $order_entry->status = \Christie\Bones\Libraries\Bones::STATUS_PUBLISHED;
        $order_entry->save();

        // Save the order details
        $details             = $order_entry->order_details;
        $details->populate(array(
            'items'       => $this->basket,
            'payment_ref' => $payment_ref,
            'total'       => $this->total(),
            'discount'    => 0
        ));
        $details->save();


        // Send an invoice
        $this->emailReceipt( $order_entry, $this->items() );

        $this->clear();
    }

    /*
     *  Send an invoice to the registered address
     */
    public function emailReceipt($order_entry, $items) {
        $bones = App::make('bones');

        $email = $bones->view('receipt', array(
                'order' => $order_entry,
                'items' => $items
            ), 'retail')->render();

        $mpdf = new mPDF('c');

        // Add the CSS file
        $stylesheet = File::get( public_path('packages/christie/bones/bootstrap/css/bootstrap.min.css') );
        $mpdf->WriteHTML($stylesheet, 1);

        $pdf_path = storage_path('receipts/'.$order_entry->id.'.pdf');

        // Generate the PDF
        $mpdf->WriteHTML($email, 0);
        $mpdf->Output( $pdf_path );

        Mail::send( 'emails.receipt', array('name' => 'Hello'), function($message) use($order_entry, $pdf_path) {
            $message->from('us@example.com', 'Laravel');

            $message->to( (string)$order_entry->email, $order_entry->name )->subject('Receipt');

            $message->attach($pdf_path);
        });
    }

}