<?php
namespace Click\ClickNfse\NFSE5205109;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
class CancelarNfse
{

    /**
     * @var stdClass
     */
    protected $pedido;

    /**
     * @var string
     */
    protected $xml;


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

    public function makeCancelarNfse()
    {
        $id = $this->pedido->Id;
        $this->xml = "";
        $this->xml .= "<CancelarNfseEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
            $this->xml .= "<Pedido>";
                $this->xml .= "<InfPedidoCancelamento Id='pedidoCancelamento_$id'>";  
                    $this->xml .= "<IdentificacaoNfse>";
                        $this->xml .= "<Numero>".$this->pedido->Numero."</Numero>";
                        $this->xml .= $this->montaCpfCnpj($this->pedido->Cpf, $this->pedido->Cnpj);
                        $this->xml .= "<InscricaoMunicipal>".$this->pedido->InscricaoMunicipal."</InscricaoMunicipal>";
                        $this->xml .= "<CodigoMunicipio>".$this->pedido->InscricaoMunicipal."</CodigoMunicipio>";
                    $this->xml .= "</IdentificacaoNfse>";
                    $this->xml .= "<CodigoCancelamento>".$this->pedido->CodigoCancelamento."</CodigoCancelamento>";
                $this->xml .= "</InfPedidoCancelamento>";    
            $this->xml .= "</Pedido>";
        $this->xml .= "</CancelarNfseEnvio>";

        $this->assinarXML($this->xml);

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

    public function getXml()
    {
        return $this->xml;
    }

    public function setPedidoCancelamento($pedido)
    {
        $this->pedido = $pedido;
    }

}


