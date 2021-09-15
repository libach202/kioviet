<?php
/**
 *   #----------------------------------------------------------#
 *   # * @Company       : photodesign.vn.                       #
 *   # * @Project       : test                               #
 *   # * @File          : index.php                              #
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
require_once ('vendor/autoload.php');
use Libach202\KiotViet\MyKiotViet;
$myKioViet = new MyKiotViet();
$categories = $myKioViet->getCategories();
//var_dump($categories);
$products = $myKioViet->getProducts();
var_dump($myKioViet->filterGetCodeAndQuantityAndCategory($products));

