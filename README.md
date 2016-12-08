# Mini Symfony Skeleton

Mini Symfony is a skeleton based on [Symfony Emtpy Edition](https://github.com/gnugat/symfony-empty-edition) and the [Micro Framework Bundle](https://github.com/gnugat/micro-framework-bundle).

## Installation  
`composer create-project mini-symfony/skeleton my-project`

After the installation is finished you can visit your project eg. `http://localhost/my-project/htdocs`.

### Wiki
[Check the Wiki](https://github.com/Edwin-Luijten/mini-symfony/wiki) for tips/how to's

## Additions  
Check that the bundle you want to use does not require symfony/framework-bundle.

### Doctrine  
https://github.com/Edwin-Luijten/mini-doctrine-bundle  
`composer require mini-symfony/mini-doctrine-bundle`

## Benchmarks

- Clean installation [Blackfire graph](https://blackfire.io/profiles/220bf2f8-f50c-48ec-8574-c6db8911a9bd/graph)

### Todo  
- [x] debug:container command
- [x] debug:router command
- [x] debug:event-dispatcher command
- [x] debugbar
- [x] create a separate package for the debug:command, Appbundle should be removable without losing functionality.
- [x] Environment detection 
- [x] Installer