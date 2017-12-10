# TechDesign TaskProcessor

TaskProcessor came to idea as replacement for nodejs + gulp + gulp toolchain because in the end
it is not a portable solution.

Many people are using gulp in their php projects because it is suppose to be industry standard for
asset compilation. However it comes with a price: bloat. First you need to have node installed, then you need npm 
packages and wrapper plugins for gulp. That is a **MASSIVE** overhead.
Some people are already [switching to npm scripts](https://medium.freecodecamp.org/why-i-left-gulp-and-grunt-for-npm-scripts-3d6853dd22b8).

So why would you even use node js for that? What can node js do which php isn't capable of?
The answer is: nothing.

## How does it work?

TaskProcessor

## Dependancies

So far this project is depends only on [php pthreads](http://php.net/manual/en/pthreads.installation.php).

## Installation

**Recommended** way to install task processor is to clone this repository and customize actions for your project.


```
git clone git@github.com:pwilkowski/TaskProcessor.git
```

Then you can use [phar-composer](https://github.com/clue/phar-composer) to build a standalone phar.

