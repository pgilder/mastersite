<?php

/**
 * Keyword Research settings
 */
class SQ_Core_BlockKeywordResearch extends SQ_Classes_BlockController {

    function hookGetContent() {
        parent::preloadSettings();
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('blockresearch');
    }
}
