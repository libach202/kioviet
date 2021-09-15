<?php

/**
 *   #----------------------------------------------------------#
 *   # * @Company       : photodesign.vn.                       #
 *   # * @Project       : test                               #
 *   # * @File          : KiotViet.php                              #
 *   # * @Developer     : taint.ict@gmail.com                   #
 *   # * @IDE           : PhpStorm                              #
 *   # * @Copyright     : 2021 - NOW                            #
 *   #----------------------------------------------------------#
 *
 *                                        CHANGE HISTORY
 *   ---------------------------------------------------------------------------------------------
 *   |   DATE         | DEVELOPER             | DESCRIPTION                                       |
 *   ---------------------------------------------------------------------------------------------
 *   | 9/13/2021      | taint.ict@gmail.com   | First creation.                                   |
 *   --------------------------------------------------------------------------------------------
 *
 *
 */

namespace Libach202\KiotViet;
use Kiotviet\Kiotviet;
use Kiotviet\KiotvietConfig;
use Kiotviet\KiotvietEndpoint;
class MyKiotViet
{
    private static $retailer = '';
    private static $clientId = '';
    private static $clientSecret = '';
    private static $accessToken;
    private $kiotviet;

    public function __construct() {
        $config = new KiotvietConfig(self::$clientId, self::$clientSecret, self::$retailer);
        $this->kiotviet = new Kiotviet();
        $data = $this->kiotviet->getAccessToken($config);
        self::$accessToken = $data['access_token'];
    }

    /**
     * get Categories from kiotviet
     * @return array|mixed
     * @throws \Exception
     * Created date : 1:59 PM 9/15/2021
     * Dev : libac
     */
	 
    public function getCategories(int $cursor = 0) {

        $params = array(
            "pageSize"              =>  100,    // 100 item for a page
            "currentItem"           =>  $cursor,      // cursor
            "orderBy"               =>  "categoryName",
            "orderDirection"        =>  "DESC",
            "hierachicalData"       =>  true //nếu HierachicalData=true thì mình sẽ lấy nhóm hang theo cấp mà không quan tâm lastModifiedFrom. Ngược lại, HierachicalData=false thì sẽ lấy 1 list nhóm hang theo lastModifiedFrom nhưng không có phân cấp
        );

        $result = $this->kiotviet->get(KiotvietEndpoint::GET_CATEGORIES, $params,self::$accessToken, self::$retailer);
        if(strcmp($result['status'],'success') !== 0) return array();
        return $result['data']['data'];
    }

    /**
     * get all Products By Category From KiotViet
     * @param int $category_id
     * @param int $cursor
     * @return array|mixed
     * @throws \Exception
     * Created date : 3:36 PM 9/15/2021
     * Dev : libac
     * Note : if $category_id == 0 then get all products of kiotviet
     */
	
    public function getProducts(int $category_id = 0, int $cursor = 0) {

        $params = array(
            'orderBy'               => 'name',
            'pageSize'              => 100,
            'currentItem'           => $cursor, //cursor
            'includeInventory'      => true, // get available number
            'includePricebook'      => false, // get price
            'IncludeSerials'        => false, // get serial imei
            'IncludeBatchExpires'   => false,
            'categoryId'            => $category_id,
            'orderDirection'        => 'ASC'
        );

        $result = $this->kiotviet->get(KiotvietEndpoint::GET_PRODUCTS, $params, self::$accessToken, self::$retailer);
        if(strcmp($result['status'], 'success') === 0) {
            $data = $result['data'];
            $total = $data['total'];
            $products = $data['data'];
            $current_total_product = count($products);
            $new_cursor = $cursor + $current_total_product;
            if($new_cursor < $total) {
                return array_merge($products,$this->getProducts($category_id, $new_cursor));
            } else {
                return $products;
            }
        }

        return array();

    }

    /**
     * filterGet Code And Quantity And Category
     * @param $products
     * @return array
     * Created date : 4:06 PM 9/15/2021
     * Dev : libac
     */
    
    public function filterGetCodeAndQuantityAndCategory($products) {
        $data = array();
        foreach ($products as $product) {
            array_push($data, array(
                'category_id'   => $product['categoryId'],
                'code'          => $product['inventories'][0]['productCode'],
                'quantity'      => $product['inventories'][0]['onHand']
            ));
        }
        return $data;
    }

}