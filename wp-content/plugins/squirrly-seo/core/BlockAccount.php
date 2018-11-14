<?php

/**
 * Account settings
 */
class SQ_Core_BlockAccount extends SQ_Classes_BlockController {

    function hookGetContent() {
        parent::preloadSettings();
    }

}
