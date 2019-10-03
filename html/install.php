<?php

require_once 'init.php';

new TSession;

$theme  = $ini['general']['theme'];

$html = file_get_contents("install.html");

$html  = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $html);

$html  = str_replace('{template}', $theme, $html);
$html  = str_replace('{username}', TSession::getValue('username'), $html);
$html  = str_replace('{frontpage}', TSession::getValue('frontpage'), $html);
$html  = str_replace('{query_string}', $_SERVER["QUERY_STRING"], $html);
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();
$html  = str_replace('{HEAD}', $css.$js, $html);


ob_start();
//$form = new DatabaseInstall();
$form = new ExtensionsInstall();
//$form->setIsWrapped(true);
$form->show();
$formContent = $mainContent = ob_get_contents();
ob_end_clean();

$html  = str_replace('{$content}', $formContent, $html);

echo $html;