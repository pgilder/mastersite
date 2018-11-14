<?php

class SQ_Core_BlockPostsAnalytics extends SQ_Classes_BlockController {
    public $checkin;

    public function hookGetContent() {

        SQ_Classes_ObjController::getClass('SQ_Classes_DisplayController')
            ->loadMedia(_SQ_THEME_URL_ . 'css/postslist.css');

        SQ_Classes_Tools::saveOptions('sq_analytics', 1); //Save analytics viewed
        SQ_Classes_Tools::saveOptions('sq_dashboard', 1); //Save dashboard viewed
        $this->postlist = SQ_Classes_ObjController::getClass('SQ_Controllers_PostsList');

        //Not yet available
        $this->checkin = json_decode(SQ_Classes_Action::apiCall('sq/rank-checker/checkin'));
        if (isset($this->checkin->active) && $this->checkin->active) {
            if (isset($this->checkin->trial) && $this->checkin->trial) {
                SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 1);
            } else {
                SQ_Classes_Tools::saveOptions('sq_google_serp_trial', 0);
            }
            SQ_Classes_Tools::saveOptions('sq_google_serp_active', 1);
            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('%sYou activated the  Business Plan with Advanced Analytics. %sStart Here%s %s'), '<strong style="font-size: 16px;">', '<a href="' . admin_url('admin.php?page=sq_posts') . '">', '</a>', '</strong>'), 'success');
        } elseif (isset($this->checkin->error) && $this->checkin->error == "subscription_notfound" && !SQ_Classes_Tools::getOption('sq_google_serp_active')) {
            $menu = SQ_Classes_Tools::getOption('menu');
            if ($menu['show_ads']) {
                SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('%sStart a FREE Trial of the Business Plan with Advanced Analytics for 7 days. No credit card required. %sSee details%s %s'), '<strong style="font-size: 16px;">', '<a href="' . _SQ_DASH_URL_ . 'login/?token=' . SQ_Classes_Tools::getToken() . '&redirect_to=' . _SQ_DASH_URL_ . 'user/plans?pid=31" target="_blank">', '</a>', '</strong>'), 'trial');
            }
        } elseif (!SQ_Classes_Tools::getOption('sq_google_serp_active')) {
            SQ_Classes_ObjController::getClass('SQ_Classes_Error')->setError(sprintf(__('To get back to the Advanced Analytics and see rankings for all the keywords in Briefcase upgrade to %sBusiness Plan%s.'), '<a href="' . SQ_Classes_Tools::getBusinessLink() . '" target="_blank">', '</a>'), 'error');
        }


        $this->model->prepare_items();

        SQ_Classes_ObjController::getClass('SQ_Classes_Error')->hookNotices();


        @ini_set('open_basedir', null);
    }

    public function getNavigationTop() {
        return $this->model->display_tablenav('top');
    }

    public function getNavigationBottom() {
        return $this->model->display_tablenav('bottom');
    }

    public function getHeaderColumns() {
        return $this->model->print_column_headers();
    }

    public function getRows() {
        return $this->model->display_rows();
    }

    public function hookFooter() {
        $this->postlist->setPosts($this->model->posts);
        $this->postlist->hookFooter();
    }

    public function getScripts() {
        return $this->postlist->getScripts();
    }

}
