<?php

/**
 * Default homepage class serving as the entry point for public website access.
 * Renders the initial landing page as configured in the framework settings.
 */
class Welcome extends Trongate {

    /**
     * Renders the (default) homepage for public access.
     *
     * @return void
     */
    public function index(): void {

        $additional_includes_top[] = '<link rel="stylesheet" href="welcome_module/css/custom.css">';
        $additional_includes_btm[] = '<script src="welcome_module/js/custom.js"></script>';

        $data = [
            'additional_includes_top' => $additional_includes_top,
            'additional_includes_btm' => $additional_includes_btm,
            'view_module' => 'welcome',
            'view_file' => 'demo_homepage'
        ];

        $this->templates->public($data);
    }
}
