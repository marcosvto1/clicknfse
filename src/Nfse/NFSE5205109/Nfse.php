<?php 

namespace Click\ClickNfse\NFSE5205109;

class Nfse
{
    private $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     *  @var DomElement
     */
    public $xml;

    /**
     * @var stdClass      
     */
    protected $valores;

    /**
     * @var stdClass      
     */
    protected $servico;


    /**
     * @var stdClass      
     */
    protected $prestador;

    /**
     * @var stdClass      
     */
    protected $tomador;

    /**
     * @var stdClass      
     */
    protected $construcaoCivil;

    /**
     * @var stdClass      
     */
    protected $intermediario;

    /**
     *  @var string 
     */
    public $urlWebService;


    function __construct($tools)
    {
    }

    public function makeLote(stdClass $stdLote, stdClass $stdPrestador, stdClass $stdRps)
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $xml =  '<ListaRps>';
        foreach($stdRps as $item)
        {
            $tagListaRps .= "<Rps><InfDeclaracaoPrestacaoServico>";
                $tagListaRps .= "<Rps>";
                    $tagListaRps .= "<IdenticacaoRps>";
                        $tagListaRps .= "<Numero>".$item->Numero."</Numero>";
                        $tagListaRps .= "<Serie>".$item->Serie."</Serie>";
                        $tagListaRps .= "<Tipo>".$item->Tipo."</Tipo>";
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

    public function makeNfse($lote, $prestador, $tomador, $servico)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;

        $tagListaRps =  '<ListaRps>';   
            $tagListaRps .= "<Rps><InfDeclaracaoPrestacaoServico>";
                $tagListaRps .= "<Rps>";
                    $tagListaRps .= "<IdenticacaoRps>";
                        $tagListaRps .= "<Numero>".$servico->Numero."</Numero>";
                        $tagListaRps .= "<Serie>".$servico->Serie."</Serie>";
                        $tagListaRps .= "<Tipo>".$servico->Tipo."</Tipo>";
                    $tagListaRps .= "</IdenticacaoRps>";
                    $tagListaRps .= "<DataEmissao>".$servico->DataEmissao."</DataEmissao>"; 
                    $tagListaRps .= "<Status>".$servico->Status."</Status>";
                $tagListaRps .= "</Rps>";
                $tagListaRps .="<Competencia>".$servico->Competencia."</Competencia>";         
                $tagListaRps .= "<Servico>";
                    $tagListaRps .= "<Valores>";
                        $tagListaRps .= "<ValorServicos>".$servico->ValorServicos."</ValorServicos>"; 
                        $tagListaRps .= "<Aliquota>".$servico->Aliquota."</Aliquota>";
                        $tagListaRps .= "<ValorDeducoes>".$servico->ValorDeducoes."</ValorDeducoes>";
                        $tagListaRps .=  "<ValorPis>".$servico->ValorPis."</ValorPis>";
                        $tagListaRps .=  "<ValorCofins>".$servico->ValorCofins."</ValorCofins>";
                        $tagListaRps .=  "<ValorIr>".$servico->ValorIr."</ValorIr>";
                        $tagListaRps .=  "<ValorCsll>".$servico->ValorCsll."</ValorCsll>";
                        $tagListaRps .=  "<ValorIss>".$servico->ValorIss."</ValorIss>";
                        $tagListaRps .=  "<ValorIssRetido>".$servico->ValorIssRetido."</ValorIssRetido>";
                        $tagListaRps .=  "<OutrasRetencoes>".$servico->OutrasRetencoes."</OutrasRetencoes>";
                        $tagListaRps .=  "<BaseCalculo>".$servico->BaseCalculo."</BaseCalculo>";
                        $tagListaRps .=  "<ValorLiquidoNfse>".$servico->ValorLiquidoNfse."</ValorLiquidoNfse>";
                        $tagListaRps .=  "<DescontoIncondicionado>".$servico->DescontoIncondicionado."</DescontoIncondicionado>";
                        $tagListaRps .=  "<DescontoCondicionado>".$servico->DescontoCondicionado."</DescontoCondicionado>";
                    $tagListaRps .= "</Valores>";
                    $tagListaRps .= "<IssRetido>".$servico->IssRetido."</IssRetido>";
                    if ($servico->IssRetido == 1)
                    {
                        $tagListaRps .= "<ResponsavelRetencao>".intval(1)."</ResponsavelRetencao>";
                    }
                    $tagListaRps .= "<ItemListaServico>".$servico->ItemListaServico."</ItemListaServico>";
                    $tagListaRps .= "<Discriminacao>".htmlspecialchars($servico->Descriminacao)."</Discriminacao>";
                    $tagListaRps .= "<CodigoMunicipio>".$servico->CodigoMunicipio."</CodigoMunicipio>";
                    $tagListaRps .= "<ExigibilidadeISS>".$servico->ExigibilidadeISS."</ExigibilidadeISS>";
                $tagListaRps .= "</Servico>";

                $tagListaRps .= "<Prestador>";
                    $tagListaRps .= "<CpfCnpj>"; 
                        $tagListaRps .= "<Cnpj>".$prestador->Cnpj."</Cnpj>"; 
                    $tagListaRps .= "</CpfCnpj>"; 
                    $tagListaRps .= "<InscricaoMunicipal>".$prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
                $tagListaRps .= "</Prestador>";

                $tagListaRps .= "<Tomador>"; 
                    $tagListaRps .="<IdentificacaoTomador>";
                        $tagListaRps .= "<CpfCnpj>";
                            if(empty($tomador->Cpf))
                            {
                                $tagListaRps .= "<Cnpj>".$tomador->Cnpj."</Cnpj>";        
                            }else{
                                $tagListaRps .= "<Cpf>".$tomador->Cpf."</Cpf>";
                            }
                        $tagListaRps .= "</CpfCnpj>";                
                        if(!empty($tomador->Cnpj))
                        {
                           $tagListaRps .= "<InscricaoMunicipal>".$tomador->InscricaoMunicipal."</InscricaoMunicipal>";
                        }
                    $tagListaRps .= "</IdentificacaoTomador>";
                    $tagListaRps .= "<Endereco>";
                        $tagListaRps .= "<Endereco>".$tomador->Endereco."</Endereco>";
                        $tagListaRps .= "<Numero>".$tomador->Numero."</Numero>";
                        $tagListaRps .= "<Bairro>".$tomador->Bairro."</Bairro>"; 
                        $tagListaRps .= "<CodigoMunicipio>".$tomador->CodigoMunicipio."</CodigoMunicipio>";
                        $tagListaRps .= "<Uf>".$tomador->Uf."</Uf>";
                        $tagListaRps .= "<CodigoPais>".$tomador->CodigoPais."</CodigoPais>";
                        $tagListaRps .=" <Cep>".$tomador->Cep."</Cep>";
                    $tagListaRps .= "</Endereco>"; 
                    $tagListaRps .= "<Contato>"; 
                        $tagListaRps .= "<Telefone>".$tomador->Telefone."</Telefone>";
                        $tagListaRps .= "<Email>".$tomador->Email."</Email>";                
                    $tagListaRps .= "</Contato>"; 
                $tagListaRps .= "</Tomador>";
                $tagListaRps .= "<RegimeEspecialTributacao>".$servico->RegimeEspecialTributacao."</RegimeEspecialTributacao>"; 
                $tagListaRps .= "<OptanteSimplesNacional>".$servico->OptanteSimplesNacional ."</OptanteSimplesNacional>"; 
                $tagListaRps .= "<IncentivoFiscal>". $servico->IncentivoFiscal. "</IncentivoFiscal>";
            $tagListaRps .= "</InfDeclaracaoPrestacaoServico></Rps>";          
     
        $tagListaRps .= '</ListaRps>';

        $tagEnviarLoteRps = "";
        $tagEnviarLoteRps .= "<?xml version='1.0' encoding='utf-8'?>";
        $tagEnviarLoteRps .= "<EnviarLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
            $tagEnviarLoteRps .= "<LoteRps id='lote:$lote->Id'>";
                $tagEnviarLoteRps .= "<NumeroLote>".intval($lote->NumeroLote)."</NumeroLote>"; 
                $tagEnviarLoteRps .= "<CpfCnpj>";
                    $tagEnviarLoteRps .= "<Cnpj>".$prestador->Cnpj."</Cnpj>"; 
                $tagEnviarLoteRps .= "</CpfCnpj>";
                $tagEnviarLoteRps .= "<InscricaoMunicipal>".$prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
                $tagEnviarLoteRps .= "<QuantidadeRps>".intval(1)."</QuantidadeRps>";
                $tagEnviarLoteRps .= $tagListaRps; 
            $tagEnviarLoteRps .= "</LoteRps>";
        $tagEnviarLoteRps .= "</EnviarLoteRpsEnvio>";

        $dom->loadXML($tagEnviarLoteRps);
   
        return $dom->saveXML();

    }

