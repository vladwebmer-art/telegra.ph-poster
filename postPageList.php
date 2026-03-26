<?php

use League\Csv\CannotInsertRecord;
use League\Csv\Exception as CsvException;
use League\Csv\Reader;
use League\Csv\Writer;
use Telegraph\Telegraph;
use Telegraph\Exceptions\TelegraphException;

require __DIR__ . '/vendor/autoload.php';

$token = '0796ee77446844152dc609888453ab8eed2da67353051cf378ccd46947a7';
$source = __DIR__ . '/contentSources/pageListSource.csv';
$result = __DIR__ . '/pageListResults/pageListResult.csv';

$telegraph = new Telegraph($token);
$account = $telegraph->account();

try {

    $resultsDirectory = dirname($result);
    //If results directory doesn't exist lets create it.
    if (!is_dir($resultsDirectory)) {
        if (mkdir($resultsDirectory, 0755, true)) {
            echo "Directory created: $resultsDirectory\n";
        } else {
            throw new CsvException("Can not create directory: $resultsDirectory\n");
        }
    }

    $reader = Reader::from( $source, 'r');
    $reader->setHeaderOffset(0);
    $records = $reader->getRecords();
    $defaultAuthorName = $account->getInfo()->authorName();

    $recordsToWrite = [];
    foreach ($records as $record) {
        //We will not publish pages with empty data
        if(empty($record['title']) || empty($record['content'])) continue;
        $authorName = !empty($record['author_name']) ? $record['author_name'] : $defaultAuthorName;
        //Create the page
        $page = $account->createPage(
            title: $record['title'],
            content: $record['content'],
            authorName: $authorName
        );
        //If there is no url the page have not been created
        if(empty($page->url())) continue;
        //Fill records list to save as result log
        $recordsToWrite[] = [
            $record['title'],
            $authorName,
            $page->url(),
            date('Y-m-d')
        ];
    }
    //No headers needed for new file
    if(!file_exists($result)) {
        $headers = ['Title', 'Author', 'URL', 'Date'];
        array_unshift($recordsToWrite, $headers);
    }
    //Get csv writer
    $writer = Writer::from($result, 'a+');
    //Save result records
    $writer->insertAll($recordsToWrite);
} catch (
    TelegraphException|
    CannotInsertRecord|
    CsvException $exception) {
    echo $exception->getMessage();
    exit;
}

echo 'Pages have been inserted successfully!';