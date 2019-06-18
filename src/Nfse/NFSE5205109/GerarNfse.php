<?php 

namespace Click\ClickNfse\NFSE5205109;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use InvalidArgumentException;
class GerarNfse
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
    protected $urlWebService = 'http://200.23.238.210/prodataws/services/NfseWSService';


    /**
     *  @var string
     */
    protected $certificado;


    function __construct($tools)
    {
        if(is_file($tools['certificado'])) {
            $this->certificado = $tools['certificado'];
        }
    }

    private function assinarXML($xml)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        // Create a new Security object
        $objDSig = new XMLSecurityDSig();
        // Use the c14n exclusive canonicalization
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        // Sign using SHA-1
        $objDSig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315'),
            ['force_uri' => true]
        );

        // Create a new (private) Security key
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
        /*
        If key has a passphrase, set it using
        $objKey->passphrase = '<passphrase>';
        */
        // Load the private key
        $objKey->loadKey($this->certificado, TRUE);

        // Sign the XML file
        $objDSig->sign($objKey);

        // Add the associated public key to the signature
        $objDSig->add509Cert(file_get_contents($this->certificado));

        // Append the signature to the XML
        $objDSig->appendSignature($doc->documentElement);
        // Save the signed XML
        $xmlAss =  $doc->saveXML();

        $this->xml = $xmlAss;
    }

    /**
     *  GERAR NFSE
     *  Tag GerarNfseEnvio
     */
    public function makeGerarNfse()
    {
        $this->xml = "";
        $this->xml .= "<GerarNfseEnvio>";
        $this->xml .= "<Rps>";
        $this->xml .= $this->montarInfDeclaracaoPrestacaoServico();
        $this->xml .= "</Rps>";
        $this->xml .= "</GerarNfseEnvio>";     
        $this->assinarXML($this->xml);           
    }

    /** 
     * GERAR LOTE RPS 
     * Tag EnviarLoteRpsEnvio
     */
    public function makeLoteRps()
    {
        $this->xml = "";
        $this->xml .= "<EnviarLoteRpsEnvio>";
        $this->xml .= "<LoteRps>";
        $this->xml .= "<NumeroLote>".$lote."</NumeroLote>"; 
        $this->xml .= "<CpfCnpj>";
        $this->xml .= "<Cnpj>".$prestador->Cnpj."</Cnpj>"; 
        $this->xml .= "</CpfCnpj>";
        $this->xml .= "<InscricaoMunicipal>".$prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
        $this->xml .= "<QuantidadeRps>".$qtdeRps."</QuantidadeRps>";
        $this->xml .= $this->montarListaRps() ;
        $this->xml .= "</LoteRps>";
        $this->xml .= "</EnviarLoteRpsEnvio>";
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

       return $xml;
    }

    /**
     *  Tag Rps
     */
    private function montaInfRps()
    {
        $id = 'R'.$this->servico->Numero;
        $xml = "";
        $xml .= "<Rps>";
        $xml .= "<IdenticacaoRps Id='$id'>";
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
        if ($this->servico->IssRetido == 1) {
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