    /**
     *  GERAR NFSE
     *  Tag GerarNfseEnvio
     */
    public function makeGerarNfse()
    {
        $this->xml = "";
        $this->xml .= "<GerarNfseEnvio>";
        $this->xml .= $this->montarInfDeclaracaoPrestacaoServico();
        $this->xml .= "</GerarNfseEnvio>";                
    }

    /** 
     * GERAR LOTE RPS 
     * Tag EnviarLoteRpsEnvio
     */
    public function makeLoteRps()
    {

    }

    /**
     *  Tag InfDeclaracaoPrestacaoServico  
     */
    private function montarInfDeclaracaoPrestacaoServico()
    {
       $xml = "";
       $xml .= "<InfDeclaracaoPrestacaoServico>";
       $xml .= $this->montaInfRps();
       $xml .= $this->montaServicos();
       $xml .="<Competencia>".$this->servico->Competencia."</Competencia>";  
       $xml .= $this->montaPrestador();
       $xml .= $this->montaTomador();
       $xml .= $this->montaIntermediario();
       $xml .= $this->montaConstrucaoCivil();
       $xml .= "<RegimeEspecialTributacao>".$this->servico->RegimeEspecialTributacao."</RegimeEspecialTributacao>"; 
       $xml .= "<OptanteSimplesNacional>".$this->servico->OptanteSimplesNacional ."</OptanteSimplesNacional>"; 
       $xml .= "<IncentivoFiscal>". $this->servico->IncentivoFiscal. "</IncentivoFiscal>";
       $xml .= "</InfDeclaracaoPrestacaoServico>";
      // $this->xml .= $this->montaDadosIntermediario();
      // $this->xml .= $this->montaDadosConstrucaoCivil();
       return $xml;
    }

