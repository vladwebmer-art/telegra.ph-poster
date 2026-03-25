# Telegraph pages publisher.

## _The script fetches pages data from csv, that publishes it via telegra.ph API and saves results into another csv file._  

## Requirements
- PHP 8.1 or higher
- cURL extension
- JSON extension

## Usage

1. Download repository data to server 
2. Perform composer command via CLI:

```bash
composer install
```
3. Add file pageListSource.csv under contentSources folder
4. pageListSource.csv must have at least two columns named title and content
5. Launch script via CLI with command:

```bash
php postPageList.php
```
6. If pages are published successfully the result log pageListResult.csv will appear under /pageListResults folder.