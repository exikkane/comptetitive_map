<?php

namespace Tygh\Addons\CompetitiveMap\Service;

use Tygh\Enum\OutOfStockActions;
use Tygh\Enum\YesNo;


class ProductService
{
    public function getFilteredProducts(?int $category_id, ?string $features_hash): array
    {
        $params = [
            'category_id'   => $category_id,
            'features_hash' => $features_hash,
            'subcats'       => YesNo::YES,
            'cid'           => $category_id
        ];

        [$products,] = fn_get_products($params);

        fn_gather_additional_products_data($products, [
            'get_features' => true,
        ]);

        return $this->setStockInfo($products);
    }

    protected function setStockInfo(array &$products): array
    {
        if (empty($products)) {
            return [];
        }

        foreach ($products as &$product) {
            if ($product['amount'] > 0) {
                $product['stock_info'] = __("in_stock");
            } else {
                switch ($product['out_of_stock_actions']) {
                    case OutOfStockActions::BUY_IN_ADVANCE:
                        $product['stock_info'] = __("on_backorder");
                        break;
                    case OutOfStockActions::NONE:
                        $product['stock_info'] = __("text_out_of_stock");
                        break;
                }
            }
        }
        return $products;
    }
}
