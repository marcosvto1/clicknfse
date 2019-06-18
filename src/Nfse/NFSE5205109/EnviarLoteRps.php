<?php 

namespace Click\ClickNfse\NFSE5205109;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use InvalidArgumentException;

class EnviarLoteRps
{
    private $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     *  @var DomElement
     */
    public $xml;

    /**
     * @var stdClass
     */
    protected $lote;
    /**
     * @var stdClass      
     */
    protected $valores;

    /**
     * @var stdClass      
     */
    protected $servicos;

    /**
     * @var stdClass      
     */
    protected $prestador;

    /**
     * @var stdClass      
     */
    protected $tomadores;

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

    /** 
     * GERAR LOTE RPS 
     * Tag EnviarLoteRpsEnvio
     */
    public function makeLoteRps()
    {
        $id = 'lote:'.$this->lote->NumeroLote;
        $this->xml = "";
        $this->xml .= "<EnviarLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $this->xml .= "<LoteRps Id='$id' versao='2.01'>";
        $this->xml .= "<NumeroLote>".$this->lote->NumeroLote."</NumeroLote>"; 
        $this->xml .= "<CpfCnpj>";
        $this->xml .= "<Cnpj>".$this->prestador->Cnpj."</Cnpj>"; 
        $this->xml .= "</CpfCnpj>";
        $this->xml .= "<InscricaoMunicipal>".$this->prestador->InscricaoMunicipal."</InscricaoMunicipal>"; 
        $this->xml .= "<QuantidadeRps>".$this->lote->QuantidadeRps."</QuantidadeRps>";
        $this->xml .= $this->montarListaRps() ;
        $this->xml .= "</LoteRps>";
        $this->xml .= "</EnviarLoteRpsEnvio>";

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($this->xml);
        $xml = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        $this->assinarXML($xml);
    }

    private function montarListaRps()
    {
        $xml = '';
        $xml .= "<ListaRps>";
        $xml .= $this->montarInfDeclaracaoPrestacaoServico();
        $xml .= "</ListaRps>";

        return $xml;
    }
    

    /**
     *  Tag InfDeclaracaoPrestacaoServico  
     */
    private function montarInfDeclaracaoPrestacaoServico()
    {
       $xml = "";
       for($i = 0; $i < $this->lote->QuantidadeRps; $i++) {
            $xml .= "<Rps>";
            $xml .= "<InfDeclaracaoPrestacaoServico>";
            $xml .= $this->montaInfRps($i);
            $xml .= $this->montaServicos($i);
            $xml .="<Competencia>".$this->servicos[$i]->Competencia."</Competencia>";  
            $xml .= $this->montaPrestador();
            $xml .= $this->montaTomador($i);
            $xml .= $this->montaIntermediario($i);
            $xml .= $this->montaConstrucaoCivil($i);
            $xml .= "<RegimeEspecialTributacao>".$this->servicos[$i]->RegimeEspecialTributacao."</RegimeEspecialTributacao>"; 
            $xml .= "<OptanteSimplesNacional>".$this->servicos[$i]->OptanteSimplesNacional ."</OptanteSimplesNacional>"; 
            $xml .= "<IncentivoFiscal>". $this->servicos[$i]->IncentivoFiscal. "</IncentivoFiscal>";
            $xml .= "</InfDeclaracaoPrestacaoServico>";
            $xml .= "</Rps>";
       }

       return $xml;
    }

    /**
     *  Tag Rps
     */
    private function montaInfRps($index)
    {
        $xml = "";
        $xml .= "<Rps>";
        $xml .= "<IdentificacaoRps>";
        $xml .= "<Numero>".$this->servicos[$index]->Numero."</Numero>";
        $xml .= "<Serie>".$this->servicos[$index]->Serie."</Serie>";
        $xml .= "<Tipo>".$this->servicos[$index]->Tipo."</Tipo>";
        $xml .= "</IdentificacaoRps>";
        $xml .= "<DataEmissao>".$this->servicos[$index]->DataEmissao."</DataEmissao>";
        $xml .= "<Status>".$this->servicos[$index]->Status."</Status>";
        $xml .= "</Rps>";

        return $xml;
    }

