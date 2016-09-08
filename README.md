Simple Technical English Compressor
===================================

This is a demo for the (partial) surface realizer for STE built during the INLG2016 hackathon.

It requires the StanfordNLP Parser and contains a fork of
PHP-Stanford-NLP that supports basic dependencies.

To install:

* Download the Stanford NLP parser
* Set in compress.php the path to the parser and to the java command
* Feed a text, one sentence per line through the web interface.

A demo is available at

http://cheron.ca/pablo/inlg2016hackathon/compress.php


How the compression works
-------------------------

Using the written summary by James Clarke's thesis, the script
PHP-Stanford-NLP/sentcomprtraining.php parses both the original and
the compressed sentence, marking the dependencies in the original
sentence as kept or pruned.

From here, a table of counts of "depth / dependency name / number of
times kept / number of times pruned" can be assembled. With this table
of counts, a crude pruning of dependencies can be done: if at a given
depth, a given dependency was pruned more often than not, then
compress.php will prune it.

This is very poor for a number of reasons: it doesn't normalize /
smooth the counts and the model is not lexicalized (it uses only the
dependency, not the words).


A note on the surface realizer
------------------------------

The surface realizer is intended to generate Simple Technical
English. Try feeding the system text from http://simple.wikipedia.org.
Regular English text will most likely exceed the generator
capabilities most of the time.


License
-------

This example is distributed under the same license as StanfordNLP
parser (GPL v2).