    /**
     *  Tag Rps
     */
    private function montaInfRps()
    {
        $xml = "";
        $xml .= "<Rps>";
        $xml .= "<IdenticacaoRps>";
        $xml .= "<Numero>".$this->servico->Numero."</Numero>";
        $xml .= "<Serie>".$this->servico->Serie."</Serie>";
        $xml .= "<Tipo>".$this->servico->Tipo."</Tipo>";
        $xml .= "</IdenticacaoRps>";
        $xml .= "<DataEmissao>".$this->servico->DataEmissao."</DataEmissao>";
        $xml .= "<Status>".$this->servico->Status."</Status>";
        $xml .= "</Rps>";

        return $xml;
    }

    /**
    *  Tag Servicos
    */
    private function montaServicos()
    {   
        $xml = '';
        $xml .= "<Servico>";
        $xml .= $this->montaValores();
        $xml .= "<IssRetido>".$this->servico->IssRetido."</IssRetido>";
        if ($this->servico->IssRetido == 1)
        {
            $xml .= "<ResponsavelRetencao>".intval(1)."</ResponsavelRetencao>";
        }
        $xml .= "<ItemListaServico>".$this->servico->ItemListaServico."</ItemListaServico>";
        $xml .= "<Discriminacao>".htmlspecialchars($this->servico->Descriminacao)."</Discriminacao>";
        $xml .= "<CodigoMunicipio>".$this->servico->CodigoMunicipio."</CodigoMunicipio>";
        $xml .= "<ExigibilidadeISS>".$this->servico->ExigibilidadeISS."</ExigibilidadeISS>";
        $xml .= "</Servico>";
        return $xml;
    }

