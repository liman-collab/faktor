<?php

class Helper
{
    static function getWcProducts()
    {
        $args = array(
            'limit'  => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        $products = wc_get_products( $args );
        foreach($products as $product){
            $categoriesArr = [];

            foreach(get_the_terms( $product->get_id(), 'product_cat' ) as $term){
                $categoriesArr[] = $term->slug;
            }
            $product->categories_as_string = implode(',', $categoriesArr);
        }
        return $products;
    }
}