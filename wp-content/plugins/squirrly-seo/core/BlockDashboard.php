<?php

/**
 * Dashboard settings
 */
class SQ_Core_BlockDashboard extends SQ_Classes_BlockController {

    function hookGetContent() {
        parent::preloadSettings();
    }

}
