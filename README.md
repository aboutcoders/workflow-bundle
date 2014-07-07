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
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Abc\Bundle\WorkflowBundle\AbcWorkflowBundle(),
        // ...
    );
}
```

Follow the installation and configuration instructions of the third party bundles:

* [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/index.md)
* [AbcJobBundleBundle](https://bitbucket.org/hasc/job-bundle)

Configure the bundle

``` yaml
# app/config/config.yml
abc_workflow:
  db_driver: orm
```

## Usage
