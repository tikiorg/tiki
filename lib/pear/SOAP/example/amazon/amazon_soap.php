<?php
require_once 'amazon_types.php';
require_once 'SOAP/Client.php';

class AmazonSearchService {
    var $devtag;
    var $associateid;
    var $client;
    
    function AmazonSearchService($devtag, $associateid='webservices-20') {
        $this->client = new SOAP_Client("http://soap.amazon.com/onca/soap", 0);
        
        // tell the client to use our type classes above
        $this->client->_auto_translation = true;
        
        // this gets set into all search classes
        $this->devtag = $devtag;
        $this->associateid = $associateid;
    }
    
    function keywordSearchRequest($KeywordSearchRequest) {
        // KeywordSearchRequest is a ComplexType, refer to wsdl for more info
        $KeywordSearchRequest->devtag = $this->devtag;
        $KeywordSearchRequest =& new SOAP_Value('KeywordSearchRequest','{urn:PI/DevCentral/SoapService}KeywordRequest',$KeywordSearchRequest);
        //$KeywordRequest = new SOAP_Value('{urn:PI/DevCentral/SoapService}KeywordRequest',false,$KeywordRequest);
        return $this->client->call("KeywordSearchRequest", 
                        $v = array("KeywordSearchRequest"=>$KeywordSearchRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function BrowseNodeSearchRequest($BrowseNodeRequest) {
        // BrowseNodeSearchRequest is a ComplexType, refer to wsdl for more info
        $BrowseNodeRequest->devtag = $this->devtag;
        $BrowseNodeRequest =& new SOAP_Value('BrowseNodeSearchRequest','{urn:PI/DevCentral/SoapService}BrowseNodeRequest',$BrowseNodeRequest);
        return $this->client->call("BrowseNodeSearchRequest", 
                        $v = array("BrowseNodeSearchRequest"=>$BrowseNodeRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function asinSearchRequest($AsinRequest) {
        // AsinSearchRequest is a ComplexType, refer to wsdl for more info
        $AsinRequest->devtag = $this->devtag;
        $AsinRequest =& new SOAP_Value('AsinSearchRequest','{urn:PI/DevCentral/SoapService}AsinRequest',$AsinRequest);
        return $this->client->call("AsinSearchRequest", 
                        $v = array("AsinSearchRequest"=>$AsinRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function upcSearchRequest($UpcRequest) {
        // UpcSearchRequest is a ComplexType, refer to wsdl for more info
        $UpcRequest->devtag = $this->devtag;
        $UpcRequest =& new SOAP_Value('UpcSearchRequest','{urn:PI/DevCentral/SoapService}UpcRequest',$UpcRequest);
        return $this->client->call("UpcSearchRequest", 
                        $v = array("UpcSearchRequest"=>$UpcRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function authorSearchRequest($AuthorRequest) {
        // AuthorSearchRequest is a ComplexType, refer to wsdl for more info
        $AuthorRequest->devtag = $this->devtag;
        $AuthorRequest =& new SOAP_Value('AuthorSearchRequest','{urn:PI/DevCentral/SoapService}AuthorRequest',$AuthorRequest);
        return $this->client->call("AuthorSearchRequest", 
                        $v = array("AuthorSearchRequest"=>$AuthorRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function artistSearchRequest($ArtistRequest) {
        // ArtistSearchRequest is a ComplexType, refer to wsdl for more info
        $ArtistRequest->devtag = $this->devtag;
        $ArtistRequest =& new SOAP_Value('ArtistSearchRequest','{urn:PI/DevCentral/SoapService}ArtistRequest',$ArtistRequest);
        return $this->client->call("ArtistSearchRequest", 
                        $v = array("ArtistSearchRequest"=>$ArtistRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function actorSearchRequest($ActorRequest) {
        // ActorSearchRequest is a ComplexType, refer to wsdl for more info
        $ActorRequest->devtag = $this->devtag;
        $ActorRequest =& new SOAP_Value('ActorSearchRequest','{urn:PI/DevCentral/SoapService}ActorRequest',$ActorRequest);
        return $this->client->call("ActorSearchRequest", 
                        $v = array("ActorSearchRequest"=>$ActorRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function manufacturerSearchRequest($ManufacturerRequest) {
        // ManufacturerSearchRequest is a ComplexType, refer to wsdl for more info
        $ManufacturerRequest->devtag = $this->devtag;
        $ManufacturerRequest =& new SOAP_Value('ManufacturerSearchRequest','{urn:PI/DevCentral/SoapService}ManufacturerRequest',$ManufacturerRequest);
        return $this->client->call("ManufacturerSearchRequest", 
                        $v = array("ManufacturerSearchRequest"=>$ManufacturerRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function directorSearchRequest($DirectorRequest) {
        // DirectorSearchRequest is a ComplexType, refer to wsdl for more info
        $DirectorRequest->devtag = $this->devtag;
        $DirectorRequest =& new SOAP_Value('DirectorSearchRequest','{urn:PI/DevCentral/SoapService}DirectorRequest',$DirectorRequest);
        return $this->client->call("DirectorSearchRequest", 
                        $v = array("DirectorSearchRequest"=>$DirectorRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    function ListManiaSearchRequest($ListManiaRequest) {
        // ListManiaSearchRequest is a ComplexType, refer to wsdl for more info
        $ListManiaRequest->devtag = $this->devtag;
        $ListManiaRequest =& new SOAP_Value('ListManiaSearchRequest','{urn:PI/DevCentral/SoapService}ListManiaRequest',$ListManiaRequest);
        return $this->client->call("ListManiaSearchRequest", 
                        $v = array("ListManiaSearchRequest"=>$ListManiaRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
    
    function similaritySearchRequest($SimilarityRequest) {
        // SimilaritySearchRequest is a ComplexType, refer to wsdl for more info
        $SimilarityRequest->devtag = $this->devtag;
        $SimilarityRequest =& new SOAP_Value('SimilaritySearchRequest','{urn:PI/DevCentral/SoapService}SimilarityRequest',$SimilarityRequest);
        return $this->client->call("SimilaritySearchRequest", 
                        $v = array("SimilaritySearchRequest"=>$SimilarityRequest), 
                        array('namespace'=>'urn:PI/DevCentral/SoapService',
                            'soapaction'=>'urn:PI/DevCentral/SoapService',
                            'style'=>'rpc',
                            'use'=>'encoded')); 
    }
}

?>