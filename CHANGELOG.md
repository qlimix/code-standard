## 4.0.1 - 04-11-2026

### Dependencies
 - allow qlimix/codesniffer-standard ^6.0

## 4.0.0 - 04-11-2026

### Dependencies
 - php >=8.4
 - phpunit ^13.0, phpstan ^2.1, psalm ^7.0, grumphp ^2.0, phpmd ^2.15 || ^3.0
 - qlimix/codesniffer-standard ^5.0

### Configs
 - PHPUnit config uses `<source>` and the installed schema
 - removed `totallyTyped` from the Psalm config (removed in Psalm 5)
 - GrumPHP template uses `phpcs.xml.dist` / `psalm.xml.dist` and limits phpcs to `src`

### q-codestandard-defaults
 - fixed the PHPCS and PHPUnit configs being written to each other's file
 - writes `phpcs.xml.dist`, `phpunit.xml.dist` and `psalm.xml.dist` (was the non-dist names)
 - skips configs that already exist instead of overwriting them
 - exits with an error when the composer autoloader is missing

## 3.0.3 - 22-02-2021

### PHPUnit
 - update deprecated schema

## 3.0.2 - 14-11-2020

## Strictness
 - made default Psalm more strict
 
## 3.0.1 - 14-11-2020

## Bug fixes
 - copy to correct file bug fix

## 3.0.0 - 11-11-2020

### Changes
 - Removed composer plugin dependency
 
### New features:
 - added Psalm
 - Added bin command for default code standard configurations
 
### Compatibility:
 - Upgrade grumphp to 1.0
 - Updated all other dependencies to their latest

## 2.0.3 - 01-11-2020

### Compatibility:
 - Updated to be compatible with composer 2.0
 - Locked grumphp to 0.18.* to prevent required config changes and be compatible with composer 2.0
 - Codesniffer standard upgrade for composer 2.0 compatibility

## 2.0.2 - 29-03-2020

### Changes:
 - ignore short name method phpmd rule
 - removed CouplingBetweenObjects exclusion from phpmd ruleset
 
## 2.0.1 - 29-03-2020

### Changes:
 - ignore short name variable phpmd rule
 
## 2.0.0 - 17-01-2020

### New features:
 - php 7.4 support
 - codesniffer-standard updated to version 2
 - updated composer packages to the latest
