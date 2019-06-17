<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../vendor/autoload.php';

use Click\ClickNfse\NFSE5205109\ConsultarLoteRps;
use Click\ClickNfse\NFSE5205109\NfseRequest;
use stdClass;

$tools = [
    'certificado' => dirname(__FILE__).'/certificado.pem',
    'senha_certificado' => ''
];


$consultarLoteRps = new ConsultarLoteRps();
$protocolo = "2019104072";
$consultarLoteRps->setProtocolo($protocolo);

$prestador = new stdClass;
$prestador->Cpf = "";
$prestador->Cnpj = '02481828000134';
$prestador->InscricaoMunicipal = '18513001';
$prestador->RazaoSocial = "";
$consultarLoteRps->setPrestador($prestador);

$consultarLoteRps->makeConsultarLoteRps();

//header("Content-type: text/xml");
$xml = $consultarLoteRps->getXml();
$tools = new NfseRequest($xml);
var_dump($tools->ConsultarLoteRpsEnvio());