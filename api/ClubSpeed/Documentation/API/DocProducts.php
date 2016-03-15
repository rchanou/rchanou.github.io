<?php

namespace ClubSpeed\Documentation\API;

class DocProducts Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'products';
        $this->header          = 'Products';
        $this->url             = 'products';
        $this->info            = $this->info();
        $this->version         = 'V1';
        $this->json            = $this->json();
        $this->expand();
    }

    private function json() {
        return <<<EOS
{
  "products": [
    {
      "productId": 14416,
      "productType": 4,
      "description": "Reservation Product for API Unit Testing",
      "price1": 2,
      "price2": 0,
      "taxId": 3459,
      "productClassId": 1,
      "enabled": true,
      "deleted": false,
      "p_Points": null,
      "r_Points": 10,
      "g_Points": null,
      "priceCadet": 0,
    }
  ]
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "productId",
                "type" => "Integer",
                "default" => "{Generated}",
                "create" => "available",
                "update" => "available",
                "description" => "The primary key for the record"
            ),
            // array(
            //     "name" => "availableDay",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "availableFromTime",
            //     "type" => "DateTime",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "availableToTime",
            //     "type" => "DateTime",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "bonusValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "comValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "cost",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "custom1",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Custom data holder 1"
            ),
            array(
                "name" => "custom2",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Custom data holder 2"
            ),
            array(
                "name" => "custom3",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Custom data holder 3"
            ),
            array(
                "name" => "custom4",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Custom data holder 4"
            ),
            // array(
            //     "name" => "dateCreated",
            //     "type" => "DateTime",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => "The date on which the product was created"
            // ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether the product has been soft deleted"
            ),
            array(
                "name" => "description",
                "type" => "String",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Description for the product"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "Flag indicating whether the product is currently enabled"
            ),
            // array(
            //     "name" => "entitle1",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle2",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle3",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle4",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle5",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle6",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle7",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "entitle8",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "g_Points",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The amount of gift card points provided when this product is purchased"
            ),
            // array(
            //     "name" => "isInventory",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isRequiredMembership",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isShowStat",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isSpecial",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "isTrackable",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "largeIcon",
            //     "type" => "",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mMembershiptypeId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "mPoints",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "p_Points",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The number of points provided when this product is purchased"
            ),
            // array(
            //     "name" => "pPointTypeId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "paidValue",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "price1",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The price of the product"
            ),
            // array(
            //     "name" => "price2",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "price3",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "price4",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "price5",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "priceCadet",
            //     "type" => "Double",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "productClassId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the reporting product class for the product"
            ),
            array(
                "name" => "productType",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The type of the product"
            ),
            // array(
            //     "name" => "rLocalOnly",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "r_Points",
                "type" => "Double",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The amount of reservation points provided when this product is purchased"
            ),
            // array(
            //     "name" => "req",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sCustId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sNoOfLapsOrSeconds",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sSaleBy",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sVol",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "showOnWeb",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "sku",
            //     "type" => "String",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            array(
                "name" => "taxId",
                "type" => "Integer",
                "default" => "",
                "create" => "available",
                "update" => "available",
                "description" => "The ID of the tax for the product"
            ),
            // array(
            //     "name" => "vendorId",
            //     "type" => "Integer",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // ),
            // array(
            //     "name" => "webShop",
            //     "type" => "Boolean",
            //     "default" => "",
            //     "create" => "available",
            //     "update" => "available",
            //     "description" => ""
            // )
        );
    }
}
