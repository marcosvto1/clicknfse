<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../../vendor/autoload.php';

use Click\ClickNfse\NFSE5205109\Nfse;
use stdClass;

$tools = [
    'certificado' => '',
    'senha_certificado' => ''
];

$nfse = new Nfse($tools);

$tagEnviarLoteRpsEnvio = new stdClass;
$tagLote = new stdClass;
$tagLote->Id = 1;
$tagLote->NumeroLote = 1;
$tagCpfCnpj = new stdClass;
$tagCpfCnpj->Cpf = '04383423132';
$tagLote->CpfCnpj = $tagCpfCnpj;
$tagLote->InscricaoMunicipal = 1200202;
$tagLote->QuantidadeRps = 1;
$tagRpsGrupo = new stdClass;
    $tagRpsLista = new stdClass;
        $tagInfDeclaracaoPrestacaoServico = new stdClass;
            $tagRps = new stdClass;
                $tagIdenticacaoRps =  new stdClass;
                    $tagIdenticacaoRps->Numero = 1; 
                    $tagIdenticacaoRps->Serie = 1; 
                    $tagIdenticacaoRps->Tipo = 1; 
                $tagRps->IdenticacaoRps = $tagIdenticacaoRps;
                $tagRps->DataEmissao = "";
                $tagRps->Status = "";
            $tagInfDeclaracaoPrestacaoServico->Rps = $tagRps;    
            $tagInfDeclaracaoPrestacaoServico->Competencia = "";   
                $tagServico = new stdClass;
                    $tagValores = new stdClass;
                        $tagValores->ValorServicos = 100;
                        $tagValores->Aliquota = 4;
                    $tagServico->Valores = $tagValores;
                    $tagServico->IssRetido = 1;
                    $tagServico->ResponsavelRetencao = 1; 
                    $tagServico->ItemListaServico = 4.01; 
                    $tagServico->Descriminacao = 1; 
                    $tagServico->CodigoMunicipio = 1; 
                    $tagServico->ExigibilidadeISS = 1;

                $tagPrestador = new stdClass;
                    //ID PRESTADOR
                    $tagCpfCnpj = new stdClass;
                        $tagCpfCnpj->Cnpj = "0202002020202";
                    $tagPrestador->CpfCnpj = $tagCpfCnpj;    
                    $tagPrestador->IncricaoMunicipal = "00002323";


                $tagTomador = new stdClass;    
                    $tagIdentificacaoTomador = new stdClass;
                        $tagCpfCnpj = new stdClass;
                            $tagCpfCnpj->Cnpj = "0202002020202";
                        $tagIdentificacaoTomador->CpfCnpj = $tagCpfCnpj;
                        $tagIdentificacaoTomador->InscricaoMunicipal = "1231213123";
                    $tagTomador->IndentificacaoTomador = $tagIdentificacaoTomador;
                    // TAG ENDERECO TOMADOR
                    $tagEnderecoTomador = new stdClass;
                        $tagEnderecoTomador->Endereco = "";
                        $tagEnderecoTomador->Numero = "";
                        $tagEnderecoTomador->Bairro = "";
                        $tagEnderecoTomador->CodigoMunicipio = "";
                        $tagEnderecoTomador->Uf = "";
                        $tagEnderecoTomador->CodigoPais = "";
                        $tagEnderecoTomador->Cep = "";
                    $tagTomador->Endereco = $tagEnderecoTomador;
                    
                    // TAG CONTATO TOMADOR
                    $tagContatoTomador = new stdClass;
                        $tagContatoTomador->Telefone = "";
                        $tagContatoTomador->Email = "";
                    $tagTomador->Contato = $tagContatoTomador;    
            $tagInfDeclaracaoPrestacaoServico->Servico = $tagServico;
            $tagInfDeclaracaoPrestacaoServico->Prestador = $tagPrestador;
            $tagInfDeclaracaoPrestacaoServico->Tomador = $tagTomador;
            $tagInfDeclaracaoPrestacaoServico->RegimeEspecialTributacao = "";
            $tagInfDeclaracaoPrestacaoServico->OptanteSimplesNacional = "";
            $tagInfDeclaracaoPrestacaoServico->IncentivoFiscal = "";
        $tagRpsLista->InfDeclaracaoPrestacaoServico = $tagInfDeclaracaoPrestacaoServico;
    $tagRpsGrupo->Rps = $tagRpsLista;    
$tagLote->ListaRps[] = $tagRpsGrupo;
$tagEnviarLoteRpsEnvio->LoteRps = $tagLote;

$lote = new stdClass;
$lote->Id = 0;
$lote->NumeroLote = 0;

$servico = new stdClass;
$servico->Numero = 100;
$servico->Serie = "E";
$servico->Tipo = 1;
$servico->Status = "";
$servico->Competencia = "";
$servico->DataEmissao = "";
$servico->RegimeEspecialTributacao = "";
$servico->OptanteSimplesNacional = "";
$servico->IncentivoFiscal = "";
$servico->IssRetido = 0;
$servico->ItemListaServico = 0;
$servico->Descriminacao = "";
$servico->CodigoMunicipio = "";
$servico->ExigibilidadeISS = 0;
$nfse->setServico($servico);

$valores = new stdClass;
$valores->ValorServicos = 0;
$valores->ValorDeducoes = 0;
$valores->ValorPis = "";
$valores->ValorCofins = 0;
$valores->ValorInss = 0;
$valores->ValorIr = 0;
$valores->ValorCsll = 0;
$valores->OutrasRetencoes = 0;
$valores->ValorIss = 0;
$valores->Aliquota = 0;
$valores->BaseCalculo = 0.00;
$valores->DescontoIncondicionado = 0.00;
$valores->DescontoCondicionado = 0.00;
$valores->ValorLiquidoNfse = 0.00;
$valores->ValorIssRetido = 0.00;
$nfse->setValores($valores);

$prestador = new stdClass;
$prestador->Cpf = "";
$prestador->Cnpj = "";
$prestador->InscricaoMunicipal = "";
$prestador->RazaoSocial = "";
$nfse->setPrestador($prestador);

$tomador = new stdClass;
$tomador->Cpf = "";
$tomador->Cnpj = "";
$tomador->InscricaoMunicipal = "";
$tomador->RazaoSocial = "";
$tomador->Endereco = "";
$tomador->Bairro = "";
$tomador->Numero = "";
$tomador->CodigoMunicipio = "";
$tomador->CodigoPais = "";
$tomador->Uf = "";
$tomador->Cep = "";
$tomador->Telefone = "";
$tomador->Email = "";
$nfse->setTomador($tomador);

$nfse->makeGerarNfse();
header("Content-type: text/xml");
echo $nfse->getXml();