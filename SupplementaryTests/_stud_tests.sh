#!/usr/bin/env bash

# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# IPP - cst - doplňkové testy - 2014/2015
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# Činnost: 
# - vytvoří výstupy studentovy úlohy v daném interpretu na základě sady testů
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
# Popis (README):
#
# Struktura skriptu _stud_tests.sh (v kódování UTF-8):
# Každý test zahrnuje až 4 soubory (vstupní soubor, případný druhý vstupní 
# soubor, výstupní soubor, soubor logující chybové výstupy *.err vypisované na 
# standardní chybový výstup (pro ilustraci) a soubor logující návratový kód 
# skriptu *.!!!). Pro spuštění testů je nutné do stejného adresáře zkopírovat i 
# váš skript. V komentářích u jednotlivých testů jsou uvedeny dodatečné 
# informace jako očekávaný návratový kód. 
#
# Proměnné ve skriptu _stud_tests.sh pro konfiguraci testů:
#  INTERPRETER - využívaný interpret 
#  EXTENSION - přípona souboru s vaším skriptem (jméno skriptu je dáno úlohou) 
#  LOCAL_IN_PATH/LOCAL_OUT_PATH - testování různých cest ke vstupním/výstupním
#    souborům
#  
# Další soubory archivu s doplňujícími testy:
# V adresáři ref-out najdete referenční soubory pro výstup (*.out nebo *.xml), 
# referenční soubory s návratovým kódem (*.!!!) a pro ukázku i soubory s 
# logovaným výstupem ze standardního chybového výstupu (*.err). Pokud některé 
# testy nevypisují nic na standardní výstup nebo na standardní chybový výstup, 
# tak může odpovídající soubor v adresáři chybět nebo mít nulovou velikost.
#
# Doporučení a poznámky k testování:
# Tento skript neobsahuje mechanismy pro automatické porovnávání výsledků vašeho 
# skriptu a výsledků referenčních (viz adresář ref-out). Pokud byste rádi 
# využili tohoto skriptu jako základ pro váš testovací rámec, tak doporučujeme 
# tento mechanismus doplnit.
# Dále doporučujeme testovat různé varianty relativních a absolutních cest 
# vstupních a výstupních souborů, k čemuž poslouží proměnné začínající 
# LOCAL_IN_PATH a LOCAL_OUT_PATH (neomezujte se pouze na zde uvedené triviální 
# varianty). 
# Výstupní soubory mohou při spouštění vašeho skriptu již existovat a pokud není 
# u zadání specifikováno jinak, tak se bez milosti přepíší!           
# Výstupní soubory nemusí existovat a pak je třeba je vytvořit!
# Pokud běh skriptu skončí s návratovou hodnotou různou od nuly, tak není 
# vytvoření souboru zadaného parametrem --output nutné, protože jeho obsah 
# stejně nelze považovat za validní.
# V testech se jako pokaždé určitě najdou nějaké chyby nebo nepřesnosti, takže 
# pokud nějakou chybu najdete, tak na ni prosím upozorněte ve fóru příslušné 
# úlohy (konstruktivní kritika bude pozitivně ohodnocena). Vyhrazujeme si také 
# právo testy měnit, opravovat a případně rozšiřovat, na což samozřejmě 
# upozorníme na fóru dané úlohy.
#
# =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

TASK=cst
INTERPRETER="php -d open_basedir=\"\""
EXTENSION=php
#INTERPRETER=python3
#EXTENSION=py

# cesty ke vstupním a výstupním souborům
LOCAL_IN_PATH="./" # (simple relative path)
LOCAL_IN_PATH2="" #Alternative 1 (primitive relative path)
LOCAL_IN_PATH3=`pwd`"/" #Alternative 2 (absolute path)
LOCAL_OUT_PATH="./" # (simple relative path)
LOCAL_OUT_PATH2="" #Alternative 1 (primitive relative path)
LOCAL_OUT_PATH3=`pwd`"/" #Alternative 2 (absolute path)
# cesta pro ukládání chybového výstupu studentského skriptu
LOG_PATH="./"


# test01: Zobrazeni napovedy; Expected output: test01.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --help > ${LOCAL_OUT_PATH}test01.out 2> ${LOG_PATH}test01.err
echo -n $? > test01.!!!

# test02: Parametr -o; Expected output: test02.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}dir/file.c --output=${LOCAL_OUT_PATH}test02.out -o 2> ${LOG_PATH}test02.err
echo -n $? > test02.!!!

# test03: Parametr -k; Expected output: test03.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH3}dir/ --output=${LOCAL_OUT_PATH2}test03.out -k 2> ${LOG_PATH}test03.err
echo -n $? > test03.!!!

# test04: Prazdny adresar; Expected output: test04.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}dir/emptydir --output=${LOCAL_OUT_PATH}test04.out -k 2> ${LOG_PATH}test04.err
echo -n $? > test04.!!!

# test05: Parametr -i; Expected output: test05.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}dir/ --output=${LOCAL_OUT_PATH3}test05.out -i 2> ${LOG_PATH}test05.err
echo -n $? > test05.!!!

# test06: Parametr -c; Expected output: test06.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH2}dir/file.c -c > ${LOCAL_OUT_PATH}test06.out 2> ${LOG_PATH}test06.err
echo -n $? > test06.!!!

# test07: Parametr -w; Expected output: test07.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION -w=ZZ > ${LOCAL_OUT_PATH}test07.out 2> ${LOG_PATH}test07.err
echo -n $? > test07.!!!

# test08: Parametr -p; Expected output: test08.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION -w=ZZ -p --output=${LOCAL_OUT_PATH3}test08.out 2> ${LOG_PATH}test08.err
echo -n $? > test08.!!!

# test09: Neznamy parametr; Expected output: test09.out; Expected return code: 1
$INTERPRETER $TASK.$EXTENSION --unknown-parameter > ${LOCAL_OUT_PATH}test09.out 2> ${LOG_PATH}test09.err
echo -n $? > test09.!!!

# test10: Neplatna kombinace parametru; Expected output: test10.out; Expected return code: 1
$INTERPRETER $TASK.$EXTENSION -k -o > ${LOCAL_OUT_PATH}test10.out 2> ${LOG_PATH}test10.err
echo -n $? > test10.!!!

# test11: Nelze otevrit vstupni soubor; Expected output: test11.out; Expected return code: 2
$INTERPRETER $TASK.$EXTENSION --input=/path/to/a/hopefully/nonexistent/file -o > ${LOCAL_OUT_PATH}test11.out 2> ${LOG_PATH}test11.err
echo -n $? > test11.!!!

# test12: Nelze otevrit vystupni soubor; Expected output: test12.out; Expected return code: 3
$INTERPRETER $TASK.$EXTENSION --output=/hopefully-no-write-permissions -o > ${LOCAL_OUT_PATH}test12.out 2> ${LOG_PATH}test12.err
echo -n $? > test12.!!!

# test13: Parametr -k kombinovany s --nosubdir; Expected output: test13.out; Expected return code: 0
$INTERPRETER $TASK.$EXTENSION --input=${LOCAL_IN_PATH}dir/ --output=test13.out -k --nosubdir 2> test13.err
echo -n $? > test13.!!!

