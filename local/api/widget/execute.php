
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Widget\ClientWidgetComponent;
use Widget\DiscountWidgetComponent;
use Widget\ExecuteWidgetComponent;

header('Content-Type: application/json charset=utf-8');
$session = \Bitrix\Main\Application::getInstance()->getSession();

$widgetAction = null;
if (!empty($_GET['act'])) {
    $widgetAction = $_GET['act'];
}

if (!$session->has('user_id')) {
    echo json_encode('Session user_id is empty');
    return 1;
}

if (!$session->has('token')) {
    echo json_encode('Session token is empty');
    return 1;
}

$result = null;
$widgetDeals = new ClientWidgetComponent();
$executeWidget = new ExecuteWidgetComponent($widgetDeals, $session['user_id'], $session['token'], $_SERVER['REMOTE_ADDR']);

/*
 * кликаем по кнопке и получаем остаток по времени и другие данные
 */
if (empty($widgetAction)) {
    $result = $executeWidget->clickWidget($_GET["code"]);

    new DiscountWidgetComponent(1, $_GET["code"], 1, 1, json_encode($result['discount']));
    http_response_code($widgetDeals->getResponseCode());

    echo json_encode($result);
    return 1;
}

/*
 * тут получаем данные по пролонгации в случае если эта пролонгация включена
 */
if ($widgetAction === 'prolongation') {
    $result = $executeWidget->clickProlongationWidget($_GET["code"]);
    http_response_code($widgetDeals->getResponseCode());

    echo json_encode($result);
    return 1;
}

/*
 * говорим серверу что пользователь использовал промокод
 */
if ($widgetAction === 'use') {

    $result = $executeWidget->usePromoCode($_GET["code"]);
    http_response_code($widgetDeals->getResponseCode());

    echo json_encode($result);
    return 1;
}

