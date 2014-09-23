<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Zend\Form\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Core form.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Form extends ZendForm implements DescriptionAwareFormInterface, DisableElementsCapableInterface
{
    /**
     * Form parameters.
     * An array of key  => value pairs which are rendered as hidden fields.
     *
     * @var array
     */
    protected $params;

    /**
     * Flag, if descriptions handling is enabled or not.
     *
     * @var bool
     */
    protected $isDescriptionsEnabled = false;

    public function setIsDescriptionsEnabled($flag)
    {
        $this->isDescriptionsEnabled = (bool) $flag;
        return $this;
    }
    
    public function isDescriptionsEnabled()
    {
        return $this->isDescriptionsEnabled;
    }
    
    public function setDescription($description)
    {
        $this->options['description'] = $description;
        return $this;
    }

    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable']
               : true;
    }

    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
               ? $this->options['is_disable_elements_capable']
               : true;
    }

    public function disableElements(array $map)
    {
        if (!$this->isDisableElementsCapable()) {
            return $this;
        }

        foreach ($map as $key => $name) {

            if (is_numeric($key)) {
                $key = $name;
                $name = null;
            }

            if (!$this->has($key)) {
                continue;
            }

            $element = $this->get($key);

            if (null === $name) {
                if (($element instanceOf DisableCapableInterface && $element->isDisableCapable())
                    || false !== $element->getOption('is_disable_capable')) {
                    $this->remove($element);
                }
                continue;
            }

            if ($element instanceOf FieldsetInterface
                && $element instanceOf DisableElementsCapableInterface
                && $element->isDisableElementsCapable()
            ) {
                $element->disableElements($name);
            }
        }
        return $this;
    }
    
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function setOptions($options)
    {
        $desc = isset($this->options['description']) ? $this->options['description'] : null;
        
        parent::setOptions($options);
        
        if (isset($options['enable_descriptions'])) {
            $this->setIsDescriptionsEnabled($options['enable_descriptions']);
        }
        
        if (!isset($options['description']) && null !== $desc) {
            $this->options['description'] = $desc;
        }
    }

    /**
     * Sets many form parameters at once.
     *
     * @param array $params
     *
     * @return self
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
        return $this;
    }

    /**
     * Sets a form parameter.
     *
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function setParam($key, $value)
    {

        if ($this->has($key)) {
            $this->get($key)->setValue($value);
        } else {
            $this->add(array(
                'type' => 'hidden', 
                'name' => $key, 
                'attributes' => array(
                    'value' => $value
                )
            ));
        }
        return $this;
    }

    /**
     * Adds hydrator strategies to the default hydrator upon instanciation.
     *
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     */
    protected function addHydratorStrategies($hydrator)
    { }
    

    public function addClass($spec) {
        $class = array();
        if ($this->hasAttribute('class')) {
            $class = $this->getAttribute('class');
        }
        if (!is_array($class)) {
            $class = explode( ' ', $class);
        }
        if (!in_array($spec, $class)) {
            $class[] = $spec;
        }
        $this->setAttribute('class', implode(' ',$class));
        return $this;
    }

    public function setValidate() {
        return $this->addClass('validate');
    }

    /**
     * {@inheritDoc}
     * Rebinds the bound object or sets data to the filtered values.
     */
    public function isValid()
    {
        $isValid = parent::isValid();
        if ($isValid) {
            if (is_object($this->object)) {
                $this->bind($this->object);
            } else {
                $filter = $this->getInputFilter();
                $this->setData($filter->getValues());
            }
        }
        
        return $isValid;
    }

}