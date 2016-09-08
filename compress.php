<?php

use nlgen\Generator;
require 'php-nlgen/generator.php';
require 'php-nlgen/examples/ste/ste.php';
require 'PHP-Stanford-NLP/src/StanfordNLP/Base.php';
require 'PHP-Stanford-NLP/src/StanfordNLP/Parser.php';

global $argv,$argc;


function prune($head,$link,$depth,$data,$table){
    $result = array();
    if($depth < 10){
        foreach($data as $entry){
            if($entry[1] == $head){
                $counts = array(0, 0);
                if(isset($table[$entry[0]][$depth])){
                    $counts = $table[$entry[0]][$depth];
                }
                
                if(!$depth || ($counts[0] > $counts[1])){
                    $result[] = $entry;
                    $extra = prune($entry[2],$entry[0],$depth+1,$data,$table);
                    foreach($extra as $e){
                        $result[] = $e;
                    }
                }
            }
        }
    }
    return $result;
}

function show_tree($link, $array,$indent){
    $link = preg_replace("/_orig$/","",$link);
    if(isset($array['text'])){
        if(strlen($array['text'])>0){
            echo $indent.$link.": ".$array['text']."\n";
        }
    }
    foreach($array as $key => $value){
        if(is_array($value)){
            show_tree($key, $value, "$indent    ");
        }
    }
}


