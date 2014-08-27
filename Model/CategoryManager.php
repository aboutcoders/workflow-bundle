<?php
namespace Abc\Bundle\WorkflowBundle\Model;

abstract class CategoryManager implements CategoryManagerInterface
{

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class = $this->getClass();
        $user  = new $class;

        return $user;
    }

}