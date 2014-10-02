<?php

namespace ClubSpeed\Logic;

/**
 * The business logic class
 * for ClubSpeed products.
 */
class ProductsLogic extends BaseLogic {

    /**
     * Constructs a new instance of the ProductsLogic class.
     *
     * The ProductsLogic constructor requires an instantiated DbService class for injection,
     * as well as a reference to the LogicService container where this class will be stored.
     *
     * @param LogicService $logic The parent logic service container.
     * @param DbService $db The database collection provider to inject.
     */
    public function __construct(&$logic, &$db) {
        parent::__construct($logic, $db);
        $this->interface = $this->db->products;

        // $this->updatable = array( // TODO
        //       'CadetQty'
        //     , 'Qty'
        //     , 'Status'
        //     , 'Type'
        // );
    }

    /**
     * Document: TODO
     */
    public final function create($params = array()) {
        $logic = &$this->logic; // we have to do this for PHP 5.3, as we run into issues with protected/private -- 5.4 has access to correctly scoped $this
        return parent::_create($params, function($product) use (&$logic) {
            if (is_null($product->TaxID))
                $product->TaxID = 1; // this cannot be null, so set it to something, or let it fail?
            $tax = $logic->taxes->get($product->TaxID);
            $tax = $tax[0];
            $product->ProductClassID = 1; // TODO: non-null
            $product->IsSpecial = false; // TODO: non-null
            $product->AvailableDay = ''; // TODO: non-null
            $product->AvailableFromTime = \ClubSpeed\Utility\Convert::getDate(); // TODO: non-null
            $product->AvailableToTime = \ClubSpeed\Utility\Convert::getDate(); // TODO: non-null
            $product->IsRequiredMembership = false; // TODO: non-null
            return $product;
        });
    }

    // public final function all() {
    //     $all = $this->db->products->all();
    //     $compressed = $this->compress($all);
    //     return $compressed;
    // }

    // public final function get($id) {
    //     $get = $this->db->products->get($id);
    //     $compressed = $this->compress($get);
    //     return $compressed;
    // }

    // public final function match($params = array()) {
    //     $clean = \ClubSpeed\Utility\Params::clean($params);
    //     $mapped = $this->map('server', $clean['params']);
    //     $match = $this->db->products->match($mapped);

    //     // override Products.Deleted to only find items which have deleted = 0 ?

    //     $compressed = $this->compress($match, @$clean['select']);
    //     return $compressed;
    // }

    // public final function find($params = array()) {
    //     $clean = \ClubSpeed\Utility\Params::clean($params);
    //     $find = $this->db->products->find(@$clean['filter']);
    //     $compressed = $this->compress($find, @$clean['select']);
    //     return $compressed;
    // }

    // public final function update($id, $params = array()) {
    //     $clean = \ClubSpeed\Utility\Params::clean($params);
    //     $mapped = $this->map('server', $clean['params']);
    //     $product = $this->db->products->get($id);
    //     if (is_null($product))
    //         throw new \RecordNotFoundException("Attempted to update a non-existent online booking! Received onlineBookingsId: " . $onlineBookingsId);
    //     $product->load($mapped);
    //     return $this->db->products->update($product);
    // }

    // public final function delete($id) {
    //     // business logic - set Product.Deleted to 1, instead of deleting the record
    //     $product = $this->db->products->get($id);
    //     if (is_null($product))
    //         return 0; // 0 items edited
    //     $product->Deleted = true;
    //     return $this->db->products->update($product);
    // }
}