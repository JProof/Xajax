Xajax ist nun deutlich in die Jahre gekommen gewesen. Die alte Xajax Javascript-struktur hatte den eigentlichen Ajax-Kern sowie diverse Html/Dom Methoden
in einem Namensbereich. Das macht es natürlich schwierig neue Erweiterungen in Xajax einzubauen. Um dieses Knäuel aufzulösen wurden in dieser Xajax Version
die Ajax und Html/Dom Bestandteile weitestgehend von einander getrennt. Selbstverständlich bleiben alle Parts in einer Datei enthalten.

Ohne die jQuery-Implementierung im Detail zu kennen, jedoch wohlwissend um die Verwendung dieser Klasse und dessen Sprachgebrauchs, wurden die ohnehin vorhanden xajax-html Teile in Anlehnung an jQuery programmiert.   
Damit lässt sich das Potenzial von xajax, welches ohnehin vorhanden war, deutlich einfacher ausschöpfen. 


````javascript 1.5

$x('element-id');
````