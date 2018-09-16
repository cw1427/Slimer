<?php
/**
 * Author: Shawn Chen
 * Desc: Admin module controller
 */

namespace App\Controller;

/**
 * Admin Controler for the GAM admin component.
 *
 * That controller handles work with auth for you
 */
class Admin extends \App\Controller
{
    public function AdminAction()
    {
        return $this->render('index/index.html.twig');
    }
    
}