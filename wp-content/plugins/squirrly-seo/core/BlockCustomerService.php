<?php

/**
 * Customer Service Page
 */
class SQ_Core_BlockCustomerService extends SQ_Classes_BlockController {

    function hookGetContent() {
        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')->loadMedia('blocksupport');
    }
}
