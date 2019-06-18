<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../vendor/autoload.php';

use Click\ClickNfse\NFSE5205109\ConsultarNfseRps;
use Click\ClickNfse\NFSE5205109\NfseRequest;
use stdClass;

$tools = [
    'certificado' => dirname(__FILE__).'/certificado.pem',
    'senha_certificado' => ''
];


$consultarNfseRps = new ConsultarNfseRps();

$identificacaoRps = new stdClass;
$identificacaoRps->Numero = 200;
$identificacaoRps->Serie = 1;
$identificacaoRps->Tipo = 1;
$consultarNfseRps->setIdentificacaoRps($identificacaoRps);

$prestador = new stdClass;
$prestador->Cpf = "";
$prestador->Cnpj = '02481828000134';
$prestador->InscricaoMunicipal = '18513001';
$prestador->RazaoSocial = "";
$consultarNfseRps->setPrestador($prestador);

$consultarNfseRps->makeConsultarNfseRps();

//header("Content-type: text/xml");
//echo $consultarNfseRps->getXml();

$tools = new NfseRequest($consultarNfseRps->getXml());
$retorno = $tools->ConsultarNfseRpsEnvio();
var_dump($retorno);
//file_put_contents('retorno/ConsultarNfseRpsResponse.xml', $tools->ConsultarNfseRpsEnvio());

//$xml = file_get_contents('retorno/ConsultarNfseRpsResponse.xml');
//ar_dump($tools->returnRequest($xml));
// exit;