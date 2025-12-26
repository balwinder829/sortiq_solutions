<?php

namespace App\Traits;

trait PdfLayoutTrait
{
    protected function getPDFHeader()
    {
        return '<div style="position: fixed; top: -35px;" class="head-shape">
            <img src="'. public_path('images/confirmation_images/head-shape.png').'"/>
        </div>';
    }

    protected function getPDFFooter()
    {
        return '<div style="position: fixed; bottom: -35px;" class="ct-footer-shape">
            <img src="'.public_path('images/confirmation_images/footer-shape-1.png').'"/>
        </div>';
    }
}
