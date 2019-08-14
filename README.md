# pr0gramm-comment2txt
CLI-Script zum Exportieren aller Kommentare eines Users.

## Abhängigkeiten
Damit der Bot funktioniert muss der [pr0gramm-apiCall](https://github.com/RundesBalli/pr0gramm-apiCall) eingebunden werden.  
Der apiCall wird in [Zeile 15](https://github.com/RundesBalli/pr0gramm-comment2txt/blob/master/comment2txt.php#L15) eingebunden (Pfad muss angepasst werden!)

## Nutzung
Es handelt sich hierbei um ein CLI Script, d.h. es wird im Terminal ausgeführt.  
Beispielaufruf:  
`php ./comment2text.php RundesBalli 15`  
Der User `RundesBalli` wird mit Flags `15` (all) gecrawlt.  
Für andere Flags siehe [hier](https://github.com/RundesBalli/pr0gramm-comment2txt/blob/master/comment2txt.php#L88).

## Ausgabe
Das Script legt im Arbeitsverzeichnis eine `.txt` Datei an:  
`comments_{USERNAME}_f{FLAGS}_{dmY_His}.txt`
