#!/usr/bin/python

import os, sys;

variable1 = "";
variable1 = "Ahoj první řádek\n";

variable1+="Ahoj druhý řádek\n";
variable1+="ahoj třetí řádek";
variable1+=" - aahoj, stále třetí řádek\n";

sys.stdout.write(variable1);