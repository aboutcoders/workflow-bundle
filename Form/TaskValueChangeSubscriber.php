<?php
namespace Abc\Bundle\WorkflowBundle\Form;

use Abc\FileDistributionBundle\Form\FieldValueChangeSubscriber;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

class TaskValueChangeSubscriber extends FieldValueChangeSubscriber
{
    public function __construct(array $providers)
    {
        parent::__construct($providers);
    }

    /**
     * This method handles initial structure.
     *
     * @param FormEvent $event Form event.
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // note that form data is now our entity object
        $this->buildTypeSettingsForm($form, $data->getType());
    }

    /**
     * This method handles changing structure after form submit.
     *
     * @param FormEvent $event Form event.
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // this time data is just a plain array as parsed from request
        $this->buildTypeSettingsForm($form, $data['type']);
    }

    /**
     * @param Form   $form         Main form.
     * @param string $providerName Provider.
     */
    protected function buildTypeSettingsForm(Form $form, $providerName)
    {
        //Add provider form
        if ($providerName && isset($this->providers[$providerName])) {
            $provider = $this->providers[$providerName];
            // delegate form structure building for specific provider
            $provider->buildForm($form);
        }
    }
}