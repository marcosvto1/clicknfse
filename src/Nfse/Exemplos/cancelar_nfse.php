<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../vendor/autoload.php';

use Click\ClickNfse\NFSE5205109\CancelarNfse;
use Click\ClickNfse\NFSE5205109\NfseRequest;
use stdClass;

$tools = [
    'certificado' => dirname(__FILE__).'/certificado.pem',
    'senha_certificado' => ''
];


$cancelarNfse = new CancelarNfse($tools);
$pedido = new stdClass;
$pedido->Id = 1;
$pedido->Numero = 93925;
$pedido->Cpf = "";
$pedido->Cnpj = '02481828000134';
$pedido->InscricaoMunicipal = '18513001';
$pedido->CodigoMunicio = "5205109";
$pedido->CodigoCancelamento = 1;
$cancelarNfse->setPedidoCancelamento($pedido);

$cancelarNfse->makeCancelarNfse();

//header("Content-type: text/xml");
$xml = $cancelarNfse->getXml();
$tools = new NfseRequest($xml);
var_dump($tools->CancelarNfseEnvio());