<?php

/* response classes */

class ProductInfo {
    var $Details; /* DetailsArray */
    
    function displayList($type = 'lite')
    {
        print '<table border="1">';
        foreach ($this->Details as $detail) {
            print '<tr><td>'.$detail->listDisplay($type).'</td></tr>';
        }
        print '</table>';
    }
}

class Details {
    var $Url; /* string */
    var $Asin; /* string */
    var $ProductName; /* string */
    var $Catalog; /* string */
    var $KeyPhrases; /* KeyPhraseArray */
    var $Artists; /*ArtistArray */
    var $Authors; /* AuthorArray */
    var $Mpn; /* string */
    var $Starring; /* StarringArray */
    var $Directors; /* DirectorArray */
    var $TheatricalReleaseDate; /* string */
    var $ReleaseDate; /* string */
    var $Manufacturer; /* string */
    var $Distributor; /* string */
    var $ImageUrlSmall; /* string */
    var $ImageUrlMedium; /* string */
    var $ImageUrlLarge; /* string */
    var $ListPrice; /* string */
    var $OurPrice; /* string */
    var $UsedPrice; /* string */
    var $RefurbishedPrice; /* string */
    var $CollectiblePrice; /* string */
    var $ThirdPartyNewPrice; /* string */
    var $SalesRank; /* string */
    var $BrowseList; /* BrowseNodeArray */
    var $Media; /* string */
    var $ReadingLevel; /* string */
    var $Publisher; /* string */
    var $NumMedia; /* string */
    var $Isbn; /* string */
    var $Features; /* FeaturesArray */
    var $MpaaRating; /* string */
    var $EsrbRating; /* string */
    var $AgeGroup; /* string */
    var $Availability; /* string */
    var $Upc; /* string */
    var $Tracks; /* TrackArray */
    var $Accessories; /* AccessoryArray */
    var $Platforms; /* PlatformArray */
    var $Encoding; /* string */
    var $Reviews; /* Reviews */
    var $SimilarProducts; /* SimilarProductsArray */
    var $Lists; /* ListsArray */ 
    
    function listDisplay($type)
    {
        $end = '';
        switch($type) {
        case 'heavy':
            $end = "Manufacturer: $this->Manufacturer<br/>\n";
            $end .= "Isbn: $this->Isbn<br/>\n";
            if (count($this->Reviews->CustomerReviews)) {
                foreach($this->Reviews->CustomerReviews as $review) {
                    $end .= $review->display();
                }
            }
        default:
            $html= "<a href='$this->Url'><img src='$this->ImageUrlSmall' border='0' align='left'/>$this->ProductName</a><br/>\n";
            if (is_array($this->Authors)) {
                $authors = join($this->Authors, ", ");
                $html.= "by $authors<br/>\n";
            }
            $html.= "Price: $this->OurPrice";
            if (isset($this->Reviews->AvgCustomerRating)) $html .= " Rating: {$this->Reviews->AvgCustomerRating}";
            $html .= "<br/>\n";
            return $html.$end;
        break;
        }
    }

    function pageDisplay($displayAs)
    {
    }
}

class KeyPhrase {
    var $KeyPhrase; /* string */
    var $Type; /* string */ 
}

class Track {
    var $TrackName; /* string */
    var $ByArtist; /* string */ 
}

class BrowseNode {
    var $BrowseId; /* string */
    var $BrowseName; /* string */ 
}

class Reviews {
    var $AvgCustomerRating; /* string */
    var $CustomerReviews; /* CustomerReviewArray */ 
}

class CustomerReview {
    var $Rating; /* string */
    var $Summary; /* string */ 
    var $Comment; /* string */ 

    
    function display()
    {
        $text = "Rating: $this->Rating<br/>\n";
        $text .= "Summary: $this->Summary<br/>\n";
        $text .= "<p>$this->Comment</p>\n";
        return $text;
    }
}

/* request classes */

class BaseRequest {
    var $devtag; /* string */ 
    var $tag='webservices-20'; /* string */
    var $type='lite'; /* string */ 
    var $version = '1.0'; /* string */
    
    function BaseRequest(&$info)
    {
        if (isset($info['search_tag'])) $this->tag = $info['search_tag'];
        if (isset($info['search_type'])) $this->type = $info['search_type'];
        if (isset($info['search_devtag'])) $this->devtag = $info['search_devtag'];
    }
}

class PagedRequest extends BaseRequest {
    var $page=1; /* string */ 
    var $mode='books'; /* string */ 

    function PagedRequest(&$info)
    {
        if (isset($info['search_page'])) $this->page = $info['search_page'];
        if (isset($info['search_mode'])) $this->mode = $info['search_mode'];
        parent::BaseRequest($info);
    }    
}

class KeywordRequest extends PagedRequest {
    var $keyword; /* string */
    
    function KeywordRequest($info)
    {
        if (!$info) return;
        $this->keyword = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class BrowseNodeRequest extends PagedRequest {
    var $browse_node; /* string */
    
    function BrowseNodeRequest($info)
    {
        if (!$info) return;
        $this->browse_node = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class AsinRequest extends BaseRequest {
    var $asin; /* string */
    
    function AsinRequest($info)
    {
        if (!$info) return;
        $this->asin = $info['search_words'];
        parent::BaseRequest($info);
    }
}

class UpcRequest extends BaseRequest {
    var $upc; /* string */ 
    var $mode='books'; /* string */ 

    function UpcRequest(&$info)
    {
        if (!$info) return;
        $this->upc = $info['search_words'];
        if (isset($info['search_mode'])) $this->mode = $info['search_mode'];
        parent::BaseRequest($info);
    }    
}

class ArtistRequest extends PagedRequest {
    var $artist; /* string */
    
    function ArtistRequest($info)
    {
        if (!$info) return;
        $this->artist = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class AuthorRequest extends PagedRequest {
    var $author; /* string */
    
    function AuthorRequest($info)
    {
        if (!$info) return;
        $this->author = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class ActorRequest extends PagedRequest {
    var $actor; /* string */
    
    function ActorRequest($info)
    {
        if (!$info) return;
        $this->actor = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class DirectorRequest extends PagedRequest {
    var $director; /* string */
    
    function DirectorRequest($info)
    {
        if (!$info) return;
        $this->director = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class ManufacturerRequest extends PagedRequest {
    var $manufacturer; /* string */
    
    function ManufacturerRequest($info)
    {
        if (!$info) return;
        $this->manufacturer = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class ListManiaRequest extends PagedRequest {
    var $lm_id; /* string */
    
    function ListManiaRequest($info)
    {
        if (!$info) return;
        $this->lm_id = $info['search_words'];
        parent::PagedRequest($info);
    }
}

class SimilarityRequest extends BaseRequest {
    var $asin; /* string */
    var $mode='books'; /* string */ 
    
    function SimilarityRequest($info)
    {
        if (!$info) return;
        $this->asin = $info['search_words'];
        if (isset($info['search_mode'])) $this->mode = $info['search_mode'];
        parent::BaseRequest($info);
    }
}


?>
