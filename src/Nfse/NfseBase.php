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