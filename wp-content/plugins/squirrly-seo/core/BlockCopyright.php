<?php

/**
 * Live Assistant settings
 */
class SQ_Core_BlockCopyright extends SQ_Classes_BlockController {

    function hookGetContent() {
        parent::preloadSettings();
    }
}
