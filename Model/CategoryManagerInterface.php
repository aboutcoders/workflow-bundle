<?php

namespace Abc\Bundle\WorkflowBundle\Model;

/**
 * @author Wojciech Ciolko <w.ciolko@gmail.com>
 */
interface CategoryManagerInterface
{

    /**
     * @return CategoryInterface
     */
    public function create();

    /**
     * @param CategoryInterface $item
     * @return void
     */
    public function update(CategoryInterface $item);

    /**
     * @param CategoryInterface $item
     * @return void
     */
    public function delete(CategoryInterface $item);

    /**
     * @param array $criteria
     * @return CategoryInterface
     */
    public function findBy(array $criteria);

    /**
     * @param string $id
     * @return CategoryInterface
     */
    public function findById($id);

    /**
     * @param string $slug
     * @return CategoryInterface
     */
    public function findBySlug($slug);

    /**
     * Returns a collection with all instances.
     *
     * @return \Traversable
     */
    public function findAll();

    /**
     * @return string
     */
    public function getClass();
}