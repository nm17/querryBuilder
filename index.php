<?php

require 'application/src/dev.php';

include 'application/src/autoload.php';

session_start();

$Qb = new \application\dbms\MysqlBuilder();
$De = new \application\lib\DataExtractor();

$select = ['ID AS ELEM_ID', 'CODE AS ELEM_CODE', 'PREVIEW_TEXT'];
$elem_property = ['VALUE AS ELEM_PROP_VALUE'];
$property = ['NAME AS PROP_NAME', 'CODE AS PROP_CODE', 'PROPERTY_TYPE AS PROP_TYPE'];
$relations1 = [
    'b_iblock_element_property' => 'IBLOCK_ELEMENT_ID',
    'b_iblock_element' => 'ID'
];
$relations2 = [
    'b_iblock_element_property' => 'IBLOCK_PROPERTY_ID',
    'b_iblock_property' => 'ID'
];
$De->fetch('b_iblock_element')
    ->select($select)
    ->leftJoin('b_iblock_element_property', $elem_property, $relations1)
    ->leftJoin('b_iblock_property', $property, $relations2)
    ->where('b_iblock_element.ID', '>', '5')
    ->andWhere('b_iblock_element.CODE', '=', 'RP-CHE30')
    ->andWhere('b_iblock_element.ACTIVE', '=', 'Y')
    ->orWhere('b_iblock_element.CODE', '=', 'TY-ER3D5ME')
    ->get();


//Запрос тот самый
//$Qb->execute("SELECT
//	bie.ID AS ELEM_ID,
//    DATE_FORMAT(bip.TIMESTAMP_X, '%d.%m.%Y %H:%i:%s') AS TIMESTAMP_PROP,
//    bie.NAME AS ELEM_NAME,
//    bip.NAME AS PROP_NAME,
//    bip.CODE AS PROP_CODE,
//    bip.PROPERTY_TYPE AS PROP_TYPE,
//	  IF((bip.PROPERTY_TYPE = 'L'), IF((biep.VALUE IS NOT NULL), 'Y', 'N'), biep.VALUE) AS PROP_VALUE,
//    IF((bip.PROPERTY_TYPE = 'F'), CONCAT_WS('/', bf.SUBDIR, bf.ORIGINAL_NAME), 'NOT FILE') AS FILE_PATH
//FROM b_iblock_element AS bie
//LEFT JOIN b_file AS bf ON bie.PREVIEW_PICTURE = bf.ID
//LEFT JOIN b_iblock_element_property AS biep ON biep.IBLOCK_ELEMENT_ID = bie.ID
//LEFT JOIN b_iblock_property AS bip ON biep.IBLOCK_PROPERTY_ID = bip.ID
//ORDER BY `ELEM_ID` ASC
//LIMIT 200");
