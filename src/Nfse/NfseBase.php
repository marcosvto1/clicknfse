<?php

namespace Click\ClickNfse;

class NfseBase
{

    /**
     *  @var DomElement
     */
    public $dom;

    /**
     * @var DOMElement
     */
    protected $NFSe;

    /**
     *  @var DOMElement
     */
    protected $LoteRps;

    /**
     * Função construtora cria um objeto DOMDocument
     * que será carregado com o documento fiscal
     */
    public function __construct()
    {
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
    }

    public function montaNfseLote()
    {
        $this->buildNFSe();
    }


    /**
     * Tag LoteRps
     */
    public function tagLoteRps(stdClass $std)
    {
        $possible = 
        [
            'Id',
            'NumeroLote', 
            'CpfCnpj', 
            'InscricaoMunicipal', 
            'QuantidadeRps'
        ];
        $std = $this->equilizeParameters($std, $possible);

        $id = preg_replace('/[^0-9]/', '', $std->Id);
        $this->LoteRps = $this->dom->createElement("LoteRps");
        $this->LoteRps->setAttribute("Id", $id);

        return $this->LoteRps;
    }

    /**
     * Tag raiz da NFSe
     * tag EnviarLoteRpsEnvio DOMNode
     * Função chamada pelo método [ montaNfseLote ]
     *
     * @return DOMElement
     */
    protected function buildNFSe()
    {
        if (empty($this->NFSe)) {
            $this->NFSe = $this->dom->createElement("EnviarLoteRpsEnvio");
            $this->NFSe->setAttribute("xmlns", "http://www.abrasf.org.br/nfse.xsd");
        }
        return $this->NFSe;
    }

    function gerarRPS($lote, $prestador, $servicos){

        $lista_rps = '<ListaRps>';
        foreach ($servicos as $servico){
            $lista_rps .= "<Rps> 
            <InfDeclaracaoPrestacaoServico> 
              <Rps> 
                <IdentificacaoRps> 
                  <Numero>$servico->Numero</Numero> 
                  <Serie>$servico->Serie</Serie> 
                  <Tipo>$servico->Tipo</Tipo> 
                </IdentificacaoRps> 
                <DataEmissao>$servico->DataEmissao</DataEmissao> 
                <Status>$servico->Status</Status>
              </Rps> 
              <Competencia>$servico->Competencia</Competencia> 
              <Servico> 
                <Valores>
                  <ValorServicos>$servico->ValorServicos</ValorServicos> 
                  <Aliquota>$servico->Aliquota</Aliquota>
                </Valores> 
                <IssRetido>$servico->IssRetido</IssRetido>";
                if($servico->IssRetido == 1) {
                    $lista_rps .= "<ResponsavelRetencao>1</ResponsavelRetencao>";
                }
                $lista_rps .= "<ItemListaServico>$servico->ItemListaServico</ItemListaServico> 
                <Discriminacao>".htmlspecialchars($servico->Descriminacao)."\n\n"."obs: ".htmlspecialchars($servico->OutrasInformacoes)."</Discriminacao> 
                <CodigoMunicipio>$servico->CodigoMunicipio</CodigoMunicipio> 
                <ExigibilidadeISS>$servico->ExigibilidadeIss</ExigibilidadeISS> 
              </Servico> 
              <Prestador> 
                <CpfCnpj> 
                  <Cnpj>$servico->prestador_Cnpj</Cnpj> 
                </CpfCnpj> 
                <InscricaoMunicipal>$servico->prestador_InscricaoMunicipal</InscricaoMunicipal> 
              </Prestador> 
              <Tomador> 
                <IdentificacaoTomador> 
                  <CpfCnpj>";
                    if(strlen($servico->tomador_CpfCnpj) >= 14){
                        $lista_rps .= "<Cnpj>$servico->tomador_CpfCnpj</Cnpj>";

                    }else{
                        $lista_rps .= "<Cpf>$servico->tomador_CpfCnpj</Cpf>";
                    }
                   $lista_rps .="</CpfCnpj>";
                     if(strlen($servico->tomador_CpfCnpj) >= 14){
                           $lista_rps .= "<InscricaoMunicipal>$servico->tomador_Im</InscricaoMunicipal>";
                    }
                    $lista_rps .= "
                </IdentificacaoTomador> 
                <RazaoSocial>".htmlspecialchars($servico->tomador_RazaoSocial)."</RazaoSocial> 
                <Endereco> 
                  <Endereco>$servico->tomador_Endereco</Endereco> 
                  <Numero>$servico->tomador_Numero</Numero> 
                  <Bairro>$servico->tomador_Bairro</Bairro> 
                  <CodigoMunicipio>$servico->tomador_CodigoMunicipio</CodigoMunicipio> 
                  <Uf>$servico->tomador_Uf</Uf> 
                  <CodigoPais>$servico->tomador_CodigoPais</CodigoPais> 
                  <Cep>$servico->tomador_Cep</Cep> 
                </Endereco> 
                <Contato> 
                  <Telefone></Telefone>";
                     if ($servico->tomador_Email != '' && $servico->tomador_Email != null) {
                         $lista_rps .= "
                                <Email>$servico->tomador_Email</Email>";
                     }
                  $lista_rps .= "
                </Contato> 
              </Tomador> 
              <RegimeEspecialTributacao>$servico->RegimeEspecialTributacao</RegimeEspecialTributacao> 
              <OptanteSimplesNacional>$servico->OptanteSimplesNacional</OptanteSimplesNacional> 
              <IncentivoFiscal>$servico->IncentivoFiscal</IncentivoFiscal> 
            </InfDeclaracaoPrestacaoServico>      
          </Rps>";
        }

        $lista_rps .= '</ListaRps>';

       // $rps_item_signed = $this->assinarXML($lista_rps);
        //file_put_contents('xml/rps_item_signed.xml', $rps_item_signed);
       // $xml_item_rps = file_get_contents('xml/rps_item_signed.xml');

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($lista_rps);
        $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());

