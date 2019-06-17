<?php
namespace Click\ClickNfse\NFSE5205109;

class ConsultarNfseRps
{

    /**
     * @var stdClass
     */
    protected $identificacaoRps;

    /**
     * @var stdClass
    */
    protected $prestador;


    /**
     * @var string
     */
    protected $xml;

    public function makeConsultarNfseRps()
    {
        $this->xml = "";
        $this->xml .= "<ConsultarNfseRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>";
        $this->xml .= $this->montarIdentificacaoRps();
        $this->xml .= $this->montarPrestador();
        $this->xml .= "</ConsultarNfseRpsEnvio>";
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

    private function montarIdentificacaoRps()
    {
        $xml = "";
        $xml .= "<IdentificacaoRps>";
        $xml .= "<Numero>".$this->identificacaoRps->Numero."</Numero>";
        $xml .= "<Serie>".$this->identificacaoRps->Serie."</Serie>";
        $xml .= "<Tipo>".$this->identificacaoRps->Tipo."</Tipo>";
        $xml .= "</IdentificacaoRps>";
        return $xml;
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function setIdentificacaoRps($identificacaoRps)
    {
        $this->identificacaoRps = $identificacaoRps;
    }

    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;
    }
}


