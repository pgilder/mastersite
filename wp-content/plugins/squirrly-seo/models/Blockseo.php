<?php

/**
 * The model class for SQ_Core_Blockseo
 *
 */
class SQ_Models_Blockseo {

    /**
     * Get the advanced SEO from database
     * @global integer $sq_postID
     * @return string
     */
    public function getAdvSeo() {
        global $sq_postID;

        if ((int) $sq_postID == 0)
            return array();

        $sq = SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getAdvancedMeta($sq_postID, 'title');

        return array('_sq_fp_title' => SQ_Classes_ObjController::getClass('SQ_Models_Frontend')->getAdvancedMeta($sq_postID, 'title'),
            '_sq_fp_description' => (isset($sq->description) ? $sq->description : ''),
            '_sq_fp_keywords' => (isset($sq->keywords) ? $sq->keywords : ''),
            '_sq_fp_ogimage' => (isset($sq->og_media) ? $sq->og_media : ''),
            '_sq_fp_canonical' => (isset($sq->canonical) ? $sq->canonical : ''));
    }

}
