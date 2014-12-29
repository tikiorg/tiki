<?php
class FutureLink_Pair
{
    public $futurelink;
    public $pastlink;

    public function __construct(&$pastlink, &$futurelink)
    {
        $this->futurelink =& FutureLink_MetadataAssembler::fromRawToMetaData($futurelink);
        $this->pastlink =& FutureLink_MetadataAssembler::fromRawToMetaData($pastlink);
    }
}