if($_POST['text']){
    header("Content-Type: text/plain");
    $parser = new \StanfordNLP\Parser(
        '/path/to/stanford-parser-full-2015-12-09/stanford-parser.jar',
        '/path/to/stanford-parser-full-2015-12-09/stanford-parser-3.6.0-models.jar',
        array('-mx2G')
    );
    $parser->setJavaPath("/path/to/java-8/bin/java");
    $parser->setOutputFormat("typedDependencies");

    $table_str = explode("\n",<<<HERE
acl	1	7	6	2	58	59	3	54	41	4	26	43	5	14	22	6	15	28	7	11	20	8	14	28	9	15	35
acl:for	3	0	1
acl:of	2	0	1
acl:relcl	1	4	6	2	60	74	3	47	49	4	96	97	5	73	95	6	135	164	7	132	202	8	246	436	9	327	745
acl:to	2	1	0
advcl	1	141	87	2	81	75	3	29	33	4	11	16	5	12	15	6	15	8	7	18	12	8	21	5	9	25	10
advcl:about	3	0	1	5	0	1	7	0	1	9	0	1
advcl:among	1	0	1
advcl:by	2	0	1
advcl:compared_to	3	0	1
advcl:compared_with	1	1	0	2	0	1
advcl:down	4	0	1
advcl:for	2	2	0
advcl:in	1	1	2	2	0	3	3	0	2	4	0	4	5	2	4	6	0	4	7	2	4	8	0	4	9	2	4
advcl:on	1	1	0	2	0	2	3	0	2	4	0	2	5	0	2	6	0	2	7	0	2	8	0	2	9	0	2
advcl:to	2	1	0
advcl:under	1	1	0
advcl:until	4	0	1	6	0	1	8	0	1
advcl:with	2	0	1
advcl:within	3	0	1
advmod	1	54	185	2	86	166	3	88	136	4	51	98	5	37	82	6	32	77	7	44	77	8	67	88	9	129	115
amod	1	19	21	2	255	337	3	316	340	4	257	316	5	176	224	6	137	225	7	135	252	8	146	376	9	178	606
appos	1	3	1	2	76	66	3	48	41	4	16	17	5	20	19	6	12	16	7	8	12	8	5	15	9	8	15
aux	1	227	59	2	208	60	3	96	48	4	61	24	5	58	34	6	54	25	7	67	49	8	91	63	9	158	118
auxpass	1	153	28	2	76	32	3	34	20	4	27	11	5	21	7	6	32	8	7	39	9	8	71	10	9	128	11
case	1	12	2	2	434	279	3	668	424	4	608	456	5	516	356	6	520	300	7	645	300	8	1082	405	9	1862	610
cc	1	96	113	2	108	89	3	101	122	4	99	126	5	102	115	6	143	161	7	231	237	8	423	416	9	810	756
cc:preconj	3	3	0	4	2	2	5	2	0	6	2	0	7	2	0	8	2	0	9	2	0
ccomp	1	245	110	2	61	42	3	24	16	4	11	9	5	7	4	6	6	4	7	11	7	8	7	11	9	15	21
compound	1	17	9	2	480	253	3	496	209	4	372	226	5	317	141	6	266	140	7	292	135	8	453	215	9	745	304
compound:prt	1	26	5	2	28	8	3	22	13	4	17	11	5	11	16	6	10	28	7	13	55	8	13	101	9	14	204
conj	2	0	1	3	0	8	4	0	10	5	0	12	6	0	22	7	0	38	8	0	68	9	0	132
conj:and	1	82	48	2	125	107	3	118	126	4	99	138	5	112	149	6	168	226	7	280	361	8	516	671	9	1002	1254
conj:but	1	18	18	2	4	6	3	1	6	4	2	5	5	0	4	6	2	4	7	1	4	8	4	4	9	4	4
conj:negcc	2	1	1	3	0	3	4	0	1	5	0	12	6	0	11	7	0	48	8	0	74	9	0	219
conj:or	1	1	1	2	4	2	3	8	15	4	8	13	5	6	11	6	4	7	7	4	7	8	4	7	9	4	8
cop	1	112	43	2	85	32	3	32	34	4	20	13	5	16	24	6	20	10	7	19	25	8	26	13	9	28	27
csubj	1	0	1	2	2	1	3	1	0
csubjpass	1	0	1	2	0	1
dep	1	16	39	2	29	57	3	33	70	4	23	32	5	5	17	6	11	24	7	3	15	8	7	10	9	1	13
det	1	40	17	2	625	293	3	607	338	4	507	304	5	412	224	6	459	219	7	672	231	8	1170	335	9	2127	523
det:predet	2	0	5	3	3	7	4	1	11	5	0	20	6	0	36	7	0	73	8	0	144	9	0	289
discourse	1	0	1
dobj	1	345	61	2	323	118	3	247	92	4	185	67	5	143	65	6	147	58	7	212	84	8	299	100	9	506	175
expl	1	15	4	2	15	9	3	7	0	4	2	1	6	1	2	7	0	2	8	2	2	9	1	2
iobj	1	5	2	2	3	2	3	4	2	4	2	1	5	3	1	6	0	1	7	2	2	8	1	1	9	2	3
mark	1	0	1	2	241	178	3	217	138	4	148	79	5	58	44	6	62	32	7	55	39	8	62	46	9	71	53
mwe	3	16	11	4	6	30	5	5	22	6	4	17	7	1	15	8	1	28	9	1	35
neg	1	31	5	2	44	21	3	33	10	4	16	8	5	13	7	6	10	4	7	10	5	8	8	5	9	10	5
nmod	1	0	2	2	1	0	3	1	0	4	2	0
nmod:'s	1	1	0	2	2	0	3	1	1	4	2	0	6	1	0	8	1	0
nmod:about	1	2	1	2	5	2	3	8	3	4	2	2	5	2	1	6	1	1	7	2	3	8	1	3	9	2	3
nmod:above	1	2	1	4	1	0	5	1	0
nmod:according_to	1	5	3	2	1	0	6	0	1	8	0	1
nmod:across	2	1	1	6	2	1	7	1	1	9	2	0
nmod:after	1	11	3	2	7	2	3	4	4	4	1	2	5	4	0	6	1	0	7	2	0	8	1	0	9	2	0
nmod:against	1	6	1	2	1	4	3	1	5	4	1	4	5	1	0	6	2	3	7	6	0	8	4	3	9	6	0
nmod:along	3	1	1
nmod:amid	1	0	1	3	1	0	5	1	0	7	1	0	9	1	0
nmod:among	1	4	3	2	1	1	3	1	1	4	2	0	5	1	0	6	2	0	7	1	0	8	2	0	9	1	0
nmod:apart_from	1	0	1	2	0	1
nmod:around	1	0	1	2	1	0	3	0	4	4	0	4	5	0	2	6	0	2	7	0	2	8	0	2	9	0	2
nmod:as	1	16	6	2	11	16	3	6	8	4	5	10	5	5	6	6	5	4	7	6	7	8	7	5	9	10	6
nmod:at	1	26	24	2	19	18	3	8	17	4	10	9	5	8	10	6	4	10	7	3	23	8	6	42	9	5	100
nmod:because_of	1	0	1	2	0	1	3	1	0	4	0	1
nmod:before	1	4	0	2	3	2	4	0	1	5	3	0	6	6	0	7	12	0	8	24	0	9	48	0
nmod:behind	1	0	1	2	2	0
nmod:beneath	5	0	1
nmod:between	1	9	0	2	4	3	3	6	7	4	1	6	5	8	8	6	0	1	7	12	7	8	6	0	9	36	5
nmod:beyond	3	1	0	5	1	0	7	1	0	9	1	0
nmod:but	4	2	0	5	0	2
nmod:by	1	53	16	2	19	21	3	26	25	4	23	21	5	29	17	6	44	23	7	90	38	8	174	60	9	362	109
nmod:close_to	1	1	1	4	0	1	5	0	1
nmod:compared_with	2	0	1
nmod:concerning	3	1	0
nmod:despite	1	3	1	2	2	0	6	0	1	8	1	1	9	1	0
nmod:during	1	6	3	2	4	6	3	0	2	4	4	4	5	2	2	6	2	4	7	2	2	8	2	4	9	3	2
nmod:except	3	1	0	4	1	0	5	1	0	6	1	0	7	1	0	8	1	0	9	1	0
nmod:following	1	1	0	2	0	2	3	2	0	4	1	0	5	1	0	6	1	0	8	0	1
nmod:for	1	42	27	2	53	27	3	54	33	4	61	24	5	69	22	6	115	14	7	208	19	8	400	17	9	807	21
nmod:from	1	20	9	2	19	24	3	19	19	4	15	19	5	5	34	6	7	55	7	9	107	8	8	201	9	5	398
nmod:given	1	1	0
nmod:in	1	92	92	2	83	86	3	72	99	4	83	94	5	102	82	6	165	118	7	316	180	8	599	336	9	1191	618
nmod:in_accordance_with	3	0	1
nmod:in_addition_to	1	1	0
nmod:including	1	0	5	2	4	10	3	0	5	4	1	1	6	0	1
nmod:inside	1	1	0	3	0	1	4	1	0
nmod:instead_of	4	1	0
nmod:into	1	5	1	2	11	2	3	9	1	4	7	0	5	4	0	6	3	1	7	3	1	8	3	1	9	3	2
nmod:involving	4	0	2	6	0	1	8	0	1
nmod:like	1	0	1	2	0	1	3	2	3	4	1	0	5	1	3
nmod:near	1	0	1	2	3	1	4	0	1
nmod:npmod	1	0	1	2	5	2	3	3	10	4	5	7	5	1	7	6	2	4	7	2	4	8	1	1	9	0	4
nmod:of	1	30	5	2	181	135	3	177	119	4	92	90	5	64	75	6	48	63	7	63	82	8	87	130	9	146	218
nmod:off	1	0	1	2	0	1	5	0	1
nmod:on	1	28	11	2	31	21	3	25	22	4	18	18	5	14	10	6	3	5	7	11	8	8	8	4	9	18	5
nmod:on_behalf_of	3	1	0
nmod:opposite	1	1	0
nmod:out	2	1	0
nmod:outside	2	0	2	3	3	0	4	1	0	6	0	1	8	0	1	9	0	1
nmod:over	1	3	3	2	9	3	3	6	5	4	4	5	5	3	3	6	2	2	7	3	2	8	2	3	9	3	0
nmod:past	3	1	0
nmod:per	1	2	0	2	5	4	3	9	2	4	8	5	5	1	2	6	3	1	7	1	1	8	2	0	9	2	1
nmod:poss	1	5	1	2	126	50	3	131	71	4	98	67	5	69	43	6	50	36	7	31	37	8	43	37	9	32	44
nmod:rather_than	3	0	1
nmod:regardless_of	1	1	0
nmod:since	1	2	6	2	1	2	3	1	0
nmod:such_as	2	1	4	3	0	5	4	0	5	5	1	3	6	0	5	7	1	2	8	1	10	9	1	4
nmod:than	2	2	3	3	1	2	4	0	2	5	0	2	6	0	3	7	0	3	8	0	5	9	0	6
nmod:that	9	0	1
nmod:through	1	2	0	2	3	4	3	0	2	4	0	1	5	0	1	6	1	0	8	1	0
nmod:throughout	1	4	0	2	1	0	3	1	1	4	1	0	5	1	0	6	1	1	7	1	0	8	1	0	9	1	0
nmod:tmod	1	16	61	2	9	15	3	4	16	4	4	5	5	5	5	6	3	2	7	4	3	8	2	2	9	4	3
nmod:to	1	49	20	2	50	27	3	49	46	4	27	30	5	24	24	6	22	22	7	20	24	8	23	25	9	24	32
nmod:towards	2	2	1	3	3	0	4	2	1	5	2	0	6	2	0	7	2	0	8	2	0	9	2	0
nmod:under	1	4	1	2	2	3	3	2	0	4	1	0	5	1	1	6	0	1	7	1	1	8	0	1	9	1	1
nmod:unlike	1	1	0
nmod:until	1	2	0	2	1	0	3	0	1	4	0	3	5	0	2	7	0	1	9	0	1
nmod:up	1	1	0	2	0	1
nmod:upon	2	1	0	3	1	0	5	0	1
nmod:via	4	0	1
nmod:with	1	22	15	2	38	20	3	28	23	4	40	19	5	49	14	6	57	15	7	89	12	8	152	12	9	280	12
nmod:within	1	1	3	2	4	3	3	8	2	4	13	0	5	24	1	6	48	0	7	97	0	8	192	1	9	384	0
nmod:without	1	1	0	2	2	0	3	0	2	4	0	4	5	0	2	6	0	3	7	0	3	8	0	4	9	0	2
nsubj	1	888	183	2	579	178	3	357	162	4	254	88	5	330	95	6	417	87	7	735	145	8	1228	189	9	2372	365
nsubjpass	1	158	28	2	76	31	3	34	17	4	25	10	5	23	8	6	34	7	7	40	10	8	72	11	9	129	13
nummod	1	1	1	2	65	33	3	61	37	4	44	33	5	15	26	6	15	22	7	6	25	8	17	32	9	9	54
parataxis	1	9	28	2	0	5	4	0	1	5	0	2	7	0	1	9	0	1
ref	1	4	5	2	46	51	3	39	43	4	78	84	5	55	85	6	96	129	7	75	158	8	126	285	9	116	451
root	0	1249	0
xcomp	1	158	63	2	83	44	3	51	47	4	19	20	5	35	21	6	30	26	7	29	29	8	40	34	9	56	42
HERE
);
    $sentences = explode("\n", $_POST['text']);

    $table=array();
    foreach($table_str as $line){
        $entries = explode("\t", $line);
        $entry = array();
        for($i=1;$i<count($entries);$i+=3){
            $entry[$entries[$i]] = array($entries[$i+1],$entries[$i+2]);
        }
        $table[$entries[0]] = $entry;
    }

    $results = $parser->parseSentences($sentences);

    foreach($results as $result){

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
        $deps_pruned = prune('ROOT', 'root', 0, $deps, $table);
        $repeated = array();
        $deps_pruned_cleaned = array();
        foreach($deps_pruned as $entry){
            $key = implode(" ",$entry);
            if(!isset($repeated[$key])){
                $deps_pruned_cleaned[] = $entry;
                $repeated[$key] = 1;
            }
        }
        if(count($deps_pruned_cleaned)){
            if($_POST['show']){
                echo "\n\n";
                foreach($deps as $entry){
                    echo $entry[0]."(".$entry[1].",".$entry[2].")\n";
                }
                echo "\n";
                foreach($deps_pruned_cleaned as $entry){
                    echo $entry[0]."(".$entry[1].",".$entry[2].")\n";
                }
                echo "\n";
            }
            echo ucfirst($gen->generate($deps_pruned_cleaned)).".";
            if($_POST['tree']){
                echo "\n\n";
                show_tree('root', $gen->semantics(),"");
                echo "\n\n\n";
            }
            if($_POST['show']){
                echo"\n";
            }else{
                echo" ";
            }
        }
    }
}else{
?>
<html><head><title>Sentence Compression by re-generation using PHP-NLGen Simplified Technical English Demo</title>
    <body>
    <h1>Sentence Compression by re-generation using PHP-NLGen Simplified Technical English Demo</h1>
    <p>Using <a href="http://nlp.stanford.edu:8080/parser/index.jsp">Stanford dependencies</a> over <a href="https://en.wikipedia.org/wiki/Simplified_Technical_English">Simplified Technical English</a> text, using <a href="https://github.com/DrDub/php-nlgen/">PHP-NLGen</a>. Model trained  on <a href="http://jamesclarke.net/research/resources/">James Clarke's sentence compresssion corpus</a></p>
    <p>Text to compress, one sentence per line:</p>
    <form method="post">
    <textarea name="text" cols=60 rows=20>Artificial Intelligence (AI) is the ability of a computer program or a machine to think and learn.
It is also a field of study which tries to make computers "smart".
John McCarthy came up with the name "artificial intelligence" in 1955.
The goal of AI research is to create computer programs that can learn, solve problems, and think logically.
AI involves many different fields like computer science, mathematics, linguistics, psychology, neuroscience, and philosophy.
Eventually researchers hope to create a "general artificial intelligence" which can solve many problems instead of focusing on just one.
Researchers are also trying to create creative and emotional AI which can possibly empathize or create art.
Many approaches and tools have been tried.
AI research really started with a conference at Dartmouth College in 1956. 
It was a month long brainstorming session attended by many people that are important in AI today. 
At the conference they wrote programs that were amazing at the time, beating people at checkers or solving word problems. 
The U.S. Department of Defense started giving a lot of money to AI research and labs were created all over the world.
Unfortunately, researchers really underestimated just how hard some problems were. 
The tools they had used still did not give computers things like emotions or common sense. 
Mathematician James Lighthill wrote a report on AI saying that "in no part of the field have discoveries made so far produced the major impact that was then promised", and U.S and British governments wanted to fund more productive projects. 
Funding for AI research was cut, starting an "AI winter" where little to no research was done.
AI research revived in the 1980s because of the popularity of expert systems, which simulated the knowledge of a human expert. 
By 1985, 1 billion dollars were spent on AI. 
New, faster computers convinced U.S and British governments to start funding AI research again. 
However, the market for Lisp machines collapsed in 1987 and funding was pulled again, starting an even longer AI winter.
AI revived again in the 90s and early 2000s with its use in data mining and medical diagnosis. 
This was possible because of faster computers and focusing on solving more specific problems. 
In 1997, Deep Blue became the first computer program to beat chess world champion Garry Kasparov. 
Faster computers and access to more data have made AI popular throughout the world. 
In 2011 IBM Watson beat the top two Jeopardy! players Brad Rutter and Ken Jennings, and in 2016 Google's AlphaGo beat top Go player Lee Sedol 4 out of 5 times.</textarea><br>
    Show compressed dependencies <input type="checkbox" name="show"><br>
    Show generator tree <input type="checkbox" name="tree"><br>
    <input type="submit">
    </form>
</body>
</html>
<?php
}
