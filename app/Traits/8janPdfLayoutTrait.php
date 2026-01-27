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

    protected function getPDFLastPageFooter(): string
{
    return '
    {if {PAGENO} == {nbpg}}
        <img src="' . public_path('images/footer-image.png') . '" style="width:100%; display:block;">
    {/if}
    ';
}



    /**
     * Summary of getStudentTestPDFFooter
     * @return string
     * can be used on Student Printable test PDF 
     */
    protected function getStudentTestPDFFooter()
    {
        return '<div class="footer-shape" style="padding-bottom: -40px">
                    <img src="'.public_path('images/footer-shape-1-test.png').'"/>
                </div>';
    }
}

