<?php
namespace Click\ClickNfse\NFSE5205109;

class ConsultarLoteRps
{

    /**
     * @var stdClass
     */
    protected $protocolo;

    /**
     * @var stdClass
    */
    protected $prestador;


    /**
     * @var string
     */
    protected $xml;

    public function makeConsultarLoteRps()
    {
        $this->xml = "";
        $this->xml .= "<ConsultarLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $this->xml .= $this->montarPrestador();
        $this->xml .= "<Protocolo>".$this->protocolo."</Protocolo>";
        $this->xml .= "</ConsultarLoteRpsEnvio>";
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

    private function montarPrestador()
    {
        $xml = "";
        $xml .= "<Prestador>";
        $xml .= $this->montaCpfCnpj($this->prestador->Cpf, $this->prestador->Cnpj);   
        $xml .= "<InscricaoMunicipal>".$this->prestador->InscricaoMunicipal."</InscricaoMunicipal>";
        $xml .= "</Prestador>";
        return $xml;
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function setProtocolo($protocolo)
    {
        $this->protocolo = $protocolo;
    }

    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;
    }
}


