<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\ApiJobListByOrganizationController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\InputFilter\InputFilter;

class ApiJobListByOrganizationControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Jobs\Repository\Job $jobRepository */
        $jobRepository = $container->get('repositories')->get('Jobs/Job');

        /** @var \Jobs\Model\ApiJobDehydrator $apiJobDehydrator */
        $apiJobDehydrator = $container->get('Jobs\Model\ApiJobDehydrator');

        $inputFilter = new InputFilter();
        $inputFilter->add([
                            'name' => 'callback',
                            'filters'  => [
                                ['name' => 'StringTrim'],
                                ['name' => 'Alpha'],
                            ],
                          ]);

        $controller = new ApiJobListByOrganizationController($jobRepository, $apiJobDehydrator, $inputFilter);

        return $controller;
    }
}
