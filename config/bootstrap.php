<?php

define('ROOT_DIR',strip_tags($_SERVER['DOCUMENT_ROOT']));

require_once(ROOT_DIR.'/api/config/dbConfig.php');
require_once(ROOT_DIR.'/api/config/Database.php');
require_once(ROOT_DIR.'/api/config/ErrorExit.php');
require_once(ROOT_DIR.'/api/config/InputValidation.php');
require_once(ROOT_DIR.'/api/models/Product.php');
require_once(ROOT_DIR.'/api/models/Picture.php');
require_once(ROOT_DIR.'/api/models/Address.php');
require_once(ROOT_DIR.'/api/models/Customer.php');
require_once(ROOT_DIR.'/api/models/Order.php');
require_once(ROOT_DIR.'/api/models/OrderedProduct.php');
