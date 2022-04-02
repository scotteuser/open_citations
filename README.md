# Open Citations Demo

## Installation

Add the following to your `repositories` in composer.json:
```
{
    "type": "vcs",
    "url": "git@gitlab.com:sb-dev-team/soapbox-drupal-modules.git"
}
```
Add the following within your `require` in composer.json:
```
    "scotteuser/drupal-dev-days-batch-demo": "dev-main",
```
Run composer, then use Drush or the Drupal UI to enable the module.