        $quantidade_servicos = count($servicos);

        $xml = "<?xml version='1.0' encoding='utf-8'?>
 <EnviarLoteRpsEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'> 
   <LoteRps Id='$lote' versao='2.01'> 
     <NumeroLote>$lote</NumeroLote> 
     <CpfCnpj> 
       <Cnpj>$prestador->Cnpj</Cnpj> 
     </CpfCnpj> 
     <InscricaoMunicipal>$prestador->InscricaoMunicipal</InscricaoMunicipal> 
     <QuantidadeRps>$quantidade_servicos</QuantidadeRps>   
        $message 
  </LoteRps>
 </EnviarLoteRpsEnvio>";


        $xml_assinado = $this->assinarXML($xml);
        if (is_file('xml/rps_signed.xml')){
            unlink('xml/rps_signed.xml');
        }


        $path_xml = 'xml/gerados/'.$lote.'_rps_signed.xml';

        if($this->saveXML('xml/gerados/'.$lote.'_rps_signed.xml', $xml_assinado)){
            return $path_xml;
        }else{
            return null;
        }

        //TRAMITAR XML
      //  $envelope = $this->makeEnvolepe('RecepcionarLoteRps', $xml_assinado);
       // $dadosRetorno = $this->tramite('RecepcionarLoteRps', $envelope);



    }

    function enviarRpsWebService($xml){
        $envelope = $this->makeEnvolepe('RecepcionarLoteRps', $xml);
        $dadosRetorno = $this->tramite('RecepcionarLoteRps', $envelope);
        return $dadosRetorno;
    }

    function consultarRps($rps, $prestador){
        $xml = "<?xml version='1.0' encoding='UTF-8'?>
<ConsultarNfseRpsEnvio xmlns=\"http://www.abrasf.org.br/nfse.xsd\">
	<IdentificacaoRps>
		<Numero>$rps->Numero</Numero>
		<Serie>$rps->Serie</Serie>
		<Tipo>$rps->Tipo</Tipo>
	</IdentificacaoRps>
	<Prestador>
		<CpfCnpj>
			<Cnpj>$prestador->Cnpj</Cnpj>
		</CpfCnpj>
		<InscricaoMunicipal>$prestador->InscricaoMunicipal</InscricaoMunicipal>
	</Prestador>
</ConsultarNfseRpsEnvio>";

        $env_xml =  $this->makeEnvolepe("ConsultarNfsePorRps", $xml);
        return $this->tramite('ConsultarNfsePorRps', $env_xml);
    }

    function consultarLoteRps($protocolo, $prestador){
       $xml = "<?xml version='1.0' encoding='UTF-8'?>
        <ConsultarLoteRpsEnvio xmlns=\"http://www.abrasf.org.br/nfse.xsd\">
            <Prestador>
                <CpfCnpj>
                    <Cnpj>$prestador->Cnpj</Cnpj>
                </CpfCnpj>
                <InscricaoMunicipal>$prestador->InscricaoMunicipal</InscricaoMunicipal>
            </Prestador>
            <Protocolo>$protocolo</Protocolo>
        </ConsultarLoteRpsEnvio>";

        $this->saveXML('xml/xml_request/consultaLoteRps.xml', $xml);

        $env_xml =  $this->makeEnvolepe("ConsultarLoteRps", $xml);

        $this->saveXML('xml/xml_request/consultaLoteRpsEnv.xml', $env_xml);

        return $this->tramite('ConsultarLoteRps', $env_xml);
    }

    function cancelarRps($codigoCancelamento, $numeroNotaFiscal, $prestador ){
        $xml = "<CancelarNfseEnvio xmlns='http://www.abrasf.org.br/nfse.xsd'>
          <Pedido>
            <InfPedidoCancelamento Id='pedidoCancelamento_$codigoCancelamento'>        
              <IdentificacaoNfse>
                <Numero>$numeroNotaFiscal</Numero>
                <CpfCnpj>
                  <Cnpj>$prestador->Cnpj</Cnpj>
                </CpfCnpj>
                <InscricaoMunicipal>$prestador->InscricaoMunicipal</InscricaoMunicipal>
                <CodigoMunicipio>$prestador->CodigoMunicipio</CodigoMunicipio>
              </IdentificacaoNfse>
              <CodigoCancelamento>$codigoCancelamento</CodigoCancelamento>
            </InfPedidoCancelamento>
          </Pedido>
        </CancelarNfseEnvio>";

        //ASSINAR XML
        $xml = $this->assinarXML($xml);

        $envelope = $this->makeEnvolepe('CancelarNfse',$xml);
        return $this->tramite('CancelarNfse', $envelope);

       // header("Content-type: text/xml");
      //  echo $xml;
    }

    function checkStatusWebService() {
        $url = 'http://187.111.62.1:8585/prodataws/services/NfseWSServssice?wsdl';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $html="";
        $html = curl_exec($ch);
        var_dump($html);
    }


    //FUNCOES PARA CONTROLE E INSTREGRACAO WEBSERVICE PREFEITURA

    private function assinarXML($xml){
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
                $objKey->loadKey(dirname(__FILE__).'/certificado/certificado.pem', TRUE);

        // Sign the XML file
                $objDSig->sign($objKey);

        // Add the associated public key to the signature
                $objDSig->add509Cert(file_get_contents(dirname(__FILE__).'/certificado/certificado.pem'));

        // Append the signature to the XML
                $objDSig->appendSignature($doc->documentElement);
        // Save the signed XML
                $xmlAss =  $doc->saveXML();

                return $xmlAss;
    }

    private function tramite($action, $envelope){
        $url = 'http://200.23.238.210/prodataws/services/NfseWSService';

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
        ////soapenv:ServerRecepcionarLoteRpsResponse cannot be null!
        if ($html == 'soapenv:ServerRecepcionarLoteRpsResponse cannot be null'){
            return ['error' => 1, 'dados' => [['Mensagem' => 'Errão da porra']]];
        }
        //$nome_arquivo = $action.'_'.uniqid(time()).'.xml';
        $nome_arquivo = $action.'.xml';
        $this->saveXML('xml/xml_retorno/'.$nome_arquivo, $html);
        curl_close($ch);
        return $this->retorno($action, $html ,null);
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

    private function saveXML($filename, $xml){
        try{
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;
            $dom->loadXML($xml);
            $message = str_replace('<?xml version="1.0"?>', '', $dom->saveXML());
            file_put_contents($filename, $xml);
            return true;
        }catch (\Exception $ex){
            return false;
        }

    }

    function retorno($action, $dadosRetornno = null, $responseWebService){
        if($action == 'RecepcionarLoteRps') {
            $objectResponse = null;
            $errors = [];
            if($dadosRetornno){
                $objectResponse = $this->parseReturnObject($dadosRetornno);
            }else{
                $responseWebService = file_get_contents('xml/RecepcionarLoteRps.xml');
                $objectResponse = $this->parseReturnObject($responseWebService);
            }

            if(is_array($objectResponse)) {
                return $objectResponse;
            }

            if(property_exists($objectResponse, 'ListaMensagemRetorno')){
               //$errors[] = $objectResponse->ListaMensagemRetorno;
               foreach ($objectResponse->ListaMensagemRetorno->MensagemRetorno as $item){
                   $errors[] = $item;
               }
                return ['error' => 1, 'dados' => $errors];
            }else{
                return ['error' => 0 , 'dados' => $objectResponse];
            }

        }else if($action == 'ConsultarNfsePorRps') {
            //$responseWebService = file_get_contents('xml/ConsultarNfsePorRps.xml');
            $objectResponse = $this->parseReturnObject($dadosRetornno);
            if(is_array($objectResponse)) {
                return $objectResponse;
            }
            if(property_exists($objectResponse, 'ListaMensagemRetorno')){
                //$errors[] = $objectResponse->ListaMensagemRetorno;
                foreach ($objectResponse->ListaMensagemRetorno->MensagemRetorno as $item){
                    $errors[] = $item;
                }
                return ['error' => 1, 'dados' => $errors];
            }else{
                return ['error' => 0, 'dados' => $objectResponse->CompNfse->Nfse->InfNfse];
            }
           // echo json_encode($objectResponse->CompNfse->Nfse->InfNfse);

        }else if($action == 'ConsultarLoteRps'){
            //$responseWebService = file_get_contents('xml/ConsultarLoteRps_15453263765c1bcf2891d21.xml');
            $objectResponse = $this->parseReturnObject($dadosRetornno);
            if(is_array($objectResponse)) {
                return $objectResponse;
            }
            if(property_exists($objectResponse, 'ListaMensagemRetorno')){
                //$errors[] = $objectResponse->ListaMensagemRetorno;
                if(property_exists($objectResponse->ListaMensagemRetorno, 'MensagemRetorno')){
                   foreach ($objectResponse->ListaMensagemRetorno->MensagemRetorno as $item){
                        $errors[] = $item;
                    }
                    return ['error' => 1, 'dados' => $errors];
                }else{
                    return ['error' => 0, 'dados' => $objectResponse];
                }

            }else{
                return ['error' => 0, 'dados' => $objectResponse];
            }

        }else if($action == 'CancelarNfse'){
            //$dadosRetornno = file_get_contents('xml/xml_retorno/CancelarNfse_15481791875c4756f3dfbe0.xml');
            $objectResponse = $this->parseReturnObject($dadosRetornno);

            if(is_array($objectResponse)) {
                return $objectResponse;
            }

            if(property_exists($objectResponse, 'ListaMensagemRetorno')){
                //$errors[] = $objectResponse->ListaMensagemRetorno;
                foreach ($objectResponse->ListaMensagemRetorno->MensagemRetorno as $item){
                    $errors[] = $item;
                }
                return ['error' => 1, 'dados' => $errors];
            }else{
                return ['error' => 0, 'dados' => $objectResponse->RetCancelamento->NfseCancelamento->Confirmacao->Pedido->InfPedidoCancelamento];
            }
        }
    }

    private function parseReturnObject($xmlFile){

        $DOMDocument = new \DOMDocument( '1.0', 'UTF-8' );
        $DOMDocument->preserveWhiteSpace = false;
        try{
            $DOMDocument->loadXML( $xmlFile );
        }catch (\Exception $exception){
            return ['error' => 1, 'dados' => [['Mensagem' => $exception->getMessage()]]];
        }

        try{

            $arrxml = simplexml_load_string($DOMDocument->textContent);
            return $arrxml;
        }catch (\Exception $ex){
            return ['error' => 1, 'dados' => [['Mensagem' => $ex->getMessage()]]];
        }


    }

    // Funcoes usadas pra testes

    function test(){
        $xmlfile = file_get_contents('xml/ConsultarNfsePorRps.xml');
        $DOMDocument = new DOMDocument( '1.0', 'UTF-8' );
        $DOMDocument->preserveWhiteSpace = false;
        $DOMDocument->loadXML( $xmlfile );
      //  $out = $DOMDocument->getElementsByTagNameNS('ns1','ConsultarNfsePorRpsResponse');
      ////  var_dump($out);
      // exit;

      // header("Content-type: text/xml");
        $nxml = $DOMDocument->saveXML();
        $xml = $nxml;
       // echo $xml;
        $DOMDocument = new DOMDocument( '1.0', 'UTF-8' );
        $DOMDocument->preserveWhiteSpace = false;
        $DOMDocument->loadXML( $xml);
        $arrxml = simplexml_load_string($DOMDocument->textContent)->CompNfse->Nfse->InfNfse;
        var_dump($arrxml);


        //$
            //var_dump($env);
        //$xml = simplexml_load_file($xmlfile)->Envelope;
       // var_dump($xml);
     //   foreach($xml -> item as $item){ //faz o loop nas tag com o nome "item"
            //exibe o valor das tags que estão dentro da tag "item"
            //utilizamos a função "utf8_decode" para exibir os caracteres corretamente
           // var_dump($item);
      //  } //fim do foreach
    }

    private function send($xml){
        $xmlAssinado = $this->makeDados($xml);
        $cabecalho = $this->makeCabecalho();

        echo $xmlAssinado;

        $envelope =
            "<?xml version='1.0' encoding='UTF-8'?>
        <x:Envelope xmlns:x='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ser='http://services.nfse'>
            <x:Header/>
            <x:Body>
                <ser:RecepcionarLoteRpsRequest>
                    <nfseCabecMsg>".$cabecalho."</nfseCabecMsg>
                    <nfseDadosMsg>".$xmlAssinado."</nfseDadosMsg>
                </ser:RecepcionarLoteRpsRequest>
            </x:Body>
        </x:Envelope>";

        $url = 'http://187.111.62.1:8585/prodataws/services/NfseWSService';

        $headers = array(
            "Content-type: text/xml; charset=utf-8",
            "OAPAction: RecepcionarLoteRps",
            "Content-length: ".strlen($envelope),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $envelope);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $html = utf8_decode(curl_exec($ch));
        header("Content-type: text/xml");
        echo $html;
        exit;

        file_put_contents("xmlass_retorno.xml", $html);
        curl_close($ch);
    }



    /**
     * Includes missing or unsupported properties in stdClass
     * @param stdClass $std
     * @param array $possible
     * @return stdClass
     */
    protected function equilizeParameters(stdClass $std, $possible)
    {
        $arr = get_object_vars($std);
        foreach ($possible as $key) {
            if (!array_key_exists($key, $arr)) {
                $std->$key = null;
            } else {
                if (is_string($std->$key)) {
                    $std->$key = trim(Strings::replaceUnacceptableCharacters($std->$key));
                    if ($this->replaceAccentedChars) {
                        $std->$key = Strings::toASCII($std->$key);
                    }
                }
            }
        }
        return $std;
    }


}