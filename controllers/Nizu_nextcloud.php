<?php

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Nizu_nextcloud extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied('Nizu_nextcloud');
        }
    }
    /* List all surveys */
    public function index()
    {
        $data['title'] = 'Nizu NextCloud';
        $this->load->view('nizu_nextcloud', $data);
    }
}