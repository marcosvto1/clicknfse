<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../vendor/autoload.php';

use Click\ClickNfse\NFSE5205109\GerarNfse;
use Click\ClickNfse\NFSE5205109\NfseRequest;
use stdClass;

$tools = [
    'certificado' => dirname(__FILE__).'/certificado.pem',
    'senha_certificado' => ''
];

$nfse = new GerarNfse($tools);



$lote = new stdClass;
$lote->Id = 0;
$lote->NumeroLote = 0;

$servico = new stdClass;
$servico->Numero = 200;
$servico->Serie = 1;
$servico->Tipo = 1;
$servico->Status = 1;
$servico->Competencia = date('Y-m-d');
$servico->DataEmissao = "2019-06-17T05:49:46";
$servico->RegimeEspecialTributacao = 1;
$servico->OptanteSimplesNacional = 1;
$servico->IncentivoFiscal = "";
$servico->IssRetido = 0;
$servico->ItemListaServico = 09.01;
$servico->Descriminacao = "Teste";
$servico->CodigoMunicipio = "";
$servico->ExigibilidadeISS = 0;
$nfse->setServico($servico);


$valores = new stdClass;
$valores->ValorServicos = 10;
$valores->ValorDeducoes = 0;
$valores->ValorPis = "";
$valores->ValorCofins = 0;
$valores->ValorInss = 0;
$valores->ValorIr = 0;
$valores->ValorCsll = 0;
$valores->OutrasRetencoes = 0;
$valores->ValorIss = 0;
$valores->Aliquota = 4;
$valores->BaseCalculo = 0.00;
$valores->DescontoIncondicionado = 0.00;
$valores->DescontoCondicionado = 0.00;
$valores->ValorLiquidoNfse = 0.00;
$valores->ValorIssRetido = 0.00;
$nfse->setValores($valores);


$prestador = new stdClass;
$prestador->Cpf = "";
$prestador->Cnpj = '02481828000134';
$prestador->InscricaoMunicipal = '18513001';
$prestador->RazaoSocial = "";
$nfse->setPrestador($prestador);

$tomador = new stdClass;
$tomador->Cpf = "04383423132";
$tomador->Cnpj = "";
$tomador->InscricaoMunicipal = "";
$tomador->RazaoSocial = "Marcos Vinicius Tomaz";
$tomador->Endereco = "Rua primeiro de maio";
$tomador->Bairro = "Jardim Brasilia";
$tomador->Numero = "569";
$tomador->CodigoMunicipio = "5205109";
$tomador->CodigoPais = "";
$tomador->Uf = "GO";
$tomador->Cep = "75712225";
$tomador->Telefone = "";
$tomador->Email = "";
$nfse->setTomador($tomador);

$nfse->makeGerarNfse();
//header("Content-type: text/xml");
//echo $nfse->getXml();
$tools = new NfseRequest($nfse->getXml());
var_dump($tools->GerarNfseEnvio());