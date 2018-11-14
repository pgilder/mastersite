<?php

class SQ_Core_Blockseo extends SQ_Classes_BlockController {

    public function action() {

    }

    public function hookHead() {
        parent::hookHead();

        echo '<script type="text/javascript">
             var __snippetshort = "' . __('Too short', _SQ_PLUGIN_NAME_) . '";
             var __snippetlong = "' . __('Too long', _SQ_PLUGIN_NAME_) . '";
             var __sq_snippet = "' . __('snippet', _SQ_PLUGIN_NAME_) . '";
        ' . "\n" . '</script>';

    }

}
