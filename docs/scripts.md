Alle Javascript Dateien jederzeit an jeder Stelle geändert werden.
Hierzu gibt es "Scripts" als generelle Methode

"scriptlate" anderer Name für Script und Template
Hier werden einzelne Javascript Teile geparst.
Overrideable

Scriptsordering wird benötigt um den Ablauf bzw. die Reihenfolge der Scripts zu organisieren.

Alle directories müssen vom public_html dir aus markiert sein
// prevents from rendering/parsing into frontend
lockScript($name)

* Erklärung scriptname und load-Check-Method
* Load-Checkmethod auch für andere scripte
    * configurierbar machen