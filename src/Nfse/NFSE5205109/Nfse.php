<?php 

namespace Click\ClickNfse\NFSE5205109;

class Nfse
{
    private $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     *  @var string 
     */
    public $urlWebService;

    function __construct()
    {
    }

    public function makeLote(stdClass $stdLote, stdClass $stdPrestador, stdClass $stdRps)
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $tagListaRps =  '<ListaRps>';
        foreach($stdRps as $item)
        {
            $tagListaRps .= "<Rps><InfDeclaracaoPrestacaoServico>";
                $tagListaRps .= "<Rps>";
                    $tagListaRps .= "<IdenticacaoRps>";
                        $tagListaRps .= "<Numero>".$item->Numero."</Numero>";
                        $tagListaRps .= "<Serie>".$item->Serie."</Serie>";
                        $tagListaRps .= "<Serie>".$item->Tipo."</Serie>";
                    $tagListaRps .= "</IdenticacaoRps>";
                    $tagListaRps .= "<DataEmissao>".$item->DataEmissao."</DataEmissao>"; 
                    $tagListaRps .= "<Status>".$item->Status."</Status>";
                $tagListaRps .= "</Rps>";
                $tagListaRps .="<Competencia>".$item->Competencia."</Competencia>";         
                $tagListaRps .= "<Servico>";
                    $tagListaRps .= "<Valores>";
                        $tagListaRps .= "<ValorServicos>".$item->ValorServicos."</ValorServicos>"; 
                        $tagListaRps .= "<Aliquota>".$item->Aliquota."</Aliquota>";
                    $tagListaRps .= "</Valores>";
                    $tagListaRps .= "<IssRetido>".$item->IssRetido."</IssRetido>";
                    if ($item->IssRetido == 1)
                    {
                        $tagListaRps .= "<ResponsavelRetencao>".intval(1)."</ResponsavelRetencao>";
                    }
                    $tagListaRps .= "<ItemListaServico>".$item->ItemListaServico."</ItemListaServico>";
                    $tagListaRps .= "<Discriminacao>".htmlspecialchars($item->Descriminacao)."</Discriminacao>";
                    $tagListaRps .= "<CodigoMunicipio>".$item->CodigoMunicipio."</CodigoMunicipio>";
                    $tagListaRps .= "<ExigibilidadeISS>".$item->ExigibilidadeIss."</ExigibilidadeISS>";
                $tagListaRps .= "</Servico>";
                $tagListaRps .= "<Prestador>";
                    $tagListaRps .= "<CpfCnpj>"; 
                        $tagListaRps .= "<Cnpj>".$item->prestador_Cnpj."</Cnpj>"; 
                    $tagListaRps .= "</CpfCnpj>"; 
                    $tagListaRps .= "<InscricaoMunicipal>".$item->prestador_InscricaoMunicipal."</InscricaoMunicipal>"; 
                $tagListaRps .= "<Prestador>";
                $tagListaRps .= "<Tomador>"; 
                    $tagListaRps .="<IdentificacaoTomador>";
                        $tagListaRps .= "<CpfCnpj>";
                            if(strlen($item->tomador_CpfCnpj) >= 14)
                            {
                                $tagListaRps .= "<Cnpj>$item->tomador_CpfCnpj</Cnpj>";        
                            }else{
                                $tagListaRps .= "<Cpf>$item->tomador_CpfCnpj</Cpf>";
                            }
                        $tagListaRps .= "</CpfCnpj>";                
                        if(strlen($item->tomador_CpfCnpj) >= 14)
                        {
                           $tagListaRps .= "<InscricaoMunicipal>".$item->tomador_Im."</InscricaoMunicipal>";
                        }
                    $tagListaRps .= "</IdentificacaoTomador>";
                    $tagListaRps .= "<Endereco>";                    
                        $tagListaRps .= "<Endereco>".$item->tomador_Endereco."</Endereco>";
                        $tagListaRps .= "<Numero>".$item->tomador_Numero."</Numero>";
                        $tagListaRps .= "<Bairro>".$item->tomador_Bairro."</Bairro>"; 
                        $tagListaRps .= "<CodigoMunicipio>".$item->tomador_CodigoMunicipio."</CodigoMunicipio>";
                        $tagListaRps .= "<Uf>".$item->tomador_Uf."</Uf>";
                        $tagListaRps .= "<CodigoPais>".$item->tomador_CodigoPais."</CodigoPais>";
                        $tagListaRps .=" <Cep>".$item->tomador_Cep."</Cep>";
                    $tagListaRps = "</Endereco>"; 
                    $tagListaRps = "<Contato>"; 
                        $tagListaRps .= "<Telefone>".$item->tomador_Telefone."</Telefone>";
                        $tagListaRps .= "<Email>".$item->tomador_Email."</Email>";                
                    $tagListaRps .= "</Contato>"; 
                $tagListaRps .= "</Tomador>";
                $tagListaRps .= "<RegimeEspecialTributacao>".$item->RegimeEspecialTributacao."</RegimeEspecialTributacao>"; 
                $tagListaRps .= "<OptanteSimplesNacional>".$item->OptanteSimplesNacional ."</OptanteSimplesNacional>"; 
                $tagListaRps .= "<IncentivoFiscal>". $servico->IncentivoFiscal. "</IncentivoFiscal>";
            $tagListaRps .= "</InfDeclaracaoPrestacaoServico></Rps>";          
        }
        $tagListaRps .= '</ListaRps>';
    

   
        $qtdeRps = sizeof($stdRps);
    
        $tagEnviarLoteRps .= "<?xml version='1.0' encoding='utf-8'?>";
        $tagEnviarLoteRps .= "<EnviarLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
            $tagEnviarLoteRps .= "<LoteRps>";
                $tagEnviarLoteRps .= "<NumeroLote>".$lote."</NumeroLote>"; 
                $tagEnviarLoteRps .= "<CpfCnpj>";
                    $tagEnviarLoteRps .= "<Cnpj>".$prestador->Cnpj."</Cnpj>"; 
                $tagEnviarLoteRps .= "</CpfCnpj>";
                $tagEnviarLoteRps .= "<InscricaoMunicipal>".$prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
                $tagEnviarLoteRps .= "<QuantidadeRps>".$qtdeRps."</QuantidadeRps>";
                $tagEnviarLoteRps .= $tagListaRps; 
            $tagEnviarLoteRps .= "</LoteRps>";
        $tagEnviarLoteRps .= "</EnviarLoteRpsEnvio>";

        
        $dom->loadXML($tagEnviarLoteRps);
        $xml = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        return $xml;
        
    }


}