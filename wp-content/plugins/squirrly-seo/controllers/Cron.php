<?php

class SQ_Controllers_Cron extends SQ_Classes_FrontController {

    public function processSEOPostCron() {
        SQ_Classes_ObjController::getClass('SQ_Classes_Tools');
        SQ_Classes_ObjController::getClass('SQ_Classes_Action');

        if (get_option('sq_seopost') !== false) {
            $process = json_decode(get_option('sq_seopost'), true);

            if(!empty($process)) {
                foreach ($process as $key => $call) {

                    if (!$response = json_decode(SQ_Classes_Action::apiCall('sq/seo/post', $call, 10))) {
                        break;
                    }

                    if (isset($response->saved) && $response->saved == true) {
                        unset($process[$key]);
                    }
                }

            }
            update_option('sq_seopost', json_encode($process));
        }
    }

    public function processRankingCron() {
        if(SQ_Classes_Tools::getOption('sq_google_serp_active')) {
            SQ_Classes_ObjController::getClass('SQ_Controllers_SerpChecker')->processCron();
        }else {
            SQ_Classes_ObjController::getClass('SQ_Classes_Ranking')->processCron();
        }
    }

}