    /**
    *  Tag Servicos
    */
    private function montaServicos($index)
    {   
        $xml = '';
        $xml .= "<Servico>";
        $xml .= $this->montaValores($index);
        $xml .= "<IssRetido>".$this->servicos[$index]->IssRetido."</IssRetido>";
        if ($this->servicos[$index]->IssRetido == 1) {
            $xml .= "<ResponsavelRetencao>".intval(1)."</ResponsavelRetencao>";
        }
        $xml .= "<ItemListaServico>".$this->servicos[$index]->ItemListaServico."</ItemListaServico>";
        $xml .= "<Discriminacao>".htmlspecialchars($this->servicos[$index]->Descriminacao)."</Discriminacao>";
        if ($this->servicos[$index]->CodigoMunicipio) {
            $xml .= "<CodigoMunicipio>".$this->servicos[$index]->CodigoMunicipio."</CodigoMunicipio>";
        }
        $xml .= "<ExigibilidadeISS>".$this->servicos[$index]->ExigibilidadeISS."</ExigibilidadeISS>";
        $xml .= "</Servico>";

        return $xml;
    }

    /**
    *  Tag Valores
    */
    private function montaValores($index)
    {
        $xml = "";
        $xml .= "<Valores>";
            $xml .= "<ValorServicos>".$this->valores[$index]->ValorServicos."</ValorServicos>";           
            $xml .= "<Aliquota>".$this->valores[$index]->Aliquota."</Aliquota>";
        
            if (!empty($this->valores[$index]->ValorDeducoes)) {
                $xml .= "<ValorDeducoes>".floatval($this->valores[$index]->ValorDeducoes)."</ValorDeducoes>";
            }
            if (!empty($this->valores[$index]->ValorPis)) {
                $xml .=  "<ValorPis>".floatval($this->valores[$index]->ValorPis)."</ValorPis>";
            }
            if (!empty($this->valores[$index]->ValorCofins)) {
                $xml .=  "<ValorCofins>".$this->valores[$index]->ValorCofins."</ValorCofins>";
            }
            if (!empty($this->valores[$index]->ValorIr)) {
                $xml .=  "<ValorIr>".$this->valores[$index]->ValorIr."</ValorIr>";
            }
            if (!empty($this->valores[$index]->ValorCsll)) {
                $xml .=  "<ValorCsll>".$this->valores[$index]->ValorCsll."</ValorCsll>";
            }
            if (!empty($this->valores[$index]->ValorIss)) {
                $xml .=  "<ValorIss>".$this->valores[$index]->ValorIss."</ValorIss>";
            }
            if (!empty($this->valores[$index]->OutrasRetencoes)) {
                $xml .=  "<OutrasRetencoes>".$this->valores[$index]->OutrasRetencoes."</OutrasRetencoes>";
            }
            if (!empty($this->valores[$index]->DescontoIncondicionado)) {
                $xml .=  "<DescontoIncondicionado>".$this->valores[$index]->DescontoIncondicionado."</DescontoIncondicionado>";
            }
            if (!empty($this->valores[$index]->DescontoCondicionado)) {
                $xml .=  "<DescontoCondicionado>".$this->valores[$index]->DescontoCondicionado."</DescontoCondicionado>";
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
    private function montaTomador($index)
    {
        $xml = "";
        $xml .= "<Tomador>"; 
            $xml .= "<IdentificacaoTomador>";
            $xml .= $this->montaCpfCnpj($this->tomador[$index]->Cpf, $this->tomador[$index]->Cnpj);           
            if(!empty($this->tomador[$index]->Cnpj))
            {
                $xml .= "<InscricaoMunicipal>".$this->tomador[$index]->InscricaoMunicipal."</InscricaoMunicipal>";
            }
            $xml .= "</IdentificacaoTomador>";
            $xml .= "<RazaoSocial>".$this->tomador[$index]->RazaoSocial."</RazaoSocial>";
            $xml .= "<Endereco>";
                $xml .= "<Endereco>".$this->tomador[$index]->Endereco."</Endereco>";
                $xml .= "<Numero>".$this->tomador[$index]->Numero."</Numero>";
                $xml .= "<Bairro>".$this->tomador[$index]->Bairro."</Bairro>"; 
                $xml .= "<CodigoMunicipio>".$this->tomador[$index]->CodigoMunicipio."</CodigoMunicipio>";
                $xml .= "<Uf>".$this->tomador[$index]->Uf."</Uf>";
                $xml .= "<CodigoPais>".$this->tomador[$index]->CodigoPais."</CodigoPais>";
                $xml .=" <Cep>".$this->tomador[$index]->Cep."</Cep>";
            $xml .= "</Endereco>"; 
            $xml .= "<Contato>"; 
                $xml .= "<Telefone>".$this->tomador[$index]->Telefone."</Telefone>";
                $xml .= "<Email>".$this->tomador[$index]->Email."</Email>";                
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

    private function montaConstrucaoCivil($index)
    {
        if (empty($this->construcaoCivil)){
            return "";
        } else {
            $xml = "";
            $xml = "<CodigoObra>".$this->construcaoCivil[$index]->CodigoObra."</CodigoObra>";
            $xml = "<Art>".$this->construcaoCivil[$index]->Art."</Art>";
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
     *  Getters and Setters
     */
    public function getXml()
    {
        return $this->xml;
    }

    public function setValores($valores)
    {   
        foreach($valores as $valor)
        {
            $params_required = [
                'ValorServicos',
                'Aliquota'
            ];

            $this->checkParamenters($valor, $params_required);
        }
        $this->valores = $valores;
    }

    public function setServicos($servicos)
    {
        foreach($servicos as $servico) 
        {
            $params_required = [                
                'Numero',
                'Serie',
                'Tipo',
                'Status',
                'Competencia',
                'DataEmissao',
                'RegimeEspecialTributacao',
                'OptanteSimplesNacional',
                'IncentivoFiscal',
                'IssRetido',
                'ItemListaServico',
                'Descriminacao',
                'CodigoMunicipio',
                'ExigibilidadeISS'
            ];

            $this->checkParamenters($servico, $params_required);
        }

        $this->servicos = $servicos;
    }

    public function setTomador($tomadores)
    {
        foreach($tomadores as $tomador) {

            if (key_exists('Cpf', $tomador) && empty($tomador->Cpf))
            {
                $params_required = [
                    'Cnpj',
                    'InscricaoMunicipal',
                    'RazaoSocial',
                    'Endereco',
                    'Bairro',
                    'Numero',
                    'CodigoMunicipio',
                    'Uf',
                    'Cep'
                ];
            } else {

                $params_required = [
                    'Cpf',
                    'RazaoSocial',
                    'Endereco',
                    'Bairro',
                    'Numero',
                    'CodigoMunicipio',
                    'Uf',
                    'Cep'
                ];
            }
              
            $this->checkParamenters($tomador, $params_required);

        }

        $this->tomador = $tomadores;

    }

    public function setPrestador($prestador)
    {   

        if (key_exists('Cpf', $prestador) && empty($prestador->Cpf))
        {
            $params_required = [
                'Cnpj',
                'InscricaoMunicipal',
            ];
        } else {
            $params_required = [
                'Cpf',
                'InscricaoMunicipal',
            ]; 
        }

        $this->checkParamenters($prestador, $params_required);
        $this->prestador = $prestador;
    }

    public function setLote($lote){

        $params_required = [
            'NumeroLote',
            'QuantidadeRps',
        ];

        $this->checkParamenters($lote, $params_required);
        $this->lote = $lote;
    }

    private function checkParamenters($obj,$params_required)
    {
        foreach($params_required as $var)
        {
            if (!key_exists($var, $obj)){
                throw new InvalidArgumentException('Parâmentro não encontrado:'.$var);
            } else if (empty($obj->$var)) {
                throw new InvalidArgumentException('Parâmentro : '. $var);
            }       
        }
        
    }

}