#!/bin/sh
iconv -f IBM866 -t UTF8 < TNVED2.Txt > tnved1.csv
iconv -f IBM866 -t UTF8 < TNVED3.TXT > tnved2.csv
iconv -f IBM866 -t UTF8 < TNVED4.TXT > tnved3.csv
