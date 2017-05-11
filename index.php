<?php

session_start();
require 'classSmarty.php';
$smarty = classSmarty::getSmarty();

error_reporting(E_ALL);
//ini_set('display_errors', 1);

$css = "<link type=\"text/css\" rel=\"stylesheet\" href=\"/min/b=owlcarousel&amp;f=owl.carousel.min.css,owl.theme.default.min.css\" />";
$js = "<script type=\"text/javascript\" src=\"/min/f=owlcarousel/owl.carousel.min.js,OwlCarousel\mousewheel.js\"></script>";
$js_captcha = "<script type=\"text/javascript\" src='https://www.google.com/recaptcha/api.js'></script>";
$str = 0;
$module = 'index';
$action = 'index';
$params = array();

$url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

if ($url != '/') {
    try {
        $url_path = parse_url($url, PHP_URL_PATH);
        $uri_parts = explode('/', trim($url_path, ' /'));

//		if (count($uri_parts) % 2) {
//			throw new Exception();
//		}

        $module = array_shift($uri_parts); // Получили имя модуля
        //$action = array_shift($uri_parts); // Получили имя действия
        // Получили в $params параметры запроса
        for ($i = 0; $i < count($uri_parts) / 2; $i++) {
            if(isset($uri_parts[$i+1]))
            $params[$uri_parts[$i]] = $uri_parts[++$i];
        }
    } catch (Exception $e) {
        $smarty->assign('TPL_NAME', "error");
    }
}
//echo "\$module: $module\n";
//echo "\$action: $action\n";
//echo "\$params:\n";
//print_r($params);

$db = classSmarty::getDB('admin', 'admin');

if (empty($module)) {
    $smarty->assign('TPL_NAME', "error");
} else {
    switch ($module) {
        case 'index':
            $st = $db->query("select * from item limit $str,12");
            $row = $st->fetchAll();
            $smarty->assign('row', $row);
            $smarty->assign('CSS', $css);
            $smarty->assign('JS', $js);
            $smarty->assign('TITLE', "Главная");
            $smarty->assign('TPL_NAME', "index");
            break;
        case 'search':
            if (!empty($params["query"])) {
                $query = urldecode($params["query"]);

                $st = $db->prepare("select * from item i INNER JOIN category c ON i.`id_category`=c.id_category
                        where name=? OR name_cat=?
                        limit $str,12");
                $st->execute(array($query, $query));
                $row = $st->fetchAll();
                $smarty->assign('row', $row);
                $smarty->assign('QUERY', $query);
                $smarty->assign('TITLE', "Поиск товаров");
                $smarty->assign('TPL_NAME', "search");
            } else {
                $smarty->assign('TITLE', "Ошибка");
                $smarty->assign('TPL_NAME', "error");
            }
            break;
        case 'user':
            if (isset($_SESSION['name'])) {
                $mobile = filter_input(INPUT_COOKIE, 'MOBILE', FILTER_VALIDATE_BOOLEAN);
                if (isset($mobile)) {
                    $smarty->assign('MOBILE', $mobile);
                } else {
                    $smarty->assign('MOBILE', 'true');
                }
                if (count($params) == 0) {
                    $st = $db->prepare("SELECT o.id_order,summa,date_order,name_status
                FROM orders_user o INNER JOIN status_of_order s ON o.status=s.id_status AND id_user=? ORDER BY date_order DESC;");
                    $st->bindParam(1, $_SESSION['id']);
                    $st->execute();
                    $row = $st->fetchAll();
                    foreach ($row as &$value) {
                        $value['date_order'] = month_replace($value['date_order']);
                    }
                    $smarty->assign('ORDERS_LIST', $row);
                } else {
                    $smarty->assign('ORDER_INFO', 'true');
                    try {
                        $st = $db->prepare("SELECT i.id_item,name,img,price,quantity
                        FROM item i INNER JOIN orders_items o ON o.id_item = i.id_item AND id_order=?;");
                        $st->bindParam(1, $params['order']);
                        $st->execute();
                        $row = $st->fetchAll();
                        $smarty->assign('ITEMS_LIST', $row);
                        $smarty->assign('ID_ORDER', $params['order']);
                    } catch (PDOException $e) {
                        echo "Ошибка: " . $e->getMessage();
                    }
                }
                $smarty->assign('TITLE', "Личный кабинет");
                $smarty->assign('TPL_NAME', "personal_cabinet");
            } else {
                $smarty->assign('TITLE', "Вход");
                $smarty->assign('TPL_NAME', "login");
            }
            break;
        case 'registr':
            $smarty->assign('TITLE', "Регистрация");
            $smarty->assign('TPL_NAME', "registr");
            $smarty->assign('JS_HEAD', $js_captcha);
            break;
        default:
            $smarty->assign('TITLE', "Ошибка");
            $smarty->assign('TPL_NAME', "error");
            break;
    }
}
$smarty->display('page.tpl');

function month_replace($date) {
    $date_c = strftime("%#d %b, %Y %H:%M", strtotime($date));
    $replacements = array(
        "Jan" => "января",
        "Feb" => "февраля",
        "Mar" => "марта",
        "Apr" => "апреля",
        "May" => "мая",
        "Jun" => "июня",
        "Jul" => "июля",
        "Aug" => "августа",
        "Sep" => "сентября",
        "Oct" => "октября",
        "Nov" => "ноября",
        "Dec" => "декабря"
    );
    return strtr($date_c, $replacements);
}
