<?php

/**
 * Class WikiLingoTikiEvents
 * Used to bind WikiLingo to tiki's event lib
 */
class WikiLingoTikiEvents
{
    public function tiki_wiki_view_pastlink($args)
    {
        //listener
        FutureLink_ReceiveFromPast::wikiView($args);

        //page link, not really used
        FutureLink_PageLookup::wikiView($args);

        //ui, and redirect
        FutureLink_FutureUI::wikiView($args);
        FutureLink_PastUI::wikiView($args);
    }

    public function tiki_wiki_save_pastlink($args)
    {
        FutureLink_FutureUI::wikiSave($args);
        FutureLink_PastUI::wikiSave($args);
    }

    public function wikilingo_flp_view($args)
    {
        $headerlib = TikiLib::lib('header');
        $page = $args['object'];
        $version = $args['version'];
        $body = $args['data'];
        require_once 'lib/wikiLingo_tiki/WikiEvents.php';

        $events = new WikiEvents($page, $version, $body);


        //listener
        $events->listen();


        //redirect start
        $events->direct();


        //futurelink
        $events->load();

        //pastlink now happens inside of wikiLingo

        //view for wiki

        //need partial metadata
        $metadataLookup = new WikiMetadataLookup($page);
        $partialMetadata = $metadataLookup->getPartial();

        $headerlib
            ->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-core.js')
            ->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-cssclassapplier.js')
            ->add_jsfile('vendor/rangy/rangy/uncompressed/rangy-selectionsaverestore.js')
            ->add_jsfile('vendor/flp/flp/Phraser/rangy-phraser.js')
            ->add_jsfile('vendor/flp/flp/Phraser/Phraser.js')
            ->add_jsfile('vendor/jquery/md5/js/md5.js')
            ->add_jsfile('lib/wikiLingo_tiki/tiki_wikiLingo_flp_view.js')
            ->add_jsfile('lib/ZeroClipboard.js')
            ->add_jsfile('vendor/flp/flp/scripts/flp.js')
            ->add_jsfile('vendor/flp/flp/scripts/flp.Link.js')
            ->add_jsfile('lib/wikiLingo_tiki/tiki_flp.Link.js')
            ->add_jsfile('vendor/jquery/plugins/tablesorter/js/jquery.tablesorter.js')
            ->add_cssfile('vendor/jquery/plugins/tablesorter/css/theme.dropbox.css')
            ->add_jq_onready('(new WikiLingoFLPView($("#page-data"), ' . json_encode($partialMetadata) . '));');
    }

    public function wikilingo_flp_save($args)
    {
        require_once 'lib/wikiLingo_tiki/WikiEvents.php';

        $page = $args['object'];
        $version = $args['version'];
        $output = new WikiLibOutput($args, $args['data']);
        $body = $output->parsedValue;

        $events = new WikiEvents($page, $version, $body);

        $events->save();

    }
}