    /**
    *  Tag Valores
    */
    private function montaValores()
    {
        $xml = "";
        $xml .= "<Valores>";
            $xml .= "<ValorServicos>".$this->valores->ValorServicos."</ValorServicos>";           
            $xml .= "<Aliquota>".$this->valores->Aliquota."</Aliquota>";
        
            if (!empty($this->valores->ValorDeducoes)) {
                $xml .= "<ValorDeducoes>".floatval($this->valores->ValorDeducoes)."</ValorDeducoes>";
            }
            if (!empty($this->valores->ValorPis)) {
                $xml .=  "<ValorPis>".floatval($this->valores->ValorPis)."</ValorPis>";
            }
            if (!empty($this->valores->ValorCofins)) {
                $xml .=  "<ValorCofins>".$this->valores->ValorCofins."</ValorCofins>";
            }
            if (!empty($this->valores->ValorIr)) {
                $xml .=  "<ValorIr>".$this->valores->ValorIr."</ValorIr>";
            }
            if (!empty($this->valores->ValorCsll)) {
                $xml .=  "<ValorCsll>".$this->valores->ValorCsll."</ValorCsll>";
            }
            if (!empty($this->valores->ValorIss)) {
                $xml .=  "<ValorIss>".$this->valores->ValorIss."</ValorIss>";
            }
            if (!empty($this->valores->OutrasRetencoes)) {
                $xml .=  "<OutrasRetencoes>".$this->valores->OutrasRetencoes."</OutrasRetencoes>";
            }
            if (!empty($this->valores->DescontoIncondicionado)) {
                $xml .=  "<DescontoIncondicionado>".$this->valores->DescontoIncondicionado."</DescontoIncondicionado>";
            }
            if (!empty($this->valores->DescontoCondicionado)) {
                $xml .=  "<DescontoCondicionado>".$this->valores->DescontoCondicionado."</DescontoCondicionado>";
            }
        $xml .= "</Valores>";

        return $xml;
    }

    /**
    *  Tag Prestador
    */
    private function montaPrestador()
    {
        $xml = "";
        $xml .= "<Prestador>";
        $xml .= $this->montaCpfCnpj($this->prestador->Cpf, $this->prestador->Cnpj);
        $xml .= "<InscricaoMunicipal>".$this->prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
        $xml .= "</Prestador>";

        return $xml;

    }

    /**
    *  Tag Tomador
    */
    private function montaTomador()
    {
        $xml = "";
        $xml .= "<Tomador>"; 
            $xml .= "<IdentificacaoTomador>";
            $xml .= $this->montaCpfCnpj($this->tomador->Cpf, $this->tomador->Cnpj);           
            if(!empty($this->tomador->Cnpj))
            {
                $xml .= "<InscricaoMunicipal>".$this->tomador->InscricaoMunicipal."</InscricaoMunicipal>";
            }
            $xml .= "</IdentificacaoTomador>";
            $xml .= "<Endereco>";
                $xml .= "<Endereco>".$this->tomador->Endereco."</Endereco>";
                $xml .= "<Numero>".$this->tomador->Numero."</Numero>";
                $xml .= "<Bairro>".$this->tomador->Bairro."</Bairro>"; 
                $xml .= "<CodigoMunicipio>".$this->tomador->CodigoMunicipio."</CodigoMunicipio>";
                $xml .= "<Uf>".$this->tomador->Uf."</Uf>";
                $xml .= "<CodigoPais>".$this->tomador->CodigoPais."</CodigoPais>";
                $xml .=" <Cep>".$this->tomador->Cep."</Cep>";
            $xml .= "</Endereco>"; 
            $xml .= "<Contato>"; 
                $xml .= "<Telefone>".$this->tomador->Telefone."</Telefone>";
                $xml .= "<Email>".$this->tomador->Email."</Email>";                
            $xml .= "</Contato>"; 
        $xml .= "</Tomador>";
        return $xml;
    }

    private function montaIntermediario()
    {
        if (empty($this->intermediario)){
            return "";
        } else {
            $xml = "";
        }
    }

    private function montaConstrucaoCivil()
    {
        if (empty($this->construcaoCivil)){
            return "";
        } else {
            $xml = "";
            $xml = "<CodigoObra>".$this->construcaoCivil->CodigoObra."</CodigoObra>";
            $xml = "<Art>".$this->construcaoCivil->Art."</Art>";
            return $xml;
        }
    }

    private function montaCpfCnpj($cpf = "", $cnpj = "")
    {
        $xml = "";
        $xml .= "<CpfCnpj>";
        if(empty($cpf))
        {
            $xml .= "<Cnpj>".$cnpj."</Cnpj>";        
        }else{
            $xml .= "<Cpf>".$cpf."</Cpf>";
        }
        $xml .= "</CpfCnpj>";   
        
        return $xml;
    }
    
    /**
     *  Getters and Setters
     */
    public function getXml()
    {
        return $this->xml;
    }

    public function setValores($valores)
    {
        $this->valores = $valores;
    }

    public function setServico($servico)
    {
        $this->servico = $servico;
    }

    public function setTomador($tomador)
    {
        $this->tomador = $tomador;
    }

    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;
    }

}