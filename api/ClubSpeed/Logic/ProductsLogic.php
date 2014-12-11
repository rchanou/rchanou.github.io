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
    }

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
}