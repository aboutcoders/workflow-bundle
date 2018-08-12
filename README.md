Symfony Workflow Bundle
==========================

A symfony bundle that allows define and manage workflows.

## Configuration

Add the bundle:

``` json
{
    "require": {
        "aboutcoders/workflow-bundle": "dev-master"
    }
}
```

Enable the bundles in the kernel:

``` php
# app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Abc\Bundle\WorkflowBundle\AbcWorkflowBundle(),
        // ...
    );
}
```

Configure routing 

``` yaml
# app/config/routing.yml
abc-rest-workflow:
    type: rest
    resource: "@AbcWorkflowBundle/Resources/config/rest.yml"
    prefix: /api    
```

Follow the installation and configuration instructions of the third party bundles:

* [AbcJobBundleBundle](https://bitbucket.org/hasc/job-bundle)

Configure the bundle

``` yaml
# app/config/config.yml
abc_workflow:
  db_driver: orm
```

## Usage

Display workflow configuration GUI

``` twig
{{ workflow_configuration(workflowEntity) }}
```

Display workflow history GUI

``` twig
{{ workflow_history(workflowEntity) }}
```

Get workflow history via AJAX

``` twig
{{ path('execution_history', { 'id': workflowId }) }}
```