<?php

if (!defined('_PS_VERSION_'))
    exit;

/***
 * 
 */
class TaCartPopup extends Module {
    public function __construct() {
        $this->name = 'tacartpopup';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'theads.pl';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('TA Cart popup');
        $this->description = $this->l('Show popup after add product to cart.');
    }
    
    public function install() {
        if(
            parent::install() == false
            || $this->registerHook('displayHeader') == false
            || $this->registerHook('displayFooter') == false
         ) {
            return false;
        }
        return true;
    }
    
    public function uninstall() {
        if (!parent::uninstall())
            return false;
        return true;
    }
    
    /**
    * Return products by ids
    *
    * @param integer $id_lang Language ID
    * @param boolean $ids array of products id
    * @param integer $p Page number
    * @param integer $n Number of products per page
    * @return array Products 
    */
    private function getProducts($id_lang, $ids = array(), $p = 1, $n = 6) {
        
        if(empty($ids)) return array();
        
        $context = Context::getContext();
        
        if ($p < 1) $p = 1;
        
        $front = true;
        
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                    pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
                    il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                    DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
                    INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
                            DAY)) > 0 AS new, product_shop.price AS orderprice
            FROM `'._DB_PREFIX_.'category_product` cp
            LEFT JOIN `'._DB_PREFIX_.'product` p
                    ON p.`id_product` = cp.`id_product`
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
            ON (p.`id_product` = pa.`id_product`)
            '.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
            '.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                    ON (product_shop.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                    ON (p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN `'._DB_PREFIX_.'image` i
                    ON (i.`id_product` = p.`id_product`)'.
            Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il
                    ON (image_shop.`id_image` = il.`id_image`
                    AND il.`id_lang` = '.(int)$id_lang.')
            LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
                    ON m.`id_manufacturer` = p.`id_manufacturer`
            WHERE product_shop.`id_shop` = '.(int)$context->shop->id . ' '
                    . ' AND product_shop.`active` = 1' 
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    . ' AND p.id_product IN (' . implode(', ', $ids) . ')'
                    .' GROUP BY product_shop.id_product';
        
        $sql .= ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return Product::getProductsProperties($id_lang, $result);
    }
    
    public function hookDisplayFooter($params) {
        $allowed_controllers = array('product');
        $_controller = $this->context->controller;
        if (isset($_controller->php_self) && in_array($_controller->php_self, $allowed_controllers)) {
            $products = array();
            if($id_product = (int)Tools::getValue('id_product')) {
                $productTags = Tag::getProductTags($id_product);
                $productTags = $productTags[intval(Context::getContext()->cookie->id_lang)];
                if(!empty($productTags)) {
                    foreach ($productTags as $tag) {
                        $tagObj = new Tag(null, $tag, Context::getContext()->cookie->id_lang);
                        $tmpProducts = $tagObj->getProducts();
                        foreach($tmpProducts as $product) {
                            if(!array_key_exists($product['id_product'], $products) && $product['id_product'] != $id_product) {
                                $products[$product['id_product']] = $product['id_product'];
                            }
                        }
                    }
                }
            }
            
            $productsList = $this->getProducts(intval(Context::getContext()->cookie->id_lang), $products);
            
            $this->smarty->assign(array(
                'products' => $productsList,
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            ));
            return $this->display(__FILE__, 'tacartpopup.tpl');
        }
    }
    
    public function hookDisplayHeader($params)
    {
        $allowed_controllers = array('product');
        $_controller = $this->context->controller;
        if (isset($_controller->php_self) && in_array($_controller->php_self, $allowed_controllers)) {
            $_controller->addCss($this->_path . 'tacartpopup.css', 'all');
            $_controller->addJs($this->_path . 'tacartpopup.js');
        }
    }
}

