<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Job.php */ 
namespace Jobs\Form\InputFilter;


class NewJob extends EditJob
{
    
    public function init()
    {
        parent::init();
        $input = $this->get('applyId')
                      ->getValidatorChain()
                      ->attachByName('Jobs/Form/UniqueApplyId');
        
    }
}

