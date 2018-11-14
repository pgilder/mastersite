<?php

/**
 * Core class for SQ_Core_Blocksearch
 */
class SQ_Core_Blocksearch extends SQ_Classes_BlockController {

    public function action() {
        $start = 0;
        $nbr = 8;
        $exclude = array();

        parent::action();
        switch (SQ_Classes_Tools::getValue('action')) {
            case 'sq_type_click':
                SQ_Classes_Tools::saveOptions('sq_img_licence', SQ_Classes_Tools::getValue('licence'));
                exit();
                break;
            case 'sq_search_blog':
                if (SQ_Classes_Tools::getValue('exclude') && SQ_Classes_Tools::getValue('exclude') <> 'undefined')
                    $exclude = array((int) SQ_Classes_Tools::getValue('exclude'));

                if (SQ_Classes_Tools::getValue('start'))
                    $start = array((int) SQ_Classes_Tools::getValue('start'));

                if (SQ_Classes_Tools::getValue('nrb'))
                    $nrb = (int) SQ_Classes_Tools::getValue('nrb');

                if (SQ_Classes_Tools::getValue('q') <> '')
                    echo SQ_Classes_ObjController::getClass('SQ_Models_Post')->searchPost(SQ_Classes_Tools::getValue('q'), $exclude, (int) $start, (int) $nrb);
                break;
        }

        exit();
    }

    public function hookHead() {
        parent::hookHead();
    }

}
