<?php
require 'autoload.php';

global $argv,$argc;

$parser = new \StanfordNLP\Parser(
  '/path/to/stanford-parser-full-2015-12-09/stanford-parser.jar',
  '/path/to/stanford-parser-full-2015-12-09/stanford-parser-3.6.0-models.jar',
  array('-mx2G')
);
$parser->setJavaPath("/path/to/java-8/bin/java");
$parser->setOutputFormat("typedDependencies");

$training = file($argv[1]);
$to_parse = array();
foreach($training as $line){
    if(preg_match('/^\<original/',$line)){
        $to_parse[] = preg_replace('/\<\/?original[^>]*\>/','',$line);
    }
    if(preg_match('/^\<compressed/',$line)){
        $to_parse[] = preg_replace('/\<\/?compressed[^>]*\>/','',$line);
    }
}
$pairs = array();
for($i=0;$i<count($to_parse);$i+=2){
    $pairs[] = array($to_parse[$i], $to_parse[$i+1]);
}
//print_r($pairs);
// filter pairs with more than one element
$to_parse = array();
foreach($pairs as $pair){
    if(count(explode('.',$pair[0])) > 2 || count(explode('.',$pair[1])) > 2){
        // ignore
    }else{
        $to_parse[] = $pair[0];
        $to_parse[] = $pair[1];
    }
}
//print_r($to_parse);

$results = $parser->parseSentences($to_parse);
if(count($results)!=count($to_parse)){
    throw new Exception("length mismatch");
}
$training=array();
$original = false;
foreach($results as $result){
    # find repeated words
    $repeated = array();
    foreach($result["typedDependencies"] as $entry){
        if(!isset($repeated[$entry[0]['feature']])){
            $repeated[$entry[0]['feature']] = array();
        }
        $repeated[$entry[0]['feature']][$entry[0]['index']] = 1;
        if(!isset($repeated[$entry[1]['feature']])){
            $repeated[$entry[1]['feature']] = array();
        }
        $repeated[$entry[1]['feature']][$entry[1]['index']] = 1;
    }
    $deps=array();
    foreach($result["typedDependencies"] as $entry){
        $type = $entry['type'];
        $arg1 = $entry[0]['feature'];
        if(count($repeated[$entry[0]['feature']]) > 1){
            $arg1.='-'.$entry[0]['index'];
        }
        $arg2 = $entry[1]['feature'];
        if(count($repeated[$entry[1]['feature']]) > 1){
            $arg2 .= '-'.$entry[1]['index'];
        }
        $deps[] = array($type, $arg1, $arg2);
    }
    if($original){
        $training[] = array($original,$deps);
        $original = false;
    }else{
        $original = $deps;
    }
}

//print_r($training);

// determine which untyped dep pairs have been removed

for($i=0;$i<count($training); $i++){
    $pair=$training[$i];
    $pruned=array();
    foreach($pair[1] as $dep){
        $pruned[$dep[0]."-".$dep[1]] = 1;
    }
    for($j=0;$j<count($pair[0]);$j++){
        $dep = $pair[0][$j];
        $training[$i][0][$j][] = isset($pruned[$dep[0]."-".$dep[1]]) ? "0":"1";
    }
}

//print_r($training);

function dump_training($head, $link, $depth, $data, $handle){
    if($depth < 10){
        foreach($data as $entry){
            if($entry[1] == $head){
                #fwrite($handle,"$link\t$depth\t".$entry[3]."\t\t".$entry[0]."(".$entry[1].",".$entry[2].")\n");
                fwrite($handle,"$entry[0]\t$depth\t".$entry[3]."\t\t".$entry[0]."(".$entry[1].",".$entry[2].")\n");
                dump_training($entry[2],$entry[0],$depth+1,$data,$handle);
            }
        }
    }
}


$handle = fopen("training","a");
for($i=0;$i<count($training); $i++){
    dump_training('ROOT', 'root', 0, $training[$i][0], $handle);
}
fclose($handle);


