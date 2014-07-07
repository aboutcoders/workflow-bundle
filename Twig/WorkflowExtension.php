<?php
namespace Abc\Bundle\WorkflowBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WorkflowExtension extends \Twig_Extension
{
    /** @var TranslatorInterface */
    private $translator;
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->container  = $container;
    }

    public function getFunctions()
    {
        return array(
            'workflow_configuration' => new \Twig_Function_Method($this, 'workflowConfiguration', array('is_safe' => array('html'))),
        );
    }

    public function workflowConfiguration($entity)
    {
        return $this->container->get('templating')
            ->render("AbcWorkflowBundle:Task:configureWorkflow.html.twig",
                array(
                    'entity' => $entity,
                )
            );
    }

    public function getName()
    {
        return 'workflow_extension';
    }
} 