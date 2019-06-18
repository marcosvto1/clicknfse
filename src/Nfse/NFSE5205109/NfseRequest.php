<?php 

namespace Click\ClickNfse\NFSE5205109;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class NfseRequest
{
    private $xmlns = 'http://www.abrasf.org.br/nfse.xsd';

    /**
     *  @var DomElement
     */
    public $xml;

    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    public function GerarNfseEnvio()
    {
        $action = "GerarNfse";
        $envelope = $this->makeEnvolepe($action, $this->xml);
        $response = $this->enviar($action, $envelope);   
        return $response;
    }

    public function EnviarLoteRpsEnvio()
    {
        $action = "RecepcionarLoteRps";
        $envelope = $this->makeEnvolepe($action, $this->xml);
        $response = $this->enviar($action, $envelope);
        return $response;
    }

    public function ConsultarNfseRpsEnvio()
    {
        $action = "ConsultarNfsePorRps";
        $envelope = $this->makeEnvolepe($action, $this->xml);
        $response = $this->enviar($action, $envelope);
        return $response; 
    }

    public function ConsultarLoteRpsEnvio()
    {
        $action = "ConsultarLoteRps";
        $envelope = $this->makeEnvolepe($action, $this->xml);
        $response = $this->enviar($action, $envelope);
        return $response; 
    }

    public function CancelarNfseEnvio()
    {
        $action = "CancelarNfse";
        $envelope = $this->makeEnvolepe($action, $this->xml);
        $response = $this->enviar($action, $envelope);
        return $response; 
    }

    private function returnRequest($xmlFile)
    {
        $DOMDocument = new \DOMDocument( '1.0', 'UTF-8' );
        $DOMDocument->preserveWhiteSpace = false;
        try
        {
            $DOMDocument->loadXML( $xmlFile );

            if ($this->isValidXml($DOMDocument->textContent))
            {
                $arrxml = simplexml_load_string($DOMDocument->textContent);
                return $arrxml;
            } else 
            {
                return [
                'cstatus' => 001,
                'dados' => [
                    'mensagem' => $DOMDocument->textContent
                ]];
            }

        }
        catch (\Exception $exception)
        {
            return [
                'cstatus' => 001, 
                'dados' => 
                [['mensagem' => $exception->getMessage()]]];
        }
   
    }
    
    private function enviar($action, $envelope)
    {
        $url = 'http://200.23.238.210:8585/prodataws/services/NfseWSService';

        $headers = array(
            "Content-type: text/xml; charset=utf-8",
            "OAPAction: $action",
            "Content-length: ".strlen($envelope),
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $envelope);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $html="";
        $html = curl_exec($ch);
        curl_close($ch);
        return $this->returnRequest($html);
    }

    private function makeEnvolepe($action, $message){
        $xml = $this->makeDados($message);
        $cabecalho = $this->makeCabecalho();
        $envelope =
            "<?xml version='1.0' encoding='UTF-8'?>
        <x:Envelope xmlns:x='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://services.nfse'>
            <x:Header/>
            <x:Body>
                <ser:".$action."Request>
                    <nfseCabecMsg>".$cabecalho."</nfseCabecMsg>
                    <nfseDadosMsg>".$xml."</nfseDadosMsg>
                </ser:".$action."Request>
            </x:Body>
        </x:Envelope>";

        return $envelope;
    }

    private function makeCabecalho(){

        $versao = 2.01;
        $cabecalho =
            "<![CDATA["
            . "<cabecalho xmlns='$this->xmlns' versao='$versao'><versaoDados>$versao</versaoDados></cabecalho>"
        . "]]>";

        return $cabecalho;
    }

    private function makeDados($message){
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($message);

        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        $dados = "<![CDATA["
        . $message
        . ']]>';

        return $dados;
    }

    private function isValidXml($content)
    {
        $content = trim($content);
        if (empty($content)) {
            return false;
        }
        //html go to hell!
        if (stripos($content, '<!DOCTYPE html>') !== false) {
            return false;
        }

        libxml_use_internal_errors(true);
        simplexml_load_string($content);
        $errors = libxml_get_errors();          
        libxml_clear_errors();  

        return empty($errors);
    }

}