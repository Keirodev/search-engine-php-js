<?php
$indexJson = <<<JSON
{"completely":[{"f":1,"w":1.076}],"headline":[{"f":1,"w":1.076}],"another":[{"f":1,"w":1.16}],"example":[{"f":1,"w":1.16},{"f":2,"w":1.2381656804734}],"lorem":[{"f":1,"w":1.151022592},{"f":2,"w":1.0461538461538}],"ipsum":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"dolor":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"sit":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"amet":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"consetetur":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"sadipscing":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"elitr":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"ut":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"wisi":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"enim":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"ad":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"minim":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"veniam":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"quis":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"nostrud":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"exerci":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"tation":[{"f":1,"w":1.048},{"f":2,"w":1.0461538461538}],"l'illusion":[{"f":1,"w":1.048}],"this":[{"f":2,"w":1.0730769230769}],"is":[{"f":2,"w":1.0730769230769}],"an":[{"f":2,"w":1.0730769230769}],"page":[{"f":2,"w":1.2381656804734}],"l'attente":[{"f":2,"w":1.0461538461538}]}
JSON;

$filesJson = <<<JSON
{"1":{"url":".\/\/data\/other.html","title":"A completely different headline"},"2":{"url":".\/\/data\/simple.html","title":"This is an Example Page"}}
JSON;

// preg_replace removes "Control Characters" to avoid further json_decode error
$indexJson = preg_replace('/[[:cntrl:]]/', '', $indexJson);
$index = json_decode($indexJson);

$filesJson = preg_replace('/[[:cntrl:]]/', '', $filesJson);
$files = json_decode($filesJson);